<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users_m extends MY_Model {

	protected $_table = 'users';

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ion_auth_model');
		$this->install();
	}

	function install(){
		$this->db->query("
		create table if not exists user_pin_access_token(
			id int not null auto_increment primary key,
			`phone` blob,
			`email` blob,
			`user_id` blob,
			`pin` blob,
			`access_token` blob,
			`access_token_created_on` blob,
			`access_token_expire_on` blob,
			`is_subscribed` blob,
			`active` blob,
			`terms_and_conditions` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists user_change_phone_number_requests(
			id int not null auto_increment primary key,
			`old_number` blob,
			`new_number` blob,
			`user_id` blob,
			`document_number` blob,
			`document_type` blob,
			`one_time_pin` blob,
			`status` blob,
			`old_first_name` blob,
			`old_last_name` blob,
			`old_date_of_birth` blob,
			`new_date_of_birth` blob,
			`new_first_name` blob,
			`new_last_name` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists user_demo_requests(
			id int not null auto_increment primary key,
			`first_name` blob,
			`last_name` blob,
			`phone` blob,
			`email` blob,
			`solution` blob,
			`product` blob,
			`enable_phone_contact` blob,
			`enable_email_contact` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists user_password_reset_history(
			id int not null auto_increment primary key,
			`user_id` blob,
			`password` blob,
			`changed_on` blob,	
			`is_change_success` blob,		
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	public function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('users',$input);
	}


	public function insert_password_reset_history($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('user_password_reset_history',$input);
	}

	function insert_demo_requests($input = array(),$skip_value = FALSE)
	{
		return $this->insert_secure_data('user_demo_requests',$input);
	}

	function insert_group($input = array(),$skip_value = FALSE)
	{
		return $this->insert_secure_data('groups',$input);
	}

	function get($id=0){
		$this->select_all_secure('users');
		$this->db->where('id',$id);
		return $this->db->get('users')->row();
	}

	function update_group($id,$input,$skip_value = FALSE)
	{
		return $this->ion_auth_model->update_group($id,'',$input);
	}

	function update_user($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'users',$input);
	}

	function get_options($include_contacts = FALSE)
	{
		$arr = array();
		if($include_contacts){
			$this->db->select(array('id',$this->dx('first_name').' as first_name',$this->dx('last_name').' as last_name',$this->dx('phone').' as phone',$this->dx('email').' as email'));
		}else{
			$this->db->select(array('id',$this->dx('first_name').' as first_name',$this->dx('last_name').' as last_name'));
		}
		$users = $this->db->get('users')->result();
		foreach($users as $user){
			if($include_contacts){
				if($user->phone&&$user->email){
					$arr[$user->id] = $user->first_name.' '.$user->last_name.'&nbsp;<br/><span><i class="fa fa-mobile"></i></span>&nbsp;'.$user->phone.'<br/><span><i class="fa fa-envelope"></i></span>&nbsp;'.$user->email;
				}else if($user->email){
					$arr[$user->id] = $user->first_name.' '.$user->last_name.'<br/>&nbsp;<span><i class="fa fa-envelope"></i></span>&nbsp;'.$user->email;
				}else if($user->phone){
					$arr[$user->id] = $user->first_name.' '.$user->last_name.'<br/>&nbsp;<span><i class="fa fa-mobile"></i></span>&nbsp;'.$user->phone;
				}else{
					$arr[$user->id] = $user->first_name.' '.$user->last_name;
				}
			}else{
				$arr[$user->id] = $user->first_name.' '.$user->last_name;
			}
		}
		return $arr;
	}

	function get_user_last_login_options(){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('last_login')." as last_login ",
				'id'
			)
		);
		$users = $this->db->get('users')->result();
		foreach($users as $user):
			$arr[$user->id] = $user->last_login;
		endforeach;
		return $arr;
	}	

	function get_contact_options()
	{
		$arr = array();
		$this->db->select(array('id',$this->dx('phone').' as phone',$this->dx('email').' as email'));
		
		$users = $this->db->get('users')->result();
		foreach($users as $user){
			if($user->phone&&$user->email){
				$arr[$user->id] = $user->phone.' '.$user->email;
			}else if($user->email){
				$arr[$user->id] = $user->email;
			}else if($user->phone){
				$arr[$user->id] = $user->phone;
			}
			
		}
		return $arr;
	}

	function get_options_email_as_key_id_as_value(){
		$arr = array();
		$this->db->select(array('id',$this->dx('email').' as email'));
		$users = $this->db->get('users')->result();
		foreach($users as $user){
			if($user->email):
				$arr[$user->email] = $user->id;
			endif;
		}
		return $arr;
	}

	function get_options_phone_as_key_id_as_value(){
		$arr = array();
		$this->db->select(array('id',$this->dx('phone').' as phone'));
		$users = $this->db->get('users')->result();
		foreach($users as $user){
			if($user->phone):
				$arr[valid_phone($user->phone)] = $user->id;
			endif;
		}
		return $arr;
	}

	function get_all_groups()
	{
		$this->select_all_secure('groups');
		return $this->db->get('groups')->result();
	}

	function get_user_groups_option($id = 0)
	{
		$groups = $this->ion_auth->get_user_groups($id);
		return $groups;
	}

	function get_group($id=0)
	{
		$this->select_all_secure('groups');
		$this->db->where('id',$id);
		return $this->db->get('groups')->row();
	}

	function delete($id=0)
	{
		$this->db->where('id',$id);
		return $this->db->delete('users');
	}

	function delete_group($id=0)
	{
		return $this->ion_auth_model->delete_group($id);
	}

	function delete_user_data($user_id=0,$table_name=''){
    	$this->db->where($this->dx('user_id').' = '.$this->db->escape($user_id),NULL,FALSE);
    	return $this->db->delete($table_name);
    }

	function get_group_options()
	{
		$this->select_all_secure('groups');
		$query = $this->db->get('groups')->result();

		$arr = array();

		foreach ($query as $value)
		{
			$arr[$value->id] = ucwords($value->name);
		}

		return $arr;
	}

	function count_all_active_groups()
	{
		return $this->count_all_results('groups');
	}

	function count_all_demo_users()
	{
		return $this->count_all_results('user_demo_requests');
	}

	function count_all_active_users($groups=NULL,$identity=0,$name='',$user_ids = array()){
		$list ='';
		if($user_ids){
			foreach ($user_ids as $key => $value) {
				if($list){
					$list.=','.$value;
				}else{
					$list=$value;
				}
				
			}
		}
		if (isset($groups))
        {
            //build an array if only one group was passed
            if (is_numeric($groups))
            {
                $groups = Array($groups);
            }

            //join and then run a where_in against the group ids
            if (isset($groups) && !empty($groups)){
                //$this->db->distinct();
                $this->db->join(
                        'users_groups', 'users_groups' . '.user_id = ' . 'users'. '.id', 'inner'
                );

                $this->db->where_in('users_groups' . '.group_id', $groups);
            }
        }
		if(isset($identity) && $identity){
        	if(is_numeric($identity)){
        		$phone = valid_phone($this->db->escape_str($identity),FALSE);
        		$phone2 = $this->invalid_phone($this->db->escape_str($identity),FALSE);
				$this->db->where("(( CONVERT(".$this->dx('users.phone')." USING 'latin1') LIKE '%".$phone."%') OR ".$this->dx('users.phone')." ='".$identity."' OR ".$this->dx('users.phone')." ='".$phone."' OR (CONVERT(".$this->dx('users.phone')." USING 'latin1') LIKE '%".$phone2."%'))",NULL,FALSE);
        	}
        	else
        	{
				$email = strtolower($this->db->escape_str($identity));
				$this->db->where("( CONVERT(".$this->dx('users.email')." USING 'latin1') LIKE '%".$email."%')",NULL,FALSE);
        	}

        }
        if(isset($name) && $name){
        	$name = $this->db->escape_str($name);

			$this->db->where('CONVERT(CONCAT('.$this->dx('users.first_name').', " ", '.$this->dx('users.last_name').')'.' USING "latin1") LIKE "%'.$name.'%" OR '.'CONVERT(CONCAT('.$this->dx('users.last_name').', " ", '.$this->dx('users.first_name').')'.' USING "latin1") LIKE "%'.$name.'%"' ,NULL,FALSE);
        }
        if($list){
        	$this->db->where('id IN('.$list.')',NULL,FALSE);
        }
		return $this->count_all_results('users');
	}

	function count_from_date($from=0,$to=0){
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		return $this->db->count_all_results('users');
	}

	function count_users_not_logged_in_certain_days($date = 0){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('last_login')." as last_login ",
				'id'
			)
		);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('last_login')."),'%Y %D %M') <= '" . date('Y jS F',strtotime($date)) . "'", NULL, FALSE);
		$users = $this->db->get('users')->result();
		return $this->db->count_all_results('users');
	}

	function get_users_not_logged_in_certain_days($date = 0 , $from = 0){
		$arr = array();
		$this->db->select(array('id',$this->dx('first_name').' as first_name',$this->dx('last_name').' as last_name',$this->dx('phone').' as phone',$this->dx('email').' as email',$this->dx('last_login')." as last_login ",));
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('last_login')."),'%Y %D %M') <= '" . date('Y jS F',strtotime($date)) . "'", NULL, FALSE);
		$users = $this->db->get('users')->result();
		foreach ($users as $key => $user) {
			$arr[$user->id] = $user;
		}
		return $arr;
	}

	function get_group_user_options($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'users.id as id',
				$this->dx('first_name').' as first_name ',
				$this->dx('last_name').' as last_name ',
			)
		);
		$this->db->where($this->dx('members.group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('members.active').' = "1" ',NULL,FALSE);
		$this->db->join('members',$this->dx('members.user_id')." = users.id");
		$users = $this->db->get('users')->result();
		foreach ($users as $user) {
			# code...
			$arr[$user->id] = $user->first_name." ".$user->last_name;
		}
		return $arr;
	}

	function get_user_group_options($group_id = 0){
		$arr = array();
		$this->db->select(
			array(
				'users.id as id',
				$this->dx('first_name').' as first_name ',
				$this->dx('last_name').' as last_name ',
				$this->dx('group_id').' as group_id ',
			)
		);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('users.active').' = "1" ',NULL,FALSE);
		$this->db->distinct();
        $this->db->join('users_groups', 'users_groups' . '.user_id = ' . 'users'. '.id', 'inner');
        $this->db->where_in('users_groups' . '.group_id', array($group_id));
		$users = $this->db->get('users')->result();
		foreach ($users as $user) {
			# code...
			$arr[$user->id] = $user->first_name." ".$user->last_name;
		}
		return $arr;
	}


	function get_all_users($groups=Null,$identity='',$name='',$user_ids=array()){
		$list ='';
		if($user_ids){
			foreach ($user_ids as $key => $value) {
				if($list){
					$list.=','.$value;
				}else{
					$list=$value;
				}
				
			}
		}
		
    	$this->select_all_secure('users');
    	if (isset($groups))
        {
            //build an array if only one group was passed
            if (is_numeric($groups))
            {
                $groups = Array($groups);
            }

            //join and then run a where_in against the group ids
            if (isset($groups) && !empty($groups))
            {
                $this->db->distinct();
                $this->db->join(
                        'users_groups', 'users_groups' . '.user_id = ' . 'users'. '.id', 'inner'
                );

                $this->db->where_in('users_groups' . '.group_id', $groups);
            }
        }
        if(isset($identity) && $identity){
        	if(is_numeric($identity)){
				$phone = valid_phone($this->db->escape_str($identity),FALSE);
        		$phone2 = $this->invalid_phone($this->db->escape_str($identity));
				$this->db->where("(( CONVERT(".$this->dx('users.phone')." USING 'latin1') LIKE '%".$phone."%') OR ".$this->dx('users.phone')." ='".$identity."' OR ".$this->dx('users.phone')." ='".$phone."' OR (CONVERT(".$this->dx('users.phone')." USING 'latin1') LIKE '%".$phone2."%'))",NULL,FALSE);
        	}
        	else
        	{
				$email = strtolower($this->db->escape_str($identity));
				$this->db->where("( CONVERT(".$this->dx('users.email')." USING 'latin1') LIKE '%".$email."%')",NULL,FALSE);
        	}

        }
        if(isset($name) && $name){
        	$name = $this->db->escape_str($name);

			$this->db->where('CONVERT(CONCAT('.$this->dx('users.first_name').', " ", '.$this->dx('users.last_name').')'.' USING "latin1") LIKE "%'.$name.'%" OR '.'CONVERT(CONCAT('.$this->dx('users.last_name').', " ", '.$this->dx('users.first_name').')'.' USING "latin1") LIKE "%'.$name.'%"' ,NULL,FALSE);
        }
        if($list){
        	$this->db->where('id IN('.$list.')',NULL,FALSE);
        }
    	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);

    	return $this->db->get('users')->result();
    }

    function get_all_demo_users(){
		$list ='';		
    	$this->select_all_secure('user_demo_requests');
    	$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
    	return $this->db->get('user_demo_requests')->result();
    }

    function get_user_by_identity($identity = ''){
    	$this->select_all_secure('users');
    	if(valid_phone($identity)){
			$this->db->where('('.$this->dx('users.phone').'="'.$identity.'" OR '.$this->dx('users.phone').' = "'.valid_phone($identity).'"  OR '.$this->dx('users.phone').' = "+'.valid_phone($identity).'" OR '.$this->dx('users.phone').' ="+'.$identity.'" OR '.$this->dx('users.phone').' ="'.str_replace('+','', $identity).'" )',NULL,FALSE);
			return $this->db->get('users')->row();
		}else{
			// $this->db->where($this->dx('users.email').'="'.$identity.'"',NULL,FALSE);
			$this->db->where("CONVERT(".$this->dx('users.email')." using 'latin1') = '".$identity."'",NULL,FALSE);
			return $this->db->get('users')->row();
		}
    }

    function get_user_by_join_code($join_code = ''){
    	$this->select_all_secure('users');
		$this->db->where('('.$this->dx('join_code').' ="'.$join_code.'" OR '.$this->dx('email_join_code').' ="'.$join_code.'")',NULL,FALSE);
    	return $this->db->get('users')->row();
    }
    
    function check_if_identity_exists($identity=''){
    	if($identity){
    		if(valid_phone($identity)){
    			$this->db->where('('.$this->dx('users.phone').'="'.$identity.'" OR '.$this->dx('users.phone').' = "'.valid_phone($identity).'"  OR '.$this->dx('users.phone').' = "+'.valid_phone($identity).'" OR '.$this->dx('users.phone').' ="+'.$identity.'" OR '.$this->dx('users.phone').' ="'.str_replace('+','', $identity).'" )',NULL,FALSE);
				return $this->db->count_all_results('users')?:0;
    		}else{
    			$this->db->where($this->dx('users.email').'="'.$identity.'"',NULL,FALSE);
				return $this->db->count_all_results('users')?:0;
    		}
    	}else{
    		return FALSE;
    	}
    }

    function count_users_signed_up_today(){
    	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
   		return $this->db->count_all_results('users');
    }

    
	function get_group_users($members = array()){
		$arr = array();
		foreach($members as $member){
			$arr[] = $member->user_id;
		}
		$this->select_all_secure('users');
		$this->db->where_in('id',$arr);
		return $this->db->get('users')->result();
	}


    function invalid_phone($phone=0){
    	if($phone){
    		$phone = substr($phone,-9);
    		return '0'.$phone;
    	}
    }

    function get_search_options(){
		$query = trim($this->input->get("q"));
		$words_array = explode(" ",$query);
		$this->db->select(
			array(
				"id as id",
				"CONCAT(".$this->dx('first_name').",' ',".$this->dx('last_name').",' : ',IFNULL(".$this->dx('phone').",''),' - ',".$this->dx('email').") as text ",
				"CONCAT(".$this->dx('first_name').",' ',".$this->dx('last_name').") as full_name ",
				'CONCAT("'.$this->application_settings->protocol.$this->application_settings->url.'/uploads/groups/'.'",'.$this->dx('users.avatar').') as avatar_url ',
				'CONCAT("'.$this->application_settings->protocol.$this->application_settings->url.'/templates/admin_themes/groups/img/default_avatar.png'.'") as default_avatar_url ',
				"IF(".$this->dx('users.phone')." = '', 'N/A',IFNULL(".$this->dx('users.phone').",'N/A')) as phone ",
				"IF(".$this->dx('users.email')." = '', 'N/A',IFNULL(".$this->dx('users.email').",'N/A')) as email ",
				"IF(".$this->dx('users.last_login')." = 0, 'Never Logged In',IFNULL(DATE_FORMAT(FROM_UNIXTIME(".$this->dx('users.last_login')."),'%d-%m-%Y'),'Never Logged In') ) as formatted_last_login_date ",
			)
		);
		if(count($words_array)==2){
			$this->db->where(" ( 
				CONVERT(" . $this->dx('first_name') . " USING 'latin1')  like '%" . $this->escape_str($words_array[0]) . "%' AND 
				CONVERT(" . $this->dx('last_name') . " USING 'latin1')  like '%" . $this->escape_str($words_array[1]) . "%' OR
				CONVERT(" . $this->dx('email') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR
				CONVERT(" . $this->dx('phone') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' 
				)", NULL, FALSE);
		}else{
			$this->db->where(" ( 
				CONVERT(" . $this->dx('first_name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR 
				CONVERT(" . $this->dx('last_name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR
				CONVERT(" . $this->dx('email') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR
				CONVERT(" . $this->dx('phone') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' 
				)", NULL, FALSE);
		}
		$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
		//$this->db->limit(10);
		$users = $this->db->get('users')->result();
		$arr = array();
		foreach($users as $user):
			$arr[] = $user->id;
		endforeach;
		$user_groups_array = $this->groups_m->get_user_groups_array($arr);
		$users_with_groups = array();
		foreach($users as $user):
			if(isset($user_groups_array[$user->id])){
				$user->groups = $user_groups_array[$user->id];
			}else{
				$user->groups = array();
			}
			$users_with_groups[] = $user;
		endforeach;
		$result = new stdClass();
		$result->total_count = count($users);
		$result->incomplete_results = false;
		$result->items = $users;

		echo json_encode($result);
	}

	function get_user_selected_user_options($user_ids = array()){
		$arr = array();
		$this->db->select(
			array(
				"id as id",
				"CONCAT(".$this->dx('first_name').",' ',".$this->dx('last_name').",' : ',IFNULL(".$this->dx('phone').",''),' - ',".$this->dx('email').") as text ",
			)
		);
		if(empty($user_ids)){
			$this->db->where_in('users.id','0');
		}else{
			$this->db->where_in('users.id',$user_ids);
		}

		$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
		$users = $this->db->get('users')->result();
		foreach($users as $user):
			$arr[$user->id] = $user->text;
		endforeach;
		return $arr;
	}

	function get_users_array($user_ids = array()){
		$arr = array();
		$this->select_all_secure('users');
		if(empty($user_ids)){
			$this->db->where_in('users.id','0');
		}else{
			$this->db->where_in('users.id',$user_ids);
		}
		$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
		$users = $this->db->get('users')->result();
		foreach($users as $user):
			$arr[$user->id] = $user;
		endforeach;
		return $arr;
	}

	function get_partner_users($user_partner_pairings_array = array()){
		$this->select_all_secure('users');
		if(empty($user_partner_pairings_array)){
			$this->db->where_in('users.id','0');
		}else{
			$this->db->where_in('users.id',$user_partner_pairings_array);
		}
		
		$this->db->order_by($this->dx('first_name'),'ASC',FALSE);
		return $users = $this->db->get('users')->result();
	}

	function is_access_token_valid($access_token = ''){
		$access_token = str_replace('"','',$access_token);
		$this->db->select(array(
			'id',
			$this->dxa('access_token'),
			$this->dxa('access_token_expire_on'),
			$this->dxa('user_id'),
		));
		$this->db->where($this->dx('access_token')." = '".$access_token."'",NULL,FALSE);
		$this->db->limit(1);
		$auth = $this->db->get('user_pin_access_token')->row();
		if($auth){
			if($auth->access_token_expire_on<time()){
				return FALSE;
			}else{
				$input = array(
		            'access_token_expire_on' => strtotime("+30 days"),
		            'modified_on' => time(),
		            'modified_by' => 1,
		        );
		        if($this->update_user_pin_access_token($auth->id,$input)){
		        	return $auth->user_id;
		        }else{
		        	return FALSE;
		        }
			}
		}else{
			return FALSE;
		}
	}

	function get_user_authentication_by_identity($identity = 0,$phone=0,$email=0){
		$this->select_all_secure('user_pin_access_token');
		if(valid_email($email) && valid_phone($phone)){
			$this->db->where('('.$this->dx('email')." = '".$email."' OR ".$this->dx('phone')." = '".valid_phone($phone)."' )",NULL,FALSE);
		}elseif(valid_email($identity) || valid_email($email)){
			$identity = $email?:$identity;
			$this->db->where($this->dx('email')." = '".$identity."'",NULL,FALSE);
		}else{
			$identity = valid_phone($phone)?:$identity;
			$this->db->where($this->dx('phone')." = '".valid_phone($identity)."'",NULL,FALSE);
		}
		$auth = $this->db->get('user_pin_access_token')->row();
		return $auth;
	}

	function get_all_otps(){
		$this->select_all_secure('user_pin_access_token');
		return $this->db->get('user_pin_access_token')->result();
	}

	function get_or_generate_user_auth($identity='',$fixed_pin = 0){
		$this->select_all_secure('user_pin_access_token');
		if(valid_email($identity)){
			$this->db->where($this->dx('email')." = '".$identity."'",NULL,FALSE);
		}else{
			$this->db->where($this->dx('phone')." = '".valid_phone($identity)."'",NULL,FALSE);
		}
		$auth = $this->db->get('user_pin_access_token')->row();
		$pin = $fixed_pin?'1234':rand(9999,1000);
		if(valid_phone($identity) == ('2547398712777')){
			$pin = '1234';
		}else if(valid_email($identity) == "chamasofttest@chamasoft.com"){
			$pin = '1234';
		}
		if($auth){
			$input = array(
				"pin" => $pin,
				"modified_on" => time(),
				"access_token" => '',
				"active" => "1",
				'language_id' => '',
			);
			if($this->update_user_pin_access_token($auth->id,$input)){
				return (object)(array_merge((array)$auth,$input));
			}else{
				return FALSE;
			}
		}else{
			$input = array(
				"phone" => valid_phone($identity)?valid_phone($identity):'',
				"email" => valid_email($identity)?($identity):'',
				"pin" => $pin,
				"modified_on" => time(),
				"active" => "1",
				"created_on" => time(),
				"created_by" => 1,
				'language_id' => '',
			);
			if($this->insert_user_pin_access_token($input)){
				return (object)$input;
			}else{
				return FALSE;
			}
		}
	}

	function insert_user_pin_access_token($input=array(),$SKIP_VALIDATION=FALSE){
		return $this->insert_secure_data('user_pin_access_token',$input);
	}

	function update_user_pin_access_token($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'user_pin_access_token',$input);
	}

	function get_user_authentication_by_phone($phone = 0){
		$this->select_all_secure('user_pin_access_token');
		$this->db->where($this->dx('phone')." = '".$phone."'",NULL,FALSE);
		$auth = $this->db->get('user_pin_access_token')->row();
		return $auth;
	}

	function get_user_by_identity_and_document_number($phone = "",$email = "",$document_type = "",$document_number = ""){
		$this->select_all_secure('users');
		$this->db->where(" (".$this->dx('phone')." = '".$phone."' OR ".$this->dx('email')." = '".$email."' ) ",NULL,FALSE);
		$this->db->where($this->dx('document_type')." = '".$document_type."' ",NULL,FALSE);
		$this->db->where($this->dx('document_number')." = '".$document_number."' ",NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('users')->row();
	}
	function get_user_by_phone_or_id_number($phone = "",$id = ""){
		$this->select_all_secure('users');
		// $this->db->where(" (".$this->dx('phone')." = '".$phone."' OR ".$this->dx('id_number')." = '".$id."' ) ",NULL,FALSE);
		//$this->db->limit(1);
		$this->db->where($this->dx('phone').' = "'.$phone.'"',NULL,FALSE);

		return $this->db->get('users')->row();
	}
	function insert_change_phone_request($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('user_change_phone_number_requests',$input);
	}

	function get_user_change_phone_number_request($user_id=0,$old_number=0,$new_number=0){
		$this->select_all_secure('user_change_phone_number_requests');
		$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
		$this->db->where($this->dx('old_number').' = "'.$old_number.'"',NULL,FALSE);
		$this->db->where($this->dx('new_number').' = "'.$new_number.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('user_change_phone_number_requests')->row();
	}

	public function check_if_demo_request_exist($user_id=''){
		$this->select_all_secure('user_demo_requests');
		$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
		return  $this->db->get('user_demo_requests')->row();
	}


	function update_change_number_request($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'user_change_phone_number_requests',$input);
	}

	function get_password_reset_user_pairings($user_id = 0){
		$this->select_all_secure('user_password_reset_history');
		$this->db->where($this->dx('user_id').' = "'.$user_id.'"',NULL,FALSE);
		$results =   $this->db->get('user_password_reset_history')->result();
		$passw_array = [];
		foreach ($results as $key => $result) {
			if($result){
				$passw_array[$result->password] = $result->password;
			}
		}
		return $passw_array;
	}

	function user_registration_by_year(){
    	$this->db->select(array(
    		'COUNT(*) as total_count',
    		"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year",
    	));
    	$this->db->group_by(array(
    		'year',
    	));
    	$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    	return $this->db->get('users')->result();
    }
}?>