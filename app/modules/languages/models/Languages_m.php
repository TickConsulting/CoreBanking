<?php
	if(!defined('BASEPATH')) exit('You are not allowed to view this script');

	class Languages_m extends MY_Model{

		function __construct(){
			parent::__construct();
			//$this->install();
		}

		function install(){
		$this->db->query('
			create table if not exists languages(
			id int not null auto_increment primary key,
			`name` blob,
			`country_id` blob,
			`short_code` blob,
			`active` blob,
			`created_on` blob,
			`created_by` blob,
			`modified_on` blob,
			`modified_by` blob
			)'
			);

		}

		public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('languages',$input);
	}

		function update($id,$input,$skip_validation = 'false'){
    	return $this->update_secure_data($id,'languages',$input);
    }

    	function get_all(){
    		$this->select_all_secure('languages');
    		return $this->db->get('languages')->result();
    	}

    	function get_language_options(){
    		$arr = array();
    		$this->select_all_secure('languages');
    		$languages = $this->db->get('languages')->result();
    		if($languages){
				foreach ($languages as $key => $language){
					$arr[$language->id] = $language->name;
				}
			}
			return $arr;
    	}

    	public function get($id = 0){
			$this->select_all_secure('languages');
			$this->db->where('id',$id);
			$return  = $this->db->get('languages')->row();
			return ($return);
			//die;
		}
		public function get_language_by_short_code($short_code = 0){
			$this->select_all_secure('languages');
			$this->db->where($this->dx('short_code'),$short_code);
			return $this->db->get('languages')->row();
		}

		public function get_language_by_name($name = ""){
			$this->select_all_secure('languages');
			$this->db->where($this->dx('name'),$name);
			return $this->db->get('languages')->row();

		}

		function translate($language_key = "",$default_message = ""){
	        if($this->lang->line($language_key)){
	            $replace_application_name = str_replace("%application_name%",$this->application_settings->application_name,$this->lang->line($language_key));
	            $replace_group_number = str_replace("%group_number%",$this->group->account_number,$replace_application_name);
	            $message = $replace_group_number;

	            echo $message;
	        }else{
	            echo $default_message;
	        }
	    }


	    function generate_loop_slug($loops = ''){
	    	if(is_array($loops)){
			    $arr = array();
			    foreach ($loops as $key => $loop) {
			    	if(is_array($loop)){
			    		foreach ($loop as $loop_key => $loop_value) {
			    			$arr[$key][$loop_key] = ($this->lang->line(generate_menu_slug($loop_value))?:$loop_value);
			    			// $arr[$key][$loop_key] = ($this->lang->line(generate_menu_slug($loop_value))?:generate_menu_slug($loop_value));
			    		}
			    	}else{
			    		$arr[$key] = ($this->lang->line(generate_menu_slug($loop))?:$loop);
			    		// $arr[$key] = ($this->lang->line(generate_menu_slug($loop))?:generate_menu_slug($loop));

			    	}
			    }
			}else{
				$loop = generate_menu_slug($loops);
				$arr = ($this->lang->line($loop)?:$loops);
				// $arr = ($this->lang->line($loop)?:$loop);
			}
		    return $arr;
		}
		
}