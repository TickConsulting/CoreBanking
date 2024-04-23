<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
class Messaging{

	protected $ci;
	public $application_settings;
	public $default_country;

	public $email_types = array(
		'1'=>'Thank you email',
		'2'=>'Group size email',
	);

	public function __construct(){
  		$this->ci= & get_instance();
		$this->ci->load->model('sms/sms_m');
		$this->ci->load->model('emails/emails_m');
		$this->ci->load->model('settings/settings_m');
		$this->ci->load->model('countries/countries_m');
		$this->ci->load->model('groups/groups_m');
		$this->ci->load->model('notifications/notifications_m');
		$this->ci->load->model('users/users_m');
		$this->ci->load->model('bank_accounts/bank_accounts_m');
		$this->ci->load->model('loan_applications/loan_applications_m');
		$this->ci->load->model('loan_types/loan_types_m');
		$this->ci->load->model('loans/loans_m');
		$this->ci->load->model('group_roles/group_roles_m');
		$this->application_settings = $this->ci->settings_m->get_settings()?:'';
        $this->default_country = $this->ci->countries_m->get_default_country();
        $this->ci->load->library('loan');
        $this->ci->load->library('Pmailer');
		$this->ci->load->library('Emails_manager');
	}

	public function send_user_otp($auth=array()){		
		if($auth){
			$sms_success_entries = TRUE;
			$email_success_entries = TRUE;
			$auth = (object)$auth;
			if(valid_phone($auth->phone)){
				$message = $this->ci->sms_m->build_sms_message('user-activation-code',array(
					'PIN'=>$auth->pin,
					'APPLICATION_NAME'	=>$this->application_settings->application_name,
				),'',$auth->language_id);				
				if($success = $this->ci->sms_m->send_system_sms(valid_phone($auth->phone),$message,1,$auth->user_id)){
					$sms_success_entries =  TRUE;
				}else{
					$sms_success_entries = FALSE;
				}
			}
			if(valid_email($auth->email)){
				$subject = $this->application_settings->application_name.' activation code';				
				$message = $this->ci->emails_m->build_email_message('user-activation-code',array(
					'CODE'=>$auth->pin,
					'FIRST_NAME'=>$auth->first_name,
					'LAST_NAME'=>$auth->last_name,
					'APPLICATION_NAME'	=>	$this->application_settings->application_name,
					'SUBJECT' => $subject,
					'YEAR' => date('Y'),
					'SENDER' => $this->application_settings->application_name,
					'LOGO' => $this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',
					'EXPIRY_TIME' => $auth->expiry_time, 
				),'',$auth->language_id);
				// echo $this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'';
				// echo $message;die;
				
				if($success = $this->ci->emails_m->send_email($auth->email,$subject,$message)){
					$email_success_entries = TRUE;
				}else{
					$email_success_entries = FALSE;
				}
			}
			if($sms_success_entries || $email_success_entries){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	public function send_mobile_user_otp($auth=array()){
		if($auth){
			if(valid_phone($auth->phone)){
				$message = $this->ci->sms_m->build_sms_message('user-activation-code',array(
					'PIN'=>$auth->pin,
					'APPLICATION_NAME'	=>	$this->application_settings->application_name,
				),'',$auth->language_id);
				if($success = $this->ci->sms_m->send_system_sms(valid_phone($auth->phone),$message,1,$auth->user_id)){
					return TRUE;
				}else{
					return FALSE;
				}
			}else if(valid_email($auth->email)){
				$subject = $this->application_settings->application_name.' account activation pin (OTP)';
				$message = $this->ci->emails_m->build_email_message('user-activation-pin-template',array(
					'PIN'=>$auth->pin,
					'APPLICATION_NAME'	=>	$this->application_settings->application_name,
					'SENDER' => $this->application_settings->application_name,
					'SUBJECT' => $subject,
					'YEAR' => date('Y'),
				),'',$mmemkn);				
				if($success = $this->ci->emails_m->send_email($auth->email,$subject,$message)){
					return TRUE;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	public function generate_user_otp($phone_number='',$reference_number=''){
		if($reference_number&&$phone_number){
			$json_file = json_encode(array(
				"reference" => $reference_number."",
			    "to" => $phone_number,
			    "platform" => 1,
			    "operation" => "onboarding",
			    "source" => "chatbot",
			    "noOfDigit" => 6,
			    "customerId" => "0170194290581",
			    "signature" => "jhldhsfkdfdsklf"
			));
			return $this->ci->curl->equityBankRequests->generate_otp($json_file);
		}else{
			$this->ci->session->set_flashdata('error','Reference number and Phone number are required');
			return FALSE;
		}
	}

	public function generate_user_otp_websacco($phone_number='',$otp_code='', $member_id = '',$user_id = '', $group_id=''){
		if($phone_number && $otp_code && $member_id && $user_id && $group_id){
			$message =  'Kindly use '.$otp_code.' as your OTP Code for the withdrawal request';
			$result = $this->ci->sms_m->send_sms($phone_number,$message,$member_id,$user_id,$group_id,$user_id,1);
			return $result;
		}else{
			$this->ci->session->set_flashdata('error','Phone number and the OTP code are required');
			return FALSE;
		}
	}

	function verify_user_otp($reference_number='',$otp_code=0){
		if($reference_number&&$otp_code){
			$json_file = json_encode(array(
			    "reference" => $reference_number,
			    "operation" => "onboarding",
			    "source" => "chatbot",
			    "otp" => $otp_code
			));
			return $this->ci->curl->equityBankRequests->verify_otp($json_file);
		}else{
			$this->ci->session->set_flashdata('error','Reference Number and OTP code are required');
			return FALSE;
		}
	}

	public function send_withdrawal_approval_request_sms($group = array(),$member = array(),$requester_user = array(),$group_currency = "KES",$amount = 0,$withdrawal_for = ''){
		if($group&&$member&&$group_currency&&$amount&&$requester_user&&$withdrawal_for){
			$sms_data = array(
				'GROUP_NAME' => '['.$group->name.']',
				'FIRST_NAME' => $member->first_name,
				'CURRENCY' => $group_currency,
				'AMOUNT' => number_to_currency($amount),
				'WITHDRAWAL_FOR' => $withdrawal_for,
				'REQUESTED_BY' => $requester_user->first_name.' '.$requester_user->last_name,
				'MINUTES' => 12,
			);

			$message = $this->ci->sms_m->build_sms_message('withdrawal-request-notification',$sms_data,"",$member->language_id);


			$input = array(
                'sms_to' => $member->phone,
                'system_sms' => 1,
                'message' => $message,
                'group_id' => $group->id,
                'member_id' => $member->id,
                'user_id' => $member->user_id,
                'created_on' => time(),
                'created_by'=> $requester_user->id
            );
            if($sms_id = $this->ci->sms_m->insert_sms_queue($input)){
	            return TRUE;
            }else{
				return FALSE;
            }

		}else{
			return FALSE;
		}

	}


	function send_withdrawal_request_decline_sms_notification($group=array(),$group_currency="KES",$withdrawal_request=array(),$withdrawal_request_transaction_names=array(),$priority_cancel= FALSE,$signatories=array(),$expired=FALSE){
		if($group&&$signatories&&$group_currency&&$withdrawal_request&&$withdrawal_request_transaction_names){
			foreach ($signatories as $signatory) {
				if($expired){
					$sms_data = array(
						'GROUP_NAME' => '['.$group->name.']',
						'FIRST_NAME' => $signatory->first_name,
						'WITHDRAWAL_DATE' => timestamp_to_datepicker($withdrawal_request->request_date),
						'WITHDRAWAL_FOR' => translate($withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]),
						'CURRENCY' => $group_currency,
						'AMOUNT' => number_to_currency($withdrawal_request->amount),
					);
					$message = $this->ci->sms_m->build_sms_message('withdrawal-approval-request-expired',$sms_data,'',$signatory->language_id);
				}else if($signatory->user_id == $withdrawal_request->created_by){
					if($priority_cancel){
						$message = '';
					}else{
						$sms_data = array(
							'GROUP_NAME' => '['.$group->name.']',
							'FIRST_NAME' => $signatory->first_name,
							'WITHDRAWAL_DATE' => timestamp_to_datepicker($withdrawal_request->request_date),
							'WITHDRAWAL_FOR' => translate($withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]),
							'CURRENCY' => $group_currency,
							'AMOUNT' => number_to_currency($withdrawal_request->amount),
						);
						$message = $this->ci->sms_m->build_sms_message('withdrawal-approval-request-declined-for-owner',$sms_data,'',$signatory->language_id);
					}
				}else{
					$sms_data = array(
						'GROUP_NAME' => '['.$group->name.']',
						'FIRST_NAME' => $signatory->first_name,
						'WITHDRAWAL_DATE' => timestamp_to_datepicker($withdrawal_request->request_date),
						'WITHDRAWAL_FOR' => translate($withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]),
						'CURRENCY' => $group_currency,
						'AMOUNT' => number_to_currency($withdrawal_request->amount),
						'ACTION' => $priority_cancel?'cancelled':'declined',
					);
					$message = $this->ci->sms_m->build_sms_message('withdrawal-approval-request-declined',$sms_data,'',$signatory->language_id);
				}
				if($message){
					$input = array(
		                'sms_to' => $signatory->phone,
		                'system_sms' => 1,
		                'message' => $message,
		                'group_id' => $group->id,
		                'member_id' => $signatory->id,
		                'user_id' => $signatory->user_id,
		                'created_on' => time(),
		                'created_by'=> 1,
		            );
		            $sms_id = $this->ci->sms_m->insert_sms_queue($input);
				}
			}
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function notify_all_members_withdrawal_success($request=array(),$currency=''){
		if($request&&$currency){
			$members = $this->ci->members_m->get_active_group_members($request->group_id);
            $group = $this->ci->groups_m->get($request->group_id);
            $requester = $this->ci->ion_auth->get_user($request->created_by);
            $sms_object = array(
				'CURRENCY' => $currency,
				'AMOUNT' => number_to_currency($request->amount),
				'REQUESTER' => $requester->first_name.' '.$requester->last_name,
				'GROUP_NAME' => '['.$group->name.']',
			);
			$message = $this->ci->sms_m->build_sms_message('withdrawal-disbursement-notification',$sms_object,'',$requester->language_id);
			$message2 = $this->ci->sms_m->build_sms_message('withdrawal-disbursement-notification-to-user',$sms_object,'',$requester->language_id);
			$sms_input = array();
			foreach ($members as $id => $member) {
				if($member->user_id == $request->created_by){
					$sms_input[] = array(
						'sms_to' => valid_phone($member->phone),
						'group_id' => $group->id,
						'member_id' => $member->id,
						'user_id' => $member->user_id,
						'message' => str_replace('{firstName}',$member->first_name,$message2),
						'created_on' => time(),
						'created_by' => 1,
					);
				}else{
					$sms_input[] = array(
						'sms_to' => valid_phone($member->phone),
						'group_id' => $group->id,
						'member_id' => $member->id,
						'user_id' => $member->user_id,
						'message' => str_replace('{firstName}',$member->first_name,$message),
						'created_on' => time(),
						'created_by' => 1,
					);
				}
			}
			if($sms_input){
				$this->ci->sms_m->insert_batch_to_queue($sms_input);
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	public function generate_first_time_password(){
		return generate_password();
	}

	public function send_member_first_time_login_invitation_message($group=array() ,$send_member_invitation = TRUE,$user=array()){
		if($group&&$user){
			$members = $this->ci->members_m->get_active_group_members($group->id);
			$group_roles = $this->ci->group_roles_m->get_group_role_options($group->id);
			$sms_input = array();
			$email_input = array();
			
			foreach ($members as $member) {
				//ignore is user is sender
				if($member->user_id == $user->id) continue;

				if(valid_phone($member->phone)){
					$the_user = $this->ci->ion_auth->get_user($member->user_id);
					//set one time password
					$first_time_password = $this->generate_first_time_password();
					$update = $this->ci->ion_auth->update($member->user_id, array('password'=>$first_time_password));
					$sms_object = array(
						'FIRST_NAME' => $member->first_name,
						'GROUP_NAME' => $group->name,
						'ROLE' => isset($group_roles[$member->group_role_id])?translate($group_roles[$member->group_role_id]):translate('Member'),
						'SENDER' => $user->first_name.' '.$user->last_name,
						'LINK' => site_url(),
						'FIRST_TIME_PASSWORD' => $first_time_password,
						'APPLICATION_NAME' => $this->application_settings->application_name,
					);
					if(!$the_user->last_login){
						$message = $this->ci->sms_m->build_sms_message('invite-member-to-eazzy-kikundi',$sms_object,'',$member->language_id);
					} else {
						$message = $this->ci->sms_m->build_sms_message('invite-member-to-websacco',$sms_object,'',$member->language_id);
					}
					
					$sms_input[] = array(
						'sms_to' => valid_phone($member->phone),
						'group_id' => $group->id,
						'member_id' => $member->id,
						'user_id' => $user->id,
						'message' => $message,
						'created_on' => time(),
						'created_by' => $user->id,
					);
				}
				if(valid_email($member->email)){
					$build_message = array(
						'APPLICATION_NAME' => $this->application_settings->application_name,
						'GROUP_NAME' => $group->name,
						'FIRST_NAME' => $member->first_name,
						'LAST_NAME' => $member->last_name,
						'SENDER' => $user->first_name.' '.$user->last_name,
						'LINK' => site_url(),
						'FIRST_TIME_PASSWORD' => $first_time_password,
						'EMAIL_ADDRESS' => $member->email,
						'ROLE' => isset($group_roles[$member->group_role_id])?$group_roles[$member->group_role_id]:'Member',
						'LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
						'YEAR' => date('Y'),
					);
					
					if(!$the_user->last_login){//invite member first time
						$email_message = $this->ci->emails_m->build_email_message('invite-member-first-time',$build_message);
					} else {
						$email_message = $this->ci->emails_m->build_email_message('invite-existing-member',$build_message);
					}
					$email_input[] = array(
						'email_to'=>$member->email,
						'subject'=> 'Invitation to join '.$group->name,
						'email_from'=> 'info@'.$_SERVER['HTTP_HOST'],
						'group_id'=> $group->id,
						'member_id'=> $member->id,
						'user_id'=> $user->id,
						'message'=> $email_message,
						'created_on'=> time(),
						'created_by'=> $user->id,
					);
				}
			}
			if($sms_input){
				$this->ci->sms_m->insert_batch_to_queue($sms_input);
			}
			if($email_input){
				$this->ci->emails_m->insert_chunk_emails_queue($email_input);
			}
			return TRUE;
		}else{
			$this->ci->session->set_flashdata('error','User or group object is empty');
			return FALSE;
		}
	}

	public function send_single_member_first_time_login_invitation_message($group=array() ,$send_member_invitation = TRUE,$user=array(),$invited_member_user_id = 0){
		if($group&&$user){
			$member = $this->ci->members_m->get_group_member_by_user_id($group->id,$invited_member_user_id);
			$group_roles = $this->ci->group_roles_m->get_group_role_options($group->id);
			$sms_input = array();
			$email_input = array();
			
			$the_user = $this->ci->ion_auth->get_user($invited_member_user_id);	
			if(valid_phone($member->phone)){
				$the_user = $this->ci->ion_auth->get_user($member->user_id);
				//set one time password
				$first_time_password = $this->generate_first_time_password();
				$update = $this->ci->ion_auth->update($member->user_id, array('password'=>$first_time_password));
				$sms_object = array(
					'FIRST_NAME' => $member->first_name,
					'GROUP_NAME' => $group->name,
					'ROLE' => isset($group_roles[$member->group_role_id])?translate($group_roles[$member->group_role_id]):translate('Member'),
					'SENDER' => $user->first_name.' '.$user->last_name,
					'LINK' => site_url(),
					'FIRST_TIME_PASSWORD' => $first_time_password,
					'APPLICATION_NAME' => $this->application_settings->application_name,
				);
				if(!$the_user->last_login){
					$message = $this->ci->sms_m->build_sms_message('invite-member-to-eazzy-kikundi',$sms_object,'',$member->language_id);
				} else {
					$message = $this->ci->sms_m->build_sms_message('invite-member-to-websacco',$sms_object,'',$member->language_id);
				}
				
				$sms_input[] = array(
					'sms_to' => valid_phone($member->phone),
					'group_id' => $group->id,
					'member_id' => $member->id,
					'user_id' => $user->id,
					'message' => $message,
					'created_on' => time(),
					'created_by' => $user->id,
				);
			}
			if(valid_email($member->email)){
				$build_message = array(
					'APPLICATION_NAME' => $this->application_settings->application_name,
					'GROUP_NAME' => $group->name,
					'FIRST_NAME' => $member->first_name,
					'LAST_NAME' => $member->last_name,
					'SENDER' => $user->first_name.' '.$user->last_name,
					'LINK' => site_url(),
					'FIRST_TIME_PASSWORD' => $first_time_password,
					'EMAIL_ADDRESS' => $member->email,
					'ROLE' => isset($group_roles[$member->group_role_id])?$group_roles[$member->group_role_id]:'Member',
					'LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
					'YEAR' => date('Y'),
				);
				
				if(!$the_user->last_login){//invite member first time
					$email_message = $this->ci->emails_m->build_email_message('invite-member-first-time',$build_message);
				} else {
					$email_message = $this->ci->emails_m->build_email_message('invite-existing-member',$build_message);
				}
				$email_input[] = array(
					'email_to'=>$member->email,
					'subject'=> 'Invitation to join '.$group->name,
					'email_from'=> 'info@'.$_SERVER['HTTP_HOST'],
					'group_id'=> $group->id,
					'member_id'=> $member->id,
					'user_id'=> $user->id,
					'message'=> $email_message,
					'created_on'=> time(),
					'created_by'=> $user->id,
				);
			}	
			
			if($sms_input){
				$this->ci->sms_m->insert_batch_to_queue($sms_input);
			}
			if($email_input){
				$this->ci->emails_m->insert_chunk_emails_queue($email_input);
			}
			return TRUE;
		}else{
			$this->ci->session->set_flashdata('error','User or group object is empty');
			return FALSE;
		}
	}

	public function send_member_first_time_invitation_message($group=array() ,$send_member_invitation = TRUE,$user=array()){
		if($group&&$user){
			$members = $this->ci->members_m->get_active_group_members($group->id);
			$group_roles = $this->ci->group_roles_m->get_group_role_options($group->id);
			$sms_input = array();
			$email_input = array();
			
			foreach ($members as $member) {
				if(valid_phone($member->phone)){
					$sms_object = array(
						'FIRST_NAME' => $member->first_name,
						'LAST_NAME' => $member->last_name,
						'GROUP_NAME' => $group->name,
						'ROLE' => isset($group_roles[$member->group_role_id])?$group_roles[$member->group_role_id]:'Member',
						'SENDER' => $user->first_name.' '.$user->last_name,
						'LINK' => 'https://websacco.com/',
						'APPLICATION_NAME' => $this->application_settings->application_name,
						'LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
					);
					$message = $this->ci->sms_m->build_sms_message('invite-member-to-websacco',$sms_object,'',$member->language_id);
					$sms_input[] = array(
						'sms_to' => valid_phone($member->phone),
						'group_id' => $group->id,
						'member_id' => $member->id,
						'user_id' => $user->id,
						'message' => $message,
						'created_on' => time(),
						'created_by' => $user->id,
					);
				}
				if(valid_email($member->email)){
					$build_message = array(
						'APPLICATION_NAME' => $this->application_settings->application_name,
						'GROUP_NAME' => $group->name,
						'FIRST_NAME' => $member->first_name,
						'LAST_NAME' => $member->last_name,
						'SENDER' => $user->first_name.' '.$user->last_name,
						'LINK' => 'https://websacco.com',
						'ROLE' => isset($group_roles[$member->group_role_id])?$group_roles[$member->group_role_id]:'Member',
						'YEAR' => date('Y'),
						'LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',
					);
					$email_message = $this->ci->emails_m->build_email_message('invite-member-to-websacco',$build_message,'',$member->language_id);
					$email_input[] = array(
						'email_to'=>$member->email,
						'subject'=> 'Invitation to join '.$group->name,
						'email_from'=> 'info@websacco.com',
						'group_id'=> $group->id,
						'member_id'=> $member->id,
						'user_id'=> $user->id,
						'message'=> $email_message,
						'created_on'=> time(),
						'created_by'=> $user->id,
					);
					
				}
			}
			if($sms_input){
				$this->ci->sms_m->insert_batch_to_queue($sms_input);
			}
			if($email_input){
				$this->ci->emails_m->insert_chunk_emails_queue($email_input);
			}
			return TRUE;
		}else{
			$this->ci->session->set_flashdata('error','User or group object is empty');
			return FALSE;
		}
	}

	function send_demo_request_email($email_array = array()){
		if($email_array){
			$email_object = (object)$email_array;
			if(valid_email($email_object->email)){
				$email_to_send_notification = array(
					[			
						'name'=>'Lois Nduku',
						'email'=> 'lois.nduku@chamasoft.com'
					],
				
					[
						'name'=>'Lucy Muthoni',
						'email'=> 'lucy.muthoni@chamasoft.com'
					],
				
					[
						'name'=>'WebSacco Support',
					    'email'=> 'support@chamasoft.com'
					],
					[
						'name'=>'Cedric Kinyanjui',
					    'email'=> 'cedric.kinyanjui@chamasoft.com'
					]		
				);			

				foreach ($email_to_send_notification as $data) {
				    $data = (object)$data;	
					$build_message = array(
						'APPLICATION_NAME' => $this->application_settings->application_name,
						'FULL_NAME' => $email_object->full_name,
						'NAME'=>$email_object->name,
						'SUBJECT'=>'Demo Request',
						'SOLUTION' => $email_object->solution,
						'PRODUCT' => $email_object->product,
						'PHONE_NUMBER'=>$email_object->phone,
						'PREFFERED_CONTACT'=>$email_object->preffered_contact,
						'EMAIL'=>$email_object->email,
						'YEAR' => date('Y'),
					);
					$email_message = $this->ci->emails_m->build_email_message('demo-request',$build_message);
					$email_input[] = array(
						'email_to'=>$data->email,
						'subject'=> 'Demo Request',
						'email_from'=> 'info@websacco.com',
						'message'=> $email_message,
						'created_on'=> time(),
						//'created_by'=> $user->id,
					);
				}
				//print_r($email_input); die();
				if($email_input){
					$this->ci->emails_m->insert_chunk_emails_queue($email_input);
				}

			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	public function send_contribution_payment_notification_to_member($group = array(),$member = array(),$send_sms_notification = FALSE,$send_email_notification = FALSE,$amount = 0,$contribution=array(),$currency="KSH",$payment_method="cash",$deposit_date=0){
		if($group&&$member&&$contribution&&$amount){
			$result = TRUE;
			if($send_sms_notification){
	            if(valid_phone($member->phone)){
	            	$sms_body = array(
						'FIRST_NAME' => $member->first_name,
						'GROUP_NAME' => $group->name,
						'PAYMENT_FOR' => $contribution->name,
						'AMOUNT' => number_to_currency($amount),
						'CURRENCY' => $currency,
						'CHANNEL' => $payment_method,
					);
					$sms_message = $this->ci->sms_m->build_sms_message('contribution-payment-receipt',$sms_body,'',$member->language_id);
	                $data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $sms_message,
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                );
	                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        if($send_email_notification){
	            if(valid_email($member->email)){
	            	$email_body = array(
						'TITLE' => $group->name.' - '.$contribution->name.' payment confirmation',
						'GROUP_NAME' => $group->name,
						'FIRST_NAME' => $member->first_name,
						'LAST_NAME' => $member->last_name,
						'GROUP_CURRENCY' => $currency,
						'AMOUNT' => number_to_currency($amount),
						'CONTRIBUTION_NAME' => $contribution->name,
						'DEPOSIT_DATE'=> timestamp_to_receipt($deposit_date),
						'YEAR' => date('Y'),
						'APPLICATION_NAME' => $this->application_settings->application_name,
						'LOGO' => $this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',
						'LINK' => site_url(),
					);
					// $email_message = $this->ci->emails_m->build_email_message('contribution-payment-receipt',$email_body,'',$member->language_id);


	                $email_message = $this->ci->emails_m->build_email_message('contribution-payment',$email_body,'',$member->language_id);
	                $data = array(
	                    'email_to' => $member->email,
	                    'subject' => 'Contribution payment from '.$group->name,
	                    'message' => $email_message,
	                    'email_from' => '',
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time()
	                );
	                if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        return $result;
	    }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function send_contribution_payment_notification_to_member');
	    	
	    	return FALSE;
	    }
	}

	public function send_activation_code($user = array(),$activation_code = 0,$group_slug = '',$group_name = ''){
		if($user->email&&$user->phone&&$group_slug&&$group_name){
			$activation_url = $this->application_settings->protocol.$group_slug.'.'.$this->application_settings->url.'/group/activate?channel=email&activation_code='.$activation_code;

			$message = $this->ci->emails_m->build_email_message('chamasoft-activate-account-template',array(
				'APPLICATION_NAME'	=>	$this->application_settings->application_name,
				'SUBJECT'	=>	$group_name.' activation code ',
				'FIRST_NAME'=>$user->first_name,
				'LAST_NAME' => $user->last_name,
				'ACTIVATION_CODE' => $activation_code,
				'LINK'=>$activation_url,
				'GROUP_NAME' => $group_name,
				'TRIAL_DAYS'=>$this->application_settings?$this->application_settings->trial_days:14,
				'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
			),'',$user->language_id);
			$subject = $group_name.' activation code ';
			$email_result = FALSE;
			if($success = $this->ci->emails_m->send_email($user->email,$subject,$message)){
				$email_result = TRUE;
			}else{
				$this->ci->session->set_flashdata('error','Group account activation email not sent');
			}
			$message = $this->ci->sms_m->build_sms_message('chamasoft-activate-account-template',array(
				'FIRST_NAME'=>$user->first_name,
				'LAST_NAME'=>$user->last_name,
				'TRIAL_DAYS'=>$this->application_settings?$this->application_settings->trial_days:14,
				'LINK'=>$activation_url,
				'ACTIVATION_CODE'=>$activation_code,
				'APPLICATION_NAME'	=>	$this->application_settings->application_name,
				
			),'',$user->language_id);
			$sms_result = FALSE;
			if($success = $this->ci->sms_m->send_system_sms(valid_phone($user->phone),$message,$user->id,$user->id)){
			//if(TRUE){
				$sms_result = TRUE;
			}else{
				$this->ci->session->set_flashdata('error','Group account activation SMS not sent');
			}

			if($email_result&&$sms_result){
				return TRUE;
			}else if($email_result){
				$this->ci->session->set_flashdata('error','Group account activation SMS not sent but Email sent');
				return FALSE;
			}else if($sms_result){
				$this->ci->session->set_flashdata('error','Group account activation Email not sent but SMS sent');
				return FALSE;
			}else{
				$this->ci->session->set_flashdata('error','Neither group activation email nor SMS sent');
				return FALSE;
			}
		}else if($user->phone){
			$message = $this->ci->sms_m->build_sms_message('chamasoft-activate-account-template',array(
				'FIRST_NAME'=>$user->first_name,
				'TRIAL_DAYS'=>$this->application_settings?$this->application_settings->trial_days:14,
				'ACTIVATION_CODE'=>$activation_code,
				'APPLICATION_NAME'	=>	$this->application_settings->application_name,
			),'',$user->language_id);
			if($success = $this->ci->sms_m->send_system_sms(valid_phone($user->phone),$message,$user->id,$user->id)){
				return TRUE;
			}else{
				$this->ci->session->set_flashdata('warning','We could not send the activation code at this moment, please try resending the activation code again later.');
				return FALSE;
			}
		}else if($user->email){
			$activation_url = $this->application_settings->protocol.$group_slug.'.'.$this->application_settings->url.'/group/activate?channel=email&activation_code='.$activation_code;
			$message = $this->ci->emails_m->build_email_message('chamasoft-activate-account-template',array(
				'FIRST_NAME'=>$user->first_name,
				'LAST_NAME'=>$user->last_name,
				'TRIAL_DAYS'=>$this->application_settings?$this->application_settings->trial_days:14,
				'LINK'=>$activation_url,
				'ACTIVATION_CODE'=>$activation_code,
				'GROUP_NAME' => $group_name,
				'APPLICATION_NAME'	=>	$this->application_settings->application_name,
				'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
            
			),'',$user->language_id);
			$subject = $group_name.' activation code ';
			if($success = $this->ci->emails_m->send_email($user->email,$subject,$message)){
				return TRUE;
			}else{
				$this->ci->session->set_flashdata('error','Group account activation email not sent');
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Neither group activation email nor SMS sent. User phone and Email empty.');
			return FALSE;
		}
	} 

	/* New Websacco Messaging */

	public function send_password_change_notice($identity = 0){
		if($identity){
			$user = $this->ci->users_m->get_user_by_identity($identity);
			if($user){
				if($user->email){
					$url =  $this->ci->application_settings->protocol.''.$this->ci->application_settings->url.'/forgot_password';
					$message = $this->ci->emails_m->build_email_message('password-change-successfully',array(
						'FIRST_NAME'=>$user->first_name,
						'LAST_NAME' => $user->last_name,
						'EMAIL_ADDRESS' =>$user->email,
						'APPLICATION_NAME' => $this->ci->application_settings->application_name,
						'SECURE_LINK'=>$url,
						'YEAR'=>date('Y',time()),
						'RECIPIENT_EMAIL'=>$user->email,
						'DATE' =>date('d-m-Y',time()),
						'PRIMARY_THEME_COLOR'=>$this->ci->application_settings->primary_color,
			            'TERTIARY_THEME_COLOR'=>$this->ci->application_settings->tertiary_color,
			            'APPLICATION_LOGO'=>$this->ci->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',

					));
					$subject = $this->ci->application_settings->application_name.' Password Changed';
					$email_body = array(
						'email_from'=>'info@websacco.com',
			            'to'=>$user->email,
			            'cc'=> '',
			            'bcc'=>'',
			            'subject'=> $subject,
			            'message'=>$message,
			            'sending_email'=>'',
			            'attachments'=>'',
			        );
					$success = $this->ci->emails_manager->send_email($email_body);
				}else{
					$this->ci->session->set_flashdata('error','User does not have an email address');
					return FALSE;
				}
			}else{
				$this->ci->session->set_flashdata('error','User details is not available');
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','User identity is required');
			return FALSE;
		}
	}

	public function send_thank_you_email($group_id = 0 ,$send_thank_you_email = TRUE){
		if($group_id){
			$user = $this->ci->ion_auth->get_user($this->ci->group->owner);
			if($user){
				if(valid_email($user->email)){
					if($send_thank_you_email){
						$message = $this->ci->emails_m->build_email_message('thank-you-email',array(
								'FIRST_NAME' => $user->first_name,
								'LAST_NAME' => $user->last_name,
								'GROUP_NAME' => $this->ci->group->name,
								'APPLICATION_NAME'	=>	$this->ci->application_settings->application_name,
								'PRIMARY_THEME_COLOR'=>$this->ci->application_settings->primary_color,
	                            'TERTIARY_THEME_COLOR'=>$this->ci->application_settings->tertiary_color,
	                            'APPLICATION_LOGO'=>$this->ci->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
							)
						);
						$sending_email='';
						$cc = '';
						$bcc='';
						$header='';
						$attachments='';
						$subject = "Welcome to ".$this->ci->application_settings->url;
						$email_array = array(
							'to'=>$user->email,
							'email_from'=>'nonreply@websacco.com',
							'subject'=>$subject,
							'message'=>$message,
							'sending_email'=>$sending_email,
							'attachments'=>$attachments,
							'cc'=>$cc,
							'bcc'=>$bcc,
							'header'=>$header,
						);
						$result = $this->ci->emails_manager->send_email($email_array);
						if($result){
					    	return TRUE;
						}else{
							$this->ci->session->set_flashdata('error','Could not insert email into email queue ');
							return FALSE;
						}
					}
				}
			}else{
				$this->ci->session->set_flashdata('error','Group owner details missing');
				return FALSE;	
			}
		}else{
			$this->ci->session->set_flashdata('error','Group id variable is required');
			return FALSE;
		}
	}

	public function send_group_size_email_reminder($email_array = array()){
		if($email_array){
			$insert_success = 0;
			$insert_fail = 0;
			foreach ($email_array as $key => $emails):
				$email_object = (object)$emails;
				if(valid_email($email_object->email)){
					$message = $this->ci->emails_m->build_email_message('add-members',array(
						'FIRST_NAME' => $email_object->first_name,
						'LAST_NAME' => $email_object->last_name,
						'ACTIVE_SIZE' => $email_object->active_size,
						'GROUP_SIZE'=>$email_object->size,
						'APPLICATION_NAME'	=>	$this->ci->application_settings->application_name,
						'PRIMARY_THEME_COLOR'=>$this->ci->application_settings->primary_color,
                        'TERTIARY_THEME_COLOR'=>$this->ci->application_settings->tertiary_color,
                        'APPLICATION_LOGO'=>$this->ci->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
					));
					$input = array(
						'email_to'=>$email_object->email,
						'subject'=>'Group Size',
						'email_from'=>'',
						'email_type'=>2,
						'group_id'=>$email_object->group_id,
						'member_id'=>$email_object->member_id,
						'user_id'=>$email_object->user_id,
						'message'=>$message,
						'created_on'=>time(),
						'created_by'=>''
					);
					$result = $this->ci->emails_m->insert_email_queue($input);
					if($result){
				    	//everything went well do nothing	
				    	$insert_success++;
					}else{
						$insert_fail++;
					}
				}
			endforeach;
			return $insert_success;
		}else{
			$this->ci->session->set_flashdata('error','Email array is empty(var)');
			return FALSE;
		}
	}

	public function send_user_last_login_reminder($email_array = array()){
		if($email_array){
			$insert_success = 0;
			$insert_fail = 0;
			$input_array = array();
			foreach ($email_array as $key => $emails):
				$email_object = (object)$emails;
				if(valid_email($email_object->email)){
					$message = $this->ci->emails_m->build_email_message('last-login-reminder',array(
						'FIRST_NAME' => $email_object->first_name,
						'LAST_NAME' => $email_object->last_name,
						'LAST_LOGIN_TIME' =>elapsed_time($email_object->last_login),
						'APPLICATION_NAME'	=>	$this->ci->application_settings->application_name,
						'PRIMARY_THEME_COLOR'=>$this->ci->application_settings->primary_color,
                        'TERTIARY_THEME_COLOR'=>$this->ci->application_settings->tertiary_color,
                        'APPLICATION_LOGO'=>$this->ci->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
						)
					);
					$input_array[] = array(
						'email_to'=>$email_object->email,
						'subject'=>'Login Remainder',
						'email_from'=>'',
						'email_type'=>3,
						'group_id'=>$email_object->group_id,
						'member_id'=>$email_object->member_id,
						'user_id'=>$email_object->user_id,
						'message'=>$message,
						'created_on'=>time(),
						'created_by'=>''
					);
					$insert_success++;
				}
			endforeach;
			if($input_array){
				$result = $this->ci->emails_m->insert_chunk_emails_queue($input_array);
				if($result){
					return $insert_success;
				}else{
					return FALSE;
				}				
			}
		}else{
			$this->ci->session->set_flashdata('error','Email array is empty(var)');
			return FALSE;
		}
	}

	/* end new websacco messaging */

	public function send_group_invitation_to_user($group = array(),$user = array(),$member = array(),$current_user = array(),$current_member_id = 0,$send_invitation_sms=FALSE,$send_invitation_email=FALSE,$group_role_options= array()){
		if(valid_phone($user->phone) || valid_email($user->email)){
			$join_code = rand(1200,95555596);
			$input = array(
				'join_code'=>$join_code,
			);
			if($this->ci->ion_auth->update($user->id,$input)){
				if($send_invitation_sms){
					if(valid_phone($user->phone)){
						$message = $this->ci->sms_m->build_sms_message('invite-member-to-websacco',array(
							'FIRST_NAME' => $user->first_name,
							'GROUP_NAME' => $group->name,
							'LOGIN_URL' => $this->application_settings->protocol.''.$this->application_settings->url.'/login',
							'CURRENT_USER_FIRST_NAME' => $current_user->first_name,
							'CURRENT_USER_LAST_NAME' => $current_user->last_name,
							'APPLICATION_NAME'	=>	$this->application_settings->application_name,
							'ROLE' => isset($group_role_options[$member->group_role_id])?translate($group_role_options[$member->group_role_id]):translate('Member'),
							'SENDER' => $current_user->first_name,
						),'',$user->language_id);
						$input = array(
							'sms_to'=>$user->phone,
							'group_id'=>$group->id,
							'member_id'=>$member->id,
							'user_id'=>$user->id,
							'message'=>$message,
							'created_on'=>time(),
							'created_by'=>$current_user->id
						);
						$result = $this->ci->sms_m->insert_sms_queue($input);
					}else{
						$result = FALSE;
					}
					//$this->ci->users_m->update_user($user->id,array('join_code'=>$join_code));
				}else{
					$result = TRUE;
				}
				if(valid_email($user->email)){
					if($send_invitation_email){
						$build_message = array(
							'APPLICATION_NAME' => $this->application_settings->application_name,
							'GROUP_NAME' => $group->name,
							'FIRST_NAME' => $user->first_name,
							'LAST_NAME' => $user->last_name,
							'SENDER' => $current_user->first_name.' '.$current_user->last_name,
							'LINK' => site_url(),
							'FIRST_TIME_PASSWORD' => $first_time_password,
							'EMAIL_ADDRESS' => $user->email,
							'ROLE' => isset($group_roles[$member->group_role_id])?$group_roles[$member->group_role_id]:'Member',
							'YEAR' => date('Y'),
						);

						if(!$the_user->last_login){//invite member first time
							$message = $this->ci->emails_m->build_email_message('invite-member-first-time',$build_message);
						} else {
							$message = $this->ci->emails_m->build_email_message('invite-existing-member',$build_message);
						}

						// $message = $this->ci->emails_m->build_email_message('invite-member-to-websacco',array(
						// 		'FIRST_NAME' => $user->first_name,
						// 		'LAST_NAME' => $user->last_name,
						// 		'GROUP_NAME' => $group->name,
						// 		'LINK' => $this->application_settings->protocol.''.$this->application_settings->url.'/login',
						// 		'YEAR'=>date("Y"),
						// 		'CURRENT_USER_FIRST_NAME' => $current_user->first_name,
						// 		'CURRENT_USER_LAST_NAME' => $current_user->last_name,
						// 		'APPLICATION_NAME'	=>	$this->application_settings->application_name,
						// 		'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
	     //                        'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
	     //                        'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
						// ),'',$user->language_id);
						
						$input = array(
							'email_to'=>$user->email,
							'subject'=>$current_user->first_name.' '.$current_user->last_name.' has invited you to join '.$group->name.' on '.$this->application_settings->application_name,
							'email_from'=>'',
							'group_id'=>$group->id,
							'member_id'=>$member->id,
							'user_id'=>$user->id,
							'message'=>$message,
							'created_on'=>time(),
							'created_by'=>$current_user->id
						);
						$result = $this->ci->emails_m->insert_email_queue($input);

						if($result){
					    	//everything went well do nothing	
					    	//$this->ci->users_m->update_user($user->id,array('email_join_code'=>$join_code));
						}else{
							$this->ci->session->set_flashdata('error','Could not insert email into email queue ');
							return FALSE;
						}
					}else{
						$result = TRUE;
					}
				}
				if($result){
					$notification_subject = $current_user->first_name.' '.$current_user->last_name.' invited you to join '.$group->name;
					$input = array(
						'from_member_id' => $current_member_id,
						'from_user_id' => $current_user->id,
						// 'to_member_id' => $member->id,
						'to_user_id' => $user->id,
						'group_id' => $group->id,
						'subject' => $notification_subject,
						'message' => '',
						'is_read' => 0,
						'created_by' => $current_user->id,
						'created_on' => time(),
					);
					$result = $this->ci->notifications_m->insert($input);
					if($result){
						return TRUE;
					}else{
						$this->ci->session->set_flashdata('error','Could not insert notification');
						return FALSE;
					}
				}else{
					$this->ci->session->set_flashdata('error','Could not insert sms into sms queue');
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Phone value is empty');
			return FALSE;
		}
	}

	public function send_contribution_invoice_notification_to_member($group_id = 0,$member = array(),$send_sms_notification = FALSE,$send_email_notification = FALSE,$sms_template = '',$sms_data = array(),$email_data = array(),$amount_payable = 0){
		$group = $this->ci->groups_m->get($group_id);
		if($group&&$group_id&&$member&&$sms_template&&$sms_data&&$email_data&&$amount_payable){
			$result = TRUE;
			if($send_sms_notification){
	            if(valid_phone($member->phone)){

	                $sms_message = $this->ci->sms_m->build_sms_message($sms_template,$sms_data,TRUE);

	                $data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $sms_message,
	                    'group_id' => $group_id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                );
	                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                    echo "Reminder SMS Queued.<br/>";
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        if($send_email_notification){
	        	//die("Am in");
	            if(valid_email($member->email)){
	                $email_message = $this->ci->emails_m->build_email_message('contribution-invoice',$email_data);
	                $data = array(
	                    'email_to' => $member->email,
	                    'subject' => 'Contribution reminder from '.$group->name,
	                    'message' => $email_message,
	                    'email_from' => '',
	                    'group_id' => $group_id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time()
	                );
	                if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	                	return TRUE;
	                    // echo "Reminder Email Queued.<br/>";
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        return $result;
	    }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function send_contribution_invoice_notification_to_member');
	    	
	    	return FALSE;
	    }
	}

	// public function send_contribution_payment_notification_to_member($group = array(),$member = array(),$send_sms_notification = FALSE,$send_email_notification = FALSE,$sms_data = array(),$email_data = array(),$amount = 0){
		
	// 	if($group&&$member&&$sms_data&&$email_data&&$amount){
	// 		$result = TRUE;
	// 		if($send_sms_notification){
	//             if(valid_phone($member->phone)){
	//                 $sms_message = $this->ci->sms_m->build_sms_message('contribution-payment',$sms_data);
	//                 $data = array(
	//                     'sms_to' => $member->phone,
	//                     'message' => $sms_message,
	//                     'group_id' => $group->id,
	//                     'member_id' => $member->id,
	//                     'user_id' => $member->user_id,
	//                     'created_on' => time(),
	//                 );
	//                 if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                    
	//                 }else{
	//                     $result = FALSE;
	//                 }
	//             }
	//         }
	//         if($send_email_notification){
	//             if(valid_email($member->email)){
	//                 $email_message = $this->ci->emails_m->build_email_message('contribution-payment',$email_data);
	//                 $data = array(
	//                     'email_to' => $member->email,
	//                     'subject' => 'Contribution payment from '.$group->name,
	//                     'message' => $email_message,
	//                     'email_from' => '',
	//                     'group_id' => $group->id,
	//                     'member_id' => $member->id,
	//                     'user_id' => $member->user_id,
	//                     'created_on' => time()
	//                 );
	//                 if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	                    
	//                 }else{
	//                     $result = FALSE;
	//                 }
	//             }
	//         }
	//         return $result;
	//     }else{
 //            $this->ci->session->set_flashdata('error','Parameters missing for the function send_contribution_payment_notification_to_member');
	    	
	//     	return FALSE;
	//     }
	// }

	public function send_fine_payment_notification_to_member($group = array(),$member = array(),$send_sms_notification = FALSE,$send_email_notification = FALSE,$sms_data = array(),$email_data = array(),$amount = 0){
		if($group&&$member&&$sms_data&&$email_data&&$amount){
			$result = TRUE;
			if($send_sms_notification){
	            if(valid_phone($member->phone)){
	                $sms_message = $this->ci->sms_m->build_sms_message('fine-payment',$sms_data,'',$member->language_id);
	                $data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $sms_message,
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                );
	                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        if($send_email_notification){
	            if(valid_email($member->email)){
	                $email_message = $this->ci->emails_m->build_email_message('fine-payment',$email_data,'',$member->language_id);
	                $data = array(
	                    'email_to' => $member->email,
	                    'subject' => 'Fine payment receipt from '.$group->name,
	                    'message' => $email_message,
	                    'email_from' => '',
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time()
	                );
	                if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        return $result;
	    }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function send_contribution_payment_notification_to_member');
	    	
	    	return FALSE;
	    }
	}

	public function send_contribution_fine_invoice_notification_to_member($group = array(),$member = array(),$send_sms_notification = FALSE,$send_email_notification = FALSE,$sms_data = array(),$email_data = array(),$amount_payable=0){
		if($group&&$member&&$sms_data&&$email_data&&$amount_payable){
			$result = TRUE;
			if($send_sms_notification){
	            if(valid_phone($member->phone)){
	                $sms_message = $this->ci->sms_m->build_sms_message('contribution-fine-invoice',$sms_data,'',$member->language_id);
	                $data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $sms_message,
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                );
	                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        if($send_email_notification){
	            if(valid_email($member->email)){
	                $email_message = $this->ci->emails_m->build_email_message('contribution-fine-invoice',$email_data,'',$member->language_id);
	                $data = array(
	                    'email_to' => $member->email,
	                    'subject' => 'Contribution fine invoice from '.$group->name,
	                    'message' => $email_message,
	                    'email_from' => '',
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time()
	                );
	                if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        return $result;
	    }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function send_contribution_invoice_notification_to_member');
	    	
	    	return FALSE;
	    }
	}

	public function send_fine_invoice_notification_to_member($group = array(),$member = array(),$send_sms_notification = FALSE,$send_email_notification = FALSE,$sms_data = array(),$email_data = array(),$amount_payable=0){
		if($group&&$member&&$sms_data&&$email_data&&$amount_payable){
			$result = TRUE;
			if($send_sms_notification){
	            if(valid_phone($member->phone)){
	                $sms_message = $this->ci->sms_m->build_sms_message('fine-invoice',$sms_data,'',$member->language_id);
	                $data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $sms_message,
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                );
	                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        if($send_email_notification){
	            if(valid_email($member->email)){
	                $email_message = $this->ci->emails_m->build_email_message('fine-invoice',$email_data,'',$member->language_id);
	                $data = array(
	                    'email_to' => $member->email,
	                    'subject' => ' Fine Invoice from '.$group->name,
	                    'message' => $email_message,
	                    'email_from' => '',
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time()
	                );
	                if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        return $result;
	    }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function send_contribution_invoice_notification_to_member');
	    	
	    	return FALSE;
	    }
	}

	public function send_miscellaneous_invoice_notification_to_member($group = array(),$member = array(),$send_sms_notification = FALSE,$send_email_notification = FALSE,$sms_data = array(),$email_data = array(),$amount_payable=0){
		if($group&&$member&&$sms_data&&$email_data&&$amount_payable){
			$result = TRUE;
			if($send_sms_notification){
	            if(valid_phone($member->phone)){
	                $sms_message = $this->ci->sms_m->build_sms_message('miscellaneous-invoice',$sms_data,'',$member->language_id);
	                $data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $sms_message,
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                );
	                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        if($send_email_notification){
	            if(valid_email($member->email)){
	                $email_message = $this->ci->emails_m->build_email_message('miscellaneous-invoice',$email_data,'',$member->language_id);
	                $data = array(
	                    'email_to' => $member->email,
	                    'subject' => 'Miscellaneous invoice from '.$group->name,
	                    'message' => $email_message,
	                    'email_from' => '',
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time()
	                );
	                if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        return $result;
	    }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function send_contribution_invoice_notification_to_member');
	    	
	    	return FALSE;
	    }
	}

	public function send_miscellaneous_payment_notification_to_member($group = array(),$member = array(),$send_sms_notification = FALSE,$send_email_notification = FALSE,$sms_data = array(),$email_data = array(),$amount = 0){
		if($group&&$member&&$sms_data&&$email_data&&$amount){
			$result = TRUE;
			if($send_sms_notification){
	            if(valid_phone($member->phone)){
	                $sms_message = $this->ci->sms_m->build_sms_message('miscellaneous-payment',$sms_data,'',$member->language_id);
	                $data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $sms_message,
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                );
	                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        if($send_email_notification){
	            if(valid_email($member->email)){
	                $email_message = $this->ci->emails_m->build_email_message('miscellaneous-payment',$email_data,'',$member->language_id);
	                $data = array(
	                    'email_to' => $member->email,
	                    'subject' => 'Miscellaneous payment receipt from '.$group->name,
	                    'message' => $email_message,
	                    'email_from' => '',
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time()
	                );
	                if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	                    
	                }else{
	                    $result = FALSE;
	                }
	            }
	        }
	        return $result;
	    }else{
            $this->ci->session->set_flashdata('error','Parameters missing for the function send_miscellaneous_payment_notification_to_member');
	    	
	    	return FALSE;
	    }
	}

	public function send_loan_guarantorship_request_sms($guarantors = array()){
		if(empty($guarantors)){
			$this->ci->session->set_flashdata('error','No SMS parameters passed');
			return FALSE;
		}else{
			foreach ($guarantors as $guarantor) {
				$guarantor = (object)$guarantor;
				if(isset($guarantor->applicant_name)
					&&isset($guarantor->phone)
					&&isset($guarantor->requested_amount)
					&&isset($guarantor->group_currency)
					&&isset($guarantor->group_id)
					&&isset($guarantor->user_id)
					&&isset($guarantor->loan_type_name)
				){
					if($guarantor->applicant_name
						&&$guarantor->group_currency
						&&$guarantor->group_id
						&&$guarantor->user_id
						&&$guarantor->phone
						&&$guarantor->requested_amount
						&&$guarantor->loan_type_name
					){
						if(valid_phone($guarantor->phone)){
							$sms_data = array(
								'APPLICANT' => $guarantor->applicant_name,
								'CURRENCY' => $guarantor->group_currency,
								'REQUESTED_AMOUNT'=>$guarantor->requested_amount,
								'LOAN_TYPE'=>$guarantor->loan_type_name,
								'LINK'	=>	$this->application_settings->protocol.''.$this->application_settings->url,
							);
							$language_id = isset($signatory->language_id)?$signatory->language_id:'';
							$message = $this->ci->sms_m->build_sms_message('loan-guarantorship-request',$sms_data,'',$language_id);
							$input = array(
								'sms_to'=>$guarantor->phone,
								'group_id'=>$guarantor->group_id,
								'user_id'=>$guarantor->user_id,
								'message'=>$message,
								'created_on'=>time(),
								'created_by'=>$this->ci->user->id
							);
							$result = $this->ci->sms_m->insert_sms_queue($input);
						}else{
						}
					}else{
						continue; //only guarantor in current iteration will be skipped
					}
				}else{
					continue; //only guarantor in current iteration will be skipped
				}
			}
		}
	}



	public function send_group_signatories_loan_application_sms($signatories = array()){
		if(empty($signatories)){
			$this->ci->session->set_flashdata('error','No SMS parameters passed');
			return FALSE;
		}else{
			foreach ($signatories as $signatory) {
				$signatory = (object)$signatory;
				if(isset($signatory->applicant_name)
					&&isset($signatory->phone)
					&&isset($signatory->group_name)
					&&isset($signatory->group_id)
					&&isset($signatory->user_id)
					&&isset($signatory->loan_type_name)
				){
					if($signatory->applicant_name
						&&$signatory->group_id
						&&$signatory->user_id
						&&$signatory->phone
						&&$signatory->group_name
						&&$signatory->loan_type_name
					){
						if(valid_phone($signatory->phone)){
							$sms_data = array(
								'APPLICANT' => $signatory->applicant_name,
								'GROUP_NAME'=>$signatory->group_name,
								'LOAN_TYPE'=>$signatory->loan_type_name,
								'LINK'	=>	$this->application_settings->protocol.''.$this->application_settings->url,
							);
							$language_id = isset($signatory->language_id)?$signatory->language_id:'';
							$message = $this->ci->sms_m->build_sms_message('group-signatory-loan-application-notification',$sms_data,'',$language_id);
							$input = array(
								'sms_to'=>$signatory->phone,
								'group_id'=>$signatory->group_id,
								'user_id'=>$signatory->user_id,
								'message'=>$message,
								'created_on'=>time(),
								'created_by'=>$this->ci->user->id
							);
							$result = $this->ci->sms_m->insert_sms_queue($input);
						}else{
						}
					}else{
						continue; //only guarantor in current iteration will be skipped
					}
				}else{
					continue; //only guarantor in current iteration will be skipped
				}
			}
		}
	}





	/****Group General emails****/

	function create_and_queue_sms($send_to_member=array(),$message='',$message_from=array(),$group_id=0,$group_name=''){

		if(is_array($send_to_member)&&$message&&is_object($message_from)&&$group_id)
		{
			$success = 0;
			$failed = 0;
			$sms_data = array(
				'GROUP_NAME'=>'['.$group_name.']',
				'MESSAGE' => $message
			);
			foreach ($send_to_member as $member) 
			{
				if(valid_phone($member->phone)){
					$message = $this->ci->sms_m->build_sms_message('group-default-template',$sms_data,'',$member->language_id);
					$data = array(
	                    'sms_to' => valid_phone($member->phone),
	                    'message' => $message,
	                    'group_id' => $group_id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                    'created_by'=> $message_from->id
	                );

	                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                    ++$success;
	                }else{
	                    ++$failed;
	                }
				}else{
					 ++$failed;
				}	
			}
			if($success){
				$this->ci->session->set_flashdata('success',$success.' SMSes Successfully Queued. They will be sent shortly.');
			}if($failed){
				$this->ci->session->set_flashdata('error','unable to queue '.$failed.' SMSes and thus they will not be sent');
			}
			
			return TRUE;
		}
		else{
			$this->ci->session->set_flashdata('error','Sorry, some parameters are missing or not in the correct format');
			return FALSE;
		}
	}

	function create_and_queue_email($send_member=array(),$message='',$sending_user = array(),$group_id=0,$subject='',$attachments=array(),$ccs=array(),$bccs=array(),$embeded_attachments=array(),$send=1){
		if(is_array($send_member)&&$message&&is_object($sending_user)&&$group_id&&$subject){
			if(!empty($send_member)){
				$success = 0;
				$fails = 0;
				$chamasoft_team = 'ongidigeofrey@gmail.com';
				$group = $this->ci->groups_m->get_group_owner($group_id);
				foreach ($send_member as $member) {
					if($member=='chamasoft-team'){
						$email = $chamasoft_team;
						$cc = '';
						$bcc = '';
						foreach ($ccs as $value) {
							if($cc){
								if($value=='chamasoft-team')
								{
									$cc = $cc.','.$chamasoft_team;
								}elseif($value=='group-email'){
									$cc = $cc.','.$group->email;
								}else{
									$cc = $cc.','.$value->email;
								}
							}else{
								if($value=='chamasoft-team')
								{
									$cc = $chamasoft_team;
								}elseif($value=='group-email'){
									$cc = $cc.','.$group->email;
								}else{
									$cc = $value->email;
								}
							}
						}
						foreach ($bccs as $value) {
							if($bcc){
								if($value=='chamasoft-team')
								{
									$bcc = $bcc.','.$chamasoft_team;
								}elseif($value=='group-email'){
									$bcc = $bcc.','.$group->email;
								}else{
									$bcc = $bcc.','.$value->email;
								}
							}else{
								if($value=='chamasoft-team')
								{
									$bcc = $chamasoft_team;
								}elseif($value=='group-email'){
									$bcc = $bcc.','.$group->email;
								}else{
									$bcc = $value->email;
								}
							}
						}

						if($send){
							$is_draft = 0;
						}else{
							$is_draft = 1;
						}

						$email_data = array(
		                    'MAIL_TO' => $this->application_settings->application_name.' Team',
		                    'APPLICATION_NAME' => $this->application_settings->application_name,
		                    'MAIL_FROM' => $sending_user->first_name,
		                    'SUBJECT' => $subject,
		                    'EMAIL_BODY' => $message,
		                    'NAME' => 'System Email',
		                    'GROUP_NAME' => $group->name,
		                    'TIME' => timestamp_to_receipt(time()),
		                    'YEAR' => date('Y',time()),
		                    'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
							'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
							'LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 

		                );

						$email_body = $this->ci->emails_m->build_email_message('general-mailing-template',$email_data);
						
						$id = $this->ci->emails_m->insert_email_queue(array(
								'email_to'	=>	$email,
								'subject'	=>	$subject,
								'message' 	=>	$email_body,
								'email_from'=>	$sending_user->email,
								'sending_email' => '',
								'group_id'	=>	$group_id,
								'member_id'	=>	1,
								'user_id'	=>	1,
								'attachments'=> serialize($attachments),
								'cc'		=>	$cc,
								'bcc' 		=>	$bcc,
								'embeded_attachments' => serialize($embeded_attachments),
								'is_draft'	=>	$is_draft?:0,
								'created_on'	=>	time(),
								'created_by'	=>	$sending_user->id
							));
						if($id){
							++$success;
						}
						else{
							++$fails;
						}
					}elseif($member=='group-email'){
						$cc = '';
						$bcc = '';
						foreach ($ccs as $value) {
							if($cc){
								if($value=='chamasoft-team')
								{
									$cc = $cc.','.$chamasoft_team;
								}elseif($value=='group-email'){
									$cc = $cc.','.$group->email;
								}else{
									$cc = $cc.','.$value->email;
								}
							}else{
								if($value=='chamasoft-team')
								{
									$cc = $chamasoft_team;
								}elseif($value=='group-email'){
									$cc = $cc.','.$group->email;
								}else{
									$cc = $value->email;
								}
							}
						}
						foreach ($bccs as $value) {
							if($bcc){
								if($value=='chamasoft-team')
								{
									$bcc = $bcc.','.$chamasoft_team;
								}elseif($value=='group-email'){
									$bcc = $bcc.','.$group->email;
								}else{
									$bcc = $bcc.','.$value->email;
								}
							}else{
								if($value=='chamasoft-team')
								{
									$bcc = $chamasoft_team;
								}elseif($value=='group-email'){
									$bcc = $bcc.','.$group->email;
								}else{
									$bcc = $value->email;
								}
							}
						}

						if($send){
							$is_draft = 0;
						}else{
							$is_draft = 1;
						}

						$email_data = array(
		                    'MAIL_TO' => 'Group email',
		                    'MAIL_FROM' => $sending_user->first_name,
		                    'SUBJECT' => $subject,
		                    'EMAIL_BODY' => $message,
		                    'GROUP_NAME' => $group->name,
		                    'NAME' => $group->name,
		                    'TIME' => timestamp_to_receipt(time()),
		                    'YEAR' => date('Y',time()),
		                    'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
							'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
							'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 

		                );

						$email_body = $this->ci->emails_m->build_email_message('general-mailing-template',$email_data);
						$id = $this->ci->emails_m->insert_email_queue(array(
							'email_to'	=>	$group->group_email,
							'subject'	=>	$subject,
							'message' 	=>	$email_body,
							'email_from'=>	$sending_user->email,
							'sending_email' => '',
							'group_id'	=>	$group_id,
							'member_id'	=>	1,
							'user_id'	=>	1,
							'attachments'=> serialize($attachments),
							'cc'		=>	$cc,
							'bcc' 		=>	$bcc,
							'embeded_attachments' => serialize($embeded_attachments),
							'is_draft'	=>	$is_draft?:0,
							'created_on'	=>	time(),
							'created_by'	=>	$sending_user->id
						));
						if($id){
							++$success;
						}
						else{
							++$fails;
						}
					}else if(valid_email($member->email)){
						$cc = '';
						$bcc = '';
						foreach ($ccs as $value) {
							if($cc){
								if($value=='chamasoft-team')
								{
									$cc = $cc.','.$chamasoft_team;
								}elseif($value=='group-email'){
									$cc = $cc.','.$group->email;
								}else{
									$cc = $cc.','.$value->email;
								}
							}else{
								if($value=='chamasoft-team')
								{
									$cc = $chamasoft_team;
								}elseif($value=='group-email'){
									$cc = $cc.','.$group->email;
								}else{
									$cc = $value->email;
								}
							}
						}
						foreach ($bccs as $value) {
							if($bcc){
								if($value=='chamasoft-team')
								{
									$bcc = $bcc.','.$chamasoft_team;
								}elseif($value=='group-email'){
									$bcc = $bcc.','.$group->email;
								}else{
									$bcc = $bcc.','.$value->email;
								}
							}else{
								if($value=='chamasoft-team')
								{
									$bcc = $chamasoft_team;
								}elseif($value=='group-email'){
									$bcc = $bcc.','.$group->email;
								}else{
									$bcc = $value->email;
								}
							}
						}

						if($send){
							$is_draft = 0;
						}else{
							$is_draft = 1;
						}

						$email_data = array(
		                    'MAIL_TO' => $member->first_name,
		                    'MAIL_FROM' => $sending_user->first_name,
		                    'SUBJECT' => $subject,
		                    'EMAIL_BODY' => $message,
		                    'GROUP_NAME' => $group->name,
		                    'NAME' => $member->first_name.' '.$member->last_name,
		                    'TIME' => timestamp_to_receipt(time()),
		                    'YEAR' => date('Y',time()),
		                    'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
							'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
							'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 

		                );

						$email_body = $this->ci->emails_m->build_email_message('general-mailing-template',$email_data);
						$id = $this->ci->emails_m->insert_email_queue(array(
								'email_to'	=>	$member->email,
								'subject'	=>	$subject,
								'message' 	=>	$email_body,
								'email_from'=>	$sending_user->email,
								'sending_email' => '',
								'group_id'	=>	$group_id,
								'member_id'	=>	$member->id,
								'user_id'	=>	$member->user_id,
								'attachments'=> serialize($attachments),
								'cc'		=>	$cc,
								'bcc' 		=>	$bcc,
								'embeded_attachments' => serialize($embeded_attachments),
								'is_draft'	=>	$is_draft?:0,
								'created_on'	=>	time(),
								'created_by'	=>	$sending_user->id
							));
						if($id){
							++$success;
						}
						else{
							++$fails;
						}

					}else{
						++$fails;
					}
					if($success){
						if($is_draft){
							$this->ci->session->set_flashdata('success',$success.' Email(s) Successfully saved to draft.');
						}else{
							$this->ci->session->set_flashdata('success',$success.' Email(s) Successfully Queued. Will be sent shortly.');
						}
					}if($fails){
						$this->ci->session->set_flashdata('error','unable to queue '.$fails.' Email(s) and thus will not be sent');
					}
				}
			}else{
				$this->ci->session->set_flashdata('error','There are no members with valid email addresses');
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Some essential parameters are not available');
			return FALSE;
		}
	}

	/*****Loan notifications*******/

	function notify_loan_repayment_email($member=array(),$deposited_amount=0,$deposit_date=0,$deposit_method='',$group=array(),$loan_details=array(),$currency='KES',$sending_user=array()){
		if($member&&$deposited_amount&&$deposit_date&&$deposit_method&&$group&&$loan_details){
			$message = '';
			if(valid_email($member->email)){
				$loan_details = (object)$loan_details;
				$subject = 'Loan repayment to '.$group->name;
				$email_data = array(
					'APPLICATION_NAME'=> $this->application_settings->application_name,
					'PRIMARY_THEME_COLOR'=> $this->application_settings->primary_color,
					'TERTIARY_THEME_COLOR'=> $this->application_settings->tertiary_color,
					'GROUP_NAME'=> $group->name,
					'DATE' => date('d').date('S'),
					'MONTH' => date('M'),
					'YEAR' => date('Y'),
	                'FIRST_NAME' => $member->first_name,
	                'LAST_NAME' => $member->last_name,
	                'GROUP_CURRENCY' => $currency,
	                'LINK' => site_url('member/loans/loan_statement/'.$loan_details->id),
	                'AMOUNT'=> number_to_currency($deposited_amount),
	                'RECEIPT_DATE'=> timestamp_to_receipt($deposit_date),
	                'LOAN_BALANCE'=>number_to_currency($loan_details->loan_balance),
	                'LUMP_SUM'=>number_to_currency($loan_details->total_fines + $loan_details->total_installment_payable - $loan_details->total_paid),
	                'LOAN_AMOUNT'=>number_to_currency($loan_details->loan_borrowed),
	                'LOAN_PAYABLE' => number_to_currency($loan_details->total_fines + $loan_details->total_installment_payable),
	                'LOAN_PAID' => number_to_currency($loan_details->total_paid),
	            );

				$email_body = $this->ci->emails_m->build_email_message('loan-repayment-email-template',$email_data);
				
				$id = $this->ci->emails_m->insert_email_queue(array(
						'email_to'	=>	$member->email,
						'subject'	=>	$subject,
						'message' 	=>	$email_body,
						'email_from'=>	isset($sending_user->email)?$sending_user->email:'',
						'sending_email' => '',
						'group_id'	=>	$group->id,
						'member_id'	=>	$member->id,
						'user_id'	=>	$member->user_id,
						'created_on'	=>	time(),
						'created_by'	=>	0
					));

				$this->ci->session->set_flashdata('success','Email Successfully sent.');
               return TRUE;

			}
			else{
				$this->ci->session->set_flashdata('error','Sorry,email not sent since '.$member->first_name.' does not have a valid email');
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Sorry, email could not be sent because some parameters are missing');
			return FALSE;
		}
	}

	function notify_loan_repayment_sms($member=array(),$deposited_amount=0,$deposit_date=0,$deposit_method='',$group_id=0,$loan_balance=0,$created_by=array())
	{
		if($member&&$group_id&&$deposited_amount&&$deposit_date)
		{
			$group_currency = $this->ci->emails_m->get_this_group_currency($group_id);

			if(valid_phone($member->phone)){
				$message = 'Dear '.$member->first_name.', your loan repayment of '.$group_currency.' '.number_to_currency($deposited_amount).' was Successfully received and recorded on '.timestamp_to_date($deposit_date).' by '.$created_by->first_name.'. Your loan balance is '.$group_currency.' '.number_to_currency($loan_balance);
				$data = array(
                    'sms_to' => $member->phone,
                    'message' => $message,
                    'group_id' => $group_id,
                    'member_id' => $member->id,
                    'user_id' => $member->user_id,
                    'created_on' => time(),
                );

                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
                    $this->ci->session->set_flashdata('success','SMS Successfully sent.');
                    return TRUE;
                }else{
                    $this->ci->session->set_flashdata('error','SMS not sent.');
					return FALSE;
                }
			}else{
				//$this->ci->session->set_flashdata('error','Sorry, sms not sent since '.$member->first_name.' phone number registered is not valid');
				return FALSE;
			}
			
		}
		else{
			$this->ci->session->set_flashdata('error','Sorry, sms messaging some parameters are missing or not in the correct format');
			return FALSE;
		}
	}

	function send_sms_loan_installments_invoices_queued($invoice=array(),$group=array(),$member=array(),$currency='KES',$is_a_debtor=FALSE){
		if($invoice&&$group&&$member&&$currency){
			if(valid_phone($member->phone)){
				$message = 'Dear '.$member->name.', you have been invoiced   '.$currency.' '.number_to_currency($invoice->amount_payable).' for your loan installment payment due '.timestamp_to_receipt($invoice->due_date).'. Your loan balance is '.$currency.' '.number_to_currency($invoice->loan_balance).'. Kindly make timely payments -'.$group->name;
				if($is_a_debtor){
					$data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $message,
	                    'group_id' => $group->id,
	                    'debtor_id' => $member->id,
	                    'created_on' => time(),
	                    'created_by'=> 1
	                );
				}else{
					$message = 'Dear '.$member->first_name.', you have been invoiced   '.$currency.' '.number_to_currency($invoice->amount_payable).' for your loan installment payment due '.timestamp_to_receipt($invoice->due_date).'. Your loan balance is '.$currency.' '.number_to_currency($invoice->loan_balance).'. Kindly make timely payments -'.$group->name;
					$data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $message,
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                    'created_by'=> 1
	                );
				}
                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
                    return $sms_id;
                }else{
					return FALSE;
                }
			}else{
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}

	function send_email_loan_installments_invoices_queued($invoice=array(),$group=array(),$member=array(),$currency='KES',$loan_details=array(),$is_a_debtor=FALSE){
		if($invoice&&$group&&$member&&$currency&&$loan_details){
			if(valid_email($member->email)){
				$subject = 'Loan installment invoice from '.$group->name;
				$loan_details = (object)$loan_details;
				if($is_a_debtor){
					$email_data = array(
                        'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',               
						'APPLICATION_NAME'=> $this->application_settings->application_name,
						'PRIMARY_THEME_COLOR'=> $this->application_settings->primary_color,
						'TERTIARY_THEME_COLOR'=> $this->application_settings->tertiary_color,
						'LINK'=> site_url('member/loans/loan_statement/'.$loan_details->id),
						'GROUP_NAME'=> $group->name,
						'DATE' => date('d'),
						'MONTH' => date('M'),
		                'FIRST_NAME' => $member->name,
		                'LAST_NAME' => '',
		                'GROUP_CURRENCY' => $currency,
		                'AMOUNT'=> number_to_currency($invoice->amount_payable),
		                'DUE_DATE' => timestamp_to_receipt($invoice->due_date),
		                'LOAN_BALANCE'=>number_to_currency($invoice->loan_balance),
		                'LUMP_SUM'=>number_to_currency($invoice->lump_sum_remaining),
		                'LOAN_AMOUNT'=>number_to_currency($loan_details->loan_borrowed),
		                'LOAN_PAYABLE' => number_to_currency($loan_details->total_fines + $loan_details->total_installment_payable),
		                'LOAN_PAID' => number_to_currency($loan_details->total_paid),
		            );

					$email_body = $this->ci->emails_m->build_email_message('loan-installment-invoice',$email_data);	
					$email_id = $this->ci->emails_m->insert_email_queue(array(
						'email_to'	=>	$member->email,
						'subject'	=>	$subject,
						'message' 	=>	$email_body,
						'email_from'=>	$group->email,
						'sending_email' => '',
						'group_id'	=>	$group->id,
						'donor_id'	=>	$member->id,
						'created_on'	=>	time(),
						'created_by'	=>	1
					));
				}else{
					$email_data = array(
						'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',               
						'APPLICATION_NAME'=> $this->application_settings->application_name,
						'PRIMARY_THEME_COLOR'=> $this->application_settings->primary_color,
						'TERTIARY_THEME_COLOR'=> $this->application_settings->tertiary_color,
						'LINK'=> site_url('member/loans/loan_statement/'.$loan_details->id),
						'GROUP_NAME'=> $group->name,
						'DATE' => date('d'),
						'MONTH' => date('M'),
		                'FIRST_NAME' => $member->first_name,
		                'LAST_NAME' => $member->last_name,
		                'GROUP_CURRENCY' => $currency,
		                'AMOUNT'=> number_to_currency($invoice->amount_payable),
		                'DUE_DATE' => timestamp_to_receipt($invoice->due_date),
		                'LOAN_BALANCE'=>number_to_currency($invoice->loan_balance),
		                'LUMP_SUM'=>number_to_currency($invoice->lump_sum_remaining),
		                'LOAN_AMOUNT'=>number_to_currency($loan_details->loan_borrowed),
		                'LOAN_PAYABLE' => number_to_currency($loan_details->total_fines + $loan_details->total_installment_payable),
		                'LOAN_PAID' => number_to_currency($loan_details->total_paid),
		            );

					$email_body = $this->ci->emails_m->build_email_message('loan-installment-invoice',$email_data);
					$email_id = $this->ci->emails_m->insert_email_queue(array(
						'email_to'	=>	$member->email,
						'subject'	=>	$subject,
						'message' 	=>	$email_body,
						'email_from'=>	$group->email,
						'sending_email' => '',
						'group_id'	=>	$group->id,
						'member_id'	=>	$member->id,
						'user_id'	=>	$member->user_id,
						'created_on'	=>	time(),
						'created_by'	=>	1
					));
				}
               return $email_id;

			}
			else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function send_sms_late_loan_installment_payment_invoice($fine_invoice=array(),$group=array(),$member=array(),$currency='KES',$is_a_debtor=FALSE){
		if($fine_invoice&&$group&&$member&&$currency){
			if(valid_phone($member->phone)){
				if($is_a_debtor){
					$message = 'Dear '.$member->name.', you have been fined '.$currency.' '.number_to_currency($fine_invoice->amount_payable).' for your late loan installment payment. Kindly make your payments before '.timestamp_to_receipt($fine_invoice->due_date).'. Your loan balance is '.$currency.' '.number_to_currency($fine_invoice->loan_balance).'. Kindly make timely payments -'.$group->name;
					$data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $message,
	                    'group_id' => $group->id,
	                    'debtor_id' => $member->id,
	                    'created_on' => time(),
	                    'created_by'=> 1
	                );
				}else{
					$message = 'Dear '.$member->first_name.', you have been fined '.$currency.' '.number_to_currency($fine_invoice->amount_payable).' for your late loan installment payment. Kindly make your payments before '.timestamp_to_receipt($fine_invoice->due_date).'. Your loan balance is '.$currency.' '.number_to_currency($fine_invoice->loan_balance).'. Kindly make timely payments -'.$group->name;
					$data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $message,
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                    'created_by'=> 1
	                );
				}
                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
                    return $sms_id;
                }else{
					return FALSE;
                }
			}else{
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}

	function send_email_late_loan_installment_payment_invoice($fine_invoice=array(),$group=array(),$member=array(),$currency='KES',$loan_details=array(),$is_a_debtor=FALSE){
		if($fine_invoice&&$group&&$member&&$currency){
			if(valid_email($member->email)){
				$loan_details = (object)$loan_details;
				$subject = $group->name.' - Late loan installment payment fine';
				
				if($is_a_debtor){
					$email_data = array(
						'APPLICATION_NAME'=> $this->application_settings->application_name,
						'GROUP_NAME' => $group->name,
		                'FIRST_NAME' => $member->name,
		                'LAST_NAME' => '',
		                'AMOUNT'=> $fine_invoice->amount_payable,
		                'CURRENCY' => $currency,
		                'DATE' => date('d'),
		                'MONTH'	=>	date('M'),
		                'DUE_DATE' => timestamp_to_receipt($fine_invoice->due_date),
		                'LOAN_BALANCE'=>number_to_currency($loan_details->loan_balance),
		                'LUMP_SUM'=>number_to_currency($fine_invoice->lump_sum_remaining),
		                'LOAN_PAYABLE'=>number_to_currency($loan_details->total_installment_payable+$loan_details->total_fines),
		                'LOAN_PAID'=>number_to_currency($loan_details->total_paid),
		                'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                        'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                        'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',               
		            );
		            $email_body = $this->ci->emails_m->build_email_message('late-loan-installment-payment-fine',$email_data);
					$email_id = $this->ci->emails_m->insert_email_queue(array(
						'email_to'	=>	$member->email,
						'subject'	=>	$subject,
						'message' 	=>	$email_body,
						'email_from'=>	$group->email,
						'sending_email' => 'info@chamasoft.com',
						'group_id'	=>	$group->id,
						'debtor_id'	=>	$member->id,
						'created_on'	=>	time(),
						'created_by'	=>	1
					));
				}else{
					$currency_code = $this->ci->countries_m->get_currency_code($group->currency_id);
					$email_data = array(
						'LINK'=> site_url('member/loans/loan_statement/'.$loan_details->id),
						'APPLICATION_NAME'=> $this->application_settings->application_name,
						'GROUP_NAME' => $group->name,
						'GROUP_CURRENCY' => $currency_code?:"",
		                'FIRST_NAME' => $member->first_name,
		                'LAST_NAME' => $member->last_name,
		                'AMOUNT'=> $fine_invoice->amount_payable,
		                'CURRENCY' => $currency,
		                'DATE' => date('d'),
		                'MONTH'	=>	date('M'),
		                'DUE_DATE' => timestamp_to_receipt($fine_invoice->due_date),
		                'LOAN_BALANCE'=>number_to_currency($loan_details->loan_balance),
		                'LUMP_SUM'=>number_to_currency($fine_invoice->lump_sum_remaining),
		                'LOAN_PAYABLE'=>number_to_currency($loan_details->total_installment_payable+$loan_details->total_fines),
		                'LOAN_PAID'=>number_to_currency($loan_details->total_paid),
		                'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
	                    'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
	                    'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
		            );
		            $email_body = $this->ci->emails_m->build_email_message('late-loan-installment-payment-fine',$email_data);
					$email_id = $this->ci->emails_m->insert_email_queue(array(
						'email_to'	=>	$member->email,
						'subject'	=>	$subject,
						'message' 	=>	$email_body,
						'email_from'=>	$group->email,
						'sending_email' => 'info@chamasoft.com',
						'group_id'	=>	$group->id,
						'member_id'	=>	$member->id,
						'user_id'	=>	$member->user_id,
						'created_on'	=>	time(),
						'created_by'	=>	1
					));
				}
               return $email_id;

			}
			else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function send_sms_loan_oustanding_balance_invoice($fine_invoice=array(),$member=array(),$group=array(),$currency='KES',$is_a_debtor=FALSE){
		if($fine_invoice&&$group&&$member&&$currency){

			if(valid_phone($member->phone)){
				if($is_a_debtor){
					$message = 'Dear '.$member->name.', you have been fined '.$currency.' '.number_to_currency($fine_invoice->amount_payable).' for unpaid loan balance. Kindly make your payments before '.timestamp_to_receipt($fine_invoice->due_date).'. Your loan balance is '.$currency.' '.number_to_currency($fine_invoice->loan_balance).'. Kindly make timely payments -'.$group->name;
					$data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $message,
	                    'group_id' => $group->id,
	                    'debtor_id' => $member->id,
	                    'created_on' => time(),
	                    'created_by'=> 1
	                );
				}else{
					$message = 'Dear '.$member->first_name.', you have been fined '.$currency.' '.number_to_currency($fine_invoice->amount_payable).' for unpaid loan balance. Kindly make your payments before '.timestamp_to_receipt($fine_invoice->due_date).'. Your loan balance is '.$currency.' '.number_to_currency($fine_invoice->loan_balance).'. Kindly make timely payments -'.$group->name;
					$data = array(
	                    'sms_to' => $member->phone,
	                    'message' => $message,
	                    'group_id' => $group->id,
	                    'member_id' => $member->id,
	                    'user_id' => $member->user_id,
	                    'created_on' => time(),
	                    'created_by'=> 1
	                );
				}
				

                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
                    return $sms_id;
                }else{
					return FALSE;
                }
			}else{
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}

	function send_email_loan_oustanding_balance_invoice($fine_invoice=array(),$member=array(),$group=array(),$currency='KES',$loan_details=array(),$is_a_debtor=FALSE){
		if($fine_invoice&&$group&&$member&&$currency){
			if(valid_email($member->email)){
				$loan_details = (object)$loan_details;
				$subject = $group->name.' - Outstanding loan balance fine';
				
				
				if($is_a_debtor){
					$email_data = array(
						'APPLICATION_NAME'=> $this->application_settings->application_name,
		                'FIRST_NAME' => $member->name,
		                'LAST_NAME' => '',
		                'GROUP_NAME'=> $group->name,
		                'AMOUNT'=> number_to_currency($fine_invoice->amount_payable),
		                'LOAN_BALANCE'=>number_to_currency($loan_details->loan_balance),
		                'LUMP_SUM'=>number_to_currency($fine_invoice->lump_sum_remaining),
		                'DUE_DATE' => timestamp_to_receipt($fine_invoice->due_date),
		                'CURRENCY' => $currency,
		                'MONTH' => date('M'),
		                'DATE' => date('d'),
		                'LOAN_PAID' => number_to_currency($loan_details->total_paid),
		                'LOAN_PAYABLE' => number_to_currency($loan_details->total_installment_payable+$loan_details->total_fines),
		            );

					$email_body = $this->ci->emails_m->build_email_message('outstanding-loan-balance-fine-invoice',$email_data,'',$member->language_id);
					$email_id = $this->ci->emails_m->insert_email_queue(array(
						'email_to'	=>	$member->email,
						'subject'	=>	$subject,
						'message' 	=>	$email_body,
						'email_from'=>	$group->email,
						'sending_email' => 'info@chamasoft.com',
						'group_id'	=>	$group->id,
						'debtor_id'	=>	$member->id,
						'created_on'	=>	time(),
						'created_by'	=>	1
					));
				}else{
					$email_data = array(
						'APPLICATION_NAME'=> $this->application_settings->application_name,
		                'FIRST_NAME' => $member->first_name,
		                'LAST_NAME' => $member->last_name,
		                'GROUP_NAME'=> $group->name,
		                'AMOUNT'=> number_to_currency($fine_invoice->amount_payable),
		                'LOAN_BALANCE'=>number_to_currency($loan_details->loan_balance),
		                'LUMP_SUM'=>number_to_currency($fine_invoice->lump_sum_remaining),
		                'DUE_DATE' => timestamp_to_receipt($fine_invoice->due_date),
		                'CURRENCY' => $currency,
		                'MONTH' => date('M'),
		                'DATE' => date('d'),
		                'LOAN_PAID' => number_to_currency($loan_details->total_paid),
		                'LOAN_PAYABLE' => number_to_currency($loan_details->total_installment_payable+$loan_details->total_fines),
		            );

					$email_body = $this->ci->emails_m->build_email_message('outstanding-loan-balance-fine-invoice',$email_data,'',$member->language_id);
					$email_id = $this->ci->emails_m->insert_email_queue(array(
						'email_to'	=>	$member->email,
						'subject'	=>	$subject,
						'message' 	=>	$email_body,
						'email_from'=>	$group->email,
						'sending_email' => 'info@chamasoft.com',
						'group_id'	=>	$group->id,
						'member_id'	=>	$member->id,
						'user_id'	=>	$member->user_id,
						'created_on'	=>	time(),
						'created_by'	=>	1
					));
				}
				

               return $email_id;

			}
			else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	/*****member confirmation notifications****/

	function send_verification_confirmation_to_member($group = array(),$member = array(),$user = array(),$account_number = 0){
		if($group&&$member&&$user&&$account_number){
			if($user->phone&&$user->email){
				$message = $this->ci->sms_m->build_sms_message('chamasoft-verification',array(
					'FIRST_NAME'=>$user->first_name,
					'ACCOUNT_NUMBER'=>$account_number
					)
				);
				$input = array(
					'sms_to'=>$user->phone,
					'group_id'=>$group->id,
					'member_id'=>$member->id,
					'user_id'=>$user->id,
					'message'=>$message,
					'created_on'=>time(),
					'created_by'=>$user->id
				);
				$result = $this->ci->sms_m->insert_sms_queue($input);

				$message = $this->ci->emails_m->build_email_message('chamasoft-verification',array(
					'FIRST_NAME'=>$user->first_name,
					'LAST_NAME'=>$user->last_name,
					'ACCOUNT_NUMBER'=>$account_number,
					'GROUP_NAME'=>$group->name,
                    'DATE' => date('d'),
                    'MONTH' => date('M'),
				));
				$input = array(
					'email_to'=>$user->email,
					'subject'=>'Congratulations you have activated bank transaction alerts for '.$group->name.' on '.$this->application_settings->application_name,
					'email_from'=>'',
					'group_id'=>$group->id,
					'member_id'=>$member->id,
					'user_id'=>$user->id,
					'message'=>$message,
					'created_on'=>time(),
					'created_by'=>$user->id
				);
				$result = $this->ci->emails_m->insert_email_queue($input);

				if($result){
			    	//everything went well do nothing	
				}else{
					$this->ci->session->set_flashdata('error','Could not insert email into email queue ');
					return FALSE;
				}
			}else if($user->phone){
				$message = $this->ci->sms_m->build_sms_message('chamasoft-verification',array(
					'FIRST_NAME'=>$user->first_name,
					'ACCOUNT_NUMBER'=>$account_number)
				);
				
				$input = array(
					'sms_to'=>$user->phone,
					'group_id'=>$group->id,
					'member_id'=>$member->id,
					'user_id'=>$user->id,
					'message'=>$message,
					'created_on'=>time(),
					'created_by'=>$user->id
				);
				$result = $this->ci->sms_m->insert_sms_queue($input);
			}else if(valid_email($user->email)){
				$message = $this->ci->emails_m->build_email_message('chamasoft-verification',array(
					'FIRST_NAME'=>$user->first_name,
					'LAST_NAME'=>$user->last_name,
					'ACCOUNT_NUMBER'=>$account_number,
                    'DATE' => date('d'),
                    'MONTH' => date('M'),
				));
				$input = array(
					'email_to'=>$user->email,
					'subject'=>'Congratulations you have activated bank transaction alerts for '.$group->name.' on '.$this->application_settings->application_name,
					'email_from'=>'',
					'group_id'=>$group->id,
					'member_id'=>$member->id,
					'user_id'=>$user->id,
					'message'=>$message,
					'created_on'=>time(),
					'created_by'=>$user->id
				);
				$result = $this->ci->emails_m->insert_email_queue($input);

				if($result){
			    	//everything went well do nothing	
				}else{
					$this->ci->session->set_flashdata('error','Could not insert email into email queue ');
					return FALSE;
				}
			}
			return $result;
		}else{
			return FALSE;
		}
	}


	/******Billing notifications*****/

	function sms_billing_payment_received($group_id=0,$account_arrears=0,$amount=0,$receipt_date=0,$group_owner=array(),$tax=0){
		if($group_id&&$amount&&$receipt_date&&$group_owner){
			if(valid_phone($group_owner->phone)){
				if($account_arrears>0){
                    $balance = ' Group outstanding balance is '.$this->default_country->currency_code.'. '.number_to_currency($account_arrears);
                }
                else if($account_arrears==0){
                    $balance=' You have fully cleared your balance';
                }
                else if($account_arrears<0){
                    $account_arrears = abs($account_arrears);
                    $balance =' Group bill overpayments is '.$this->default_country->currency_code.'. '.number_to_currency($account_arrears);
                }
                else{
                   $balance = ' Group outstanding balance is '.$this->default_country->currency_code.'. '.number_to_currency(0);
                }
				$message = 'Dear '.$group_owner->first_name.', bill payment of '.$this->default_country->currency_code.' '.number_to_currency($amount).' was successfully received.'.$balance.' - '.$group_owner->name.'. Thank you';
				$data = array(
                    'sms_to' => $group_owner->phone,
                    'system_sms' => 1,
                    'message' => $message,
                    'group_id' => $group_id,
                    'member_id' => $group_owner->member_id,
                    'user_id' => $group_owner->user_id,
                    'created_on' => time(),
                    'created_by'=> 0
                );

                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
                    return TRUE;
                }else{
					return FALSE;
                }
			}else{
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','SMS - Some essential parameters are missing');
			return FALSE;
		}
	}

	function email_billing_payment_received($group_id=0,$account_arrears=0,$amount=0,$receipt_date=0,$group_owner=array(),$tax=0,$receipt = array(),$billing_package = array(),$payment_methods=array()){
		if($group_id&&$amount&&$receipt_date&&$group_owner){
			if(valid_email($group_owner->email)){
				$subject = '['.$this->application_settings->application_name.'] subscription receipt for '.$group_owner->name;
				//info: only show sub account arrears if account arrears is greater than 0
				if($account_arrears>0){
                    $balance = "Group outstanding balance as at ".date('d').date('S').' '.date('M').' '.date('Y').' is '.$this->default_country->currency_code.'. '.number_to_currency($account_arrears).'.';
                }
                else if($account_arrears==0){
                    $balance= "You have fully cleared your balance.";
                }
                else if($account_arrears<0){
                    $account_arrears = abs($account_arrears);
                    $balance ='Group bill overpayment is '.$this->default_country->currency_code.'. '.number_to_currency($account_arrears).'.';
                }
                else{
                   $balance = "You have fully cleared your balance.";
                }
				$email_data = array(
					'APPLICATION_NAME'=> $this->application_settings->application_name,
	                'GROUP_NAME' => $group_owner->name,
	                'TIME' => timestamp_to_date(time()),
	                'FULL_NAME' => $group_owner->first_name.' '.$group_owner->last_name,
	                'AMOUNT_WORDS' => number_to_words($amount).' only',
	                'AMOUNT' => number_to_currency($amount-$tax),
	                'TAX' => number_to_currency($tax),
	                'TOTAL_AMOUNT' => number_to_currency($amount),
	                'BALANCE' => $balance,
	                'RECEIPT_DATE' => timestamp_to_receipt($receipt_date),
	                'PAYMENT_LINK'=> $this->application_settings->protocol.$group_owner->slug.'.'.$this->application_settings->url.'/group/billing/billing_information',
	                'DATE' => date('d').date('S'),
	                'YEAR' => date('Y'),
	                'MONTH' => date('M'),
					'CURRENCY' => 'KES',
	                'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
					'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
					'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
	            );

	            $attachments = serialize(array());
	            if($receipt){
	            	$this->data['package'] = $billing_package;
			        $this->data['post'] = $receipt;
			        $this->data['payment_methods'] = $payment_methods;
	            	$this->data['group'] = $group_owner;
	            	$this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
	            	$json_file = json_encode($this->data);
					$response = $this->ci->curl_post_data->curl_post_json_pdf($json_file,'https://pdfs.chamasoft.com/billing_receipts',$group_owner->name.' - '.$this->application_settings->application_name.' billing receipt ',FALSE);
					$attachments = serialize(array(
						'./'.$response,
					));
	            }

				$email_body = $this->ci->emails_m->build_email_message('billing-payment-online-receipt',$email_data);
				
				$id = $this->ci->emails_m->insert_email_queue(array(
						'email_to'	=>	$group_owner->email,
						'subject'	=>	$subject,
						'message' 	=>	$email_body,
						'email_from'=>	'billing@chamasoft.com',
						'sending_email' => 'billing@chamasoft.com',
						'group_id'	=>	$group_id,
						'member_id'	=>	$group_owner->member_id,
						'user_id'	=>	$group_owner->user_id,
						'attachments' => $attachments,
						'created_on'	=>	time(),
						'created_by'	=> 0
					));
               return TRUE;

			}
			else{
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Email - Some essential parameters are missing');
			return FALSE;
		}
	}

	function send_billing_invoice_sms_notification($group_id=0,$group_owner=array(),$amount=array(),$due_date='',$billing_cycle='',$account_arrears=0,$cycles=array(),$group=array()){
		
		if($group_owner&&$group_id&&$amount&&$due_date&&$billing_cycle&&$group){
			if(valid_phone($group_owner->phone)){
				if($account_arrears>0){
                    $balance = number_to_currency($account_arrears);
                }
                else if($account_arrears==0){
                    $balance= number_to_currency(0);
                }
                else if($account_arrears<0){
                    $account_arrears = abs($account_arrears);
                    $balance = number_to_currency($account_arrears);
                }
                else{
                   $balance = number_to_currency(0);
                }

				$message = '['.$group->name.'] A/C '.$group_owner->account_number.' billing is due on '.timestamp_to_receipt($due_date).'. Top up '.$this->default_country->currency_code.' '.$balance.' to avoid account being locked. Mpesa Paybill 967600. ['.$this->application_settings->application_name.']';
				
				$data = array(
                    'sms_to' => $group_owner->phone,
					'system_sms' => 1,
                    'message' => $message,
                    'group_id' => $group_id,
                    'member_id' => $group_owner->member_id,
                    'user_id' => $group_owner->user_id,
                    'created_on' => time(),
                    'created_by'=> 0
                );

				$result = $this->ci->sms_m->insert_sms_queue($data);
				
                if($result){
                    return TRUE;
                }else{
					return FALSE;
                }
			}else{
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Billing invoice sms can not be created. Essential parameters missing');
			return FALSE;
		}
	}

	function send_billing_invoice_email_notification($group_id=0,$group_owner=array(),$amount=array(),$due_date,$billing_cycle,$account_arrears,$cycles=array(),$billing_date=0,$billing_cycle_end_date=0,$invoice=array(),$billing_package=array()){
		if($group_owner&&$group_id&&$amount&&$due_date&&$billing_cycle){
			if(valid_email($group_owner->email)){
				$subject = '['.$this->application_settings->application_name.'] subscription proforma invoice';
				if($account_arrears>0){
                    $balance = number_to_currency($account_arrears);
                }
                else if($account_arrears==0){
                    $balance=number_to_currency(0);
                }
                else if($account_arrears<0){
                    //$account_arrears = abs($account_arrears);
                    $balance = number_to_currency($account_arrears);
                }
                else{
                   $balance = number_to_currency(0);
                }
                if($billing_cycle==3){
                	$billing_cycle = 'Annual';
                }else if($billing_cycle==2){
                	$billing_cycle = 'Quarterly';
                }else{
                	$billing_cycle = 'Monthly';
                }
				$email_data = array(
					'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
					'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
					'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
					'APPLICATION_NAME'=> $this->application_settings->application_name,
	                'DATE' => date('d').date('S'),
	                'MONTH' => date('M'),
	                'GROUP_NAME' => $group_owner->name,
	                'CURRENCY' => "KES",
	                'BILLING_CYCLE' => $billing_cycle,
	                'FIRST_NAME' => $group_owner->first_name,
	                'LAST_NAME' => $group_owner->last_name,
	                'BALANCE' => number_to_currency($balance),
	                'YEAR' => date('Y',time()),
	                'BILLING_DATE' =>timestamp_to_date($billing_date),
	                'BILLING_CYCLE_END_DATE' =>timestamp_to_date($billing_cycle_end_date),
	                'DUE_DATE' => timestamp_to_date($due_date),
	                'AMOUNT' => $amount->amount+$amount->tax+$amount->prorated_amount,
	                'LINK'=> $this->application_settings->protocol.$group_owner->slug.'.'.$this->application_settings->url.'/group/billing/billing_information',
	            );

				$email_body = $this->ci->emails_m->build_email_message('chamasoft-billing-invoice',$email_data);
				$attachments = serialize(array());
				if($invoice){
					$this->data['package'] = $billing_package;
			        $this->data['post'] = $invoice;
			        $this->data['billing_cycles'] = $cycles;
			        $this->data['group'] = $group_owner;
			        $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
			        $this->data['application_settings'] = $this->application_settings;
			        if($account_arrears - $invoice->amount){
			            $this->data['balance'] = $account_arrears - $invoice->amount;
			        }else{
			            $this->data['balance'] = 0;
			        }
					$json_file = json_encode($this->data);
					$response = $this->ci->curl_post_data->curl_post_json_pdf($json_file,'https://pdfs.chamasoft.com/billing_invoices',$group_owner->name.' - '.$this->application_settings->application_name.' billing invoice ',FALSE);
					$attachments = serialize(array(
						'./'.$response,
					));
				}
				$id = $this->ci->emails_m->insert_email_queue(array(
						'email_to'	=>	$group_owner->email,
						'subject'	=>	$subject,
						'message' 	=>	$email_body,
						'email_from'=>	'billing@chamasoft.com',
						'sending_email' => 'billing@chamasoft.com',
						'group_id'	=>	$group_id,
						'member_id'	=>	$group_owner->member_id,
						'user_id'	=>	$group_owner->user_id,
						'attachments' => $attachments,
						'created_on'	=>	time(),
						'created_by'	=> 0
					));
               return TRUE;

			}
			else{
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Billing invoice email can not be created. Essential parameters missing');
			return FALSE;
		}
	}

	function sms_notify_trial_days($group_id=0,$group_owner=array(),$trial_days=0){
		if($group_owner&&$trial_days&&$group_id){
			if(valid_phone($group_owner->phone)){
				$expiry_date = strtotime('+'.$trial_days.' days,',time());
				
				$sms_data = array(
					'FIRST_NAME' => $group_owner->first_name,
					'GROUP_NAME' => $group_owner->name,
					'EXPIRY_DATE' => timestamp_to_receipt($expiry_date),
					'APPLICATION_NAME' => $this->application_settings->application_name,
				);

				$message = $this->ci->sms_m->build_sms_message('notify-trial-days',$sms_data,'',$group_owner->language_id);

				//$message = 'Dear '.$group_owner->first_name.', '.$group_owner->name.' group trial days will expire on '.timestamp_to_receipt($expiry_date).'. Kindly subscribe to continue enjoying '.$this->application_settings->application_name.' services. Contact support 0733366240. Thank you.';
				
				$data = array(
                    'sms_to' => $group_owner->phone,
                    'system_sms' => 1,
                    'message' => $message,
                    'group_id' => $group_id,
                    'member_id' => $group_owner->member_id,
                    'user_id' => $group_owner->user_id,
                    'created_on' => time(),
                    'created_by'=> 0
                );

                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
                    return TRUE;
                }else{
					return FALSE;
                }
			}else{
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Some important parameters are missing');
			return FALSE;
		}
	}
	

	function email_notify_trial_days($group_id=0,$group_owner=array(),$trial_days=0){
		if($group_id&&$trial_days&&$group_owner){
			if(valid_email($group_owner->email)){
				$expiry_date = strtotime('+'.$trial_days.',',time());
				$subject = $this->application_settings->application_name.' trial days';
				$email_data = array(
					'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                    'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                    'APPLICATION_LOGO'=>$this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'', 
	                'APPLICATION_NAME' => $this->application_settings->application_name,
	                'EXPIRY_DATE' => timestamp_to_receipt($expiry_date),
	                'TRIAL_DAYS'=>$trial_days,
	                'FIRST_NAME' => $group_owner->first_name,
	                'LAST_NAME' => $group_owner->first_name,
	                'GROUP_NAME' => $group_owner->name,
	                'LINK' => $this->application_settings->protocol.$group_owner->slug.'.'.$this->application_settings->url.'/group/billing/billing_information',
	            );

				$email_body = $this->ci->emails_m->build_email_message('billing-trial-days-alert',$email_data);
				
				$id = $this->ci->emails_m->insert_email_queue(array(
						'email_to'	=>	$group_owner->email,
						'subject'	=>	$subject,
						'message' 	=>	$email_body,
						'email_from'=>	'info@chamasoft.com',
						'sending_email' => 'billing@chamasoft.com',
						'group_id'	=>	$group_id,
						'member_id'	=>	$group_owner->member_id,
						'user_id'	=>	$group_owner->user_id,
						'created_on'	=>	time(),
						'created_by'	=> 0
					));
               return TRUE;

			}
			else{
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Email - Some essential parameters are missing');
			return FALSE;
		}
	}

	function notify_admin_sms_balance($group=array()){
		if($group){
			if(valid_phone($group->phone)){
				$sms_balance = $group->sms_balance;
				$message = 'Dear '.$group->first_name.', '.$group->name.' currently has '.$sms_balance.' smses remaining. Kindly top up to ensure all your smses are delivered. Visit Group Account Information to learn how to top up. Thank you. ';
				$data = array(
                    'sms_to' => $group->phone,
                    'system_sms' => 1,
                    'message' => $message,
                    'group_id' => $group->id,
                    'member_id' => $group->member_id,
                    'user_id' => $group->user_id,
                    'created_on' => time(),
                    'created_by'=> 0
                );

                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
                    return TRUE;
                }else{
					return FALSE;
                }
			}else{
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Sorry, some parameters are missing');
			return FALSE;
		}
	}

	function notify_sms_purchase_payment($group_id=0,$sms_purchased=0,$amount=0,$receipt_date=0,$group_owner=array(),$phone=0,$customer='',$send_sms=FALSE,$send_email=FALSE){
		if($group_id&&$sms_purchased&&$amount&&$receipt_date){
			if($group_owner || $phone){
				if($send_sms){
					if($group_owner){
						$message = 'Dear '.$group_owner->first_name.', you have successfully purchased '.$sms_purchased.' SMSes for '.$group_owner->name.'. Thank you.';
						$data = array(
		                    'sms_to' => $group_owner->phone,
		                    'system_sms' => 1,
		                    'message' => $message,
		                    'group_id' => $group_id,
		                    'member_id' => $group_owner->member_id,
		                    'user_id' => $group_owner->user_id,
		                    'created_on' => time(),
		                    'created_by'=> 0
		                );
					}

					else{
						$message = 'Dear '.$customer.', you have successfully purchased '.$sms_purchased.' SMSes for '.$group_owner->name.'Thank you.';
						$data = array(
		                    'sms_to' => $phone,
		                    'message' => $message,
		                    'group_id' => $group_id,
		                    'member_id' => '',
		                    'user_id' => '',
		                    'created_on' => time(),
		                    'created_by'=> 0
		                );
					}
	                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                    return TRUE;
	                }else{
						return FALSE;
	                }
				}

				if($send_email){

				}

			}else{
				$this->ci->session->set_flashdata('error','Sorry, some parameters are missing');
				return FALSE;
			}

		}else{
			$this->ci->session->set_flashdata('error','Sorry, some parameters are missing');
			return FALSE;
		}
	}

	function send_bank_account_scheme_code_change_request($user = array(),$group = array(),$account_number = ""){
		if($user&&$group&&$account_number){
			$email_data = array(
				'ACCOUNT_NUMBER' => $account_number,
				'FIRST_NAME' => $user->first_name,
				'LAST_NAME' => $user->last_name,
				'PHONE' => $user->phone,
				'EMAIL' => $user->email,
				'GROUP_NAME' => $group->name,
				'APPLICATION_NAME' => $this->application_settings->application_name,
				'LOGIN_URL' => $this->application_settings->protocol.$this->application_settings->url.'/login',
				'SENDER' => "Chamasoft Team",
				'YEAR' => date('Y'),
                'SUBJECT' => ' Scheme Code Change Request for '.$account_number,
			);
			$email_message = $this->ci->emails_m->build_email_message('bank-account-scheme-code-change-request',$email_data);
            $data = array(
                'email_to' => 'caroline.musyoka@equitybank.co.ke',
                'subject' => ' Scheme Code Change Request for '.$account_number,
                'message' => $email_message,
                'email_from' => '',
                'group_id' => $group->id,
                'cc' => 'edwin.njoroge@digitalvision.co.ke',
                //'member_id' => $member->id,
                //'user_id' => $member->user_id,
                'created_on' => time()
            );
            if($email_id = $this->ci->emails_m->insert_email_queue($data)){
                return TRUE;
            }else{
                return FALSE;
            }
		}else{
			return FALSE;
		}
	}


	
	/* to be cleaned up */
	function notify_admin_loan_application($group_owner=array(),$loan = array(),$user,$currency=''){
		if(is_object($group_owner) && !empty($group_owner) && is_array($loan) && !empty($loan) && is_object($user) && !empty($user)){
			if(valid_phone($group_owner->phone)){
				$message = 'Dear '.$group_owner->first_name.', '.$user->first_name.' has made a loan application of '.$currency.' '.number_to_currency($loan['loan_amount']).'. Kindly login to '.$this->application_settings->application_name.' to review this loan. '.$group_owner->name;
					$data = array(
	                    'sms_to' => $group_owner->phone,
	                    'system_sms' => 1,
	                    'message' => $message,
	                    'group_id' => $group_owner->group_id,
	                    'member_id' => $group_owner->member_id,
	                    'user_id' => $group_owner->user_id,
	                    'created_on' => time(),
	                    'created_by'=> 0
	                );
	            if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                return TRUE;
	            }else{
					return FALSE;
	            }
			}

			/*if(valid_phone($group->email)){
				$email_data = array(
					'LOAN' => $account_number,
					'FIRST_NAME' => $user->first_name,
					'LAST_NAME' => $user->last_name,
					'PHONE' => $user->phone,
					'EMAIL' => $user->email,
					'GROUP_NAME' => $group->name,
					'APPLICATION_NAME' => $this->application_settings->application_name,
					'LOGIN_URL' => $this->application_settings->protocol.$this->application_settings->url.'/login',
					'SENDER' => "Chamasoft Team",
					'YEAR' => date('Y'),
	                'SUBJECT' => ' Scheme Code Change Request for '.$account_number,
				);
				$email_message = $this->ci->emails_m->build_email_message('bank-account-scheme-code-change-request',$email_data);
	            $data = array(
	                'email_to' => 'andrene.terry@equitybank.co.ke',
	                'subject' => ' Scheme Code Change Request for '.$account_number,
	                'message' => $email_message,
	                'email_from' => '',
	                'group_id' => $group->id,
	                'cc' => 'caroline.musyoka@equitybank.co.ke,edwin.njoroge@digitalvision.co.ke,Samuel.TKamau@equitybank.co.ke,paul.githinji@equitybank.co.ke,Peter.Nabutete@equitybank.co.ke,Christine.Njihia@equitybank.co.ke',
	                //'member_id' => $member->id,
	                //'user_id' => $member->user_id,
	                'created_on' => time()
	            );
	            if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	                return TRUE;
	            }else{
	                return FALSE;
	            }
			}*/
			
		}
	}

	function eazzy_club_notify_guarantor_about_loan_application_request($loan_details_array= array(),$applicant_array=array(),$guarantor_array= array()){
		$loan_details_object = (object)$loan_details_array;
		$loan_applicant_object = (object)$applicant_array;
		$guarantor_details_object = (object)$guarantor_array;
		if($loan_details_object){
			if($loan_applicant_object){
				if($guarantor_details_object){
					if(valid_phone($guarantor_details_object->guarantor_phone_no)){ 
		            	$message = $this->ci->sms_m->build_sms_message('guarantor-loan-requests-template',array(
		            		'FIRST_NAME' =>$guarantor_details_object->first_name,
		            		'LOAN_APPLICANT_NAME'=>$loan_applicant_object->first_name,
		            		'LOAN_TYPE_NAME'=>$loan_details_object->loan_name,
		            		'GROUP_CURRENCY'=> $loan_details_object->currency,
		            		'LOAN_GUARANTOR_AMOUNT'=>$guarantor_details_object->guarantor_amount,
		            		'GROUP_NAME'=>$this->ci->group->name
		            	));
						$data = array(
		                    'sms_to' => valid_phone($guarantor_details_object->guarantor_phone_no),
		                    'message' => $message,
		                    'group_id' => $loan_details_object->group_id,
		                    'member_id' => $guarantor_details_object->guarantor_member_id,
		                    'user_id' => $guarantor_details_object->guarantor_user_id,
		                    'created_on' => time(),
		                    'created_by'=> $loan_applicant_object->loan_applicant_user_id
		                );
		                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
							$this->ci->notifications->create(
		                        'You have a pending loan request ',
		                        ''.$loan_applicant_object->first_name.' '.$loan_applicant_object->last_name.' has requested a loan('.$loan_details_object->loan_name.') of amount '.$loan_details_object->currency.' '.number_to_currency($loan_details_object->loan_amount).' and has requested you to be a guarantor of amount '.$loan_details_object->currency.'&nbsp;'.$guarantor_details_object->guarantor_amount.' please review ',
		                        $this->ci->ion_auth->get_user($loan_applicant_object->loan_applicant_user_id),
		                        $loan_applicant_object->loan_applicant_member_id,
		                        $guarantor_details_object->guarantor_user_id,
		                        $guarantor_details_object->guarantor_member_id,
		                        $loan_details_object->group_id,
		                        'View Pending Loan Requests ',
		                        'member/members/view_eazzy_club_loan_requests/'. $loan_details_object->loan_application_id,
		                        10,
		                        $loan_details_object->loan_application_id
		                       );							
		                }else{
		                }
		            }else{
		                $this->ci->session->set_flashdata('error','Loan application failed: Cannot find guarantor phone no');
		            }
				}else{
					$this->ci->session->set_flashdata('error','Loan application failed: Cannot find guarantor details');
				}				
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot find loan applicant details');
			}			 
		}else{
			$this->ci->session->set_flashdata('error','Couldnt not find loan details');
		}
	}

	function notify_guarantor_about_loan_application_request($loan_details_array= array(),$applicant_array=array(),$guarantor_array= array()){
		$loan_details_object = (object)$loan_details_array;
		$loan_applicant_object = (object)$applicant_array;
		$guarantor_details_object = (object)$guarantor_array;
		if($loan_details_object){
			if($loan_applicant_object){
				if($guarantor_details_object){
					if(valid_phone($guarantor_details_object->guarantor_phone_no)){ 
		            	$message = $this->ci->sms_m->build_sms_message('guarantor-loan-requests-template',array(
		            		'FIRST_NAME' =>$guarantor_details_object->first_name,
		            		'LOAN_APPLICANT_NAME'=>$loan_applicant_object->first_name,
		            		'LOAN_TYPE_NAME'=>$loan_details_object->loan_name,
		            		'GROUP_CURRENCY'=> $loan_details_object->currency,
		            		'LOAN_GUARANTOR_AMOUNT'=>$guarantor_details_object->guarantor_amount,
		            	));
						$data = array(
		                    'sms_to' => valid_phone($guarantor_details_object->guarantor_phone_no),
		                    'message' => $message,
		                    'group_id' => $loan_details_object->group_id,
		                    'member_id' => $guarantor_details_object->guarantor_member_id,
		                    'user_id' => $guarantor_details_object->guarantor_user_id,
		                    'created_on' => time(),
		                    'created_by'=> $loan_applicant_object->loan_applicant_user_id
		                );
		                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
							$this->ci->notifications->create(
		                        'You have a pending loan request ',
		                        ''.$loan_applicant_object->first_name.' '.$loan_applicant_object->last_name.' has requested a loan('.$loan_details_object->loan_name.') of amount '.$loan_details_object->currency.' '.number_to_currency($loan_details_object->loan_amount).' and has requested you to be a guarantor of amount '.$loan_details_object->currency.'&nbsp;'.$guarantor_details_object->guarantor_amount.' please review ',
		                        $this->ci->ion_auth->get_user($loan_applicant_object->loan_applicant_user_id),
		                        $loan_applicant_object->loan_applicant_member_id,
		                        $guarantor_details_object->guarantor_user_id,
		                        $guarantor_details_object->guarantor_member_id,
		                        $loan_details_object->group_id,
		                        'View Pending Loan Requests ',
		                        'member/loans/loan_requests/'. $loan_details_object->loan_application_id,
		                        10,
		                        $loan_details_object->loan_application_id
		                       );							
		                }else{
		                }
		            }else{
		                $this->ci->session->set_flashdata('error','Loan application failed: Cannot find guarantor phone no');
		            }
				}else{
					$this->ci->session->set_flashdata('error','Loan application failed: Cannot find guarantor details');
				}				
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot find loan applicant details');
			}			 
		}else{
			$this->ci->session->set_flashdata('error','Couldnt not find loan details');
		}
	}
	
	function notify_guarantor_about_loan_edit_application_request($group_id=0,$loan_applicant_id=0,$loan_type_id=0,$guarantor_id=0,$guaranteed_amounts=0,$currency='KES',$loan_amount,$loan_applicant_member_id,$loan_application_id){
		if($loan_type_id){
			$get_loan_type_options = $this->ci->loan_types_m->get($loan_type_id,$group_id);
			if($get_loan_type_options->name){				
				$get_loan_guarantor_member_details = $this->ci->members_m->get($guarantor_id);
				if($get_loan_guarantor_member_details){
                    $guarantor_user_id = $get_loan_guarantor_member_details->user_id;                    
                    $get_guarantor_user_details = $this->ci->users_m->get($guarantor_user_id);
                    $guarantor_phone_no = $get_guarantor_user_details->phone;
                    $get_loan_applicant_user_details = $this->ci->users_m->get($loan_applicant_id);
                    if(valid_phone($guarantor_phone_no)){ 
                    	$message = $this->ci->sms_m->build_sms_message('guarantor-loan-requests-edit-template',array(
                    		'FIRST_NAME' =>$get_guarantor_user_details->first_name,
                    		'LOAN_APPLICANT_NAME'=>$get_loan_applicant_user_details->first_name,
                    		'LOAN_TYPE_NAME'=>$get_loan_type_options->name,
                    		'GROUP_CURRENCY'=> $currency,
                    		'LOAN_GUARANTOR_AMOUNT'=>$guaranteed_amounts,
                    	));

						$data = array(
		                    'sms_to' => valid_phone($guarantor_phone_no),
		                    'message' => $message,
		                    'group_id' => $group_id,
		                    'member_id' => $guarantor_id,
		                    'user_id' => $guarantor_user_id,
		                    'created_on' => time(),
		                    'created_by'=> $loan_applicant_id
		                );		                

		                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
							$this->ci->notifications->create(
                                'You have a pending loan edit request ',
                                ''.$get_loan_applicant_user_details->first_name.' '.$get_loan_applicant_user_details->last_name.' has changed his loan request and has requested a loan('.$get_loan_type_options->name.') of amount '.$currency.' '.number_to_currency($loan_amount).' and has requested you to be a guarantor of amount '.$currency.'&nbsp;'.$guaranteed_amounts.' please review ',
                                $this->ci->ion_auth->get_user($loan_applicant_id),
                                $loan_applicant_member_id,
                                $guarantor_user_id,
                                $guarantor_id,
                                $group_id,
                                'View Pending Loan Requests ',
                                'member/members/view_loan_requests/'.$loan_application_id,
                                10,
                                $loan_application_id
                               );
		                }else{
		                	//return FALSE;
		                    //++$failed;
		                }
                    }else{
                        $this->ci->session->set_flashdata('error','Loan application failed: Cannot find guarantor phone no');
                        //return FALSE;
                    }                                       
                }else{
                    $this->ci->session->set_flashdata('error','Loan application failed: Cannot get guarantor Applicant Details
');
                   // return FALSE;
                }
			}else{
				$this->ci->session->set_flashdata('error','Could not get loan type options');
				//return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Couldn\'nt send notification to member');
			///return FALSE;
		}
	}

	function notify_applicant_of_loan_request_status($guarantor_array= array(),$loan_applicant_array= array(),$loan_details_array=array() ,$supervisor_details_array = array()){
		$loan_details_object = (object)$loan_details_array;
		$guarantor_object = (object)$guarantor_array;
		$loan_applicant_object = (object)$loan_applicant_array;
		$supervisor_object = (object)$supervisor_details_array;
		if($loan_details_object){
			if($guarantor_object){
				if($loan_applicant_object){	
                    if($loan_details_object->action == 'approve'){                        
                		if($this->notify_applicant_of_loan_approve($guarantor_array,$loan_applicant_array,$loan_details_array)){
                			return TRUE;
                		}else{
                			return FALSE;
                		}
            		}else if($loan_details_object->action == 'decline'){
            			if($this->notify_applicant_of_loan_decline($guarantor_array,$loan_applicant_array,$loan_details_array)){
                			return TRUE;
                		}else{
                			return FALSE;
                		}
            		}
				}else{
					$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan applicant  details');
				}
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot get guarantor  details');	
			}
		}else{
			$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan  details');	
		}
	}

	function notify_applicant_of_loan_approve($guarantor_array= array(),$loan_applicant_array= array(),$loan_details_array=array() ){
		$loan_details_object = (object)$loan_details_array;
		$guarantor_object = (object)$guarantor_array;
		$loan_applicant_object = (object)$loan_applicant_array;			
		if($loan_details_object){
			if($guarantor_object){
				if($loan_applicant_object){ 
					if(valid_phone($loan_applicant_object->phone_no)){						
						$message = $this->ci->sms_m->build_sms_message('guarantor-loan-request-approve-template',array(
				    		'LOAN_APPLICANT_NAME'=>$loan_applicant_object->first_name,
				    		'LOAN_GUARANTOR_NAME'=>$guarantor_object->first_name,
				    		'LOAN_TYPE_NAME'=>$loan_details_object->loan_name,
				    		'GROUP_CURRENCY'=>$loan_details_object->currency,
				    		'LOAN_GUARANTOR_AMOUNT'=>$guarantor_object->guarantor_amount,
				    		'GROUP_NAME'=>$this->ci->group->name
				    	));
				    	$data = array(
		                    'sms_to' => valid_phone($loan_applicant_object->phone_no),
		                    'message' => $message,
		                    'group_id' => $loan_details_object->group_id,
		                    'member_id' => $loan_applicant_object->loan_applicant_member_id,
		                    'user_id' => $loan_applicant_object->loan_applicant_user_id,
		                    'created_on' => time(),
		                    'created_by'=> $guarantor_object->guarantor_user_id
		                );
		                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
		                	$notification_success = $this->ci->notifications->create(
			                    'Loan request approved ',
			                    ''.$guarantor_object->first_name.' '.$guarantor_object->last_name.' has approved to be your guarantor of your loan ('.$loan_details_object->loan_name.')  request of amount '.$loan_details_object->currency.' '.number_to_currency($guarantor_object->guarantor_amount),
			                    $this->ci->ion_auth->get_user($guarantor_object->guarantor_user_id),                   
			                    $guarantor_object->guarantor_member_id,
			                    $loan_applicant_object->loan_applicant_user_id,                               
			                    $loan_applicant_object->loan_applicant_member_id,
			                    $loan_details_object->group_id,
			                    'View Loan request approval ',
			                    'member/members/view_loan_requests_status/'.$loan_details_object->loan_application_id,
			                    10,
			                    $loan_details_object->loan_application_id
			                );
			                if($notification_success){			                	
			                	if(empty($loan_details_object->loan_request_status)){
			                		return FALSE;
			                	}else{
			                		$progress_array =  $loan_details_object->loan_request_status;
			                		//print_r($progress_array);
			                		if(in_array(2, $progress_array)){			                			
			                			//guarantor has  declined don't send notification
			                			return TRUE;
			                		}else{			                			
			                			if(in_array(1, $progress_array)){
			                				//no guarantor action don't send notification
			                				return TRUE;
										}else{
											if(count(array_count_values($progress_array)) == 1){
												$active_group_holders = $this->ci->members_m->get_active_group_role_holder_member_details($loan_details_object->group_id);												
											    if($active_group_holders){
											    	unset($active_group_holders[$loan_applicant_object->loan_applicant_member_id]);
											    	foreach ($active_group_holders as $key => $active_group_holder) {
											    		$signatory_details_data = array(
													    	'loan_type_id'=>$loan_details_object->loan_type_id,
													    	'loan_application_id'=> $loan_details_object->loan_application_id,
													    	'loan_request_applicant_user_id'=>$loan_applicant_object->loan_applicant_user_id,
													    	'loan_request_member_id'=>$loan_applicant_object->loan_applicant_member_id,
													    	'signatory_user_id'=>$active_group_holder->user_id,
													    	'loan_signatory_progress_status'=>1,
													    	'signatory_member_id'=>$active_group_holder->id,
													    	'group_id'=>$loan_details_object->group_id,
													    	'loan_amount'=>$loan_details_object->loan_amount,
													    	'active'=>1,
													    	'created_on'=>time(),
													    	'created_by'=>$this->ci->user->id,
													    );
														if($loan_signatory_success_id = $this->ci->loans_m->insert_loan_signatory_requests($signatory_details_data)){

												    		if(valid_phone($active_group_holder->phone)){
														    	$signatory_message = $this->ci->sms_m->build_sms_message('signatories-loan-request-application',array(
														    		'LOAN_APPLICANT_NAME'=>$loan_applicant_object->first_name,
														    		'SIGNATORY'=>$active_group_holder->first_name,
														    		'LOAN_TYPE_NAME'=>$loan_details_object->loan_name,
														    		'GROUP_CURRENCY'=>$loan_details_object->currency,
														    		'GROUP_NAME'=>$this->ci->group->name,
														    		'LOAN_REQUEST_AMOUNT'=>$loan_details_object->loan_amount,
														    	));
														    	$signatory_data = array(
												                    'sms_to' => valid_phone($active_group_holder->phone),
												                    'message' => $signatory_message,
												                    'group_id' => $loan_details_object->group_id,
												                    'member_id' => $active_group_holder->id,
												                    'user_id' => $active_group_holder->user_id,
												                    'created_on' => time(),
												                    'created_by'=> $this->ci->user->id
												                );
												                if($sms_id = $this->ci->sms_m->insert_sms_queue($signatory_data)){
												                	$notification_success = $this->ci->notifications->create(
													                    'Loan approval request',
													                    ''.$loan_applicant_object->first_name.' '.$loan_applicant_object->last_name.' has request a loan  ('.$loan_details_object->loan_name.')   of amount '.$loan_details_object->currency.' '.number_to_currency($loan_details_object->loan_amount).' please review ',
													                    $this->ci->ion_auth->get_user($guarantor_object->guarantor_user_id),
						                    							$guarantor_object->guarantor_member_id,
													                    $active_group_holder->user_id,                               
													                    $active_group_holder->id,
													                    $loan_details_object->group_id,
													                    'View Loan request',
													                    'member/loans/signatory_approval/'.$loan_signatory_success_id,
													                    10,
													                    $loan_signatory_success_id
													                );												                										                
												                }else{
												                	return FALSE;
												                }
												            }else{
												            	$this->ci->session->set_flashdata('error','Invalid phone number');
												            	return FALSE;
												            }
												        }
										            }
										            return TRUE;
											    }else{
											    	$this->ci->session->set_flashdata('error','Could not find group role holders');
											    	return FALSE;
											    }
											}
										}
			                		}
	                            }
			                }
		                	
		                }else{
		                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create sms queue');
		                	return FALSE;
		                }
					}else{
						$this->ci->session->set_flashdata('error','Loan application failed: Cannot not a valid phone number ');
						return FALSE;
					}	                
				}else{
					$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan applicant  details');
					return FALSE;
				}
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot get guarantor  details');
				return FALSE;	
			}
		}else{
			$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan  details');	
			return FALSE;
		}
	}

	function notify_applicant_of_loan_decline($guarantor_array= array(),$loan_applicant_array= array(),$loan_details_array=array() ){
	    $loan_details_object = (object)$loan_details_array;
		$guarantor_object = (object)$guarantor_array;
		$loan_applicant_object = (object)$loan_applicant_array;
		if($loan_details_object){
			if($guarantor_object){
				if($loan_applicant_object){ 
					if(valid_phone($loan_applicant_object->phone_no)){
						$message = $this->ci->sms_m->build_sms_message('guarantor-loan-request-decline-template',array(
				    		'LOAN_APPLICANT_NAME'=>$loan_applicant_object->first_name,
				    		'LOAN_GUARANTOR_NAME'=>$guarantor_object->first_name,
				    		'LOAN_TYPE_NAME'=>$loan_details_object->loan_name,
				    		'GROUP_CURRENCY'=>$loan_details_object->currency,
				    		'LOAN_GUARANTOR_AMOUNT'=>$guarantor_object->guarantor_amount,
				    		'GROUP_NAME'=>$this->ci->group->name
				    	));
				    	$data = array(
		                    'sms_to' => valid_phone($loan_applicant_object->phone_no),
		                    'message' => $message,
		                    'group_id' => $loan_details_object->group_id,
		                    'member_id' => $loan_applicant_object->loan_applicant_member_id,
		                    'user_id' => $loan_applicant_object->loan_applicant_user_id,
		                    'created_on' => time(),
		                    'created_by'=> $guarantor_object->guarantor_user_id
		                );
		                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
		                	$notification_success = $this->ci->notifications->create(
			                    'Loan request declined ',
			                    ''.$guarantor_object->first_name.' '.$guarantor_object->last_name.' has declined to be your guarantor of your loan ('.$loan_details_object->loan_name.')  request of amount '.$loan_details_object->currency.' '.number_to_currency($guarantor_object->guarantor_amount),
			                    $this->ci->ion_auth->get_user($guarantor_object->guarantor_user_id),                   
			                    $guarantor_object->guarantor_member_id,
			                    $loan_applicant_object->loan_applicant_user_id,                               
			                    $loan_applicant_object->loan_applicant_member_id,
			                    $loan_details_object->group_id,
			                    'View Loan request declined ',
			                    'member/members/view_loan_requests_status/'.$loan_details_object->loan_application_id,
			                    10,
			                    $loan_details_object->loan_application_id
			                );
                			/*$this->ci->session->set_flashdata('info',' You have declined to guarantee a loan ( '.$loan_details_object->loan_name.') of  '.$loan_details_object->currency.' '.number_to_currency($loan_details_object->loan_amount).' and  you were to guarantee '.$loan_details_object->currency.' ' .number_to_currency($guarantor_object->guarantor_amount));*/
                			return TRUE;
                            //redirect('member/loans/loan_guarantor_listing');
		                }else{
		                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create sms queue');
		                	return FALSE;
		                }
					}else{
						$this->ci->session->set_flashdata('error','Loan application failed: Cannot not a valid phone number ');
						return FALSE;
					}	                
				}else{
					$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan applicant  details');
					return FALSE;
				}
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot get guarantor  details');
				return FALSE;	
			}
		}else{
			$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan  details');
			return FALSE;	
		}    

	}
	
	function notify_loan_applicant_of_loan_request_status($guarantor_array= array(),$loan_applicant_array= array(),$loan_details_array=array() ,$supervisor_details_array = array()){
		$loan_details_object = (object)$loan_details_array;
		$guarantor_object = (object)$guarantor_array;
		$loan_applicant_object = (object)$loan_applicant_array;
		$supervisor_object = (object)$supervisor_details_array;
		if($loan_details_object){
			if($guarantor_object){
				if($loan_applicant_object){	
                    if($loan_details_object->action == 'approve'){                        
                		if($this->notify_loan_applicant_of_loan_approve($guarantor_array,$loan_applicant_array,$loan_details_array ,$supervisor_details_array)){
                			return TRUE;
                		}else{
                			return FALSE;
                		}
            		}else if($loan_details_object->action == 'decline'){
					    $this->notify_loan_applicant_of_loan_decline($guarantor_array,$loan_applicant_array,$loan_details_array,$supervisor_details_array);
            		}
				}else{
					$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan applicant  details');
				}
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot get guarantor  details');	
			}
		}else{
			$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan  details');	
		}
	}

	function notify_loan_applicant_of_loan_approve($guarantor_array= array(),$loan_applicant_array= array(),$loan_details_array=array() ,$supervisor_details_array){
		$loan_details_object = (object)$loan_details_array;
		$guarantor_object = (object)$guarantor_array;
		$loan_applicant_object = (object)$loan_applicant_array;
		$supervisor_object = (object)$supervisor_details_array;		
		if($loan_details_object){
			if($guarantor_object){
				if($loan_applicant_object){ 
					if(valid_phone($loan_applicant_object->phone_no)){
						$message = $this->ci->sms_m->build_sms_message('guarantor-loan-request-approve-template',array(
				    		'LOAN_APPLICANT_NAME'=>$loan_applicant_object->first_name,
				    		'LOAN_GUARANTOR_NAME'=>$guarantor_object->first_name,
				    		'LOAN_TYPE_NAME'=>$loan_details_object->loan_name,
				    		'GROUP_CURRENCY'=>$loan_details_object->currency,
				    		'LOAN_GUARANTOR_AMOUNT'=>$guarantor_object->guarantor_amount,
				    		'GROUP_NAME'=>$this->ci->group->name
				    	));
				    	$data = array(
		                    'sms_to' => valid_phone($loan_applicant_object->phone_no),
		                    'message' => $message,
		                    'group_id' => $loan_details_object->group_id,
		                    'member_id' => $loan_applicant_object->loan_applicant_member_id,
		                    'user_id' => $loan_applicant_object->loan_applicant_user_id,
		                    'created_on' => time(),
		                    'created_by'=> $guarantor_object->guarantor_user_id
		                );
		                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
		                	$notification_success = $this->ci->notifications->create(
			                    'Loan request approved ',
			                    ''.$guarantor_object->first_name.' '.$guarantor_object->last_name.' has approved to be your guarantor of your loan ('.$loan_details_object->loan_name.')  request of amount '.$loan_details_object->currency.' '.number_to_currency($guarantor_object->guarantor_amount),
			                    $this->ci->ion_auth->get_user($guarantor_object->guarantor_user_id),                   
			                    $guarantor_object->guarantor_member_id,
			                    $loan_applicant_object->loan_applicant_user_id,                               
			                    $loan_applicant_object->loan_applicant_member_id,
			                    $loan_details_object->group_id,
			                    'View Loan request approval ',
			                    'member/members/view_loan_requests_status/'.$loan_details_object->loan_application_id,
			                    10,
			                    $loan_details_object->loan_application_id
			                );
			                if($notification_success){
			                	if(empty($loan_details_object->loan_request_status)){
			                		return FALSE;
			                	}else{
			                		$progress_array =  $loan_details_object->loan_request_status;
			                		if(in_array(2, $progress_array)){
			                			//guarantor has  declined don't send notification
			                			return TRUE;
			                		}else{
			                			if(in_array(1, $progress_array)){
			                				//no guarantor action don't send notification
			                				return TRUE;
										}else{
											if(count(array_count_values($progress_array)) == 1){
											    if($supervisor_object){
											    	$supervisor_message = $this->ci->sms_m->build_sms_message('loan-supervisor-request-approve-template',array(
											    		'LOAN_APPLICANT_NAME'=>$loan_applicant_object->first_name,
											    		'SUPERVISOR_NAME'=>$supervisor_object->supervisor_first_name,
											    		'LOAN_TYPE_NAME'=>$loan_details_object->loan_name,
											    		'GROUP_CURRENCY'=>$loan_details_object->currency,
											    		'GROUP_NAME'=>$this->ci->group->name,
											    		'LOAN_AMOUNT'=>$loan_details_object->loan_amount,
											    	));
											    	$supervisor_data = array(
									                    'sms_to' => valid_phone($supervisor_object->supervisor_phone_no),
									                    'message' => $supervisor_message,
									                    'group_id' => $loan_details_object->group_id,
									                    'member_id' => $supervisor_object->supervisor_member_id,
									                    'user_id' => $supervisor_object->supervisor_user_id,
									                    'created_on' => time(),
									                    'created_by'=> $guarantor_object->guarantor_user_id
									                );
									                if($sms_id = $this->ci->sms_m->insert_sms_queue($supervisor_data)){
									                	$notification_success = $this->ci->notifications->create(
										                    'Loan request supervisor approved ',
										                    ''.$loan_applicant_object->first_name.' '.$loan_applicant_object->last_name.' has choosen you to be a supervisor of his loan  ('.$loan_details_object->loan_name.')  request of amount '.$loan_details_object->currency.' '.number_to_currency($loan_details_object->loan_amount),
										                    $this->ci->ion_auth->get_user($guarantor_object->guarantor_user_id),                 
			                    							$guarantor_object->guarantor_member_id,
										                    $supervisor_object->supervisor_user_id,                               
										                    $supervisor_object->supervisor_member_id,
										                    $loan_details_object->group_id,
										                    'View Loan request supervisor approval ',
										                    'member/loans/supervisor_recommendation/'.$loan_details_object->loan_application_id,
										                    10,
										                    $loan_details_object->loan_application_id
										                );
										                return TRUE;										                
									                }else{
									                	return FALSE;
									                }
											    }
											}
										}
			                		}
	                            }
			                }
		                	
		                }else{
		                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create sms queue');
		                }
					}else{
						$this->ci->session->set_flashdata('error','Loan application failed: Cannot not a valid phone number ');
					}	                
				}else{
					$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan applicant  details');
				}
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot get guarantor  details');	
			}
		}else{
			$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan  details');	
		}
	}

	function notify_loan_applicant_of_loan_decline($guarantor_array= array(),$loan_applicant_array= array(),$loan_details_array=array() ,$supervisor_details_array){
	    $loan_details_object = (object)$loan_details_array;
		$guarantor_object = (object)$guarantor_array;
		$loan_applicant_object = (object)$loan_applicant_array;
		if($loan_details_object){
			if($guarantor_object){
				if($loan_applicant_object){ 
					if(valid_phone($loan_applicant_object->phone_no)){
						$message = $this->ci->sms_m->build_sms_message('guarantor-loan-request-decline-template',array(
				    		'LOAN_APPLICANT_NAME'=>$loan_applicant_object->first_name,
				    		'LOAN_GUARANTOR_NAME'=>$guarantor_object->first_name,
				    		'LOAN_TYPE_NAME'=>$loan_details_object->loan_name,
				    		'GROUP_CURRENCY'=>$loan_details_object->currency,
				    		'LOAN_GUARANTOR_AMOUNT'=>$guarantor_object->guarantor_amount,
				    		'GROUP_NAME'=>$this->ci->group->name
				    	));
				    	$data = array(
		                    'sms_to' => valid_phone($loan_applicant_object->phone_no),
		                    'message' => $message,
		                    'group_id' => $loan_details_object->group_id,
		                    'member_id' => $loan_applicant_object->loan_applicant_member_id,
		                    'user_id' => $loan_applicant_object->loan_applicant_user_id,
		                    'created_on' => time(),
		                    'created_by'=> $guarantor_object->guarantor_user_id
		                );
		                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
		                	$notification_success = $this->ci->notifications->create(
			                    'Loan request declined ',
			                    ''.$guarantor_object->first_name.' '.$guarantor_object->last_name.' has declined to be your guarantor of your loan ('.$loan_details_object->loan_name.')  request of amount '.$loan_details_object->currency.' '.number_to_currency($guarantor_object->guarantor_amount),
			                    $this->ci->ion_auth->get_user($guarantor_object->guarantor_user_id),                   
			                    $guarantor_object->guarantor_member_id,
			                    $loan_applicant_object->loan_applicant_user_id,                               
			                    $loan_applicant_object->loan_applicant_member_id,
			                    $loan_details_object->group_id,
			                    'View Loan request declined ',
			                    'member/members/view_loan_requests_status/'.$loan_details_object->loan_application_id,
			                    10,
			                    $loan_details_object->loan_application_id
			                );
                			$this->ci->session->set_flashdata('info',' You have declined to guarantee a loan ( '.$loan_details_object->loan_name.') of  '.$loan_details_object->currency.' '.number_to_currency($loan_details_object->loan_amount).' and  you were to guarantee '.$loan_details_object->currency.' ' .number_to_currency($guarantor_object->guarantor_amount));
                            redirect('member/loans/loan_guarantor_listing');
		                }else{
		                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create sms queue');
		                }
					}else{
						$this->ci->session->set_flashdata('error','Loan application failed: Cannot not a valid phone number ');
					}	                
				}else{
					$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan applicant  details');
				}
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot get guarantor  details');	
			}
		}else{
			$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan  details');	
		}    

	}

	function notify_loan_applicant($loan_application_particulars = array()){
		if(empty($loan_application_particulars)){
			$this->ci->session->set_flashdata('error','Loan application details array is empty');
		}else{
			$loan_object = (object)$loan_application_particulars;
			$group_members = $this->ci->members_m->get_group_members_array($this->ci->group->id);
			$loan_type = $this->ci->loan_types_m->get($loan_object->loan_type_id);
			if(valid_phone($group_members[$loan_object->loan_request_member_id]->phone)){
				if($loan_object->is_approve){
					$message = $this->ci->sms_m->build_sms_message('supervisor-loan-approve-template',array(
			    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_request_member_id]->first_name,
			    		'SUPERVISOR_NAME'=>$group_members[$loan_object->supervisor_member_id]->first_name,
			    		'LOAN_TYPE_NAME'=>$loan_type->name,
			    		'LOAN_AMOUNT'=>$loan_object->loan_amount,
			    		'GROUP_CURRENCY'=>$this->ci->group_currency,
			    		'GROUP_NAME'=>$this->ci->group->name
			    	));
				}else if($loan_object->is_decline){
					$message = $this->ci->sms_m->build_sms_message('supervisor-loan-decline-template',array(
			    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_request_member_id]->first_name,
			    		'SUPERVISOR_NAME'=>$group_members[$loan_object->supervisor_member_id]->first_name,
			    		'LOAN_TYPE_NAME'=>$loan_type->name,
			    		'LOAN_AMOUNT'=>$loan_object->loan_amount,
			    		'GROUP_CURRENCY'=>$this->ci->group_currency,
			    		'GROUP_NAME'=>$this->ci->group->name
			    	));

				}
		    	$data = array(
                    'sms_to' => valid_phone($group_members[$loan_object->loan_request_member_id]->phone),
                    'message' => $message,
                    'group_id' => $loan_object->group_id,
                    'member_id' => $loan_object->loan_request_member_id,
                    'user_id' => $group_members[$loan_object->loan_request_member_id]->user_id,
                    'created_on' => time(),
                    'created_by'=> $group_members[$loan_object->loan_request_member_id]->user_id
                );
                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
                	if($loan_object->is_approve){
                		$action = 'approved';
                	}else if($loan_object->is_decline){
                		$action = 'declined';
                	}
                	$notification_success = $this->ci->notifications->create(
	                    'Supervisor Recommendation',
	                    ''.$group_members[$loan_object->supervisor_member_id]->first_name.' '.$group_members[$loan_object->supervisor_member_id]->last_name.' your supervisor has '.$action.' your loan ('.$loan_type->name.') of amount '.number_to_currency($loan_object->loan_amount).' ',
	                    $this->ci->ion_auth->get_user($group_members[$loan_object->supervisor_member_id]->user_id),                   
	                    $loan_object->supervisor_member_id,
	                    $group_members[$loan_object->loan_request_member_id]->user_id,                               
	                    $loan_object->loan_request_member_id,
	                    $loan_object->group_id,
	                    'View Loan request declined ',
	                    'member/loans/view_loan_application/'.$loan_object->loan_application_id,
	                    10,
	                    $loan_object->loan_application_id
	                );
	                if($notification_success){
	                	$member_role_holders = $this->ci->members_m->get_active_organizational_role_holder_options($this->ci->group->id);
	                	if($member_role_holders){
	                		//inform hr appraisal
	                		foreach ($member_role_holders as $key => $role_holder) {
	                			if($loan_object->is_approve){
		                			if($role_holder->organization_role_id == 1){
			                			if(valid_phone($role_holder->phone)){
			                				$hr_message = $this->ci->sms_m->build_sms_message('loan-appraisal-template',array(
									    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_request_member_id]->first_name,
									    		'HR_APPRAISAL_OFFICER'=>$role_holder->first_name,
									    		'LOAN_TYPE_NAME'=>$loan_type->name,
									    		'LOAN_AMOUNT'=>$loan_object->loan_amount,
									    		'GROUP_CURRENCY'=>$this->ci->group_currency,
									    		'GROUP_NAME'=>$this->ci->group->name
									    	));
									    	$hr_data = array(
							                    'sms_to' => valid_phone($role_holder->phone),
							                    'message' => $hr_message,
							                    'group_id' => $loan_object->group_id,
							                    'member_id' => $loan_object->loan_request_member_id,
							                    'user_id' => $role_holder->user_id,
							                    'created_on' => time(),
							                    'created_by'=> $group_members[$loan_object->supervisor_member_id]->user_id
							                );
							                if($hr_sms_id = $this->ci->sms_m->insert_sms_queue($hr_data)){
							                	$hr_notification_success = $this->ci->notifications->create(
								                    'Human resource Appraisal ',
								                    ' '.$group_members[$loan_object->loan_request_member_id]->first_name.' has requested a loan ('.$loan_type->name.') of amount '.$this->ci->group_currency.' '.number_to_currency($loan_object->loan_amount).'  has been approved by all guarantors and by the loan  supervisor please review ',
								                    $this->ci->ion_auth->get_user($group_members[$loan_object->loan_request_member_id]->user_id),                 
								                    $loan_object->loan_request_member_id,
								                    $role_holder->user_id,                               
								                    $role_holder->id,
								                    $loan_object->group_id,
								                    'Hr appraisal',
								                    'member/loans/view_hr_appraisals/'.$loan_object->loan_application_id,
								                    10,
								                    $loan_object->loan_application_id
								                );
							                }
			                			}else{
			                				$this->ci->session->set_flashdata('error','Hr appraisal invalid phone no');
			                			}
			                		}
			                	}
	                		}
	                		return TRUE;	                		
	                	}        			    
        			}else{
        				$this->ci->session->set_flashdata('error','Could not create notifications');
        			}
                }else{
                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create sms queue');
                }
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot not a valid phone number ');
			}
		}
	}

	function notify_sacco_officer_applicant($loan_application_particulars = array()){
		if(empty($loan_application_particulars)){
			$this->ci->session->set_flashdata('error','Loan application details array is empty');
		}else{
			$loan_object = (object)$loan_application_particulars;
			$group_members = $this->ci->members_m->get_group_members_array($this->ci->group->id);
			$loan_type = $this->ci->loan_types_m->get($loan_object->loan_type_id);
			if(valid_phone($group_members[$loan_object->loan_member_id]->phone)){
				if($loan_object->is_approve){
					$message = $this->ci->sms_m->build_sms_message('payroll-accountant-approve',array(
			    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_member_id]->first_name,
			    		'ACCOUNTANT_NAME'=>$group_members[$loan_object->hr_member_id]->first_name,
			    		'LOAN_TYPE_NAME'=>$loan_type->name,
			    		'LOAN_AMOUNT'=>$loan_object->loan_amount,
			    		'GROUP_CURRENCY'=>$this->ci->group_currency,
			    		'GROUP_NAME'=>$this->ci->group->name
			    	));
				}else if($loan_object->is_decline){
					$message = $this->ci->sms_m->build_sms_message('payroll-accountant-decline',array(
			    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_member_id]->first_name,
			    		'ACCOUNTANT_NAME'=>$group_members[$loan_object->hr_member_id]->first_name,
			    		'LOAN_TYPE_NAME'=>$loan_type->name,
			    		'LOAN_AMOUNT'=>$loan_object->loan_amount,
			    		'GROUP_CURRENCY'=>$this->ci->group_currency,
			    		'GROUP_NAME'=>$this->ci->group->name
			    	));
				}
		    	$data = array(
                    'sms_to' => valid_phone($group_members[$loan_object->loan_member_id]->phone),
                    'message' => $message,
                    'group_id' => $loan_object->group_id,
                    'member_id' => $loan_object->loan_member_id,
                    'user_id' => $group_members[$loan_object->loan_member_id]->user_id,
                    'created_on' => time(),
                    'created_by'=> $group_members[$loan_object->loan_member_id]->user_id
                );
                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
                	if($loan_object->is_approve){
                		$action = 'approved';
                	}else if($loan_object->is_decline){
                		$action = 'declined';
                	}
                	$notification_success = $this->ci->notifications->create(
	                    'Payroll Accountant Response',
	                    ''.$group_members[$loan_object->hr_member_id]->first_name.' '.$group_members[$loan_object->hr_member_id]->last_name.' a payroll accountant  has '.$action.' your loan ('.$loan_type->name.') of amount '.number_to_currency($loan_object->loan_amount).' ',
	                    $this->ci->ion_auth->get_user($group_members[$loan_object->hr_member_id]->user_id),                   
	                    $loan_object->hr_member_id,
	                    $group_members[$loan_object->loan_member_id]->user_id,                               
	                    $loan_object->loan_member_id,
	                    $loan_object->group_id,
	                    'View Loan request declined ',
	                    'member/loans/view_loan_application/'.$loan_object->loan_application_id,
	                    10,
	                    $loan_object->loan_application_id
	                );
	                if($notification_success){
	                	$member_role_holders = $this->ci->members_m->get_active_organizational_role_holder_options($this->ci->group->id);
	                	if($member_role_holders){
	                		//inform loan officer 
	                		foreach ($member_role_holders as $key => $role_holder) {
	                			if($loan_object->is_approve){
		                			if($role_holder->organization_role_id == 2){
			                			if(valid_phone($role_holder->phone)){
			                				$officer_message = $this->ci->sms_m->build_sms_message('loan-sacco-officer',array(
									    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_member_id]->first_name,
									    		'Group_OFFICER'=>$role_holder->first_name,
									    		'LOAN_TYPE_NAME'=>$loan_type->name,
									    		'LOAN_AMOUNT'=>$loan_object->loan_amount,
									    		'GROUP_CURRENCY'=>$this->ci->group_currency,
									    		'GROUP_NAME'=>$this->ci->group->name
									    	));
									    	$officer_data = array(
							                    'sms_to' => valid_phone($role_holder->phone),
							                    'message' => $officer_message,
							                    'group_id' => $loan_object->group_id,
							                    'member_id' => $loan_object->loan_member_id,
							                    'user_id' => $role_holder->user_id,
							                    'created_on' => time(),
							                    'created_by'=> $group_members[$loan_object->hr_member_id]->user_id
							                );
							                if($officer_sms_id = $this->ci->sms_m->insert_sms_queue($officer_data)){
							                	$hr_notification_success = $this->ci->notifications->create(
								                    'Sacco officer',
								                    ' '.$group_members[$loan_object->loan_member_id]->first_name.' has requested a loan ('.$loan_type->name.') of amount '.$this->ci->group_currency.' '.number_to_currency($loan_object->loan_amount).'  and it has been approved by all guarantors , loan supervisor and a payroll accountant please review.',
								                    $this->ci->ion_auth->get_user($group_members[$loan_object->loan_member_id]->user_id),                 
								                    $loan_object->loan_member_id,
								                    $role_holder->user_id,                               
								                    $role_holder->id,
								                    $loan_object->group_id,
								                    'Hr appraisal',
								                    'member/loans/view_sacco_appraisals/'.$loan_object->loan_application_id,
								                    10,
								                    $loan_object->loan_application_id
								                );
							                }
			                			}else{
			                				$this->ci->session->set_flashdata('error','Sacco officer has an invalid phone no');
			                			}
			                		}
			                	}
	                		}
	                		return TRUE;	                		
	                	}        			    
        			}else{
        				$this->ci->session->set_flashdata('error','Could not create notifications');
        			}
                }else{
                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create sms queue');
                }
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot not a valid phone number ');
			}
		}
	}

	function notify_loan_applicant_about_sacco_action($loan_application_particulars = array()){
		if(empty($loan_application_particulars)){
			$this->ci->session->set_flashdata('error','Loan application details array is empty');
		}else{
			$loan_object = (object)$loan_application_particulars;
			$group_members = $this->ci->members_m->get_group_members_array($this->ci->group->id);
			//print_r($group_members); die();
			$loan_type = $this->ci->loan_types_m->get($loan_object->loan_type_id);
			if(valid_phone($group_members[$loan_object->loan_member_id]->phone)){
				if($loan_object->is_approve){
					$message = $this->ci->sms_m->build_sms_message('sacco-officer-loan-request-approve-template',array(
			    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_member_id]->first_name,
			    		'Group_OFFICER_NAME'=>$group_members[$loan_object->officer_member_id]->first_name,
			    		'LOAN_TYPE_NAME'=>$loan_type->name,
			    		'LOAN_AMOUNT'=>$loan_object->loan_amount,
			    		'GROUP_CURRENCY'=>$this->ci->group_currency,
			    		'GROUP_NAME'=>$this->ci->group->name
			    	));
				}else if($loan_object->is_decline){
					$message = $this->ci->sms_m->build_sms_message('sacco-officer-loan-request-decline-template',array(
			    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_member_id]->first_name,
			    		'Group_OFFICER_NAME'=>$group_members[$loan_object->officer_member_id]->first_name,
			    		'LOAN_TYPE_NAME'=>$loan_type->name,
			    		'LOAN_AMOUNT'=>$loan_object->loan_amount,
			    		'GROUP_CURRENCY'=>$this->ci->group_currency,
			    		'GROUP_NAME'=>$this->ci->group->name
			    	));
				}
		    	$data = array(
                    'sms_to' => valid_phone($group_members[$loan_object->loan_member_id]->phone),
                    'message' => $message,
                    'group_id' => $loan_object->group_id,
                    'member_id' => $loan_object->loan_member_id,
                    'user_id' => $group_members[$loan_object->loan_member_id]->user_id,
                    'created_on' => time(),
                    'created_by'=> $group_members[$loan_object->loan_member_id]->user_id
                );
                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
                	if($loan_object->is_approve){
                		$action = 'approved';
                	}else if($loan_object->is_decline){
                		$action = 'declined';
                	}
                	$notification_success = $this->ci->notifications->create(
	                    'Level 2 sacco appraisal',
	                    ''.$group_members[$loan_object->officer_member_id]->first_name.' '.$group_members[$loan_object->officer_member_id]->last_name.' a sacco officer  has '.$action.' your loan ('.$loan_type->name.') of amount '.number_to_currency($loan_object->loan_amount).' ',
	                    $this->ci->ion_auth->get_user($group_members[$loan_object->officer_member_id]->user_id),                   
	                    $loan_object->officer_member_id,
	                    $group_members[$loan_object->loan_member_id]->user_id,                               
	                    $loan_object->loan_member_id,
	                    $loan_object->group_id,
	                    'View Loan request ',
	                    'member/loans/applicant_affirmation/'.$loan_object->loan_application_id,
	                    10,
	                    $loan_object->loan_application_id
	                );
	                if($notification_success){
	                	return TRUE;        			    
        			}else{
        				$this->ci->session->set_flashdata('error','Could not create notifications');
        			}
                }else{
                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create sms queue');
                }
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot not a valid phone number ');
			}
		}
	}

	function notify_sacco_commitee_members($loan_application_particulars = array()){
		if(empty($loan_application_particulars)){
			$this->ci->session->set_flashdata('error','Loan application details array is empty');
		}else{
			$loan_object = (object)$loan_application_particulars;
			$group_members = $this->ci->members_m->get_group_members_array($this->ci->group->id);
			if($loan_object->affirmation == 1){
				//approve				
				$active_role_holders = $this->ci->members_m->get_eazzy_sacco_active_group_role_holder_options($this->ci->group->id);
				$loan_type = $this->ci->loan_types_m->get($loan_object->loan_type_id);
				if($group_members){
					unset($active_role_holders[$loan_object->loan_member_id]);
					foreach ($group_members as $key => $group_member) {
						if($group_member->organization_role_id == 3){
							if(valid_phone($group_member->phone)){
								$signatory_details = array(
							    	'loan_type_id'=>$loan_object->loan_type_id,
							    	'loan_application_id' => $loan_object->loan_application_id,
							    	'loan_request_applicant_user_id'=>'',
							    	'loan_request_member_id' => $loan_object->loan_member_id,
							    	'commitee_member_id' => $group_member->id,
							    	'loan_signatory_progress_status'=>1,
							    	'committee_progress_status'=>1,
							    	'group_id'=>$loan_object->group_id,
							    	'loan_amount'=>$loan_object->loan_amount,
							    	'active'=>1,
							    	'created_on'=>time(),
							    	'created_by'=>$this->ci->user->id,
							    );
							    if($committee_success = $this->ci->loans_m->insert_loan_signatory_requests($signatory_details)){ 
									$message = $this->ci->sms_m->build_sms_message('credit-commitee-loan-request-template',array(
							    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_member_id]->first_name,
							    		'CREDIT_COMMITEE_MEMBER'=>$group_member->first_name,
							    		'LOAN_TYPE_NAME'=>$loan_type->name,
							    		'LOAN_AMOUNT'=>$loan_object->loan_amount,
							    		'GROUP_CURRENCY'=>$this->ci->group_currency,
							    		'GROUP_NAME'=>$this->ci->group->name
							    	));
							    	$data = array(
					                    'sms_to' => valid_phone($group_member->phone),
					                    'message' => $message,
					                    'group_id' => $loan_object->group_id,
					                    'member_id' => $loan_object->loan_member_id,
					                    'user_id' => $group_members[$loan_object->loan_member_id]->user_id,
					                    'created_on' => time(),
					                    'created_by'=> $group_members[$loan_object->loan_member_id]->user_id
					                );
					                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
					                	$notification_success = $this->ci->notifications->create(
					                        'Pending loan request Approval ',
					                        ''.$group_members[$loan_object->loan_member_id]->first_name.' '.$group_members[$loan_object->loan_member_id]->last_name.' has requested a loan('.$loan_type->name.') of amount '.$this->ci->group_currency.' '.number_to_currency($loan_object->loan_amount).' please review ',
					                        $this->ci->ion_auth->get_user($group_members[$loan_object->loan_member_id]->user_id),
						                    $loan_object->loan_member_id,
						                    $group_member->user_id,
						                    $group_member->id,
						                    $loan_object->group_id,
						                    'Pending loan request Approval',
						                    'member/loans/pending_loan_request/'.$committee_success,
						                    10,
						                    $committee_success
				                        );
						                if($notification_success){
						                	//return TRUE;        			    
					        			}else{
					        				$this->ci->session->set_flashdata('error','Could not create notifications');
					        			}
					                }else{
					                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create sms queue');
					                }
					            }else{
					            	$this->ci->session->set_flashdata('error','could not create group commitee loan request details');
					            }
							}
						}
						$members = isset($active_role_holders[$group_member->id])?$active_role_holders[$group_member->id]:'';	
						if($members){
							$committee_signatory_details = array(
						    	'loan_type_id'=>$loan_object->loan_type_id,
						    	'loan_application_id'=> $loan_object->loan_application_id,
						    	'loan_request_applicant_user_id'=>'',
						    	'loan_request_member_id'=>$loan_object->loan_member_id,
						    	'signatory_user_id'=>'',
						    	'loan_signatory_progress_status'=>1,
						    	'signatory_member_id'=>$members->id,
						    	'group_id'=>$loan_object->group_id,
						    	'loan_amount'=>$loan_object->loan_amount,
						    	'active'=>1,
						    	'created_on'=>time(),
						    	'created_by'=>$this->ci->user->id,
						    );
						   // $loan_signatory_success_id = $this->ci->loans_m->insert_loan_signatory_requests($signatory_details);
							if($loan_signatory_success_id = $this->ci->loans_m->insert_loan_signatory_requests($committee_signatory_details)){
								if(valid_phone($members->phone)){
									$commitete_message = $this->ci->sms_m->build_sms_message('signatories-loan-request-application',array(
				                		'SIGNATORY'=>$members->first_name,
				                		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_member_id]->first_name,
				                		'LOAN_TYPE_NAME'=>$loan_type->name,
				                		'GROUP_CURRENCY'=>$this->ci->group_currency,
				                		'LOAN_REQUEST_AMOUNT'=>number_to_currency($loan_object->loan_amount)
				                	));
				                	$committee_data = array(
					                    'sms_to' => valid_phone($members->phone),
					                    'message' => $commitete_message,
					                    'group_id' =>$loan_object->group_id,
					                    'member_id' => $members->id,
					                    'user_id' => $members->user_id,
					                    'created_on' => time(),
					                    'created_by'=> $this->ci->user->id
					                );
					                if($sms_id = $this->ci->sms_m->insert_sms_queue($committee_data)){
					                	$signatory_success = $this->ci->notifications->create(
					                        'Pending loan request Approval ',
					                        ''.$group_members[$loan_object->loan_member_id]->first_name.' '.$group_members[$loan_object->loan_member_id]->last_name.' has requested a loan('.$loan_type->name.') of amount '.$this->ci->group_currency.' '.number_to_currency($loan_object->loan_amount).' please review ',
					                        $this->ci->ion_auth->get_user($group_member->user_id),
					                        $group_member->id,
					                        $members->user_id,
					                        $members->id,
					                        $loan_object->group_id,
					                        'Pending loan request Approval',
						                    'member/loans/pending_loan_request/'.$loan_signatory_success_id,
					                        10,
					                        $loan_signatory_success_id
				                       );
					                }else{
					                	$this->ci->session->set_flashdata('error','Could not send sms notification to group signatories');
					                }
								}else{
									$this->ci->session->set_flashdata('error','Not a valid phone number');
								}

							}else{
								$this->ci->session->set_flashdata('error','could not create group signatory loan request details');
							}
						}
					}
					return TRUE;
				}
			}else if($loan_object->affirmation == 2){
				$update_entries =  array(
	                'is_declined'=>1,
	                'active'=>0,
	                'modified_on' => time(),
	                'modified_by' => $this->user->id
	            );
				if($this->ci->loan_applications_m->update($loan_object->loan_application_id,$update_entries)){
					$notification_success = $this->ci->notifications->create(
	                    'Loan Declined',
	                    'You have declined you loan of amount '.$this->ci->group_currency.' '.number_to_currency($loan_object->loan_amount).' ',
	                    $this->ci->ion_auth->get_user($group_members[$loan_object->loan_member_id]->user_id),                   
	                    $loan_object->loan_member_id,
	                    $group_members[$loan_object->loan_member_id]->user_id,                               
	                    $loan_object->loan_member_id,
	                    $loan_object->group_id,
	                    'View Loan request declined ',
		                'member/loans/view_loan_application/'.$loan_object->loan_application_id,
	                    10,
	                    $loan_object->loan_application_id
	                );
				}else{
					$this->ci->session->set_flashdata('error','could not decline loan');
				}
			}
		}
	}

	function notify_loan_applicant_about_committee_action($loan_particulars = array()){
		if(empty($loan_particulars)){
			$this->ci->session->set_flashdata('error','Loan application details array is empty');
		}else{
			$loan_object = (object)$loan_particulars;
			$group_members = $this->ci->members_m->get_group_members_array($this->ci->group->id);
			$loan_type = $this->ci->loan_types_m->get($loan_object->loan_type_id);
			if($loan_object->signatory_member_id){
				$saccom_committe = 'signatory';
				$member_id = $loan_object->signatory_member_id;
			}else if($loan_object->committee_member_id){
				$saccom_committe = 'committee member';
				$member_id = $loan_object->committee_member_id;
			}
			$notification_success = $this->ci->notifications->create(
                'Loan approval ',
                ''.$group_members[$member_id]->first_name.' '.$group_members[$member_id]->last_name.' a '.$saccom_committe.' of the group has approved your  loan ('.$loan_type->name.')  request of amount '.$this->ci->group_currency.''.number_to_currency($loan_object->loan_amount),
                	$this->ci->ion_auth->get_user($group_members[$member_id]->user_id),                    
                    $member_id,
                    $group_members[$loan_object->loan_member_id]->user_id,                               
                    $loan_object->loan_member_id,
                    $loan_object->group_id,
                'View Loan request approved ',
                'member/loans/view_loan_application/'.$loan_object->loan_application_id,
                10,
                $loan_object->loan_application_id
            );
            if($notification_success){
				$signatories_array = $this->ci->loans_m->get_group_signatories_array($this->ci->group->id,$loan_object->loan_application_id);
				if(in_array(1, $signatories_array)){
					return TRUE;
				}else if(in_array(2, $signatories_array)){
					return TRUE;
				}else if(in_array(4, $signatories_array)){
					return TRUE;
				}else{
					if(count(array_unique($signatories_array)) == 1){

						if($this->create_member_loan($loan_particulars)){
	                        return TRUE;
	                    }else{
	                       return FALSE; 
	                    }

						/*if(valid_phone($group_members[$loan_object->loan_member_id]->phone)){
							$message = $this->ci->sms_m->build_sms_message('signatory-loan-approval-template',array(
					    		'FIRST_NAME'=>$group_members[$loan_object->loan_member_id]->first_name,
					    		'LOAN_TYPE_NAME'=>$loan_type->name,
					    		'GROUP_CURRENCY'=>$this->ci->group_currency,
					    		'LOAN_AMOUNT'=>number_to_currency($loan_object->loan_amount)
					    	));
							$data = array(
				                'sms_to' => valid_phone($group_members[$loan_object->loan_member_id]->phone),
				                'message' => $message,
				                'group_id' => $loan_object->group_id,
				                'member_id' => $member_id,
				                'user_id' => $group_members[$loan_object->loan_member_id]->user_id,
				                'created_on' => time(),
				                'created_by'=> $this->ci->user->id,
				            );
				            if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
				            	//$get_loan_application_details = $this->ci->loan_applications_m->get($loan_application_id);
				            	// notify sacco manager
				            	foreach ($group_members as $key => $group_member) {
						        	if($group_member->organization_role_id == 2 || $group_member->organization_role_id == 4){
						        		if(valid_phone($group_member->phone)){
						        			$sacco_message = $this->ci->sms_m->build_sms_message('sacco-manager-loan-requests-template',array(
									    		'MANAGER_FIRST_NAME'=>$group_member->first_name,
									    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_member_id]->first_name,
									    		'LOAN_TYPE_NAME'=>$loan_type->name,
									    		'GROUP_CURRENCY'=>$this->ci->group_currency,
									    		'LOAN_AMOUNT'=>number_to_currency($loan_object->loan_amount),
									    		'GROUP_NAME'=>$this->ci->group->name,
									    	));

											$sacco_data = array(
								                'sms_to' => valid_phone($group_member->phone),
								                'message' => $sacco_message,
								                'group_id' => $loan_object->group_id,
								                'member_id' => $group_member->id,
								                'user_id' => $group_members[$loan_object->loan_member_id]->user_id,
								                'created_on' => time(),
								                'created_by'=> $this->ci->user->id,
								            );
								            if($sms_sacco_id = $this->ci->sms_m->insert_sms_queue($sacco_data)){
								            	$notification_success = $this->ci->notifications->create(
						                            'Pending Loans',
						                            ''.$group_members[$loan_object->loan_member_id]->first_name.' '.$group_members[$loan_object->loan_member_id]->last_name.' has requested a  loan ('.$loan_type->name.')   of amount '.$this->ci->group_currency.''.number_to_currency($loan_object->loan_amount).' and a has been approved please review',
						                                $this->ci->ion_auth->get_user($group_members[$member_id]->user_id),                 
						                                $member_id,
						                                $group_member->user_id,                               
						                                $group_member->id,
						                                $loan_object->group_id,
						                            'View Loan request',
						                            'member/loans/loan_requests/'.$loan_object->loan_application_id,
						                            10,
						                            $loan_object->loan_application_id
						                        );
								            }else{
								            	$this->ci->session->set_flashdata('error','Could not queue sms for sacco manager');
								            }
						        		}
						        	}
						        }
						        return TRUE;
				            }else{
				            	$this->ci->session->set_flashdata('error','Could not queue sms');
				            }
						}else{
							$this->ci->session->set_flashdata('error','Invalid loan applicant phone no');	
						}*/
					}
				}		
			}else{
				$this->ci->session->set_flashdata('error','Could not create loan applicant notification');
			}
		}
	}

	function create_member_loan($loan_application_particulars = array()){
		if(empty($loan_application_particulars)){
			$this->ci->session->set_flashdata('error','Loan particulars array is empty');
		}else{
			$group_members = $this->ci->members_m->get_group_members_array($this->ci->group->id);
			$loan_details_object = (object)$loan_application_particulars;
			//create loan
			$guarantors = array();  
    	    $get_loan_application_guarantors = $this->ci->loans_m->get_loan_application_guarantorship_requests_by_loan_application_id_array($loan_details_object->group_id,$loan_details_object->loan_application_id);
    	    $loan_type = $this->ci->loan_types_m->get($loan_details_object->loan_type_id);
    	    if($get_loan_application_guarantors){            	               	    
        	    foreach ($get_loan_application_guarantors as $key => $guarantor_details) {
        	     	$guarantor_id[] = $guarantor_details->guarantor_member_id;
        	     	$guaranteed_amount[] = $guarantor_details->amount; 
        	     	if($guarantor_details->approve_comment !=''){
        	     		$guarantor_comment[] = $guarantor_details->approve_comment;
        	     	}else if ($guarantor_details->decline_comment !='') {
        	     		$guarantor_comment[] = $guarantor_details->decline_comment;
        	     	}else{
        	     		$guarantor_comment[] = '';
        	     	}
        	    }
        	    $guarantors=array(
                    'guarantor_id' => $guarantor_id,
                    'guaranteed_amount' =>$guaranteed_amount ,
                    'guarantor_comment' => $guarantor_comment
                );
        	}
        	$repayment_period = $loan_details_object->repayment_period;
    	    $maximum_period =$loan_details_object->repayment_period; 
            $get_loan_application_date =$loan_details_object->loan_application_date; 
            $custom_loan_values = array(
                'date_from' =>$get_loan_application_date,
                'date_to' => strtotime("+".$maximum_period."month", $get_loan_application_date) ,
                'rate' =>  0,
            );
            $loan_details = array(
                'disbursement_date' =>time() ,
                'loan_amount' =>  $loan_details_object->loan_amount,
                'account_id'  =>$loan_details_object->account_id,
                'repayment_period'  =>$repayment_period,
                'interest_rate' =>$loan_type->interest_rate ,
                'loan_interest_rate_per' =>$loan_type->loan_interest_rate_per ,
                'interest_type' =>$loan_type->interest_type  ,
                'custom_interest_procedure'=>0,
                'grace_period'  =>$loan_type->grace_period  ,
                'grace_period_date'  =>''  ,
                'sms_notifications_enabled' =>1 ,
                'sms_template'  => 1 ,
                'email_notifications_enabled' => 1 ,
                'enable_loan_fines' =>$loan_type->enable_loan_fines  ,
                'enable_outstanding_loan_balance_fines'=>$loan_type->enable_outstanding_loan_balance_fines,
                'enable_loan_processing_fee' =>$loan_type->enable_loan_processing_fee,
                'enable_loan_fine_deferment' =>$loan_type->enable_loan_fine_deferment ,
                'enable_loan_guarantors' =>$loan_type->enable_loan_guarantors ,
                'enable_reducing_balance_installment_recalculation' => 1,
                'active'  =>  1,
                'created_by'  =>  $this->ci->user->id,
                'created_on'  =>  time(),
            );
            $loan_name = $loan_details_object->loan_name;
            $member_id = $loan_details_object->loan_member_id;
            $group_id = $loan_details_object->group_id;
            //print_r($loan_name); die();
            $id = $this->ci->loan->create_automated_group_loan($loan_name,$member_id,$group_id,$loan_details,$custom_loan_values,0,$guarantors);
            $this->ci->notifications->create(
                'Approval Success ',
                $group_members[$loan_details_object->loan_member_id]->first_name.' Your   loan ('.$loan_details_object->loan_name.')  request of amount '.number_to_currency($loan_details_object->loan_amount).' has been approved and is now being processed',
               	$this->ci->ion_auth->get_user($this->ci->user->id),                       
                $this->ci->member->id,
                $group_members[$loan_details_object->loan_member_id]->user_id,                               
                $group_members[$loan_details_object->loan_member_id]->id,
                $loan_details_object->group_id,
                'Loan Lisiting',
                'member/members/loan_listing/'.$id,
                10,
                $id
            );            
            return TRUE;
		}
	}

	function decline_member_loan($loan_application_particulars = array()){
		if(empty($loan_application_particulars)){
			$this->ci->session->set_flashdata('error','Loan particulars array is empty');
		}else{
			$group_members = $this->ci->members_m->get_group_members_array($this->ci->group->id);
			$loan_details_object = (object)$loan_application_particulars;
			$loan_type = $this->ci->loan_types_m->get($loan_details_object->loan_type_id);
			if(valid_phone($group_members[$loan_details_object->loan_member_id]->phone)){
				$message = $this->ci->sms_m->build_sms_message('sacco-manager-loan-decline-template',array(
		    		'MANAGER_FIRST_NAME'=>$group_members[$this->ci->member->id]->first_name,
		    		'LOAN_APPLICANT_NAME'=>$group_members[$loan_object->loan_member_id]->first_name,
		    		'LOAN_TYPE_NAME'=>$loan_type->name,
		    		'GROUP_CURRENCY'=>$this->ci->group_currency,
		    		'LOAN_AMOUNT'=>number_to_currency($loan_details_object->loan_amount),
		    		'GROUP_NAME'=>$this->ci->group->name,
		    	));
				$data = array(
	                'sms_to' => valid_phone($group_members[$loan_details_object->loan_member_id]->phone),
	                'message' => $message,
	                'group_id' => $loan_details_object->group_id,
	                'member_id' => $loan_details_object->loan_member_id,
	                'user_id' => $group_members[$loan_details_object->loan_member_id]->user_id,
	                'created_on' => time(),
	                'created_by'=> $this->ci->user->id,
	            );
	            if($sms_sacco_id = $this->ci->sms_m->insert_sms_queue($data)){
	            	$notification_success = $this->ci->notifications->create(
                        'My Loan',
                        ''.$group_members[$this->ci->member->id]->first_name.' '.$group_members[$this->ci->member->id]->last_name.' has declined your  loan ('.$loan_type->name.')   of amount '.$this->ci->group_currency.''.number_to_currency($loan_details_object->loan_amount).' ',
                            $this->ci->ion_auth->get_user($group_members[$this->ci->member->id]->user_id),                 
                            $this->ci->member->id,
                            $group_members[$this->ci->member->id]->user_id,                               
                            $loan_details_object->loan_member_id,
                            $loan_details_object->group_id,
                        'View Loan request',
                        'member/loans/loan_requests/'.$loan_details_object->loan_application_id,
                        10,
                        $loan_details_object->loan_application_id
                    );
                    return TRUE;
	            }else{
	            	$this->ci->session->set_flashdata('error','Could not queue sms for sacco manager');
	            }
			}else{
				$this->ci->session->set_flashdata('warning','Invalid phone no');
			}
		}
	}

	function notify_signatories_of_loan_request_without_guarantors($loan_application_particulars = array()){
		if(empty($loan_application_particulars)){
			$this->ci->session->set_flashdata('error','Loan particulars array is empty');
		}else{
			$loan_application_particulars = (object)$loan_application_particulars;
			if($loan_application_particulars->loan_type_id
				&&$loan_application_particulars->loan_application_id
				&&$loan_application_particulars->group_id
				&&$loan_application_particulars->loan_applicant_member_id
				&&$loan_application_particulars->loan_type_name
				&&$loan_application_particulars->currency
				&&$loan_application_particulars->loan_application_amount
				&&$loan_application_particulars->loan_applicant_user_id){

				$get_group_roles = $this->ci->group_roles_m->get_group_role_options();			        		
		        if(!empty($get_group_roles)){
		        	$get_member_group_roles  = $this->ci->members_m->get_active_group_role_holder_options();
					unset($get_member_group_roles[$loan_application_particulars->loan_applicant_member_id]);				
		        	foreach ($get_member_group_roles as $group_member_id  => $get_group_role_id) {
		        		$get_signatory_applicant_member_details = $this->ci->members_m->get($group_member_id);
		                $loan_signatory_user_id = $get_signatory_applicant_member_details->user_id;
		                $get_loan_signatory_user_details = $this->ci->users_m->get($loan_signatory_user_id);
					    $signatories_phone_no = $get_loan_signatory_user_details->phone;
					    $get_loan_applicant_user_details = $this->ci->users_m->get($loan_application_particulars->loan_applicant_user_id);
					    $signatory_details = array(
					    	'loan_type_id'=>$loan_application_particulars->loan_type_id,
					    	'loan_application_id'=> $loan_application_particulars->loan_application_id,
					    	'loan_request_applicant_user_id'=>$loan_application_particulars->loan_applicant_user_id,
					    	'loan_request_member_id'=>$loan_application_particulars->loan_applicant_member_id,
					    	'signatory_user_id'=>$loan_signatory_user_id,
					    	'loan_signatory_progress_status'=>1,
					    	'signatory_member_id'=>$group_member_id,
					    	'group_id'=>$loan_application_particulars->group_id,
					    	'loan_amount'=>$loan_application_particulars->loan_application_amount,
					    	'active'=>1,
					    	'created_on'=>time(),
					    	'created_by'=>$this->ci->user->id,
					    );
					    $loan_signatory_success_id = $this->ci->loans_m->insert_loan_signatory_requests($signatory_details);
							if($loan_signatory_success_id){
								if(valid_phone($signatories_phone_no)){
									$message = $this->ci->sms_m->build_sms_message('signatories-loan-request-application-without-guarantors',array(
				                		'SIGNATORY'=>$get_loan_signatory_user_details->first_name,
				                		'LOAN_APPLICANT_NAME'=>$get_loan_applicant_user_details->first_name,
				                		'LOAN_TYPE_NAME'=>$loan_application_particulars->loan_type_name,
				                		'GROUP_CURRENCY'=>$loan_application_particulars->currency,
				                		'LOAN_REQUEST_AMOUNT'=>number_to_currency($loan_application_particulars->loan_application_amount),
				                	));
				                	$data = array(
					                    'sms_to' => valid_phone($signatories_phone_no),
					                    'message' => $message,
					                    'group_id' =>$loan_application_particulars->group_id,
					                    'member_id' => $group_member_id,
					                    'user_id' => $loan_signatory_user_id,
					                    'created_on' => time(),
					                    'created_by'=> $loan_application_particulars->loan_applicant_user_id
					                );
					                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
					                	$signatory_success = $this->ci->notifications->create(
					                        'Pending loan request Approval ',
					                        ''.$get_loan_applicant_user_details->first_name.' '.$get_loan_applicant_user_details->last_name.' has requested a loan('.$loan_application_particulars->loan_type_name.') of amount '.$loan_application_particulars->currency.' '.number_to_currency($loan_application_particulars->loan_application_amount).' please review ',
					                        $this->ci->ion_auth->get_user($loan_application_particulars->loan_applicant_user_id),
					                        $loan_application_particulars->loan_applicant_member_id,
					                        $loan_signatory_user_id,
					                        $group_member_id,
					                        $loan_application_particulars->group_id,
					                        'Pending loan request Approval ',
					                        'member/loans/loan_requests/'.$loan_signatory_success_id,
					                        10,
					                        $loan_signatory_success_id
				                       );
					                }else{
					                	$this->ci->session->set_flashdata('error','Could not send sms notification to group signatories');
					                }
								}else{
									$this->ci->session->set_flashdata('error','Not a valid phone number');
								}

							}else{
								$this->ci->session->set_flashdata('error','could not create group signatory loan request details');
							}
		        	}
		        	$this->ci->session->set_flashdata('Success','Loan Signatories informed of loan application');
		        	return TRUE;
		        }else{
		        	$this->ci->session->set_flashdata('error','could not get group roles');
		        }

			}else{
				$this->ci->session->set_flashdata('error','Some required paramaters missing');
			}
		}

	}

	function notify_loan_signatories_to_process_loan($loan_details_array= array(),$signatory_details = array()){
		$loan_details_object = (object) $loan_details_array;
		$signatory_details_object =  (object)$signatory_details;
		if($loan_details_object->loan_type_id
			&&$loan_details_object->loan_application_id
			&&$loan_details_object->group_id
			&&$loan_details_object->loan_applicant_member_id
			&&$loan_details_object->loan_type_name
			&&$loan_details_object->currency
			&&$loan_details_object->loan_amount
			&&$loan_details_object->loan_applicant_user_id){
			$signatory_details = array(
				'loan_type_id'=>$signatory_details_object->loan_type_id,
		    	'loan_application_id'=>$signatory_details_object->loan_application_id,
		    	'loan_request_applicant_user_id'=>$signatory_details_object->loan_applicant_user_id,
		    	'loan_request_member_id'=>$signatory_details_object->loan_request_member_id,
		    	'signatory_user_id'=>$signatory_details_object->signatory_user_id,
		    	'loan_signatory_progress_status'=>1,
		    	'signatory_member_id'=>$signatory_details_object->signatory_member_id,
		    	'group_id'=>$signatory_details_object->group_id,
		    	'loan_amount'=>$signatory_details_object->loan_amount,
		    	'active'=>1,
		    	'created_on'=>time(),
		    	'created_by'=>$this->ci->user->id,
			);
			$loan_signatory_success_id =$this->ci->loans_m->insert_loan_signatory_requests($signatory_details);
		    if($loan_signatory_success_id){
		    	if(valid_phone($signatory_details_object->signatory_phone)){
			    	$message = $this->ci->sms_m->build_sms_message('signatories-loan-request-application',array(
		        		'SIGNATORY'=>$signatory_details_object->first_name,
		        		'LOAN_APPLICANT_NAME'=>$loan_details_object->loan_applicant_name,
		        		'LOAN_TYPE_NAME'=>$loan_details_object->loan_type_name,
		        		'GROUP_CURRENCY'=>$loan_details_object->currency,
		        		'LOAN_REQUEST_AMOUNT'=> number_to_currency($loan_details_object->loan_amount)
		        	));
	        	$data = array(
	                'sms_to' => valid_phone($signatory_details_object->signatory_phone),
	                'message' => $message,
	                'group_id' => $loan_details_object->group_id,
	                'member_id' => $signatory_details_object->signatory_member_id,
	                'user_id' => $signatory_details_object->signatory_user_id,
	                'created_on' => time(),
	                'created_by'=> $signatory_details_object->signatory_user_id
	            );
	            $sms_id = $this->ci->sms_m->insert_sms_queue($data);
		            if($sms_id){
		            	$signatory_success = $this->ci->notifications->create(
	                        'Pending loan request Approval ',
	                        ''.$loan_details_object->loan_applicant_name.' has requested a loan('.$loan_details_object->loan_type_name.') of amount '.$loan_details_object->currency.' '.number_to_currency($loan_details_object->loan_amount).' please review ',
	                        $this->ci->ion_auth->get_user($loan_details_object->loan_applicant_user_id),
	                        $loan_details_object->loan_applicant_member_id,
	                        $signatory_details_object->signatory_user_id,
	                        $signatory_details_object->signatory_member_id,
	                        $loan_details_object->group_id,
	                        'Pending loan request Approval ',
	                        'member/members/pending_loan_approval_request/'.$loan_signatory_success_id,
	                        10,
	                        $loan_signatory_success_id
	                       );
		            	if($signatory_success){
		            		if(valid_phone($loan_details_object->loan_applicant_phone)){
		            			$loan_applicant_message = $this->ci->sms_m->build_sms_message('notify-loan-applicant-about-bank-approval',array(
					        		'LOAN_APPLICANT_NAME'=>$loan_details_object->loan_applicant_name,
					        		'LOAN_TYPE_NAME'=>$loan_details_object->loan_type_name,
					        		'GROUP_CURRENCY'=>$loan_details_object->currency,
					        		'LOAN_REQUEST_AMOUNT'=> number_to_currency($loan_details_object->loan_amount)
					        	));
					        	$message_data = array(
					                'sms_to' => valid_phone($loan_details_object->loan_applicant_phone),
					                'message' => $loan_applicant_message,
					                'group_id' => $loan_details_object->group_id,
					                'member_id' => $loan_details_object->loan_applicant_member_id,
					                'user_id' => $loan_details_object->loan_application_id,
					                'created_on' => time(),
					                'created_by'=> $signatory_details_object->signatory_user_id
					            );
					            $sms_id = $this->ci->sms_m->insert_sms_queue($message_data);


		            		}else{
		            		   $this->ci->session->set_flashdata('error','Invalid loan applicant phone number');
			                   return FALSE;
		            		}
		            	}
		            }else{
		            	$this->ci->session->set_flashdata('error','Could not create sms');
			            return FALSE;
		            }	            
	            }else{
	            	$this->ci->session->set_flashdata('error','Invalid signatory phone no');
			        return FALSE;
	            }
	        }else{
	        	$this->ci->session->set_flashdata('error','Could not create loan signatory details');
			    return FALSE;
	        }

		}else{
			$this->ci->session->set_flashdata('error','loan request details not found');
			return FALSE;
		}
	}

	function notify_loan_applicant_of_bank_loan_decline($loan_details_array = array()){
		$loan_details_object =  (object)$loan_details_array;
		if($loan_details_object){
			if(valid_phone($loan_details_object->loan_applicant_phone)){
				$loan_applicant_message = $this->ci->sms_m->build_sms_message('notify-loan-applicant-about-bank-decline',array(
		        		'LOAN_APPLICANT_NAME'=>$loan_details_object->loan_applicant_name,
		        		'LOAN_TYPE_NAME'=>$loan_details_object->loan_type_name,
		        		'GROUP_CURRENCY'=>$loan_details_object->currency,
		        		'LOAN_REQUEST_AMOUNT'=> number_to_currency($loan_details_object->loan_amount)
		        	));
		        	$message_data = array(
		                'sms_to' => valid_phone($loan_details_object->loan_applicant_phone),
		                'message' => $loan_applicant_message,
		                'group_id' => $loan_details_object->group_id,
		                'member_id' => $loan_details_object->loan_applicant_member_id,
		                'user_id' => $loan_details_object->loan_application_id,
		                'created_on' => time(),
		                'created_by'=> $loan_details_object->loan_applicant_user_id
		            );
		            $sms_id = $this->ci->sms_m->insert_sms_queue($message_data);
            }else{
            	$this->ci->session->set_flashdata('error','Invalid phone no');
            }

		}else{
			$this->ci->session->set_flashdata('error','Loan details  is empty');
		}
	}

	function  loan_application_success_notification($loan_type_id=0,$loan_application_id=0,$group_id=0,$loan_applicant_member_id=0,$loan_type_name='',$currency='KES',$loan_amount=0,$guarantor_user_id=0,$loan_applicant_user_id=0){
		$get_loan_applicant_user_details = $this->ci->users_m->get($loan_applicant_user_id);
		$get_loan_type_details = $this->ci->loan_types_m->get($loan_type_id);
		 $loan_applicant_phone_no = $get_loan_applicant_user_details->phone;
	    $message = $this->ci->sms_m->build_sms_message('signatory-loan-approval-template',array(
	    		'FIRST_NAME'=>$get_loan_applicant_user_details->first_name,
	    		'LOAN_TYPE_NAME'=>$get_loan_type_details->name,
	    		'GROUP_CURRENCY'=>$currency,
	    		'LOAN_AMOUNT'=>number_to_currency($loan_amount)
	    	 ));
			$data = array(
                'sms_to' => valid_phone($loan_applicant_phone_no),
                'message' => $message,
                'group_id' => $group_id,
                'member_id' => $loan_applicant_member_id,
                'user_id' => $loan_applicant_user_id,
                'created_on' => time(),
                'created_by'=>$this->ci->user->id,
            );	           
        if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){            
	        $get_loan_application_details = $this->ci->loan_applications_m->get($loan_application_id);
	        $get_loan_application_guarantors = $this->ci->loans_m->get_loan_application_guarantorship_requests_by_loan_application_id($loan_application_id,$group_id);            	    
		    foreach ($get_loan_application_guarantors as $key => $guarantor_details) {
		     	$guarantor_id[] = $guarantor_details->guarantor_member_id;
		     	$guaranteed_amount[] = $guarantor_details->amount; 
		     	if($guarantor_details->approve_comment !=''){
		     		$guarantor_comment[] = $guarantor_details->approve_comment;
		     	}else if ($guarantor_details->decline_comment !='') {
		     		$guarantor_comment[] = $guarantor_details->decline_comment;
		     	}
		    }
		    $guarantors=array(
		            'guarantor_id' => $guarantor_id,
		            'guaranteed_amount' =>$guaranteed_amount ,
		            'guarantor_comment' => $guarantor_comment
		        );
		    $repayment_period = $get_loan_application_details->repayment_period;
		    $maximum_period =$get_loan_type_details->maximum_repayment_period; 
		    $get_loan_application_date =$get_loan_application_details->created_on;
		    $get_group_owner_id =  $this->ci->group->owner; 
		      //$get_group_owner_details = $this->ci->users_m->get($get_group_owner_id);
		    $get_group_owner_member_bank_account = $this->ci->bank_accounts_m->get_group_bank_id($get_group_owner_id,$group_id);
		    $custom_loan_values = array(
		        'date_from' =>$get_loan_application_details->created_on,
		        'date_to' => strtotime("+".$maximum_period."month", $get_loan_application_date) ,
		        'rate' =>  0,
		       );
		    $loan_details = array(
		            'disbursement_date' =>time() ,
		            'loan_amount'   =>  $get_loan_application_details->loan_amount,
		            'account_id'    =>$get_group_owner_member_bank_account->id,
		            'repayment_period'  =>$repayment_period,
		            'interest_rate' =>$get_loan_type_details->interest_rate ,
		            'loan_interest_rate_per' =>$get_loan_type_details->loan_interest_rate_per ,
		            'interest_type' =>$get_loan_type_details->interest_type  ,
		            'custom_interest_procedure'=>0,
		            'grace_period'  =>$get_loan_type_details->grace_period  ,
		            'grace_period_date'  =>strtotime("+".$get_loan_type_details->grace_period." month", $get_loan_application_details->created_on)  ,
		            'sms_notifications_enabled' =>1 ,
		            'sms_template'  => 1 ,
		            'email_notifications_enabled' => 1 ,
		            'enable_loan_fines' =>$get_loan_type_details->enable_loan_fines  ,
		            'enable_outstanding_loan_balance_fines'=>$get_loan_type_details->enable_outstanding_loan_balance_fines,
		            'enable_loan_processing_fee' =>$get_loan_type_details->enable_loan_processing_fee,
		            'enable_loan_fine_deferment' =>$get_loan_type_details->enable_loan_fine_deferment ,
		            'enable_loan_guarantors' =>$get_loan_type_details->enable_loan_guarantors ,
		            'enable_reducing_balance_installment_recalculation' => 1,
		            'active'    =>  1,
		            'created_by'    =>  $this->ci->user->id,
		            'created_on'    =>  time(),
		        );
		    $get_loan_applicant_user_details = $this->ci->users_m->get($loan_applicant_id);
		       $id = $this->ci->loan->create_automated_group_loan($get_loan_type_details->name,$loan_applicant_member_id,$group_id,$loan_details,$custom_loan_values,0,$guarantors);	       
	               if($id){
		                  $this->ci->notifications->create(
		                    'My Loan Listing Signatory approval ',
		                    $get_loan_applicant_user_details->first_name.' Your   loan ('.$get_loan_type_details->name.')  request of amount '.number_to_currency($loan_amount).' has been approved and is now being processed',
		                   	$this->ci->ion_auth->get_user($signatory_user_id),                               
		                    $signatory_member_id,
		                    $loan_applicant_id,                               
		                    $loan_applicant_member_id,
		                     $group_id,
		                    'Loan Lisiting',
		                    'member/members/loan_listing/'.$id,
		                    10,
		                    $id
		                 );
	               	     $this->ci->session->set_flashdata('success','You have agreed to approve '.$get_loan_applicant_user_details->first_name.' '.$get_loan_applicant_user_details->last_name.' loan( '.$get_loan_type_details->name.') of  '.$currency.' '.number_to_currency($loan_amount).' as a signatory of the group');        	     
	        	       redirect('member/loans/signatory_approvals_listing');
	               }else{
	               	//
	               }
	            }else{
	            	$this->ci->session->set_flashdata('error','Loan application failed: Cannot ques sms');
	            }		 
	}

	function notify_loan_applicant_of_signatory_action($signatory_array= array(),$loan_applicant_array= array(),$loan_details_array=array() ,$supervisor_details_array = array()){
		$loan_details_object = (object)$loan_details_array;
		$signatory_object = (object)$signatory_array;
		$loan_applicant_object = (object)$loan_applicant_array;
		if($loan_details_object){
			if($signatory_object){
				if($loan_applicant_object){ 
					if(valid_phone($loan_applicant_object->phone_no)){	

						if($loan_details_object->action =='approve'){
							$action = 'approved';
							$message_loan_applicant = $this->ci->sms_m->build_sms_message('signatory-loan-approval-template',array(
					    		'FIRST_NAME'=>$loan_applicant_object->first_name,
					    		'LOAN_TYPE_NAME'=>$loan_details_object->loan_name,
					    		'GROUP_CURRENCY'=>$loan_details_object->currency,
					    		'LOAN_AMOUNT'=>number_to_currency($loan_details_object->loan_amount)
					    	));
						}else if($loan_details_object->action =='decline'){
							$message_loan_applicant = $this->ci->sms_m->build_sms_message('signatory-loan-decline-template',array(
					    		'FIRST_NAME'=>$loan_applicant_object->first_name,
					    		'LOAN_TYPE_NAME'=>$loan_details_object->loan_name,
					    		'GROUP_CURRENCY'=>$loan_details_object->currency,
					    		'LOAN_AMOUNT'=>number_to_currency($loan_details_object->loan_amount)
					    	));
					    	$action = 'declined';
						}
				    	$data = array(
		                    'sms_to' => valid_phone($loan_applicant_object->phone_no),
		                    'message' => $message_loan_applicant,
		                    'group_id' => $loan_details_object->group_id,
		                    'member_id' => $loan_applicant_object->loan_applicant_member_id,
		                    'user_id' => $loan_applicant_object->loan_applicant_user_id,
		                    'created_on' => time(),
		                    'created_by'=> $signatory_object->signatory_user_id
		                );
		                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
		                	$notification_success =  $this->ci->notifications->create(
			                    'Loan approval ',
			                    $loan_applicant_object->first_name.' Your   loan ('.$loan_details_object->loan_name.')  request of amount '.number_to_currency($loan_details_object->loan_amount).' has been '.$action.' by '.$signatory_object->first_name.' a group signatory',
			                   	$this->ci->ion_auth->get_user($signatory_object->signatory_user_id),                               
			                    $signatory_object->signatory_member_id,
			                    $loan_applicant_object->loan_applicant_user_id,                               
			                    $loan_applicant_object->loan_applicant_member_id,
			                    $loan_details_object->group_id,
			                    'My Loan Lisiting',
			                    'member/members/loan_listing/'.$loan_details_object->loan_application_id,
			                    10,
			                    $loan_details_object->loan_application_id
			                );
			                if($notification_success){
			                	if($loan_details_object->action =='approve'){	
			                		$progress_array =  $loan_details_object->loan_request_status;
			                		if(in_array(2, $progress_array)){			                			
			                			//signatory has  declined don't send notification
			                			return TRUE;
			                		}else{			                			
			                			if(in_array(1, $progress_array)){
			                				//no signatory action don't send notification
			                				return TRUE;
										}else{
											if(count(array_count_values($progress_array)) == 1){
												//create loan
												if($loan_details_object){
													$input_application = array(
								                        'is_approved'=>1,
								                        'modified_by'=>$this->ci->user->id,
								                        'modified_on'=>time()
								                    );
								                    $this->ci->loan_applications_m->update($loan_details_object->loan_application_id,$input_application);
												}

												$guarantors = array(); 
							            	    $get_loan_application_guarantors = $this->ci->loans_m->get_loan_application_guarantorship_requests_by_loan_application_id_array($loan_details_object->loan_application_id,$loan_details_object->group_id);
							            	    $loan_type = $this->ci->loan_types_m->get($loan_details_object->loan_type_id);
							            	    if($get_loan_application_guarantors){            	               	    
								            	    foreach ($get_loan_application_guarantors as $key => $guarantor_details) {
								            	     	$guarantor_id[] = $guarantor_details->guarantor_member_id;
								            	     	$guaranteed_amount[] = $guarantor_details->amount; 
								            	     	$guarantor_comment[] = $guarantor_details->comment;
								            	    }
								            	    $guarantors=array(
								                        'guarantor_id' => $guarantor_id,
								                        'guaranteed_amount' =>$guaranteed_amount ,
								                        'guarantor_comment' => $guarantor_comment
								                    );
								            	}
								            	$repayment_period = $loan_details_object->repayment_period;
							            	    $maximum_period =$loan_details_object->repayment_period; 
								                $get_loan_application_date =$loan_details_object->loan_application_date; 
								                $custom_loan_values = array(
							                        'date_from' =>$get_loan_application_date,
							                        'date_to' => strtotime("+".$maximum_period."month", $get_loan_application_date) ,
							                        'rate' =>  0,
							                    );
							                    $loan_details = array(
									                'disbursement_date' =>time() ,
									                'loan_amount'   =>  $loan_details_object->loan_amount,
									                'account_id'    =>$loan_details_object->account_id,
									                'repayment_period'  =>$repayment_period,
									                'interest_rate' =>$loan_type->interest_rate ,
									                'loan_interest_rate_per' =>$loan_type->loan_interest_rate_per ,
									                'interest_type' =>$loan_type->interest_type  ,
									                'custom_interest_procedure'=>0,
									                'grace_period'  =>$loan_type->grace_period  ,
									                'grace_period_date'  =>''  ,
									                'sms_notifications_enabled' =>1 ,
									                'sms_template'  => 1 ,
									                'email_notifications_enabled' => 1 ,
									                'enable_loan_fines' =>$loan_type->enable_loan_fines  ,
									                'enable_outstanding_loan_balance_fines'=>$loan_type->enable_outstanding_loan_balance_fines,
									                'enable_loan_processing_fee' =>$loan_type->enable_loan_processing_fee,
									                'enable_loan_fine_deferment' =>$loan_type->enable_loan_fine_deferment ,
									                'enable_loan_guarantors' =>$loan_type->enable_loan_guarantors ,
									                'enable_reducing_balance_installment_recalculation' => 1,
									                'active'  =>  1,
									                'created_by'  =>  $this->ci->user->id,
									                'created_on'  =>  time(),
									            );
									            $id = $this->ci->loan->create_automated_group_loan($loan_details_object->loan_name,$loan_applicant_object->loan_applicant_member_id,$loan_details_object->group_id,$loan_details,$custom_loan_values,0,$guarantors);
								                $this->ci->notifications->create(
								                    'My Loan Listing Signatory approval ',
								                    $loan_applicant_object->first_name.' Your   loan ('.$loan_details_object->loan_name.')  request of amount '.number_to_currency($loan_details_object->loan_amount).' has been approved and is now being processed',
								                   	$this->ci->ion_auth->get_user($signatory_object->signatory_user_id),                       
								                    $signatory_object->signatory_member_id,
								                    $loan_applicant_object->loan_applicant_user_id,                               
								                    $loan_applicant_object->loan_applicant_member_id,
								                    $loan_details_object->group_id,
								                    'Loan Lisiting',
								                    'member/members/loan_listing/'.$id,
								                    10,
								                    $id
								                );
								                return TRUE;
											}
										}
			                		}
		                        }else{
		                        	return TRUE;
		                        }
			                }else{
			                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create notiication');
			                	return FALSE;	
			                }		                	
		                }else{
		                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create sms queue');
		                	return FALSE;
		                }
					}else{
						$this->ci->session->set_flashdata('error','Loan application failed: Cannot not a valid phone number ');
						return FALSE;
					}	                
				}else{
					$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan applicant  details');
					return FALSE;
				}
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot get signatory  details');
				return FALSE;	
			}
		}else{
			$this->ci->session->set_flashdata('error','Loan application failed: Cannot get loan  details');
			return FALSE;	
		}

	}

	function notify_loan_applicant_about_signatory_action($loan_signatory_id=0,$group_id=0,$loan_applicant_id=0,$loan_type_id=0,$signatory_member_id=0,$currency='KES',$loan_applicant_member_id=0,$loan_application_id=0,$signatory_user_id=0,$action='',$loan_amount=0){	
		$get_loan_type_details = $this->ci->loan_types_m->get($loan_type_id,$group_id);
		$get_signatory_user_details = $this->ci->users_m->get($signatory_user_id);
		$get_loan_applicant_user_details = $this->ci->users_m->get($loan_applicant_id);
		if($action == 'approve'){
           //get_all signatory_request
			$get_all_signatories_loan_request = $this->ci->loans_m->get_all_signatory_requests($loan_type_id,$loan_application_id,$group_id);
			if(!empty($get_all_signatories_loan_request)){
				foreach ($get_all_signatories_loan_request as $key => $signatory_details) {
	               $loan_signatory_progress_status[] = $signatory_details->loan_signatory_progress_status;                           
	            }
	            $loan_applicant_phone_no = $get_loan_applicant_user_details->phone;       
	            if(count(array_unique($loan_signatory_progress_status)) == 1){
	            	$message = $this->ci->sms_m->build_sms_message('signatory-loan-approval-template',array(
			    		'FIRST_NAME'=>$get_loan_applicant_user_details->first_name,
			    		'LOAN_TYPE_NAME'=>$get_loan_type_details->name,
			    		'GROUP_CURRENCY'=>$currency,
			    		'LOAN_AMOUNT'=>number_to_currency($loan_amount)
			    	));
					$data = array(
		                'sms_to' => valid_phone($loan_applicant_phone_no),
		                'message' => $message,
		                'group_id' => $group_id,
		                'member_id' => $loan_applicant_member_id,
		                'user_id' => $loan_applicant_id,
		                'created_on' => time(),
		                'created_by'=> $signatory_user_id,
		            );	           
	            if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){	 
	                $guarantors = array();      	 
	                $get_loan_type_details = $this->ci->loan_types_m->get($loan_type_id);
            	    $get_loan_application_details = $this->ci->loan_applications_m->get($loan_application_id);
            	    $get_loan_application_guarantors = $this->ci->loans_m->get_loan_application_guarantorship_requests_by_loan_application_id($loan_application_id,$group_id);
            	    if($get_loan_application_guarantors){            	               	    
	            	    foreach ($get_loan_application_guarantors as $key => $guarantor_details) {
	            	     	$guarantor_id[] = $guarantor_details->guarantor_member_id;
	            	     	$guaranteed_amount[] = $guarantor_details->amount; 
	            	     	if($guarantor_details->approve_comment !=''){
	            	     		$guarantor_comment[] = $guarantor_details->approve_comment;
	            	     	}else if ($guarantor_details->decline_comment !='') {
	            	     		$guarantor_comment[] = $guarantor_details->decline_comment;
	            	     	}
	            	    }
	            	    $guarantors=array(
	                        'guarantor_id' => $guarantor_id,
	                        'guaranteed_amount' =>$guaranteed_amount ,
	                        'guarantor_comment' => $guarantor_comment
	                    );
	            	}
            	    
            	      $repayment_period = $get_loan_application_details->repayment_period;
            	      $maximum_period =$get_loan_type_details->maximum_repayment_period; 
	                  $get_loan_application_date =$get_loan_application_details->created_on; 
	                  $custom_loan_values = array(
                        'date_from' =>$get_loan_application_details->created_on,
                        'date_to' => strtotime("+".$maximum_period."month", $get_loan_application_date) ,
                        'rate' =>  0,
                       );
	                 $loan_details = array(
			                'disbursement_date' =>time() ,
			                'loan_amount'   =>  $get_loan_application_details->loan_amount,
			                'account_id'    =>$get_loan_application_details->account_id,
			                'repayment_period'  =>$repayment_period,
			                'interest_rate' =>$get_loan_type_details->interest_rate ,
			                'loan_interest_rate_per' =>$get_loan_type_details->loan_interest_rate_per ,
			                'interest_type' =>$get_loan_type_details->interest_type  ,
			                'custom_interest_procedure'=>0,
			                'grace_period'  =>$get_loan_type_details->grace_period  ,
			                'grace_period_date'  =>strtotime("+".$get_loan_type_details->grace_period." month", $get_loan_application_details->created_on)  ,
			                'sms_notifications_enabled' =>1 ,
			                'sms_template'  => 1 ,
			                'email_notifications_enabled' => 1 ,
			                'enable_loan_fines' =>$get_loan_type_details->enable_loan_fines  ,
			                'enable_outstanding_loan_balance_fines'=>$get_loan_type_details->enable_outstanding_loan_balance_fines,
			                'enable_loan_processing_fee' =>$get_loan_type_details->enable_loan_processing_fee,
			                'enable_loan_fine_deferment' =>$get_loan_type_details->enable_loan_fine_deferment ,
			                'enable_loan_guarantors' =>$get_loan_type_details->enable_loan_guarantors ,
			                'enable_reducing_balance_installment_recalculation' => 1,
			                'active'    =>  1,
			                'created_by'    =>  $this->ci->user->id,
			                'created_on'    =>  time(),
			            );
	                    $id = $this->ci->loan->create_automated_group_loan($get_loan_type_details->name,$loan_applicant_member_id,$group_id,$loan_details,$custom_loan_values,0,$guarantors);
	                    if($id){
			                $this->ci->notifications->create(
			                    'My Loan Listing Signatory approval ',
			                    $get_loan_applicant_user_details->first_name.' Your   loan ('.$get_loan_type_details->name.')  request of amount '.number_to_currency($loan_amount).' has been approved and is now being processed',
			                   	$this->ci->ion_auth->get_user($signatory_user_id),                               
			                    $signatory_member_id,
			                    $loan_applicant_id,                               
			                    $loan_applicant_member_id,
			                     $group_id,
			                    'Loan Lisiting',
			                    'member/members/loan_listing/'.$id,
			                    10,
			                    $id
			                );
	                   	    $this->ci->session->set_flashdata('success','You have agreed to approve '.$get_loan_applicant_user_details->first_name.' '.$get_loan_applicant_user_details->last_name.' loan( '.$get_loan_type_details->name.') of  '.$currency.' '.number_to_currency($loan_amount).' as a signatory of the group');	            	     
	            	        redirect('member/loans/signatory_approvals_listing');
	                   }else{
	                   	//
	                   }
	                           	
	              }else{
	               $this->ci->session->set_flashdata('error','Loan application failed: Cannot ques sms');
	             }		                  
	            }else{
	            	//update notification of signatory activity
	            	if($get_loan_applicant_user_details){        		
				        
				        if(valid_phone($loan_applicant_phone_no)){
				        	$notification_success = $this->ci->notifications->create(
			                    'Loan Signatory approval ',
			                    ''.$get_signatory_user_details->first_name.' '.$get_signatory_user_details->last_name.' a signatory of the group has approved your  loan ('.$get_loan_type_details->name.')  request of amount '.$currency.''.number_to_currency($loan_amount),
			                    	$this->ci->ion_auth->get_user($signatory_user_id),                               
				                    $signatory_member_id,
				                    $loan_applicant_id,                               
				                    $loan_applicant_member_id,
				                    $group_id,
			                    'View Loan request approved ',
			                    'member/members/signatory_approved_loans/'.$loan_signatory_id,
			                    10,
			                    $loan_signatory_id
			                );
			                if($notification_success){
			                	$this->ci->session->set_flashdata('success','You have declined to approve '.$get_loan_applicant_user_details->first_name.' '.$get_loan_applicant_user_details->last_name.' loan( '.$get_loan_type_details->name.') of  '.$currency.' '.number_to_currency($loan_amount).' as a signatory of the group');
                                redirect('member/loans/signatory_approvals_listing');
			                }else{
			                	$this->ci->session->set_flashdata('error','Failed to create signatory notifications');
			                }
				        }else{
				        	$this->ci->session->set_flashdata('error',' Cannot get a valid phone number ');
				        }
	            	}else{
	            		$this->ci->session->set_flashdata('error',' Cannot get loan applicant details');
	            	}
	            	//redirect('member/loans/signatory_approvals_listing');
	            	//$this->ci->session->set_flashdata('error','Error ocured sending notification to loan applicant');
	            }
			}else{
				$this->ci->session->set_flashdata('error','Signatory details is unavailable');
			}//new update

		}elseif ($action == 'decline') {
		  // create sms and notification
			if(valid_phone($get_loan_applicant_user_details->phone)){
				    $message = $this->ci->sms_m->build_sms_message('signatory-loan-decline-template',array(
			    		'FIRST_NAME'=>$get_loan_applicant_user_details->first_name,
			    		'LOAN_TYPE_NAME'=>$get_loan_type_details->name,
			    		'LOAN_SIGNATORY_NAME'=>$this->ci->users_m->get($this->ci->user->id)->first_name,
			    		'GROUP_CURRENCY'=>$currency,
			    		'LOAN_AMOUNT'=>number_to_currency($loan_amount)
			    	));
					$data = array(
		                'sms_to' => valid_phone($get_loan_applicant_user_details->phone),
		                'message' => $message,
		                'group_id' => $group_id,
		                'member_id' => $loan_applicant_member_id,
		                'user_id' => $loan_applicant_id,
		                'created_on' => time(),
		                'created_by'=> $signatory_user_id,
		            );
                if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
	                $this->ci->notifications->create(
	                    'My Loan Listing Signatory approval ',
	                    $get_loan_applicant_user_details->first_name.' Your   loan ('.$get_loan_type_details->name.')  request of amount '.number_to_currency($loan_amount).' has been declined',
	                   	$this->ci->ion_auth->get_user($signatory_user_id),                               
	                    $signatory_member_id,
	                    $loan_applicant_id,                               
	                    $loan_applicant_member_id,
	                    $group_id,
	                    'Loan Lisiting',
	                    'member/loans/loan_guarantor_listing',
	                    10,
	                    0
	                );
	                    $this->ci->session->set_flashdata('success','You have declined to approve '.$get_loan_applicant_user_details->first_name.' '.$get_loan_applicant_user_details->last_name.' loan( '.$get_loan_type_details->name.') of  '.$currency.' '.number_to_currency($loan_amount).' as a signatory of the group');	            	     
	            	    redirect('member/loans/signatory_approvals_listing');
                }else{
                	$this->ci->session->set_flashdata('error','Loan application failed: Cannot create sms queue');
                }
			}else{
				$this->ci->session->set_flashdata('error','Loan application failed: Cannot not a valid phone number ');
			}
		}
	}



	function notify_member_about_loan_application($loan_application_id=0,$currency=''){
		if($loan_application_id){
			$loan_application = $this->ci->loan_applications_m->get($loan_application_id);
			$message = '';
			if($loan_application){
				$member = $this->ci->members_m->get_group_member($loan_application->member_id,$loan_application->group_id);
				if($loan_application->status == 2){	
					$message = 'Dear '.$member->first_name.', your '.$loan_application->name.' application of '.$currency.' .'.number_to_currency($loan_application->loan_amount).' is being reviewed. Kindly await further communication. Thank you for using  '.$this->application_settings->application_name;;

				}else if($loan_application->status == 3){
					if($loan_application->is_approved){
						$message = 'Dear '.$member->first_name.', your '.$loan_application->name.' application of '.$currency.' .'.number_to_currency($loan_application->loan_amount).' has been accepted. Await atleast 10 minutes for amount to be disbursed to your account. Thank you for using  '.$this->application_settings->application_name;
					}					
				}else if($loan_application->status == 4){
					if($loan_application->is_declined){
						$message = 'Dear '.$member->first_name.', your '.$loan_application->name.' application of '.$currency.' .'.number_to_currency($loan_application->loan_amount).' has been declined. Login to '.$this->application_settings->application_name.' to view reason';
					}
				}

				if(valid_phone($member->phone)){
						$data = array(
		                    'sms_to' => $member->phone,
		                    'system_sms' => 1,
		                    'message' => $message,
		                    'group_id' => $member->group_id,
		                    'member_id' => $member->id,
		                    'user_id' => $member->user_id,
		                    'created_on' => time(),
		                    'created_by'=> 0
		                );

		            if($sms_id = $this->ci->sms_m->insert_sms_queue($data)){
		                
		            }else{
						
		            }
				}
				if(valid_email($member->email)){

				}
			}else{
				$this->ci->session->set_flashdata('error','Loan application not found');
			}
		}else{
			$this->ci->session->set_flashdata('error','Couldn\'nt send notification to member');
		}
	}

	/* to be cleaned up  end*/

	function send_sms_delivery_toggle_email($sms_delivery_enabled = FALSE){
		$email_data = array(
			
		);
		if($sms_delivery_enabled){
			$email_message = $this->ci->emails_m->build_email_message('sms-delivery-enabled-notification',$email_data);
	        $data = array(
	            'email_to' => 'peter.kimutai@digitalvision.co.ke',
	            'subject' => ' SMS delivery for '.$this->application_settings->application_name.' has been enabled, thank you for topping up  ',
	            'message' => $email_message,
	            'email_from' => '',
	            //'group_id' => $group->id,
	            'cc' => 'martin@digitalvision.co.ke,ongidigeofrey@gmail.com,lucy.muthoni@digitalvision.co.ke',
	            //'member_id' => $member->id,
	            //'user_id' => $member->user_id,
	            'created_on' => time()
	        );
	        if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	            return TRUE;
	        }else{
	            return FALSE;
	        }
		}else{
			$email_message = $this->ci->emails_m->build_email_message('sms-delivery-disabled-notification',$email_data);
	        $data = array(
	            'email_to' => 'peter.kimutai@digitalvision.co.ke',
	            'subject' => ' SMS delivery for '.$this->application_settings->application_name.' has been disabled due to insufficient Infobip balance  ',
	            'message' => $email_message,
	            'email_from' => '',
	            //'group_id' => $group->id,
	            'cc' => 'martin@digitalvision.co.ke,ongidigeofrey@gmail.com,lucy.muthoni@digitalvision.co.ke',
	            //'member_id' => $member->id,
	            //'user_id' => $member->user_id,
	            'created_on' => time()
	        );
	        if($email_id = $this->ci->emails_m->insert_email_queue($data)){
	            return TRUE;
	        }else{
	            return FALSE;
	        }
		}
	}

	public function send_user_reset_password_code($user=array(),$code=0){
		$success = TRUE;
		if($user && $code){
			if(valid_phone($user->phone)){
				$message = $this->ci->sms_m->build_sms_message('password-recovery-template',array(
					'FIRST_NAME' => $user->first_name,
					'CODE'=>$code,
					'APPLICATION_NAME' => $this->application_settings->application_name,
				),'',$user->language_id);
				if($success = $this->ci->sms_m->send_system_sms(valid_phone($user->phone),$message,$user->id,$user->id)){
					
				}else{
					$this->ci->session->set_flashdata('error','Could not send a system sms');
					$success = FALSE;
				}
			}
			if($user->email){
				if(valid_email($user->email)){
					$this->ci->ion_auth->forgotten_password($user->email,$code,FALSE);
					
				}else{
					$this->ci->session->set_flashdata('error','Sorry the user email is invalid');
					$success = FALSE;
				}
			}
		}else{
			$success = FALSE;
		}
		return $success;
	}

	function send_member_statement($user=array(),$member=array(),$group=array(),$file='',$email='',$date_from=0,$date_to=0,$statement_type='Contribution Statement',$cc = ''){
		if($user&&$member&&$group&&$file&&$email){
			if(valid_email($email)){
				$url = $this->application_settings->protocol.$this->application_settings->url;
				$help_url = $this->application_settings->protocol.'help.'.$this->application_settings->url;
				$message = $this->ci->emails_m->build_email_message('member-email-statement',array(
					'SUBJECT' => $user->first_name.' '.$statement_type.' - '.$group->name,
					'APPLICATION_NAME' => $this->application_settings->application_name,
					'DATE_FROM' => timestamp_to_mobile_shorttime($date_from),
					'DATE_TO' => timestamp_to_mobile_shorttime($date_to),
					'LOGO' => $this->application_settings?site_url('/uploads/logos/'.$this->application_settings->logo):'',
					'LINK' => site_url(),
					'FIRST_NAME'=>$user->first_name,
					'LAST_NAME' => $user->last_name,
					'STATEMENT_TYPE' => $statement_type,
					'YEAR'		=>	date('Y',time()),
					'DATE' 		=>	date('d-m-Y',time()),
					'GROUP_NAME' => $group->name,
					
					'PRIMARY_THEME_COLOR'=>$this->application_settings->primary_color,
                    'TERTIARY_THEME_COLOR'=>$this->application_settings->tertiary_color,
                    'HELP_PAGE' => $help_url,
                    'MOBILE_LINK' => $url,
                    'SENDER' => '<strong>'.$this->application_settings->application_name.' - '.$group->name.'</strong>',
				));
				$subject = $statement_type.' for '.$user->first_name.' from '.$group->name;
				$data = array(
					'email_to' => $email,
	                'subject' => $subject,
	                'message' => $message,
	                'group_id' => $group->id,
	                'member_id' => $member->id,
	                'user_id' => $user->id,
	                'cc' => $cc,
	                'attachments' => serialize(array($file)),
	                'created_on' => time()
				);
				if($email_id = $this->ci->emails_m->insert_email_queue($data)){
		            return TRUE;
		        }else{
		            return FALSE;
		        }
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

		

	function notify_user_password_change($user=array()){
		if($user){
			$message = $this->ci->sms_m->build_sms_message('user-password-changed-successfully',array(
				'FIRST_NAME' => $user->first_name,
				'APPLICATION_NAME'	=>	$this->application_settings->application_name,
			));
			if($success = $this->ci->sms_m->send_system_sms(valid_phone($user->phone),$message,1,$user->id)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}


	function send_decline_sms_notification($group = array(),$user = array(),$member = array(),$group_currency = "KES",$withdrawal_request = array()){
		if($group&&$user&&$member&&$group_currency&&$withdrawal_request){
			$sms_data = array(
				'FIRST_NAME' => $member->first_name,
				'GROUP_CURRENCY' => $group_currency,
				'AMOUNT' => number_to_currency($withdrawal_request->amount),
				'GROUP_NAME' => $group->name,
				'APPLICATION_NAME' => $this->application_settings->application_name,
				'REQUEST_DATE' => timestamp_to_date($withdrawal_request->created_on),
			);
			$message = $this->ci->sms_m->build_sms_message('withdrawal-approval-request-declined',$sms_data,'',$member->language_id);
			$input = array(
                'sms_to' => $member->phone,
                'system_sms' => 1,
                'message' => $message,
                'group_id' => $group->id,
                'member_id' => $member->id,
                'user_id' => $member->user_id,
                'created_on' => time(),
                'created_by'=> $user->id
            );

            if($sms_id = $this->ci->sms_m->insert_sms_queue($input)){
	            return TRUE;
            }else{
				return FALSE;
            }

		}else{
			return FALSE;
		}
	}

	function send_approval_sms_notification($group = array(),$user = array(),$member = array(),$group_currency = "KES",$withdrawal_request = array()){
		if($group&&$user&&$member&&$group_currency&&$withdrawal_request){
			$sms_data = array(
				'FIRST_NAME' => $member->first_name,
				'GROUP_CURRENCY' => $group_currency,
				'AMOUNT' => number_to_currency($withdrawal_request->amount),
				'GROUP_NAME' => $group->name,
				'APPLICATION_NAME' => $this->application_settings->application_name,
				'REQUEST_DATE' => timestamp_to_date($withdrawal_request->created_on),
			);
			$message = $this->ci->sms_m->build_sms_message('withdrawal-approval-request-approved',$sms_data);
			$input = array(
                'sms_to' => $member->phone,
                'system_sms' => 1,
                'message' => $message,
                'group_id' => $group->id,
                'member_id' => $member->id,
                'user_id' => $member->user_id,
                'created_on' => time(),
                'created_by'=> $user->id
            );

            if($sms_id = $this->ci->sms_m->insert_sms_queue($input)){
	            return TRUE;
            }else{
				return FALSE;
            }

		}else{
			return FALSE;
		}
	}

	function send_disbursement_declined_sms($withdrawal_request = array(),$account_balance = 0){
		if($withdrawal_request){
			if($group = $this->ci->groups_m->get($withdrawal_request->group_id)){
				if($member = $this->ci->members_m->get_group_member($withdrawal_request->member_id,$withdrawal_request->group_id)){
					if($currency_code_options = $this->ci->countries_m->get_currency_code_options()){
						if(isset($currency_code_options[$group->currency_id])){
							$group_currency = $currency_code_options[$group->currency_id];
							$sms_data = array(
								'FIRST_NAME' => $member->first_name,
								'GROUP_CURRENCY' => $group_currency,
								'AMOUNT' => number_to_currency($withdrawal_request->amount),
								'GROUP_NAME' => $group->name,
								'APPLICATION_NAME' => $this->application_settings->application_name,
								'ACCOUNT_BALANCE' => number_to_currency($account_balance),
							);
							$message = $this->ci->sms_m->build_sms_message('disbursement-request-declined',$sms_data);
							$input = array(
				                'sms_to' => $member->phone,
				                'system_sms' => 1,
				                'message' => $message,
				                'group_id' => $group->id,
				                'member_id' => $member->id,
				                'user_id' => $member->user_id,
				                'created_on' => time(),
				                'created_by'=> $member->user_id
				            );
				            if($sms_id = $this->ci->sms_m->insert_sms_queue($input)){
					            return TRUE;
				            }else{
								return FALSE;
				            }
						}else{
							$this->ci->session->set_flashdata('warning',"Group currency not found.");
							return FALSE;
						}
					}else{
						$this->ci->session->set_flashdata('warning',"currency options not found.");
						return FALSE;
					}
				}else{
					$this->ci->session->set_flashdata('warning',"User profile not found.");
					return FALSE;
				}
			}else{
				$this->ci->session->set_flashdata('warning',"Group profile not found.");
				return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('warning',"Parameters missing.");
			return FALSE;
		}
	}

	function notify_members_member_suspension_request($group=array(),$user=array(),$member=array(),$suspesion_reason='',$role='',$members=array(),$suspend_member=array(),$member_suspension_request_id=0){
		if($group&&$member&&$user&&$suspend_member){
			if($members){
				$i=0;
				$role = $role?(' - '.$role.' '):'';
				foreach ($members as $notify_member) {
					$sms_data = array(
						'FIRST_NAME' => $notify_member->first_name,
						'INITIATOR' => $user->first_name,
						'FULLNAMES' => $suspend_member->first_name.' '.$suspend_member->last_name.$role,
						'GROUP_NAME' => $group->name,
						'APPLICATION_NAME' => $this->application_settings->application_name,
					);
					$message = $this->ci->sms_m->build_sms_message('member-suspension-request',$sms_data);
					$input = array(
							'sms_to' => $notify_member->phone,
							'system_sms' => 1,
							'message' => $message,
							'user_id' => $notify_member->user_id,
							'created_on' => time(),
							'created_by'=> $user->id,
							'system_sms' => 1,
			                'group_id' => $group->id,
			                'member_id' => $notify_member->id,
					);
					if($sms_id = $this->ci->sms_m->insert_sms_queue($input)){
						$subject = "Member suspension request";
						$message = $user->first_name.' is requesting your approval to suspend '.$suspend_member->first_name.' '.$suspend_member->last_name.$role.' on '.$group->name.'. Tap here to accept or decline the request';
						if($this->ci->notifications->create(
							$subject,
							$message,
							$user,
							$member->id,
							$notify_member->user_id,
							$notify_member->id,
							$group->id,
							'View member suspension request',
							site_url(),
							21,0, 0,0,0,0,0,0,$member_suspension_request_id)){
						}else{
						}
					}else{
					}
				}
				return TRUE;
			}else{
				return TRUE;
			}
		}else{
			return FALSE;
		}
	}

	function notify_initiator_suspension_status($group=array(),$suspension_request=array(),$status = 0){
		if($group&&$suspension_request&&$status){
			$member_user = $this->ci->members_m->get_member_where_user_id($suspension_request->created_by,$group->id);
			$initiated_member = $this->ci->members_m->get_group_member($suspension_request->member_id,$group->id);
			if($member_user && $initiated_member){
				if($status == 1){
					$sms_data = array(
						'FIRST_NAME' => $member_user->first_name,
						'FULL_NAMES' => $initiated_member->first_name.' '.$initiated_member->last_name,
						'GROUP_NAME' => $group->name,
						'APPLICATION_NAME' => $this->application_settings->application_name,
					);
					$message = $this->ci->sms_m->build_sms_message('member-suspension-approved',$sms_data);
					$input = array(
							'sms_to' => $member_user->phone,
							'system_sms' => 1,
							'message' => $message,
							'user_id' => $member_user->user_id,
							'created_on' => time(),
							'created_by'=> $member_user->user_id,
							'system_sms' => 1,
			                'group_id' => $group->id,
			                'member_id' => $member_user->id,
					);
					if($sms_id = $this->ci->sms_m->insert_sms_queue($input)){
						$subject = "Member suspension request Approved";
						$message = $initiated_member->first_name.' '.$initiated_member->last_name.' suspension request on '.$group->name.' has been approved. Tap here to view the request';
						if($this->ci->notifications->create(
							$subject,
							$message,
							$this->ci->ion_auth->get_user($member_user->user_id),
							$member_user->id,
							$member_user->user_id,
							$member_user->id,
							$group->id,
							'View member suspension request Approved',
							site_url(),
							22,0, 0,0,0,0,0,0,$suspension_request->id,$initiated_member->id)){
						}else{
							return FALSE;
						}
					}else{
						return FALSE;
					}
				}elseif ($status ==2) {
					$sms_data = array(
						'FIRST_NAME' => $member_user->first_name,
						'FULL_NAMES' => $initiated_member->first_name.' '.$initiated_member->last_name,
						'GROUP_NAME' => $group->name,
						'APPLICATION_NAME' => $this->application_settings->application_name,
					);
					$message = $this->ci->sms_m->build_sms_message('member-suspension-declined',$sms_data);
					$input = array(
							'sms_to' => $member_user->phone,
							'system_sms' => 1,
							'message' => $message,
							'user_id' => $member_user->user_id,
							'created_on' => time(),
							'created_by'=> $member_user->user_id,
							'system_sms' => 1,
			                'group_id' => $group->id,
			                'member_id' => $member_user->id,
					);
					if($sms_id = $this->ci->sms_m->insert_sms_queue($input)){
						$subject = "Member suspension request Declined";
						$message = $initiated_member->first_name.' '.$initiated_member->last_name.' suspension request on '.$group->name.' has been declined. Tap here to view the request';
						if($this->ci->notifications->create(
							$subject,
							$message,
							$this->ci->ion_auth->get_user($member_user->user_id),
							$member_user->id,
							$member_user->user_id,
							$member_user->id,
							$group->id,
							'View member suspension request declined',
							site_url(),
							22,0, 0,0,0,0,0,0,$suspension_request->id)){
						}else{
							return FALSE;
						}
					}else{
						return FALSE;
					}


				}else{
					return FALSE;
				}
			}	
		}else{
			return FALSE;
		}
	}

	function send_change_phone_activation_code($user = array(), $code = 0,$phone = 0){
		if($user && $code && valid_phone($phone)){
			$message = $this->ci->sms_m->build_sms_message("user-activation-code",array(
				'FIRST_NAME' => $user->first_name,
				'PIN'=>$code,
				'APPLICATION_NAME'	=>	$this->application_settings->application_name,
			));
			if($success = $this->ci->sms_m->send_system_sms(valid_phone($phone),$message,$user->id,$user->id)){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function send_withdrawal_request_approval_code_sms($user = array(), $code = 0){
		if($user && $code){
			$message = $this->ci->sms_m->build_sms_message("withdrawal-request-approval-code",array(
				'FIRST_NAME' => $user->first_name,
				'APPROVAL_CODE'=>$code,
				'APPLICATION_NAME'	=>	$this->application_settings->application_name,
			));
			if(valid_phone($user->phone)){
				if($success = $this->ci->sms_m->send_system_sms(valid_phone($user->phone),$message,$user->id,$user->id)){
					return TRUE;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
			
		}else{
			return FALSE;
		}
	}


    function send_developer_alert_mail($to='',$subject='',$message='',$topic='',$from='info@chamasoft.com',$reply_to="info@chamasoft.com",$cc='',$bcc='',$attachments=array()){
    	$this->ci->emails_manager->send_via_sendgrid($to,$subject,$message,$topic,$from,$reply_to,$cc,$bcc,$attachments);
    }
}