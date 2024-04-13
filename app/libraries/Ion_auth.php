<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth
*
* Version: 2.5.2
*
* Author: Ben Edmunds
*		  ben.edmunds@gmail.com
*         @benedmunds
*
* Added Awesomeness: Phil Sturgeon
*
* Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
*
* Created:  10.01.2009
*
* Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
* Original Author name has been kept but that does not mean that the method has not been modified.
*
* Requirements: PHP5 or above
*
*/

class Ion_auth{
	/**
	 * account status ('not_activated', etc ...)
	 *
	 * @var string
	 **/
	protected $status;

	/**
	 * extra where
	 *
	 * @var array
	 **/
	public $_extra_where = array();

	/**
	 * extra set
	 *
	 * @var array
	 **/
	public $_extra_set = array();

	/**
	 * caching of users and their groups
	 *
	 * @var array
	 **/
	public $_cache_user_in_group;

	/**
	 * __construct
	 *
	 * @return void
	 * @author Ben
	 **/
	public function __construct()
	{
		$this->load->config('ion_auth', TRUE);
		$this->load->library(array('email'));
		$this->lang->load('ion_auth');
		$this->load->helper(array('cookie', 'language','url'));

		$this->load->library('session');

		$this->load->model('ion_auth_model');

		$this->_cache_user_in_group =& $this->ion_auth_model->_cache_user_in_group;

		//auto-login the user if they are remembered
		if (!$this->logged_in() && get_cookie($this->config->item('identity_cookie_name', 'ion_auth')) && get_cookie($this->config->item('remember_cookie_name', 'ion_auth')))
		{
			$this->ion_auth_model->login_remembered_user();
		}
		$email_config = $this->config->item('email_config', 'ion_auth');

		if ($this->config->item('use_ci_email', 'ion_auth') && isset($email_config) && is_array($email_config))
		{
			$this->email->initialize($email_config);
		}

		$this->ion_auth_model->trigger_events('library_constructor');
		$this->load->model('settings/settings_m');
		$this->application_settings = $this->settings_m->get_settings()?:'';
	}

	/**
	 * __call
	 *
	 * Acts as a simple way to call model methods without loads of stupid alias'
	 *
	 **/
	public function __call($method, $arguments)
	{
		if (!method_exists( $this->ion_auth_model, $method) )
		{
			throw new Exception('Undefined method Ion_auth::' . $method . '() called');
		}
		if($method == 'create_user')
		{
			return call_user_func_array(array($this, 'register'), $arguments);
		}
		if($method=='update_user')
		{
			return call_user_func_array(array($this, 'update'), $arguments);
		}
		return call_user_func_array( array($this->ion_auth_model, $method), $arguments);
	}

	/**
	 * __get
	 *
	 * Enables the use of CI super-global without having to define an extra variable.
	 *
	 * I can't remember where I first saw this, so thank you if you are the original author. -Militis
	 *
	 * @access	public
	 * @param	$var
	 * @return	mixed
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}


	/**
	 * forgotten password feature
	 *
	 * @return mixed  boolian / array
	 * @author Mathew
	 **/
	public function forgotten_password($identity,$remember_code='',$send_to_phone=TRUE){
				
		if ($this->ion_auth_model->forgotten_password($identity,$remember_code)){

			if(valid_phone($identity)){
				// $identity = valid_phone($identity);
			}
			// Get user information
	      	//$identifier = $this->ion_auth_model->identity_column; // use model identity column, so it can be overridden in a controller
			$identifier = valid_phone($identity)?'phone':'email';
	      	$user = $this->ion_auth->get_user_by_identity($identity);  // changed to get_user_by_identity from email
			$exempted_names = array('customer','equity');	
			if ($user && strlen($user->first_name) <= 10 && !in_array(strtolower($user->first_name),$exempted_names) && ctype_alpha($user->first_name)){
				$data = array(
					'identity'		=> $user->{$this->config->item('identity', 'ion_auth')},
					'forgotten_password_code' => $user->forgotten_password_code,
					'phone'		=>	$user->phone,
					'email'		=>	$user->email,
					'code'		=>	$user->confirmation_code,
					'remember_code' => $user->remember_code,
					'forgotten_password_time' => time(),
					'expiry_time' => strtotime("+1 hour",time()),
				);
				if(!$this->config->item('use_ci_email', 'ion_auth')){					
					$success = '';
					if(valid_email($identity))
					{
						$segment = $this->uri->segment(1);
						if($segment=='admin')
						{
							$segment='/'.$segment;
						}
						else
						{
							$segment='';
						}
						$url = $this->application_settings->protocol.$this->application_settings->url.$segment;

						$message = $this->emails_m->build_email_message('password-recovery-template',array(
							'FIRST_NAME'=>$user->first_name,
							'LAST_NAME' => $user->last_name,
							'LINK' => $url.'/reset_password?code='.$user->forgotten_password_code,
							'APPLICATION_NAME' => $this->application_settings->application_name,
							'YEAR'		=>	date('Y',time()),
							'DATE' 		=>	date('d-m-Y',time()),
							'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                            'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                            'CODE' => $user->remember_code,
                            'LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',

						));

						$subject = $this->application_settings->application_name.' reset password';
						$success = $this->emails_m->send_email($identity,$subject,$message);
						
					}
					else if(valid_phone($identity))
					{
						if($send_to_phone){
							$message = $this->sms_m->build_sms_message('password-recovery-template',array(
									'FIRST_NAME'=>$user->first_name,
									'CODE'=>$user->confirmation_code,
									'APPLICATION_NAME' => $this->application_settings->application_name,
								),'',$user->language_id);
							$success = $this->sms_m->send_system_sms(valid_phone($identity),$message,$user->id);
												
						}else{
						
						}
					}

					if($success)
					{
						$this->set_message('forgot_password_successful');
						return $data;
					}
					else
					{
						$this->set_error('forgot_password_unsuccessful');
						return FALSE;
					}

				}
				else
				{
					$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_forgot_password', 'ion_auth'), $data, true);
					$this->email->clear();
					$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
					$this->email->to($user->email);
					$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_forgotten_password_subject'));
					$this->email->message($message);

					if ($this->email->send())
					{
						$this->set_message('forgot_password_successful');
						return TRUE;
					}
					else
					{
						$this->set_error('forgot_password_unsuccessful');
						return FALSE;
					}
				}
			}else{
				$this->set_error('forgot_password_unsuccessful');
				return FALSE;
			}
		}else{
			$this->set_error('forgot_password_unsuccessful');
			return FALSE;
		}
	}

	/**
	 * forgotten_password_complete
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code,$password)
	{
		$this->ion_auth_model->trigger_events('pre_password_change');

		$identity = $this->config->item('identity', 'ion_auth');
		$profile  = $this->where('forgotten_password_code', $code)->users()->row(); //pass the code to profile

		if (!$profile)
		{
			$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}

		//$new_password = $this->ion_auth_model->forgotten_password_complete($code, $profile->salt);
		$new_password = $password;

		if ($new_password)
		{
			if(!$profile->phone){
				$identity = 'email';
			}
			$data = array(
				'identity'     => $profile->{$identity},
				'new_password' => $new_password
			);
			if(!$this->config->item('use_ci_email', 'ion_auth'))
			{
				$this->set_message('password_change_successful');
				$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_successful'));
					return $data;
			}
			else
			{
				$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_forgot_password_complete', 'ion_auth'), $data, true);

				$this->email->clear();
				$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
				$this->email->to($profile->email);
				$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_new_password_subject'));
				$this->email->message($message);

				if ($this->email->send())
				{
					$this->set_message('password_change_successful');
					$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_successful'));
					return TRUE;
				}
				else
				{
					$this->set_error('password_change_unsuccessful');
					$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
					return FALSE;
				}

			}
		}

		$this->ion_auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
		return FALSE;
	}

	public function confirm_code($identity,$code)
	{
		$user = $this->ion_auth_model->confirm_code($identity,$code);
		if($user)
		{
			if($user->expiry_time<time())
			{
				$this->set_error('expiry_time_expired');
				return FALSE;
			}
			else
			{
				if($user->forgotten_password_code)
				{
					return $user->forgotten_password_code;
				}
				else
				{
					$this->set_error('password_change_unsuccessful');
					return FALSE;
				}
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * forgotten_password_check
	 *
	 * @return void
	 * @author Michael
	 **/
	public function forgotten_password_check($code){
		if($code){
			$profile = $this->where('forgotten_password_code', $code)->users()->row(); //pass the code to profile
			if (!is_object($profile))
			{
				$this->set_error('password_change_unsuccessful');
				return FALSE;
			}
			else
			{
				if ($this->config->item('forgot_password_expiration', 'ion_auth') > 0) {
					//Make sure it isn't expired
					$expiration = $this->config->item('forgot_password_expiration', 'ion_auth');
					if (time() - $profile->forgotten_password_time > $expiration) {
						//it has expired
						$this->clear_forgotten_password_code($code);
						$this->set_error('password_change_unsuccessful');
						return FALSE;
					}
				}
				return $profile;
			}
		}else{
			return FALSE;
		}
	}

	/**
	 * register
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function register($identity, $password, $email, $additional_data = array(), $group_ids = array()) //need to test email activation
	{
		$this->ion_auth_model->trigger_events('pre_account_creation');

		$email_activation = $this->config->item('email_activation', 'ion_auth');

		$id = $this->ion_auth_model->register($identity, $password, $email, $additional_data, $group_ids);

		if (!$email_activation)
		{
			if ($id !== FALSE)
			{
				$this->set_message('account_creation_successful');
				$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful'));
				return $id;
			}
			else
			{
				$this->set_error('account_creation_unsuccessful');
				$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_unsuccessful'));
				return FALSE;
			}
		}
		else
		{
			if (!$id)
			{
				$this->set_error('account_creation_unsuccessful');
				return FALSE;
			}

			// deactivate so the user much follow the activation flow
			$deactivate = $this->ion_auth_model->deactivate($id);

			// the deactivate method call adds a message, here we need to clear that
			$this->ion_auth_model->clear_messages();


			if (!$deactivate)
			{
				$this->set_error('deactivate_unsuccessful');
				$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_unsuccessful'));
				return FALSE;
			}

			$activation_code = $this->ion_auth_model->activation_code;
			$identity        = $this->config->item('identity', 'ion_auth');
			$user            = $this->ion_auth_model->user($id)->row();

			$data = array(
				'identity'   => $user->{$identity},
				'id'         => $user->id,
				'email'      => $email,
				'activation' => $activation_code,
			);
			if(!$this->config->item('use_ci_email', 'ion_auth'))
			{
				$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful', 'activation_email_successful'));
				$this->set_message('activation_email_successful');
				return $data;
			}
			else
			{
				$message = $this->load->view($this->config->item('email_templates', 'ion_auth').$this->config->item('email_activate', 'ion_auth'), $data, true);

				$this->email->clear();
				$this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
				$this->email->to($email);
				$this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $this->lang->line('email_activation_subject'));
				$this->email->message($message);

				if ($this->email->send() == TRUE)
				{
					$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful', 'activation_email_successful'));
					$this->set_message('activation_email_successful');
					return $id;
				}

			}

			$this->ion_auth_model->trigger_events(array('post_account_creation', 'post_account_creation_unsuccessful', 'activation_email_unsuccessful'));
			$this->set_error('activation_email_unsuccessful');
			return FALSE;
		}
	}

	/**
	 * logout
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function logout()
	{
		$this->ion_auth_model->trigger_events('logout');

		$identity = $this->config->item('identity', 'ion_auth');

                if (substr(CI_VERSION, 0, 1) == '2')
		{
			$this->session->unset_userdata( array($identity => '', 'id' => '', 'user_id' => '') );
                }
                else
                {
                	$this->session->unset_userdata( array($identity, 'id', 'user_id') );
                }

		// delete the remember me cookies if they exist
		if (get_cookie($this->config->item('identity_cookie_name', 'ion_auth')))
		{
			delete_cookie($this->config->item('identity_cookie_name', 'ion_auth'));
		}
		if (get_cookie($this->config->item('remember_cookie_name', 'ion_auth')))
		{
			delete_cookie($this->config->item('remember_cookie_name', 'ion_auth'));
		}

		// Destroy the session
		$this->session->sess_destroy();

		//Recreate the session
		if (substr(CI_VERSION, 0, 1) == '2')
		{
			$this->session->sess_create();
		}
		else
		{
			if (version_compare(PHP_VERSION, '7.0.0') >= 0){
				session_start();
			}
			$this->session->sess_regenerate(TRUE);
		}

		$this->set_message('logout_successful');
		if(!isset($_SESSION)){ 
	        session_start(); 
			session_destroy();
	    }
		return TRUE;
	}

	/**
	 * logged_in
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function logged_in()
	{
		$this->ion_auth_model->trigger_events('logged_in');

		return (bool) $this->session->userdata('identity');
	}

	/**
	 * logged_in
	 *
	 * @return integer
	 * @author jrmadsen67
	 **/
	public function get_user_id()
	{
		$user_id = $this->session->userdata('user_id');
		if (!empty($user_id))
		{
			return $user_id;
		}
		return null;
	}


	/**
	 * is_admin
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function is_admin($id=false)
	{
		$this->ion_auth_model->trigger_events('is_admin');

		$admin_group = $this->config->item('admin_group', 'ion_auth');

		return $this->in_group($admin_group, $id);
	}

	public function is_bank_admin($id=false)
	{
		$this->ion_auth_model->trigger_events('is_admin');

		$admin_group = $this->config->item('bank_admin_group', 'ion_auth');

		return $this->in_group($admin_group, $id);
	}

	public function is_group_member($id=FALSE){
		$this->ion_auth_model->trigger_events('is_group_member');
		$admin_group = $this->config->item('group_member', 'ion_auth');		
		return $this->in_group($admin_group, $id);
	}

	public function is_group_account_manager($id=FALSE){
		$this->ion_auth_model->trigger_events('is_group_account_manager');
		$admin_group = $this->config->item('group_account_manager', 'ion_auth');
		return $this->in_group($admin_group, $id);
	}

	

	/**
	 * in_group
	 *
	 * @param mixed group(s) to check
	 * @param bool user id
	 * @param bool check if all groups is present, or any of the groups
	 *
	 * @return bool
	 * @author Phil Sturgeon
	 **/
	public function in_group($check_group, $id=false, $check_all = false)
	{
		$this->ion_auth_model->trigger_events('in_group');

		$id || $id = $this->session->userdata('user_id');

		if (!is_array($check_group))
		{
			$check_group = array($check_group);
		}

		if (isset($this->_cache_user_in_group[$id]))
		{
			$groups_array = $this->_cache_user_in_group[$id];
		}
		else
		{
			$users_groups = $this->ion_auth_model->get_users_groups($id)->result();
			$groups_array = array();
			foreach ($users_groups as $group)
			{
				$groups_array[$group->id] = strtolower($group->name);
			}
			$this->_cache_user_in_group[$id] = $groups_array;
		}
		foreach ($check_group as $key => $value)
		{
			$groups = (is_string($value)) ? $groups_array : array_keys($groups_array);

			/**
			 * if !all (default), in_array
			 * if all, !in_array
			 */
			if (in_array($value, $groups) xor $check_all)
			{
				/**
				 * if !all (default), true
				 * if all, false
				 */
				return !$check_all;
			}
		}
		/**
		 * if !all (default), false
		 * if all, true
		 */
		return $check_all;
	}

	public function get_user($id=0){
		if($id){
			$user = $this->ion_auth_model->user($id)->row();
		}
		else{
			$user = $this->ion_auth_model->user()->row();
		}
		return $user;
	}

	public function get_user_groups($id = 0){
		$arr = array();
		if($id){
			$user_groups = $this->ion_auth->get_users_groups($id)->result();
			foreach($user_groups as $user_group){
				$arr[] = $user_group->id;
			}
		}
		return $arr;
	}

	public function get_user_by_email($email = ''){
		return $this->ion_auth_model->get_user_by_email($email);
	}

	public function get_user_by_phone($phone = ''){
		return $this->ion_auth_model->get_user_by_phone($phone);
	}

	function generate_user_pin($identity='',$fixed_pin=0){
		if(valid_phone($identity) || valid_email($identity) ){
			$auth = $this->users_m->get_or_generate_user_auth($identity,$fixed_pin);
			if($auth){
				return $auth;
			}else{
				return FALSE;
			}
		}else{
			$this->session->set_flashdata('error','Invalid phone');
			return FALSE;
		}
	}

	function delete_user($phone = 0,$user_id = 0){
		if($phone){
			$user = $this->users_m->get_user_by_identity($phone);
		}elseif($user_id){
			$user = $this->get_user($user_id);
		}else{
			$user = array();
		}
		if($user){
			$post = $this->get_user($user->id);
	        if(empty($post)){
	            $this->session->set_flashdata('error','Sorry the user does not exist');
	            return FALSE;
	            die;
	        }

	        if($this->is_admin($post->id) && $post->id==1){
	            $this->session->set_flashdata('error','You can not delete Super admin');
	            return FALSE;
	        }
	        
	        $tables = $this->db->list_tables();
	        foreach ($tables as $table) {
	            $table_name[$table] = $this->db->list_fields($table);
	        }
	        $success=0;
	        $fails = 0;
	        foreach ($table_name as $key => $value) {
	            if(in_array('user_id', $value)){
	                $table_names[$key] = $value;
	                if($this->users_m->delete_user_data($post->id,$key)){
	                    ++$success;
	                }else{
	                    ++$fails;
	                }
	            }
	        }
	        if($success){
	        	$this->session->set_flashdata('success',$success.' success to delete user');
	        }
	        if($fails){
	        	$this->session->set_flashdata('error',$fails.' failed to delete user');
	        }
	        if ($this->ion_auth_model->delete_user($post->id)) {
	            return TRUE;
	        }else{
	            return FALSE;
	        }
		}        
	}

	function change_language($language_id=0){
		$domain_name = $this->config->item('cookie_domain');
		if($language = $this->languages_m->get($language_id)){
            if($this->ion_auth->logged_in()){
            	$user = $this->ion_auth->get_user();
                $input = array(
                    'language_id' => $language_id,
                    'modified_on' => time(),
                    'modified_by' => $user->id,
                );
                $update = $this->users_m->update_user($user->id,$input);
                if($update){
                	if(isset($_COOKIE['language_id'])){
	                    unset($_COOKIE['language_id']);
	                }
	                setcookie('language_id', $language_id,time()+(86400*30),'/',$domain_name,TRUE,TRUE);
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                if(isset($_COOKIE['language_id'])){
                    unset($_COOKIE['language_id']);
                }
                setcookie('language_id', $language_id,time()+(86400*30),'/',$domain_name,TRUE,TRUE);
                return TRUE;
            }
        }else{
        }
	}
	

}
