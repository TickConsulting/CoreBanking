<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Contributions_m extends MY_Model{

	protected $_table = 'contributions';

	function __construct(){
		parent::__construct();
		$this->load->model('members/members_m');
		$this->install();
	}

	function install(){
		$this->db->query("
			create table if not exists contributions(
				id int not null auto_increment primary key,
				`type` blob,
				`name` blob,
				`amount` blob,
				`group_id` blob,
				`regular_invoicing_active` blob,
				`one_time_invoicing_active` blob,
				`active` blob,
				`is_hidden` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists regular_contribution_settings(
				id int not null auto_increment primary key,
				`contribution_id` blob,
				`group_id` blob,
				`contribution_frequency` blob,
				`invoice_date` blob,
				`contribution_date` blob,
				`invoice_days` blob,
				`sms_notifications_enabled` blob,
				`email_notifications_enabled` blob,
				`sms_template` blob,
				`month_day_monthly` blob,
				`week_day_monthly` blob,
				`week_day_weekly` blob,
				`week_day_fortnight` blob,
				`week_number_fortnight` blob,
				`month_day_multiple` blob,
				`week_day_multiple` blob,
				`start_month_multiple` blob,
				`disable_overpayments` blob,
				`enable_fines` blob,
				`enable_contribution_member_list` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists one_time_contribution_settings(
				id int not null auto_increment primary key,
				`contribution_id` blob,
				`group_id` blob,
				`invoice_date` blob,
				`contribution_date` blob,
				`sms_notifications_enabled` blob,
				`email_notifications_enabled` blob,
				`sms_template` blob,
				`disable_overpayments` blob,
				`enable_fines` blob,
				`enable_contribution_member_list` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists contribution_fine_settings(
				id int not null auto_increment primary key,
				`contribution_id` blob,
				`group_id` blob,
				`fine_type` blob,
				`fixed_amount` blob,
				`fixed_fine_mode` blob,
				`fixed_fine_chargeable_on` blob,
				`fixed_fine_frequency` blob,
				`percentage_rate` blob,
				`percentage_fine_on` blob,
				`percentage_fine_chargeable_on` blob,
				`percentage_fine_mode` blob,
				`percentage_fine_frequency` blob,
				`fine_limit` blob,
				`fine_date` blob,
				`fine_sms_notifications_enabled` blob,
				`fine_email_notifications_enabled` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob
			)"
		);

		$this->db->query("
			create table if not exists contribution_member_pairings(
				id int not null auto_increment primary key,
				`contribution_id` blob,
				`group_id` blob,
				`member_id` blob,
				`created_by` blob,
				`created_on` blob
			)"
		);

		$this->db->query("
			create table if not exists member_checkoff_contribution_amount_pairings(
				id int not null auto_increment primary key,
				`contribution_id` blob,
				`group_id` blob,
				`member_id` blob,
				`amount` blob,
				`created_by` blob,
				`created_on` blob
			)"
		);
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('contributions',$input);
	}

	function insert_regular_contribution_setting($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('regular_contribution_settings',$input);
	}

	function insert_one_time_contribution_setting($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('one_time_contribution_settings',$input);
	}

	function insert_contribution_fine_setting($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('contribution_fine_settings',$input);
	}

	function update_contribution_fine_setting($id,$input=array(),$SKIP_VALIDATION = FALSE){
    	return $this->update_secure_data($id,'contribution_fine_settings',$input);
	}

	function safe_delete($id=0,$group_id=0){
		$this->db->where('id',$id);
		
		return $this->update_secure_data($id,'contributions',array('is_deleted'=>1,'modified_on'=>time()));
	}

	function get($id = 0){
		$this->select_all_secure('contributions');
		$this->db->where('id',$id);
		return $this->db->get('contributions')->row();
	}


	function contribution_exists_in_group($id=0,$group_id=0){
		$this->db->where('id',$id);
		
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('contributions')?:0;
	}

	function get_contribution_fine_settings($contribution_id = 0){
		$this->select_all_secure('contribution_fine_settings');
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('contribution_fine_settings')->result();
	}

	function insert_member_checkoff_contribution_amount_pairing($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('member_checkoff_contribution_amount_pairings',$input);
	}

	function get_group_checkoff_contribution_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		
		$this->db->where($this->dx('is_hidden').' != "1"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('enable_checkoff').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name;
		}
		return $arr;
	}
	function get_group_member_checkoff_contribution_amount_pairings_array(){
		$arr = array();
		$this->select_all_secure('member_checkoff_contribution_amount_pairings');
		
		$result = $this->db->get('member_checkoff_contribution_amount_pairings')->result();
		foreach($result as $row):
			$arr[$row->contribution_id][$row->member_id] = $row->amount;
		endforeach;
		return $arr;
	}

	function get_total_checkoff_set_amount($group_id=0){
		$this->db->select(array(
			'SUM('.$this->dx('amount').') as total_amount',
		));
		
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$result = $this->db->get('member_checkoff_contribution_amount_pairings')->row();
		if($result){
			return $result->total_amount;
		}else{
			return 0;
		}
	}

	function count_members_set_checkoff($group_id=0){
		$this->db->select(array(
			'DISTINCT('.$this->dx('member_id').') as member_id'
		));
		
		$counts = $this->db->get('member_checkoff_contribution_amount_pairings')->result();
		return count($counts);
	}

	function delete_member_checkoff_contribution_amount_pairing($member_id = 0,$contribution_id = 0){
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		
		return $this->db->delete('member_checkoff_contribution_amount_pairings');
	}

	function get_all_contribution_fine_settings_array($group_id=0){
		$arr = array();
		$this->select_all_secure('contribution_fine_settings');
		 
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$contribution_fine_settings = $this->db->get('contribution_fine_settings')->result();
		foreach($contribution_fine_settings as $contribution_fine_setting){
			$arr[$contribution_fine_setting->contribution_id][] = $contribution_fine_setting; 
		}
		return $arr;
	}

    

    function delete_contribution_fine_settings($contribution_id = 0,$group_id=0){
    	/**
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		return $this->db->delete('contribution_fine_settings');
		**/ 
		if($group_id){
			
		}else{
			$group_id = $this->group->id;
		}
		$where = " ".$this->dx('contribution_id')." = '".$contribution_id."' AND ".$this->dx('group_id')." = '".$group_id."' ;";
		$input = array(
			'is_deleted'=>1,
			'modified_on'=>time(),
		);
		return $this->update_secure_where($where,'contribution_fine_settings',$input);
    }

	function insert_contribution_member_pairing($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('contribution_member_pairings',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'contributions',$input);
    }

    function update_where($where = "",$input = array()){
    	return $this->update_secure_where($where,'contributions',$input);
    }

    function update_regular_contribution_setting($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'regular_contribution_settings',$input);
    }

    function update_one_time_contribution_setting($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'one_time_contribution_settings',$input);
    }
  
    function get_contribution_member_pairings_array($contribution_id = 0,$group_id = 0){
    	$arr = array();
    	$this->db->select(array($this->dx('member_id').' as member_id '));
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		 
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$contribution_member_pairings = $this->db->get('contribution_member_pairings')->result();
		foreach($contribution_member_pairings as $contribution_member_pairing){
			$arr[] = $contribution_member_pairing->member_id;
		}
		return $arr;
    }

    function get_all_contribution_member_pairings_array($group_id=0){
    	$arr = array();
    	$this->db->select(array($this->dx('member_id').' as member_id ',$this->dx('contribution_id').' as contribution_id '));
		 
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$contribution_member_pairings = $this->db->get('contribution_member_pairings')->result();
		foreach($contribution_member_pairings as $contribution_member_pairing){
			$arr[$contribution_member_pairing->contribution_id][] = $contribution_member_pairing->member_id;
		}
		return $arr;
    }

    function delete_contribution_member_pairings($contribution_id = 0,$group_id=0){
    	/*
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		return $this->db->delete('contribution_member_pairings'); 
		*/
		if($group_id){
			
		}else{
			$group_id = $this->group->id;
		}
		$where = " ".$this->dx('contribution_id')." = '".$contribution_id."' AND ".$this->dx('group_id')." = '".$group_id."' ;";
		$input = array(
			'is_deleted'=>1,
			'modified_on'=>time(),
		);
		return $this->update_secure_where($where,'contribution_member_pairings',$input);
		//return $this->update_secure_data($contribution_id,'contribution_member_pairings',array('is_deleted'=>1,'modified_on'=>time()));
    }

	function get_group_contribution($id = 0,$group_id = 0){
		$this->select_all_secure('contributions');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where('id',$id);
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted')." = '' )",NULL,FALSE);
		return $this->db->get('contributions')->row();
	}

	function check_group_contribution($id = 0,$group_id = 0){
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where('id',$id);
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted')." = '' )",NULL,FALSE);
		return $this->db->count_all_results('contributions')?:0;
	}


	function get_group_cumulative_arrears_contribution_id_list($group_id = 0){
		$cumulative_arrears_contribution_id_list = '0';
		$this->db->select('id');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('display_contribution_arrears_cumulatively').' = "1"',NULL,FALSE);
		$result = $this->db->get('contributions')->result();
		$count = 1;
		foreach($result as $row){
			if($count==1){
				$cumulative_arrears_contribution_id_list = $row->id;
			}else{
				$cumulative_arrears_contribution_id_list .= ','.$row->id;
			}
			$count++;
		}
		return $cumulative_arrears_contribution_id_list;
	}

	function get_group_arrears_contribution_id_list($group_id = 0){
		$arrears_contribution_id_list = '';
		$this->db->select('id');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where(" ( ".$this->dx('display_contribution_arrears_cumulatively').' IS NULL OR '.$this->dx('display_contribution_arrears_cumulatively').' = "0" ) ',NULL,FALSE);
		$result = $this->db->get('contributions')->result();
		$count = 1;
		foreach($result as $row){
			if($count==1){
				$arrears_contribution_id_list = $row->id;
			}else{
				$arrears_contribution_id_list .= ','.$row->id;
			}
			$count++;
		}
		return $arrears_contribution_id_list;
	}


	function get_group_contribution_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "")',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name;
		}
		return $arr;
	}

	function get_group_contribution_detail_options($group_id = 0){
		$arr = array();
		$this->select_all_secure('contributions');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "")',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution;
		}
		return $arr;
	}

	function get_group_non_refundable_contribution_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('contributions.is_non_refundable').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "")',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name;
		}
		return $arr;
	}
	function get_group_equitable_non_refundable_contribution_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('contributions.is_non_refundable').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('contributions.is_equity').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "")',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name;
		}
		return $arr;
	}

	function get_group_savings_contribution_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where($this->dx('contributions.category').' = "2" ',NULL,FALSE);
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "")',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name;
		}
		return $arr;
	}

	function get_group_refundable_contribution_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where("( ".$this->dx('is_non_refundable').' IS NULL OR '.$this->dx('is_non_refundable').' = "" OR '.$this->dx('is_non_refundable').' = "0" )',NULL,FALSE);
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "")',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name;
		}
		return $arr;
	}

	function get_group_contribution_with_disabled_arrears_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('display_contribution_arrears_cumulatively').' = "1" ',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name;
		}
		return $arr;
	}

	function get_group_contribution_fine_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_deleted').' = "" or is_deleted IS NULL ',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name.' fines ';
		}
		return $arr;
	}

	function get_active_group_contribution_details_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name ',
				$this->dx('amount').' as amount '

			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_hidden').' != "1"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution;
		}
		return $arr;
	}
	
	function get_active_group_contribution_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		
		$this->db->where($this->dx('is_hidden').' != "1"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name;
		}
		return $arr;
	}

	function get_active_group_contribution_fine_options($group_id = 0){
		$arr = array();
		$this->db->select(array(
				'id',
				$this->dx('name').' as name '
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_hidden').' != "1"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution){
			$arr[$contribution->id] = $contribution->name.' fines ';
		}
		return $arr;
	}

	function get_group_regular_contribution_setting($contribution_id = 0,$group_id = 0){
		$this->select_all_secure('regular_contribution_settings');
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('regular_contribution_settings')->row();
	}

	function get_group_one_time_contribution_setting($contribution_id = 0,$group_id = 0){
		$this->select_all_secure('one_time_contribution_settings');
		$this->db->select(
			array(
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('one_time_contribution_settings.invoice_date')." ),'%d-%m-%Y') as formatted_invoice_date ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('one_time_contribution_settings.contribution_date')." ),'%d-%m-%Y') as formatted_contribution_date ",
			)
		);
				
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->get('one_time_contribution_settings')->row();
	}

	function get_group_regular_contribution_settings_array($group_id=0){
		$arr = array();
		$this->select_all_secure('regular_contribution_settings');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$regular_contribution_settings = $this->db->get('regular_contribution_settings')->result();
		foreach($regular_contribution_settings as $regular_contribution_setting){
			$arr[$regular_contribution_setting->contribution_id] = $regular_contribution_setting;
		}
		return $arr;
	}

	function get_group_one_time_contribution_settings_array($group_id=0){
		$arr = array();
		$this->select_all_secure('one_time_contribution_settings');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$one_time_contribution_settings = $this->db->get('one_time_contribution_settings')->result();
		foreach($one_time_contribution_settings as $one_time_contribution_setting){
			$arr[$one_time_contribution_setting->contribution_id] = $one_time_contribution_setting;
		}
		return $arr;
	}

	function get_group_contributions_array($group_id = 0){
		$arr = array();
		$this->select_all_secure('contributions');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		$contributions = $this->db->get('contributions')->result();
		foreach ($contributions as $contribution) {
			# code...
			$arr[$contribution->id] = $contribution;
		}
		return $arr;
	}

	function get_group_contributions($group_id = 0,$filter_params=array(),$asc_dir="ASC"){
		$this->select_all_secure('contributions');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'contributions')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "" )',NULL,FALSE);
		$this->db->order_by($this->dx('name'),$asc_dir,FALSE);
		return $this->db->get('contributions')->result();
	}

	function get_group_contributions_with_active_invoicing($group_id = 0,$filter_params=array(),$asc_dir="ASC"){
		$this->select_all_secure('contributions');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('regular_invoicing_active').' = "1"',NULL,FALSE);
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'contributions')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "" )',NULL,FALSE);
		$this->db->order_by($this->dx('name'),$asc_dir,FALSE);
		return $this->db->get('contributions')->result();
	}




	function get_group_contributions_display_reports($group_id = 0,$filter_params=array(),$asc_dir="ASC"){
		$this->select_all_secure('contributions');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'contributions')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "" )',NULL,FALSE);
		$this->db->where('('.$this->dx('category').' IN(1,2) OR '.$this->dx('enable_deposit_statement_display').' ="1")',NULL,FALSE);
		$this->db->order_by($this->dx('name'),$asc_dir,FALSE);
		return $this->db->get('contributions')->result();
	}

	function get_group_contribution_refundable_options($group_id = 0,$filter_params=array(),$asc_dir="ASC"){
		$this->select_all_secure('contributions');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "" )',NULL,FALSE);
		$this->db->where($this->dx('category').' IN(1,2)',NULL,FALSE);
		$this->db->order_by($this->dx('name'),$asc_dir,FALSE);

		$results =  $this->db->get('contributions')->result();
		$arr = array();
		foreach ($results as $result) {
			$arr[$result->id] = $result->name;
		}
		return $arr;
	}

	function get_group_open_contribution_ids($group_ids=array()){
		$this->db->select('id');
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id').' IN ( 0 ) ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' IN ( '.implode(",",$group_ids)." ) ",NULL,FALSE);
		}
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "" )',NULL,FALSE);
		$this->db->where('('.$this->dx('category').' IN(1,2) OR '.$this->dx('enable_deposit_statement_display').' ="1")',NULL,FALSE);
		$contributions = $this->db->get('contributions')->result();
		$arr = array();
		if($contributions){
			foreach ($contributions as $contribution) {
				$arr[] = $contribution->id;
			}
		}
		return $arr;
	}


	function get_group_contribution_display_options($group_id = 0,$filter_params=array(),$asc_dir="ASC"){
		$this->select_all_secure('contributions');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "" )',NULL,FALSE);
		$this->db->where('('.$this->dx('category').' IN(1,2) OR '.$this->dx('enable_deposit_statement_display').' ="1")',NULL,FALSE);
		$this->db->order_by($this->dx('name'),$asc_dir,FALSE);

		$results =  $this->db->get('contributions')->result();
		$arr = array();
		foreach ($results as $result) {
			$arr[$result->id] = $result->name;
		}
		return $arr;
	}

	function get_contributions_category_not_set($group_id=0){
		$this->select_all_secure('contributions');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "" )',NULL,FALSE);
		$this->db->where('('.$this->dx('category').' IS NULL OR '.$this->dx('category').' = "")',NULL,FALSE);
		// $this->db->order_by($this->dx('name'),$asc_dir,FALSE);
		$results =  $this->db->get('contributions')->result();
		return $results;
	}

	function count_group_contributions($group_id=0,$filter_params=array()){
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		if($filter_params){
			foreach ($filter_params as $key => $value) {
				if($value && $this->db->field_exists($key, 'contributions')){
					$this->db->where("
					CONVERT(" . $this->dx($key) . " USING 'latin1')  like '%" . $this->escape_str($value) . "%'
					",NULL,FALSE);
				}
			}
		}
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "" )',NULL,FALSE);
		return $this->count_all_results('contributions');
	}

	function get_regular_contributions_to_be_invoiced_today($date = 0){
		$this->db->select(
			array(
				'contributions.id as id',
				'regular_contribution_settings.id as regular_contribution_setting_id',
				$this->dx('contributions.name').' as name ',
				$this->dx('contributions.amount').' as amount ',
				$this->dx('contributions.group_id').' as group_id ',
				$this->dx('regular_contribution_settings.invoice_date').' as invoice_date ',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('regular_contribution_settings.invoice_date')." ),'%Y %D %M') as invoice_date2 ",
				$this->dx('regular_contribution_settings.contribution_date').' as contribution_date ',
				$this->dx('regular_contribution_settings.contribution_frequency').' as contribution_frequency ',
				$this->dx('regular_contribution_settings.enable_contribution_member_list').' as enable_contribution_member_list ',
				$this->dx('regular_contribution_settings.month_day_monthly').' as month_day_monthly ',
				$this->dx('regular_contribution_settings.week_day_monthly').' as week_day_monthly ',
				$this->dx('regular_contribution_settings.week_day_weekly').' as week_day_weekly ',
				$this->dx('regular_contribution_settings.week_day_fortnight').' as week_day_fortnight ',
				$this->dx('regular_contribution_settings.week_number_fortnight').' as week_number_fortnight ',
				$this->dx('regular_contribution_settings.month_day_multiple').' as month_day_multiple ',
				$this->dx('regular_contribution_settings.week_day_multiple').' as week_day_multiple ',
				$this->dx('regular_contribution_settings.start_month_multiple').' as start_month_multiple ',
				$this->dx('regular_contribution_settings.invoice_days').' as invoice_days ',
				$this->dx('regular_contribution_settings.after_first_starting_day').'as  after_first_starting_day',
				$this->dx('regular_contribution_settings.after_first_contribution_day_option').'as after_first_contribution_day_option',
				$this->dx('regular_contribution_settings.after_first_day_week_multiple').'as after_first_day_week_multiple',
				$this->dx('regular_contribution_settings.after_second_starting_day').'as after_second_starting_day',
				$this->dx('regular_contribution_settings.after_second_contribution_day_option').'as after_second_contribution_day_option',
				$this->dx('regular_contribution_settings.after_second_day_week_multiple').'as after_second_day_week_multiple',
				)
			);
		$this->db->where($this->dx('contributions.type').' = "1"',NULL,FALSE);
		//$this->db->where($this->dx('contributions.group_id').' = "4023"',NULL,FALSE);
		$this->db->where($this->dx('contributions.regular_invoicing_active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contributions.active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('regular_contribution_settings.active').' = "1"',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('regular_contribution_settings.invoice_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->where($this->dx('regular_contribution_settings.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->where($this->dx('contributions.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->join('regular_contribution_settings','contributions.id = '.$this->dx('regular_contribution_settings.contribution_id'));
		return $this->db->get('contributions')->result();
	}


	function get_regular_contributions(){
		$this->db->select(
			array(
				'contributions.id as id',
				'regular_contribution_settings.id as regular_contribution_setting_id',
				$this->dx('contributions.name').' as name ',
				$this->dx('contributions.amount').' as amount ',
				$this->dx('contributions.group_id').' as group_id ',
				$this->dx('regular_contribution_settings.invoice_date').' as invoice_date ',
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('regular_contribution_settings.invoice_date')." ),'%Y %D %M') as invoice_date2 ",
				$this->dx('regular_contribution_settings.contribution_date').' as contribution_date ',
				$this->dx('regular_contribution_settings.contribution_frequency').' as contribution_frequency ',
				$this->dx('regular_contribution_settings.enable_contribution_member_list').' as enable_contribution_member_list ',
				$this->dx('regular_contribution_settings.month_day_monthly').' as month_day_monthly ',
				$this->dx('regular_contribution_settings.week_day_monthly').' as week_day_monthly ',
				$this->dx('regular_contribution_settings.week_day_weekly').' as week_day_weekly ',
				$this->dx('regular_contribution_settings.week_day_fortnight').' as week_day_fortnight ',
				$this->dx('regular_contribution_settings.week_number_fortnight').' as week_number_fortnight ',
				$this->dx('regular_contribution_settings.month_day_multiple').' as month_day_multiple ',
				$this->dx('regular_contribution_settings.week_day_multiple').' as week_day_multiple ',
				$this->dx('regular_contribution_settings.start_month_multiple').' as start_month_multiple ',
				$this->dx('regular_contribution_settings.invoice_days').' as invoice_days ',
				$this->dx('regular_contribution_settings.after_first_starting_day').'as  after_first_starting_day',
				$this->dx('regular_contribution_settings.after_first_contribution_day_option').'as after_first_contribution_day_option',
				$this->dx('regular_contribution_settings.after_first_day_week_multiple').'as after_first_day_week_multiple',
				$this->dx('regular_contribution_settings.after_second_starting_day').'as after_second_starting_day',
				$this->dx('regular_contribution_settings.after_second_contribution_day_option').'as after_second_contribution_day_option',
				$this->dx('regular_contribution_settings.after_second_day_week_multiple').'as after_second_day_week_multiple',
				)
			);
		$this->db->where($this->dx('contributions.type').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contributions.regular_invoicing_active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contributions.active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('regular_contribution_settings.active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contributions.is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('regular_contribution_settings.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->join('regular_contribution_settings','contributions.id = '.$this->dx('regular_contribution_settings.contribution_id'));
		return $this->db->get('contributions')->result();
	}

	function get_one_time_contributions_to_be_invoiced_today($date = 0){
		$this->db->select(
			array(
				'contributions.id as id',
				'one_time_contribution_settings.id as one_time_contribution_setting_id',
				$this->dx('contributions.name').' as name ',
				$this->dx('contributions.amount').' as amount ',
				$this->dx('contributions.group_id').' as group_id ',
				$this->dx('one_time_contribution_settings.invoice_date').' as invoice_date ',
				$this->dx('one_time_contribution_settings.enable_contribution_member_list').' as enable_contribution_member_list ',
				//"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('regular_contribution_settings.invoice_date')." ),'%Y %D %M') as invoice_date2 ",
				$this->dx('one_time_contribution_settings.contribution_date').' as contribution_date ',
				)
			);
		$this->db->where($this->dx('contributions.type').' = "2"',NULL,FALSE);
		$this->db->where($this->dx('contributions.one_time_invoicing_active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contributions.active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('one_time_contribution_settings.active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('one_time_contribution_settings.invoices_queued').' = "0"',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('one_time_contribution_settings.invoice_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->where($this->dx('one_time_contribution_settings.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->where($this->dx('contributions.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->join('one_time_contribution_settings','contributions.id = '.$this->dx('one_time_contribution_settings.contribution_id'));
		return $this->db->get('contributions')->result();
	}

	function get_contributions_to_be_fined_today($date = 0){
		$this->db->select(
			array(
				'contributions.id as id',
				//"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('contribution_fine_settings.fine_date')." ),'%Y %D %M') as invoice_date2 ",
				$this->dx('contributions.name').' as name ',
				$this->dx('contributions.amount').' as amount ',
				$this->dx('contributions.group_id').' as group_id ',
				$this->dx('contribution_fine_settings.fine_date').' as fine_date ',
				$this->dx('contribution_fine_settings.fine_type').' as fine_type ',
				$this->dx('contribution_fine_settings.fixed_amount').' as fixed_amount ',
				$this->dx('contribution_fine_settings.fixed_fine_chargeable_on').' as fixed_fine_chargeable_on ',
				$this->dx('contribution_fine_settings.percentage_fine_chargeable_on').' as percentage_fine_chargeable_on ',
				$this->dx('contribution_fine_settings.fixed_fine_frequency').' as fixed_fine_frequency ',
				$this->dx('contribution_fine_settings.percentage_fine_frequency').' as percentage_fine_frequency ',
				$this->dx('contribution_fine_settings.fixed_fine_mode').' as fixed_fine_mode ',
				$this->dx('contribution_fine_settings.percentage_rate').' as percentage_rate ',
				$this->dx('contribution_fine_settings.percentage_fine_on').' as percentage_fine_on ',
				$this->dx('contribution_fine_settings.percentage_fine_mode').' as percentage_fine_mode ',
				$this->dx('contribution_fine_settings.fine_limit').' as fine_limit ',
				$this->dx('contribution_fine_settings.fine_sms_notifications_enabled').' as fine_sms_notifications_enabled ',
				$this->dx('contribution_fine_settings.fine_email_notifications_enabled').' as fine_email_notifications_enabled ',
				$this->dx('regular_contribution_settings.enable_contribution_member_list').' as enable_contribution_member_list ',
				$this->dx('regular_contribution_settings.month_day_monthly').' as month_day_monthly ',
				$this->dx('regular_contribution_settings.week_day_monthly').' as week_day_monthly ',
				$this->dx('regular_contribution_settings.week_day_weekly').' as week_day_weekly ',
				$this->dx('regular_contribution_settings.week_day_fortnight').' as week_day_fortnight ',
				$this->dx('regular_contribution_settings.week_number_fortnight').' as week_number_fortnight ',
				$this->dx('regular_contribution_settings.month_day_multiple').' as month_day_multiple ',
				$this->dx('regular_contribution_settings.week_day_multiple').' as week_day_multiple ',
				$this->dx('regular_contribution_settings.start_month_multiple').' as start_month_multiple ',
				$this->dx('regular_contribution_settings.contribution_frequency').' as contribution_frequency ',
				$this->dx('regular_contribution_settings.contribution_date').' as contribution_date ',
				$this->dx('contribution_fine_settings.is_deleted').' as is_deleted ',
				'contribution_fine_settings.id as contribution_fine_setting_id ',
				$this->dx('regular_contribution_settings.enable_contribution_member_list').' as enable_contribution_member_list ',
				)
			);
		$this->db->where($this->dx('contributions.type').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('regular_contribution_settings.enable_fines').' = "1"',NULL,FALSE);
		$this->db->where(" ( ".$this->dx('contributions.regular_invoicing_active').' = "1" OR '.$this->dx('contributions.one_time_invoicing_active').' = "1" )',NULL,FALSE);
		$this->db->where($this->dx('contributions.active').' = "1"',NULL,FALSE);
		//$this->db->where($this->dx('contributions.group_id').' = "216"',NULL,FALSE);
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('contribution_fine_settings.fine_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->where($this->dx('contributions.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->where($this->dx('regular_contribution_settings.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->where($this->dx('contribution_fine_settings.is_deleted')." IS NULL ",NULL,FALSE);
        $this->db->where($this->dx('contribution_fine_settings.active')." = '1' ",NULL,FALSE);
        $this->db->join('contribution_fine_settings','contributions.id = '.$this->dx('contribution_fine_settings.contribution_id'));
        $this->db->join('regular_contribution_settings','contributions.id = '.$this->dx('regular_contribution_settings.contribution_id'));
		return $this->db->get('contributions')->result();
	}

	function get_contributions_to_be_fined($group_id = 0,$contribution_id = 0,$contribution_fine_setting_id = 0){
		$this->db->select(
			array(
				'contributions.id as id',
				//"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('contribution_fine_settings.fine_date')." ),'%Y %D %M') as invoice_date2 ",
				$this->dx('contributions.name').' as name ',
				$this->dx('contributions.amount').' as amount ',
				$this->dx('contributions.group_id').' as group_id ',
				$this->dx('contribution_fine_settings.fine_date').' as fine_date ',
				$this->dx('contribution_fine_settings.fine_type').' as fine_type ',
				$this->dx('contribution_fine_settings.fixed_amount').' as fixed_amount ',
				$this->dx('contribution_fine_settings.fixed_fine_chargeable_on').' as fixed_fine_chargeable_on ',
				$this->dx('contribution_fine_settings.percentage_fine_chargeable_on').' as percentage_fine_chargeable_on ',
				$this->dx('contribution_fine_settings.fixed_fine_frequency').' as fixed_fine_frequency ',
				$this->dx('contribution_fine_settings.percentage_fine_frequency').' as percentage_fine_frequency ',
				$this->dx('contribution_fine_settings.fixed_fine_mode').' as fixed_fine_mode ',
				$this->dx('contribution_fine_settings.percentage_rate').' as percentage_rate ',
				$this->dx('contribution_fine_settings.percentage_fine_on').' as percentage_fine_on ',
				$this->dx('contribution_fine_settings.percentage_fine_mode').' as percentage_fine_mode ',
				$this->dx('contribution_fine_settings.fine_limit').' as fine_limit ',
				$this->dx('contribution_fine_settings.fine_sms_notifications_enabled').' as fine_sms_notifications_enabled ',
				$this->dx('contribution_fine_settings.fine_email_notifications_enabled').' as fine_email_notifications_enabled ',
				$this->dx('regular_contribution_settings.enable_contribution_member_list').' as enable_contribution_member_list ',
				$this->dx('regular_contribution_settings.month_day_monthly').' as month_day_monthly ',
				$this->dx('regular_contribution_settings.week_day_monthly').' as week_day_monthly ',
				$this->dx('regular_contribution_settings.week_day_weekly').' as week_day_weekly ',
				$this->dx('regular_contribution_settings.week_day_fortnight').' as week_day_fortnight ',
				$this->dx('regular_contribution_settings.week_number_fortnight').' as week_number_fortnight ',
				$this->dx('regular_contribution_settings.month_day_multiple').' as month_day_multiple ',
				$this->dx('regular_contribution_settings.week_day_multiple').' as week_day_multiple ',
				$this->dx('regular_contribution_settings.start_month_multiple').' as start_month_multiple ',
				$this->dx('regular_contribution_settings.contribution_frequency').' as contribution_frequency ',
				$this->dx('regular_contribution_settings.contribution_date').' as contribution_date ',
				'contribution_fine_settings.id as contribution_fine_setting_id ',
				$this->dx('regular_contribution_settings.enable_contribution_member_list').' as enable_contribution_member_list ',
				)
			);
		$this->db->where($this->dx('regular_contribution_settings.enable_fines').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contributions.type').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contributions.group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where('contributions.id',$contribution_id);
		$this->db->where('contribution_fine_settings.id',$contribution_fine_setting_id);
		$this->db->where(" ( ".$this->dx('contributions.regular_invoicing_active').' = "1" OR '.$this->dx('contributions.one_time_invoicing_active').' = "1" )',NULL,FALSE);
		$this->db->where($this->dx('contributions.active').' = "1"',NULL,FALSE);
        //$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('contribution_fine_settings.fine_date')."),'%Y %D %M') = '" . date('Y jS F',$date) . "'", NULL, FALSE);
        $this->db->where($this->dx('contribution_fine_settings.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->where($this->dx('contributions.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->where($this->dx('regular_contribution_settings.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->join('contribution_fine_settings','contributions.id = '.$this->dx('contribution_fine_settings.contribution_id'));
        $this->db->join('regular_contribution_settings','contributions.id = '.$this->dx('regular_contribution_settings.contribution_id'));
		return $this->db->get('contributions')->result();
	}


	/****for billing purposes***/

	function get_group_regular_contributions($group_id=0){
		$this->db->select(array(
				'contributions.id as id',
				$this->dx('contributions.amount').' as amount',
				$this->dx('regular_contribution_settings.contribution_frequency').' as frequency',
			));
		$this->db->where($this->dx('contributions.group_id').'="'.$group_id.'"');
		$this->db->where($this->dx('contributions.active').'="1"');
		$this->db->where($this->dx('contributions.is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('regular_contribution_settings.is_deleted').' IS NULL',NULL,FALSE);
		$this->db->join('regular_contribution_settings','contributions.id = '.$this->dx('regular_contribution_settings.contribution_id'));
		$contributions = $this->db->get('contributions')->result();
		if($contributions){
			$result = array();
			foreach ($contributions as $contribution) {
				$this->db->where($this->dx('contribution_id').'="'.$contribution->id.'"',NULL,FALSE);
				$this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
				$count = $this->db->count_all_results('contribution_member_pairings')?:0;
				if($count==0){
					$members = $this->members_m->count_all_active_group_members($group_id);
				}
				else{
					$members = $count;
				}

				$result[] = array('members'=>$members,'amount'=>$contribution->amount,'frequency'=>$contribution->frequency);
			}

			return $result;

			
		}else{
			return 0;
		}
	}

	/****for Admin view***/

	function get_group_one_time_contributions_for_admin($group_id=0){
		$this->db->select(
			array(
				'contributions.id as id',
				'one_time_contribution_settings.id as one_time_contribution_setting_id',
				$this->dx('contributions.name').' as name ',
				$this->dx('contributions.amount').' as amount ',
				$this->dx('contributions.group_id').' as group_id ',
				$this->dx('one_time_contribution_settings.invoice_date').' as invoice_date ',
				$this->dx('one_time_contribution_settings.enable_contribution_member_list').' as enable_contribution_member_list ',
				$this->dx('one_time_contribution_settings.contribution_date').' as contribution_date ',
				)
			);
		$this->db->where($this->dx('contributions.type').' = "2"',NULL,FALSE);
		$this->db->where($this->dx('contributions.group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contributions.is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('one_time_contribution_settings.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->join('one_time_contribution_settings','contributions.id = '.$this->dx('one_time_contribution_settings.contribution_id'));
		return $this->db->get('contributions')->result();
	}

	function get_group_regular_contributions_for_admin($group_id=0){
		$this->db->select(
			array(
				'contributions.id as id',
				'regular_contribution_settings.id as regular_contribution_setting_id',
				$this->dx('contributions.name').' as name ',
				$this->dx('contributions.amount').' as amount ',
				$this->dx('contributions.group_id').' as group_id ',
				$this->dx('regular_contribution_settings.invoice_date').' as invoice_date ',
				//"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('regular_contribution_settings.invoice_date')." ),'%Y %D %M') as invoice_date2 ",
				$this->dx('regular_contribution_settings.contribution_date').' as contribution_date ',
				$this->dx('regular_contribution_settings.contribution_frequency').' as contribution_frequency ',
				$this->dx('regular_contribution_settings.enable_contribution_member_list').' as enable_contribution_member_list ',
				$this->dx('regular_contribution_settings.month_day_monthly').' as month_day_monthly ',
				$this->dx('regular_contribution_settings.week_day_monthly').' as week_day_monthly ',
				$this->dx('regular_contribution_settings.week_day_weekly').' as week_day_weekly ',
				$this->dx('regular_contribution_settings.week_day_fortnight').' as week_day_fortnight ',
				$this->dx('regular_contribution_settings.week_number_fortnight').' as week_number_fortnight ',
				$this->dx('regular_contribution_settings.month_day_multiple').' as month_day_multiple ',
				$this->dx('regular_contribution_settings.week_day_multiple').' as week_day_multiple ',
				$this->dx('regular_contribution_settings.start_month_multiple').' as start_month_multiple ',
				$this->dx('regular_contribution_settings.invoice_days').' as invoice_days ',
				)
			);
		$this->db->where($this->dx('contributions.type').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('contributions.group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('regular_contribution_settings.is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('contributions.is_deleted').' IS NULL',NULL,FALSE);
        $this->db->join('regular_contribution_settings','contributions.id = '.$this->dx('regular_contribution_settings.contribution_id'));
		return $this->db->get('contributions')->result();
	}


	function count_contributions_based_on_frequecy($contribution_frequency=0){
		$this->db->where($this->dx('contribution_frequency').' = "'.$contribution_frequency.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' ="1"',NULL,FALSE);
		$this->db->where($this->dx('regular_contribution_settings.is_deleted').' IS NULL',NULL,FALSE);
		return $this->db->count_all_results('regular_contribution_settings')?:0;
	}

	function average_contribution_amount_in_frequency($contribution_frequency=0){
		$this->db->select(array(
				'sum('.$this->dx('amount').') as average_amount',
			));
		$this->db->where($this->dx('regular_contribution_settings.contribution_frequency').' = "'.$contribution_frequency.'"',NULL,FALSE);
		$this->db->where($this->dx('regular_contribution_settings.active').' ="1"',NULL,FALSE);
		$this->db->where($this->dx('contributions.active').' ="1"',NULL,FALSE);
		$this->db->where($this->dx('regular_contribution_settings.is_deleted').' IS NULL',NULL,FALSE);
		$this->db->join('regular_contribution_settings','contributions.id = '.$this->dx('regular_contribution_settings.contribution_id'));
		$result = $this->db->get('contributions')->row();
		if($result){
			return $result->average_amount;
		}
		return FALSE;
	}

	function get_group_contribution_date_by_contribution_id($group_id = 0 ,$contribution_id=0){
		$this->db->select(array(
			$this->dx('regular_contribution_settings.contribution_date').' as contribution_date',
		));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_date').' >= "'.time().'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->order_by($this->dx('contribution_date'),'ASC',FALSE);
		$this->db->limit('1');
		$date = $this->db->get('regular_contribution_settings')->row();
		if($date){
			return $date->contribution_date;
		}else{
			$this->db->select(array(
				$this->dx('contribution_date').' as contribution_date',
			));
			$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
			$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
			$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
			$this->db->where($this->dx('contribution_date').' >= "'.time().'"',NULL,FALSE);
			if($group_id){
				$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
			}else{
				
			}
			$this->db->order_by($this->dx('contribution_date'),'ASC',FALSE);
			$this->db->limit('1');
			$date = $this->db->get('one_time_contribution_settings')->row();
			if($date){
				return $date->contribution_date;
			}else{
				return time();
			}
		}
	}

	function get_group_contribution_date($group_id = 0){
		$this->db->select(array(
			$this->dx('regular_contribution_settings.contribution_date').' as contribution_date',
		));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('contribution_date').' >= "'.time().'"',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->order_by($this->dx('contribution_date'),'ASC',FALSE);
		$this->db->limit('1');
		$date = $this->db->get('regular_contribution_settings')->row();
		if($date){
			return $date->contribution_date;
		}else{
			$this->db->select(array(
				$this->dx('contribution_date').' as contribution_date',
			));
			$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
			$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
			$this->db->where($this->dx('contribution_date').' >= "'.time().'"',NULL,FALSE);
			if($group_id){
				$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
			}else{
				
			}
			$this->db->order_by($this->dx('contribution_date'),'ASC',FALSE);
			$this->db->limit('1');
			$date = $this->db->get('one_time_contribution_settings')->row();
			if($date){
				return $date->contribution_date;
			}else{
				return time();
			}
		}
	}

	function get_group_non_equitable_non_refundable_contributions($group_id = 0){
		$this->select_all_secure('contributions');
		//$this->db->select('id');
		$this->db->where($this->dx('contributions.is_non_refundable').' = "1" ',NULL,FALSE);
		$this->db->where("(".$this->dx('contributions.is_equity').' = "0" OR '.$this->dx('contributions.is_equity').' = "" OR '.$this->dx('contributions.is_equity').' IS NULL  )',NULL,FALSE);
        //$this->db->where($this->dx('contributions.is_deleted').' IS NULL ',NULL,FALSE);
        if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "")',NULL,FALSE);
        return $this->db->get('contributions')->result();
	}

	function get_group_non_refundable_contributions($group_id = 0){
		$this->select_all_secure('contributions');
		//$this->db->select('id');
		$this->db->where($this->dx('contributions.is_non_refundable').' = "1" ',NULL,FALSE);
        //$this->db->where($this->dx('contributions.is_deleted').' IS NULL ',NULL,FALSE);
        if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where("(".$this->dx('is_deleted').' IS NULL OR '.$this->dx('is_deleted').' = "")',NULL,FALSE);
        return $this->db->get('contributions')->result();
	}

	function disable_group_contributions_invoicing($group_ids = array()){
		if(empty($group_ids)){
			$group_ids_list = "0";
		}else{
			$group_ids_list = implode(',',$group_ids);
		}
		$input = array(
			'modified_on' => time(),
			'regular_invoicing_active' => 0,
			'one_time_invoicing_active' => 0,
		);
		$where = " ".$this->dx('group_id')." IN (".$group_ids_list.") ";
		return $this->update_where($where,$input);
	}

	function get_group_contribution_fine_categories($group_id = 0,$field_names = array()){
		if(empty($field_names)){
			$this->select_all_secure('contributions');
		}else{
			$arr = array();
			foreach($field_names as $field_name):
				if($field_name == 'id'){
					$arr[] = 'CONCAT("contribution-",id) as id ';
				}else{
					$arr[] = 'CONCAT('.$this->dx($field_name).",' Contribution Fine') as ".$field_name." ";
				}
			endforeach;
			$this->db->select($arr);
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			
		}
		$this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
		$this->db->order_by($this->dx('name'),'ASC',FALSE);
		return $this->db->get('contributions')->result();
	}

	function get_group_contribution_objects_array($group_ids = array(),$contribution_ids = array()){
		$arr = array();
		$this->select_all_secure('contributions');
		if(empty($contribution_ids)){
			$this->db->where_in('id',0);
		}else{
			$this->db->where_in('id',$contribution_ids);
		}
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id').' = 0 ',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' IN('.implode(',',$group_ids).') ',NULL,FALSE);
		}
		$contributions = $this->db->get('contributions')->result();
		foreach($contributions as $contribution):
			$arr[$contribution->id] = $contribution;
		endforeach;
		return $arr;
	}
	
}