<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Countries_m extends MY_Model {

	protected $_table = 'countries';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install()
	{
		$this->db->query("
		create table if not exists countries(
			id int not null auto_increment primary key,
			`name` blob,
			`code` blob,
			`calling_code` blob,
			`currency` blob,
			`currency_code` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
		$this->db->query("
		create table if not exists currency(
			id int not null auto_increment primary key,
			`country_id` varchar(100),
			`currency` varchar(100),
			`currency_code` varchar(100),
			`active` varchar(100),
			created_by varchar(100),
			created_on varchar(100),
			modified_on varchar(100),
			modified_by varchar(100)
		)");
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('countries',$input);
	}
	
	public function insert_to_currency($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('currency',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'countries',$input);
    }

	public function count_all($params=array()){
		foreach($params as $k => $v){
			if($v){
				$name = trim($this->db->escape_str($v));
            	$this->db->where(' CONVERT(' . $this->dx($k) . " USING 'latin1')  like '%" . $v . "%'", NULL, FALSE);
			}
		}
		return $this->count_all_results('countries');
	}

	function get_country_by_code($code=''){
		if($code){
			$this->db->select(array(
					'id',
					$this->dx('name').' as name',
					$this->dx('code').'as code',
					$this->dx('currency').'as currency',
					$this->dx('currency_code').'as currency_code',
					$this->dx('calling_code').'as calling_code',
				));
			$this->db->where($this->dx('code').' ="'.$code.'"',NULL,FALSE);
			return $this->db->get('countries')->row();
		}else{
			return FALSE;
		}
	}


	function get_country_by_currency_code($currency_code = 0){
		if($currency_code){
			$this->db->select(array(
					'id',
					$this->dx('name').' as name',
					$this->dx('code').'as code',
					$this->dx('currency').'as currency',
					$this->dx('currency_code').'as currency_code',
					$this->dx('calling_code').'as calling_code',
				));
			$this->db->where($this->dx('currency_code').' ="'.$currency_code.'"',NULL,FALSE);
			return $this->db->get('countries')->row();
		}else{
			return FALSE;
		}
	}

	function get_country_by_calling_code($calling_code=''){
		if($calling_code){
			$this->db->select(array(
					'id',
					$this->dx('name').' as name',
					$this->dx('code').'as code',
					$this->dx('currency').'as currency',
					$this->dx('currency_code').'as currency_code',
					$this->dx('calling_code').'as calling_code',
				));
			$this->db->where($this->dx('calling_code').' ="'.$calling_code.'"',NULL,FALSE);
			return $this->db->get('countries')->row();
		}else{
			return FALSE;
		}
	}

	function get_group_calling_code(){
		$this->db->select(array(
				$this->dx('calling_code').'as calling_code',
			));
		$this->db->where('id',$this->group->currency_id);
		return $this->db->get('countries')->row()->calling_code;
	}

	public function get_where($params = array(),$result = TRUE){
		$this->select_all_secure('countries');
		foreach($params as $k => $v){
			if($k == 'id'){
				$this->db->where('id',$id);
			}else{
				$this->db->where($this->dx($k).' = "'.$v.'"',NULL,FALSE);
			}
		}
		if($result){
			return $this->db->get('countries')->result();
		}else{
			return $this->db->get('countries')->row();
		}
	}
	
	public function get_all($params=array()){	
		$this->select_all_secure('countries');
		foreach($params as $k => $v){
			if($v){
				$name = trim($this->db->escape_str($v));
            	$this->db->where(' CONVERT(' . $this->dx($k) . " USING 'latin1')  like '%" . $v . "%'", NULL, FALSE);
			}
		}
		$this->db->order_by($this->dx('name'), 'ASC', FALSE);
		return $this->db->get('countries')->result();
	}

	public function get($id = 0){	
		$this->select_all_secure('countries');
		$this->db->where('id',$id);
		return $this->db->get('countries')->row();
	}

	public function get_admin_country_options(){
		$arr = array();
		$this->select_all_secure('countries');
		$countries = $this->db->get('countries')->result();
		foreach($countries as $country){
			if($country->active){
				$status = '';
			}else{
				$status = "- ( Hidden )";
			}
			$arr[$country->id] = $country->name.' '.$status;
		}
		return $arr;
	}

	public function get_currency_options($show_currency_code = ''){
		$arr = array();
		$this->db->select(array('id',$this->dx('currency').' as currency',$this->dx('currency_code').' as currency_code'));
		$currencies = $this->db->get('countries')->result();
		foreach($currencies as $currency){
			if($show_currency_code=TRUE){
				$arr[$currency->id] = $currency->currency.' ('.$currency->currency_code.')';
			}else{
				$arr[$currency->id] = $currency->currency;
			}
		}
		return $arr;
	}


	public function get_currency_code_options()
	{
		$arr = array();
		$this->db->select(array('id',$this->dx('currency').' as currency',$this->dx('currency_code').' as currency_code'));
		$currencies = $this->db->get('countries')->result();
		foreach($currencies as $currency){
			$arr[$currency->id] = $currency->currency_code;
		}
		return $arr;
	}

	function get_currency_code($id = 0){
		$this->db->select(array($this->dx('currency_code').' as currency_code'));
		$this->db->where('id',$id);
		if($country = $this->db->get('countries')->row()){
			return $country->currency_code;
		}else{
			return "";
		}
	}

	function get_country_code($id = 0){
		$this->db->select(array($this->dx('code').' as code'));
		$this->db->where('id',$id);
		if($country = $this->db->get('countries')->row()){
			return $country->code;
		}else{
			return "";
		}
	}


	public function get_country_options(){
		$arr = array();
		$this->db->select(array('id',$this->dx('name').' as name'));
		$countries = $this->db->get('countries')->result();
		foreach($countries as $country){
			$arr[$country->id] = $country->name;
		}
		return $arr;
	}

	public function get_country_code_options(){
		$arr = array();
		$this->db->select(array('id',$this->dx('code').' as code '));
		$countries = $this->db->get('countries')->result();
		foreach($countries as $country){
			$arr[$country->id] = $country->code;
		}
		return $arr;
	}

	function get_country_details(){
		error_reporting(0);
        $ip = $_SERVER['REMOTE_ADDR'];
        try {
        	$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
        	if(isset($details->ip)){
        		return $details;
        	}
        } catch (Exception $e) {
        	return FALSE;
        }
    }

	function get_default_calling_code(){
    	if(defined('CALLING_CODE')){
    		return CALLING_CODE;
    	}else{
    		return "254";
    	}
    }

	public function get_default_country(){
		$this->db->select(array(
				'id',
				$this->dx('name').' as name',
				$this->dx('code').'as code',
				$this->dx('currency').'as currency',
				$this->dx('currency_code').'as currency_code',
				$this->dx('calling_code').'as calling_code',
			));
		$this->db->where($this->dx('default_country').' = 1',NULL,FALSE);
		return $this->db->get('countries')->row();
	}

	function unique_country_code($calling_code=0,$id=0){
		$this->db->where($this->dx('calling_code').'="'.$calling_code.'"',NULL,FALSE);
		if($id){
			$this->db->where('id !=',$id);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$res = $this->db->count_all_results('countries')?:0;
		if($res){
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function get_group_currency($group_id=0){
		$this->db->select(array($this->dxa('currency_code')));
		if($group_id){
			$this->db->where('investment_groups.id',$group_id);
		}else{
			$this->db->where('investment_groups.id',$this->group->id);
		}
		$this->db->join('investment_groups',$this->dx('investment_groups.currency_id').' = countries.id','LEFT');
		$res = $this->db->get('countries')->row();
		if($res){
			return $res->currency_code;
		}
	}

	function get_group_currency_name($group_id=0){
		$this->db->select(array($this->dxa('currency')));
		if($group_id){
			$this->db->where('investment_groups.id',$group_id);
		}else{
			$this->db->where('investment_groups.id',$this->group->id);
		}
		$this->db->join('investment_groups',$this->dx('investment_groups.currency_id').' = countries.id','LEFT');
		$res = $this->db->get('countries')->row();
		if($res){
			return $res->currency;
		}
	}
}