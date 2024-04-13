<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_m extends MY_Model {

	protected $_table = 'sms';

	protected $sms_balance = 0;
	protected $application_settings = array();

	function __construct(){
		$this->load->model('sms_templates/sms_templates_m');
		$this->load->model('settings/settings_m');
		$this->load->dbforge();
		$this->load->library('ATSMS');
		$this->load->library('SMS_Gateway');
		$this->load->library('africastalking_manager');
		$this->application_settings = $this->settings_m->get_settings()?:'';
		//$this->install();
	}

	/**
	Note:
		sms_to is the phone number of the recipient

		$user_id => the User Id of the recipient
		$member_id => Member Id of the recipient
		$group_id = The id of the group the sms is from

		///incase is the system sending the sms
		1 - TRUE (SYSTEM SMS)
		else/NUll not a system sms

		Created by is the id of the sender


	*/

	function install(){
		//for sms's that are sent out
		$this->db->query("
		create table if not exists sms(
			id int not null auto_increment primary key,
			`sms_to` blob,
			`sms_result_id` blob,
			`message` blob,
			`group_id` blob,
			`member_id` blob,
			`user_id` blob,
			`system_sms` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		// A queue of sms's to be sent in intervals
		$this->db->query("
		create table if not exists sms_queue(
			id int not null auto_increment primary key,
			`sms_to` blob,
			`message` blob,
			`group_id` blob,
			`member_id` blob,
			`user_id` blob,
			`system_sms` blob,
			`insufficent_group_sms_balance` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		//a log of the way sms were sent

		$this->db->query("
		create table if not exists sms_result(
			id int not null auto_increment primary key,
			`sms_id` blob,
			`sms_number` blob,
			`sms_status` blob,
			`message_id` blob,
			`sms_cost` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	function send_sms($phone_number='',$message='',$member_id ='',$user_id='',$group_id='',$created_by='',$system_sms=''){
		$from = isset($this->application_settings->sender_id)?$this->application_settings->sender_id:'Eazzykikundi';
		$user = $this->ion_auth->get_user($user_id);
		$reference_number = rand(100000000,999999999);
		$sms_array = array(
			'phone'=>$phone_number,
			'message'=>$message,
			'from'=>'Websacco'
		);
		if(preg_match('/(eazzy)/i',$this->application_settings->application_name)){			
			$result = $this->sms_gateway->send_sms_via_equity_bank($phone_number,$message,$from,$reference_number,$user);			
	    }else{
			// $result = $this->africastalking_manager->send_sms_via_africstalking_api($sms_array);
			$result = $this->africastalking_manager->sendMessage($phone_number,$message);
			$result = json_encode($result);
	    }
	    if($result){
        	$id = $this->insert(
				array(
					'sms_to'	=>	$phone_number,
					'sms_result_id'	=>	$result,
					'member_id' => $member_id,
					'user_id' => $user_id,
					'message'	=>	$message,
					'group_id' => $group_id,
					'created_by'=>	$created_by,
					'reference_number' => $reference_number,
					'created_on'=>  time(),
					'system_sms' => $system_sms,
				)
			);
			if($id){
				return TRUE;
			}else{
				return FALSE;
			}
        }else{
        	return FALSE;
        }
	}
	function send_system_sms($phone_number='',$message='',$created_by='',$user_id=''){
		$from = isset($this->application_settings->sender_id)?$this->application_settings->sender_id:"Eazzykikundi";
		$user = $this->ion_auth->get_user($user_id);
		$reference_number = rand(100000000,999999999);
		$sms_array = array(
			'phone'=>$phone_number,
			'message'=>$message,
			'from'=>'Websacco'
		);
		if(preg_match('/eazzy/i', $this->application_settings->application_name)){
			$result = $this->sms_gateway->send_sms_via_equity_bank($phone_number,$message,$from,$reference_number,$user);			
	    }else{
			// $result = $this->africastalking_manager->send_sms_via_africstalking_api($sms_array);
			$result = $this->africastalking_manager->send_sms($phone_number,$message);
			$result = json_encode($result);
	    }
        if($result){
			$id = $this->insert(
				array(
					'sms_to'	=>	$phone_number,
					'sms_result_id'	=>	$result,
					'message'	=>	$message,
					'system_sms'=>	1,
					'created_by'=>	$created_by?$created_by:1,
					'reference_number' => $reference_number,
					'created_on'=>  time(),
				)
			);
			if($id){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}


	function insert($input = array(),$key=FALSE){
        return $this->insert_secure_data('sms', $input);
	}

	function insert_sms_result($input = array(),$key=FALSE){
        return $this->insert_secure_data('sms_result', $input);
	}

	function get_smses_to_send($limit = 5){
		$this->select_all_secure('sms_queue');
		$this->db->where($this->dx('message')." != '' ",NULL,FALSE);
		//$this->db->where($this->dx('sms_to')." IS NOT NULL ",NULL,FALSE);
		$this->db->where('('.$this->dx('insufficent_group_sms_balance').'is NULL OR '.$this->dx('insufficent_group_sms_balance').' ="" OR '.$this->dx('insufficent_group_sms_balance').' ="0" OR '.$this->dx('insufficent_group_sms_balance').'=" ")',NULL,FALSE);
		$this->db->limit($limit);
		$this->db->order_by('id','ASC');
		return $this->db->get('sms_queue')->result();
	}

	function get_all_smses_with_insufficient_error(){
		$select =   array(
                $this->dx('group_id').' as group_id',
                'count('.$this->dx('group_id').') as unset_smses'
            );
		return $this->db
		        ->select($select)
		        ->from('sms_queue')
		        ->where($this->dx('insufficent_group_sms_balance').'="1"',NULL,FALSE)
		        ->group_by($this->dx('sms_queue.group_id'))
		        ->get()
		        ->result_array();

	}

	function update_insufficient_where_group_id($group_id = 0){
		return $this ->db-> query("update sms_queue set
                insufficent_group_sms_balance = ".$this->exa('0')."
                where ".$this->dx("group_id")." ='".$group_id."'");
	}

	function insert_sms_queue($input = array()){
    	return $this->insert_secure_data('sms_queue',$input);		
    }

    function insert_batch_to_queue($input=array()){
    	return $this->insert_chunked_batch_secure_data('sms_queue',$input);
    }

	function get_sms_queue($group_id=0){
		$this->select_all_secure('sms_queue');
		$this->db->select(array(
				$this->dx('investment_groups.name').' as group_name',
			));
		if(isset($group_id) && !empty($group_id)){
			$this->db->where($this->dx('sms_queue.group_id').'="'.$group_id.'"',NULL,FALSE);
		}
		$this->db->join('investment_groups','investment_groups.id = '.$this->dx('sms_queue.group_id'));
		$this->db->order_by($this->dx('sms_queue.created_on'),'DESC',FALSE);
		return $this->db->get('sms_queue')->result();
	}


	function count_all_queued_smses($group_id=0){
		if(isset($group_id) && !empty($group_id)){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}
		return $this->db->count_all_results('sms_queue');
	}



	function update_queue_sms($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'sms_queue',$input);
	}

	function sms_queue_count(){
		return $this->db->count_all_results('sms_queue');
	}

	function delete_sms_queue($id = 0){
		$this->db->where('id',$id);
		return $this->db->delete('sms_queue');
	}

	function sent_sms_count(){
		return $this->db->count_all_results('sms');
	}

	function todays_sent_sms_count(){
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
		return $this->db->count_all_results('sms');
	}

	function get_all($group_id=0,$from = 0,$to = 0){
		$this->select_all_secure('sms');
		/*$this->db->select(array(
				$this->dx('investment_groups.name').' as group_name',
			));*/
		if(isset($group_id) && !empty($group_id)){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('sms.created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('sms.created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		//$this->db->join('investment_groups','investment_groups.id = '.$this->dx('sms.group_id'));

		$this->db->order_by($this->dx('sms.created_on'), 'DESC', FALSE);
		return $this->db->get('sms')->result();
	}

	function count_all($group_id=0,$from = 0,$to = 0)
	{
		if(isset($group_id) && !empty($group_id)){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		return $this->db->count_all_results('sms');
	}

	function get_all_results()
	{
		$this->select_all_secure('sms_result');
		$this->db->order_by($this->dx('created_on'), 'DESC', FALSE);
		return $this->db->get('sms_result')->result();
	}

	function build_sms_message($slug = '',$fields,$use_template = FALSE,$language_id=1){
		$data = '';
		if($use_template){
			$sms_template = $slug;
			if($sms_template){
				$data = $sms_template;
			}
		}else{
			$language_id = $language_id?:$this->application_settings->default_language_id;
			$sms_template = $this->sms_templates_m->get_by_slug($slug,'',$language_id);
			if($sms_template){
				$data = $sms_template->sms_template;
			}
		}

        foreach ($fields as $k => $v)
        {
            $data= preg_replace('/\[' . $k . '\]/', $v, $data);
        }
        return $data;
	}

	function build_predefined_sms_message($sms_template = '',$fields){
		$data = '';
		if($sms_template){
			$data = $sms_template;
		}
        foreach ($fields as $k => $v)
        {
            $data= preg_replace('/\[' . $k . '\]/', $v, $data);
        }
        return $data;
	}


	function get_queued_sms($id=0){
		$this->select_all_secure('sms_queue');
		$this->db->where('id',$id);
		return $this->db->get('sms_queue')->result();
	}

	/*************************Group sms part*******************************/


	function count_all_group_sms($filter_parameters = array(),$ignore_system_sms=FALSE){
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('created_on').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('created_on').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
				$member_list = '0';
				$members = $filter_parameters['member_id'];
				$count = 1;
				foreach($members as $member_id){
					if($member_id){
						if($count==1){
							$member_list = $member_id;
						}else{
							$member_list .= ','.$member_id;
						}
						$count++;
					}
				}

				if($member_list){
	        		$this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
				}
			}
		}
		if($ignore_system_sms){
			$this->db->where($this->dx('system_sms').'IS NULL',NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		return $this->db->count_all_results('sms')?:0;
	}

	function get_all_group_sms($filter_parameters = array(),$ignore_system_sms=FALSE){
		$this->select_all_secure('sms');
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('created_on').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('created_on').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
				$member_list = '0';
				$members = $filter_parameters['member_id'];
				$count = 1;
				foreach($members as $member_id){
					if($member_id){
						if($count==1){
							$member_list = $member_id;
						}else{
							$member_list .= ','.$member_id;
						}
						$count++;
					}
				}

				if($member_list){
	        		$this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
				}
			}
		}
		if($ignore_system_sms){
			$this->db->where($this->dx('system_sms').'IS NULL',NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'), 'DESC', FALSE);
		return $this->db->get('sms')->result();
	}

	function count_all_queued_group_sms($filter_parameters = array(),$ignore_system_sms=FALSE){
		$this->select_all_secure('sms_queue');
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('created_on').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('created_on').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
				$member_list = '0';
				$members = $filter_parameters['member_id'];
				$count = 1;
				foreach($members as $member_id){
					if($member_id){
						if($count==1){
							$member_list = $member_id;
						}else{
							$member_list .= ','.$member_id;
						}
						$count++;
					}
				}

				if($member_list){
	        		$this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
				}
			}
		}
		if($ignore_system_sms){
			$this->db->where($this->dx('system_sms').'IS NULL',NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		return $this->db->count_all_results('sms_queue')?:0;
	}

	function get_all_queued_group_sms($filter_parameters = array(),$ignore_system_sms=FALSE){
		$this->select_all_secure('sms_queue');
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('created_on').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('created_on').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
				$member_list = '0';
				$members = $filter_parameters['member_id'];
				$count = 1;
				foreach($members as $member_id){
					if($member_id){
						if($count==1){
							$member_list = $member_id;
						}else{
							$member_list .= ','.$member_id;
						}
						$count++;
					}
				}

				if($member_list){
	        		$this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
				}
			}
		}
		if($ignore_system_sms){
			$this->db->where($this->dx('system_sms').'IS NULL',NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'), 'DESC', FALSE);
		return $this->db->get('sms_queue')->result();
	}

	function count_all_group_member_received_smses($group_id=0,$member_id=0){
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
		}
		else{
			$this->db->where($this->dx('member_id').'="'.$this->member->id.'"',NULL,FALSE);
		}
		return $this->db->count_all_results('sms');
	}

	function get_all_group_member_received_smses($group_id=0,$member_id=0){
		$this->select_all_secure('sms');
		if($group_id){
			$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
		}
		else{
			$this->db->where($this->dx('member_id').'="'.$this->member->id.'"',NULL,FALSE);
		}
		return $this->db->get('sms')->result();
	}


	function delete_old_queued_smses(){
		$this->db->where($this->dx('created_on')." < '".strtotime('-2 days')."' ",NULL,FALSE);
		return $this->db->delete('sms_queue');
	}



}
