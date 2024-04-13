<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Settings_m extends MY_Model {

	protected $_table = 'settings';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install()
	{
		$this->db->query("
		create table if not exists settings(
			id int not null auto_increment primary key,
			`trial_days` blob,
			`bill_number_start` blob,
			`url` blob,
			`protocol` blob,
			`favicon` blob,
			`responsive_logo` blob,
			`logo` blob,
			`admin_login_logo` blob,
			`group_login_logo` blob,
			`paper_header_logo` blob,
			`paper_footer_logo` blob,
			`primary_color` blob,
			`secondary_color` blob,
			`tertiary_color` blob,
			`text_color` blob,
			`link_color` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('settings',$input);
	}

	function update($id,$input,$val=FALSE){
		$this->session->unset_userdata('application_settings');
    	return $this->update_secure_data($id,'settings',$input);
    }

	public function count_all(){
		return $this->count_all_results('settings');
	}
	
	public function get_all(){	
		$this->select_all_secure('settings');
		return $this->db->get('settings')->result();
	}

	// public function get($id = 0){	
	// 	$this->select_all_secure('settings');
	// 	$this->db->where('id',$id);
	// 	return $this->db->get('settings')->row();
	// }

	function get_settings(){
		$settings = $this->session->userdata('application_settings');
		if($settings){
			return $settings;
		}else{
			$this->select_all_secure('settings');
			$this->db->select(array(
					'billing_packages.id'.' as default_billing_package',
					$this->dx('billing_packages.monthly_smses').' as billing_monthly_sms',
					$this->dx('billing_packages.quarterly_smses').' as billing_quarterly_sms',
					$this->dx('billing_packages.annual_smses').' as billing_annual_sms',
				));
			$this->db->where($this->dx('billing_packages.is_default').'="1"',NULL,FALSE);
			$this->db->where($this->dx('billing_packages.active').'="1"',NULL,FALSE);
			$this->db->join('billing_packages',$this->dx('settings.active').'='.$this->dx('billing_packages.active'));
			$setting = $this->db->get('settings')->row();
			if($setting){
			}else{
				$this->select_all_secure('settings');
				$setting = $this->db->get('settings')->row();
			}
			$this->session->set_userdata('application_settings',$setting);
			return $setting;
		}
	}

}