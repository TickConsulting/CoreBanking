<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Pages extends Public_Controller
{
	public $user;

	protected $request_group_membership_rules = array(
        array(
                'field' =>  'full_name',
                'label' =>  'Full Name',
                'rules' =>  'required|trim|xss_clean|callback__full_name_has_illegal_characters',
        ),
        array(
                'field' =>  'phone',
                'label' =>  'Phone Number',
                'rules' =>  'trim|required|xss_clean|callback__valid_phone',
		),
		array(
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'trim|xss_clean|valid_email'
		),
		array(
				'field' => 'id_number',
				'label' => 'ID Number',
				'rules' => 'trim|xss_clean|numeric'
		),
		array(
				'field' => 'location', 
				'label' => 'Location', 
				'rules' => 'trim|xss_clean'
		),
		array(
				'field' => 'next_of_kin_full_name', 
				'label' => 'Next of Kin Full Name', 
				'rules' => 'trim|xss_clean|callback__next_of_kin_full_name_has_illegal_characters'
		),
		array(
				'field' => 'next_of_kin_id_number',	
				'label'	=> 'Next of Kin ID Number', 
				'rules' => 'trim|xss_clean|numeric'
		),
		array(
			
				'field' => 'next_of_kin_phone', 
				'label' => 'Next of Kin Phone',
				'rules' => 'trim|xss_clean|callback__valid_phone'
		),
		array(
				'field' => 'next_of_kin_relationship', 
				'label' => 'Next of Kin Relationship', 
				'rules' => 'trim|xss_clean'
		)
    );

	protected $path = "uploads/groups";

	function _valid_phone(){
        $phone = $this->input->post('phone');
        if(!valid_phone($phone)){
            $this->form_validation->set_message('_valid_phone','Phone number entered is not a valid Phone Number');
            return FALSE;
        }else{
            return TRUE;
        }
    }

	function _full_name_has_illegal_characters(){
        $full_name = $this->input->post('full_name');
        if(is_character_allowed($full_name)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_full_name_has_illegal_characters','You have entered illegal characters in the First Name field, avoid using the following: % $ - - & * ? < > ');
            return FALSE;
        }
    }

	function _next_of_kin_full_name_has_illegal_characters(){
        $next_of_kin_full_name = $this->input->post('next_of_kin_full_name');
        if(is_character_allowed($next_of_kin_full_name)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_next_of_kin_full_name_has_illegal_characters','You have entered illegal characters in the Next of Kin First Name field, avoid using the following: % $ - - & * ? < > ');
            return FALSE;
        }
    }

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
        $this->load->library('files_uploader');
		$this->load->helper('cookie');
		$this->load->model('pages_m');
		$this->load->model('languages/languages_m');
		$this->load->model('groups/groups_m');
		$this->load->model('members/members_m');
		$this->load->model('notifications/notifications_m');
		$this->languages = $this->languages_m->get_all();
		
		$this->currency_options= $this->countries_m->get_currency_options();
		$this->currency_code_options= $this->countries_m->get_currency_code_options();
		$this->country_code_options= $this->countries_m->get_country_code_options();
		$this->country_options= $this->countries_m->get_country_options();
		
	}

	function _check_login(){
		$uri_string = $this->uri->uri_string();
		$access_exempt = array(
			'login',
			'logout',
			'forgot_password',
			'reset_password',
			'confirm_code',
			'signup',
			'join',
		);

		foreach ($access_exempt as $key => $value){
				 $access = explode('/', $value);
				 if(preg_match('/'.$access[0].'/', $uri_string))
				 {
						return TRUE;
				 }
		 }
		 
		if(!$this->ion_auth->logged_in()){      
				return FALSE;
		}
		return TRUE;
	}

	function index(){
		$home_page_controller = trim($this->application_settings->home_page_controller);
		switch (ENVIRONMENT) {
			case 'maintenance':
				$this->maintenance();
				break;
			default:
				if($home_page_controller!=='index'){
					if(method_exists($this,$home_page_controller)){
						$this->$home_page_controller();
					}else{

						// if($_SERVER['HTTP_HOST'] == 'websacco.com') { header('location: ./home'); die; } //redirect to new landing page
				 		$this->template->set_layout('blank.html')->title($this->application_settings->application_name .'- Premium Group Management Software')->build('pages/index');	
					}
				}else{
					
					// if($_SERVER['HTTP_HOST'] == 'websacco.com') { header('location: ./home'); die; } //redirect to new landing page
				 	$this->template->set_layout('blank.html')->title($this->application_settings->application_name .'- Premium Group Management Software')->build('pages/index');	
				}
				break;
		}
		
	}

	function android(){
	 	$this->template->set_layout('android_default.html')->title($this->application_settings->application_name .' - Premium Group Management Software')->build('pages/android');	
	}

	function dismiss_android(){
		$cookie= array(
	      	'name'   => 'android_dialogue_dismissed',
	      	'value'  => '1',
	       	'expire' => '8650000',
	  	);
  		$this->input->set_cookie($cookie);
  		$referrer = $this->input->cookie('referrer');
  		if($referrer){
  			redirect($referrer);
  		}else{
  			print_r($_COOKIE);
  			die;
  			redirect(site_url("/"));
  		}
	}

	function equity_bank(){
		$cookie= array(
	      	'name'   => 'partner',
	      	'value'  => 'equity_bank',
	       	'expire' => '86500',
	  	);
  		$this->input->set_cookie($cookie);
	 	$this->template->set_layout('equity_bank_default.html')->title($this->application_settings->application_name .' - Investment Group Financial Management Software')->build('pages/equity_bank');	
	}

	function eazzyclub(){
		$cookie= array(
	      	'name'   => 'partner',
	      	'value'  => 'equity_bank',
	       	'expire' => '86500',
	  	);
  		$this->input->set_cookie($cookie);
	 	$this->template->set_layout('eazzyclub_default.html')->title($this->application_settings->application_name .' - Investment Club Financial Management Software')->build('pages/eazzyclub');	
	}

	function eazzykikundi(){
		$cookie= array(
	      	'name'   => 'partner',
	      	'value'  => 'equity_bank',
	       	'expire' => '86500',
	  	);
  		$this->input->set_cookie($cookie);
	 	$this->template->set_layout('eazzykikundi_default.html')->title($this->application_settings->application_name .' - Investment Club Financial Management Software')->build('pages/eazzykikundi');
	}

	function terms_and_conditions(){
		$cookie = array(
	      	'name'   => 'partner',
	      	'value'  => 'equity_bank',
	       	'expire' => '86500',
	  	);
  		$this->input->set_cookie($cookie);
	 	$this->template->set_layout('equity_bank_default.html')->title($this->application_settings->application_name .' - Terms and Conditions')->build('pages/terms_and_conditions');	
	}

	function associations(){
		$cookie= array(
	      	'name'   => 'partner',
	      	'value'  => 'associations',
	       	'expire' => '86500',
	  	);
  		$this->input->set_cookie($cookie);
	 	$this->template->set_layout('associations_default.html')->title($this->application_settings->application_name .' - Premium Investment Group Financial Management Software')->build('pages/associations');	
	}

	function terms_of_use(){
		$this->template->set_layout('terms_of_use.html')->title($this->application_settings->application_name .' - Terms Of Use')->build('pages/terms_of_use');	
	}

	function pricing(){
		$this->template->set_layout('pricing.html')->title($this->application_settings->application_name .' - Pricing')->build('pages/pricing');	
	}

	function technical_specs(){
		$this->template->set_layout('terms_of_use.html')->title($this->application_settings->application_name .' - Technical Specification')->build('pages/technical_specs');
	}

	function features(){
		$this->template->set_layout('features.html')->title($this->application_settings->application_name .' - Features')->build('pages/features');	
	}

	function maintenance(){
		$this->template->set_layout('blank.html')->title($this->application_settings->application_name .' - Under Maintenance')->build('pages/maintenance');
	}

	function error_404_page(){
		if(preg_match("/tick/i",$this->application_settings->application_name)){
			$this->template->set_layout('blank.html')->title($this->application_settings->application_name .' - 404 Page Not Found')->build('pages/error_404_websacco_page');
		}else{
			$this->template->set_layout('blank.html')->title($this->application_settings->application_name .' - 404 Page Not Found')->build('pages/error_404_websacco_page');
			// $this->template->set_layout('blank.html')->title($this->application_settings->application_name .' - 404 Error Page')->build('pages/error_404_page');
		}
	}

	function on_premise(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - On Premise Group Solution')->build('pages/on_premise');	
	}

	function on_cloud(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - On Cloud Group Solution')->build('pages/on_cloud');	
	}

	function loans(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Loans')->build('pages/loans');	
	}

	function communications(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Communications')->build('pages/communications');	
	}

	function sacco_accounts(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Accounts')->build('pages/sacco_accounts');	
	}

	function member_deposits(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Member Deposits')->build('pages/member_deposits');	
	}

	function transactions(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Transactions')->build('pages/transactions');	
	}

	function investments(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Investments')->build('pages/investments');	
	}

	function notifications(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Notifications')->build('pages/notifications');	
	}

	function fining(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Member Fining')->build('pages/fining');	
	}

	function reports(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Reports')->build('pages/reports');	
	}

	function member_management(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Member Onboarding & Enrollment')->build('pages/member_management');	
	}

	function ewallet(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group E-wallet')->build('pages/ewallet');	
	}

	function checkoffs(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Member Checkoffs')->build('pages/checkoffs');	
	}

	function statements(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Statements')->build('pages/statements');	
	}

	function income_and_expense_management(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Group Income & Expense Management')->build('pages/income_and_expense_management');	
	}

	function for_digital_lenders(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - For Digital Lenders')->build('pages/for_digital_lenders');	
	}

	function for_saccos(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - For Groups')->build('pages/for_saccos');	
	}

	function for_company_saccos(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - For Company Groups')->build('pages/for_company_saccos');	
	}

	function for_shylock(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - For Mr. Shylock')->build('pages/for_shylock');	
	}

	function for_microfinance_organizations(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - For Microfinance Organizations')->build('pages/for_microfinance_organizations');	
	}

	function demo_questionnaire(){
		$this->template->set_layout('solutions.html')->title($this->application_settings->application_name .' - Online Demo')->build('pages/demo_questionnaire');	
	}

	function join_group($join_code = 0){
		$join_code OR redirect('/');
		$post = new StdClass();
		$validation_errors = array();
		$success_message = '';
		$error_message = '';
		if($_POST){

			$this->form_validation->set_rules($this->request_group_membership_rules);
			$avatar['file_name'] = '';
			if($this->form_validation->run()){
				$avatar_directory = './uploads/groups';
				if(!is_dir($avatar_directory)){
					mkdir($avatar_directory,0777,TRUE);
				}
				if($_FILES['avatar']['name'])
				{
					$allowed_types = 'jpg|jpeg|png|svg|gif';
					if($avatar = $this->files_uploader->upload('avatar',$this->path,$allowed_types)){
					}else{
						$error_message = "Avatar File Type is not supported";
					}
				}
				if(empty($error_message)){
					$full_names = explode(' ',$this->input->post('full_name'));
					if(count($full_names) > 1){
						$count = count($full_names);
						if ($count == 2) {
							$first_name = $full_names[0];
							$last_name = $full_names[1];
						} else if ($count == 3) {
							$first_name = $full_names[0];
							$last_name = $full_names[1] . ' ' . $full_names[2];
						} else if ($count == 4) {
							$first_name = $full_names[0];
							$last_name = $full_names[1] . ' ' . $full_names[2] . ' ' . $full_names[3];
						}
						$user_input = array(
							'first_name' => $first_name,
							'last_name' => $last_name,
							'phone' => valid_phone($this->input->post('phone')),
							'group_id' => $this->input->post('group_id'),
							'email' => $this->input->post('email'),                    
							'id_number' => $this->input->post('id_number'),
							'avatar' => $avatar['file_name']?:'',
							'location' => $this->input->post('location'),
							'next_of_kin_full_name' => $this->input->post('next_of_kin_full_name'),
							'next_of_kin_id_number' => $this->input->post('next_of_kin_id_number'),
							'next_of_kin_phone' => $this->input->post('next_of_kin_phone'),
							'next_of_kin_relationship' => $this->input->post('next_of_kin_relationship'),
						);
						$result = $this->members_m->insert_group_membership_request_data($user_input);
						$success = $result ? true : false;		
						if($result){
							$notif_input = array(
								'to_member_id' => $this->input->post('member_id'),
								'group_id' => $this->input->post('group_id'),
								'message' => $this->input->post('first_name').' '.$this->input->post('last_name').' has requested to join your group',
								'subject' => 'Group Membership Request',
								'active' => 1,
								'is_read' => 0,
								'call_to_action_link' => 'group/members/self_registered_members',
								'created_on' => time()
							);
							$this->notifications_m->insert($notif_input);
							$success_message = "Membership request sent successfully";					
						} else {
							$error_message = "Oops! Something went wrong. Please try again";
						}
					}else {
						$error_message = "Full Name entered is not valid. Enter First Name and Last Name.";
					}
				}else{

				}		
			} else {				
				$form_errors = $this->form_validation->error_array();
				foreach ($form_errors as $key => $value) {
					$validation_errors[$key] = $value;
				}

				foreach ($this->request_group_membership_rules as $key => $field) 
				{
					$field_value = $field['field'];
					$post->$field_value= set_value($field['field']);
				}
			}
	    }
		
		$group = $this->groups_m->get_by_join_code($join_code);			
		$member = $group ? $this->members_m->get_group_member_by_user_id($group->id,$group->owner) : null;
		foreach ($this->request_group_membership_rules as $key => $field) 
		{
			$field_value = $field['field'];
			$post->$field_value= set_value($field['field']);
		}
	
		$this->data['post'] = $post;
		$this->data['group'] = $group;
		$this->data['member_id'] = $member ? $member->id : null;
		$this->data['success_message'] = $success_message;
		$this->data['error_message'] = $error_message;
		$this->data['validation_errors'] = $validation_errors;
		
		$this->template->set_layout('authentication.html')->title($this->application_settings->application_name .' - Join Group')->build('pages/join_group', $this->data);
	}
	
}
