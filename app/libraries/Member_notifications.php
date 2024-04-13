<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Member_notifications{

	protected $ci;
	public $chamasoft_settings;
	/**
		Notification Categories
		1. Profile update 
		2. Contribution invoice sent
		3. Loan Invoice
		4. Fine invoice
		5. Contribution payment
		6. Contribution fine payemnet
		7. Miscellaneous invoice
		8. Contribution Refund
		9. Loan repayment
		10. Billing Invoice
		11. Billing Payment
		12. Unset smses due to insufficient sms balance
		13. Reconcile deposit
		14. Reconcile withdrawal
		15 . External Lending Guarantee
		16 . File ready for download
		17 . Withdrawal Request 
		18 . Withdrawal Decline or Approval Notification 
		19 . Transaction Payment Status
	**/

	protected $category_options_letters = array(
		1 =>'P', 
		2 =>'C',
		3 =>'L',
		4 =>'F',
		5 =>'P',
		6 =>'P',
		7 => 'M',
		8 =>'R',
		9 =>'L',
		10 =>'B',
		11 =>'B',
		12 =>'S',
		13 =>'D',
		14 =>'W',
		15 => 'G',
		16 => 'D',
		17 => "W",
		18 => 'W',
		19 => 'P'
	);

	public function __construct(){
		//die;
		$this->ci= & get_instance();
		$this->ci->load->model('notifications/notifications_m');
		$this->ci->load->model('settings/settings_m');
		$this->ci->load->model('groups/groups_m');
		$this->ci->load->library('Curl');
		if(!isset($_SESSION['chamasoft_settings'])){
            $sessions = $this->ci->settings_m->get_settings();
           $this->ci->session->set_userdata('chamasoft_settings',$sessions);
        }
        $this->chamasoft_settings = $this->ci->session->userdata('chamasoft_settings');
	}

	public function create($subject = '',$message='',$from_user = array(),$from_member_id=0,$to_user_id=0,$to_member_id=0,$group_id=0,$call_to_action='',$call_to_action_link='',$category=0,$invoice_id = 0,$deposit_id = 0,$transaction_alert_id = 0,$file_size=0,$file_path='',$file_type=1,$reference_number=0,$payment_request_status=0,$withdrawal_request_id=0,$withdrawal_approval_request_id=0,$loan_id=0,$withdrawal_id=0){
		if($subject&&$message&&$from_user&&$from_member_id&&$to_user_id&&$to_member_id&&$group_id&&$call_to_action&&$call_to_action_link&&$category){
			$group = $this->ci->groups_m->get($group_id);
			if($group){
				if($group->disable_notifications){
					return TRUE;
				}else{
					$input = array(
						'from_member_id' => $from_member_id,
						'from_user_id' => $from_user->id,
						'to_member_id' => $to_member_id,
						'to_user_id' => $to_user_id,
						'group_id' => $group_id,
						'subject' => $subject,
						'message' => $message,
						'call_to_action' => $call_to_action,
						'call_to_action_link' => $call_to_action_link,
						'category' => $category,
						'is_read' => 0,
						'active' => 1,
						'created_by' => $from_user->id,
						'created_on' => time(),
						'invoice_id' => $invoice_id,
						'deposit_id' => $deposit_id,
						'transaction_alert_id' => $transaction_alert_id,
						'file_size' => $file_size,
						'file_path' => $file_path,
						'file_type' => $file_type,
						'reference_number' => $reference_number,
						'payment_request_status' => $payment_request_status,
						'withdrawal_request_id' => $withdrawal_request_id,
						'withdrawal_approval_request_id' => $withdrawal_approval_request_id,
						'loan_id' => $loan_id,
						'withdrawal_id' => $withdrawal_id,
					);
					$result = $this->ci->notifications_m->insert($input);
					
					if($result){
						if(preg_match('/(local)/',$_SERVER['HTTP_HOST'])){
							return TRUE;
						}else{
							$this->send_push_notification($to_user_id,$group_id,$message,$category,$invoice_id,$deposit_id,$transaction_alert_id,$file_size,$file_path,$file_type,$reference_number,$payment_request_status,$withdrawal_request_id,$withdrawal_approval_request_id,$loan_id,$withdrawal_id);
							return TRUE;							
						}
					}else{
						$this->ci->session->set_flashdata('error','Could not insert notification');
						return FALSE;
					}
				}
			}else{
				$this->ci->session->set_flashdata('error','Group records not found');
				return FALSE;
			}
			
		}else{
			$this->ci->session->set_flashdata('error','Could not insert notification, some parameters are missing');
			return FALSE;
		}
	}

	public function create_bulk($notifications = array()){
		$notification_values_are_valid = FALSE;
		$notifications = (array)$notifications;
		if(empty($notifications)){
			$this->ci->session->set_flashdata('error','Notifications empty');
			return FALSE;
		}else{
			foreach ($notifications as $key => $notification) {
				$enable_notifications = TRUE;
				$group_is_valid = TRUE;
				$notification = (object)$notification;
				if(isset($notification->group_id)){
					$group = $this->ci->groups_m->get($notification->group_id);
					if($group){
						if($group->disable_notifications){
							$enable_notifications = FALSE;
						}
					}else{
						$this->ci->session->set_flashdata('error','Group record not found');
						$group_is_valid = FALSE;
						break;
					}
				}
				if($group_is_valid){
					if($enable_notifications){
						$subject = isset($notification->subject)?$notification->subject: '';
						$message = isset($notification->message)?$notification->message:'';
						$from_user = isset($notification->from_user)?$notification->from_user:array();
						$from_member_id = isset($notification->from_member_id)?$notification->from_member_id:0;
						$to_user_id = isset($notification->to_user_id)?$notification->to_user_id:0;
						$to_member_id = isset($notification->to_member_id)?$notification->to_member_id:0;
						$group_id = isset($notification->group_id)?$notification->group_id:0;
						$call_to_action = isset($notification->call_to_action)?$notification->call_to_action:'';
						$call_to_action_link = isset($notification->call_to_action_link)?$notification->call_to_action_link:'';
						$category = isset($notification->category)?$notification->category:0;
						$invoice_id = isset($notification->invoice_id)?$notification->invoice_id:0;
						$deposit_id = isset($notification->deposit_id)?$notification->deposit_id:0;
						$transaction_alert_id = isset($notification->transaction_alert_id)?$notification->transaction_alert_id:0;
						$file_size = isset($notification->file_size)?$notification->file_size:0;
						$file_path = isset($notification->file_path)?$notification->file_path:'';
						$file_type = isset($notification->file_type)?$notification->file_type:1;
						$reference_number = isset($notification->reference_number)?$notification->reference_number:0;
						$payment_request_status = isset($notification->payment_request_status)?$notification->payment_request_status:0;
						$withdrawal_request_id = isset($notification->withdrawal_request_id)?$notification->withdrawal_request_id:0;
						$withdrawal_approval_request_id = isset($notification->withdrawal_approval_request_id)?$notification->withdrawal_approval_request_id:0;
						$loan_id = isset($notification->loan_id)?$notification->loan_id:0;
						$withdrawal_id = isset($notification->withdrawal_id)?$notification->withdrawal_id:0;
						if($subject&&$message&&$from_user&&$from_member_id&&$to_user_id&&$to_member_id&&$group_id&&$call_to_action&&$call_to_action_link&&$category){
							$notification_entries[] = array(
								'from_member_id' => $from_member_id,
								'from_user_id' => $from_user->id,
								'to_member_id' => $to_member_id,
								'to_user_id' => $to_user_id,
								'group_id' => $group_id,
								'subject' => $subject,
								'message' => $message,
								'call_to_action' => $call_to_action,
								'call_to_action_link' => $call_to_action_link,
								'category' => $category,
								'is_read' => 0,
								'active' => 1,
								'created_by' => $from_user->id,
								'created_on' => time(),
								'invoice_id' => $invoice_id,
								'deposit_id' => $deposit_id,
								'transaction_alert_id' => $transaction_alert_id,
								'file_size' => $file_size,
								'file_path' => $file_path,
								'file_type' => $file_type,
								'reference_number' => $reference_number,
								'payment_request_status' => $payment_request_status,
								'withdrawal_request_id' => $withdrawal_request_id,
								'withdrawal_approval_request_id' => $withdrawal_approval_request_id,
								'loan_id' => $loan_id,
								'withdrawal_id' => $withdrawal_id,
							);
							$notification_values_are_valid = TRUE;
						}
					}else{
						$notification_values_are_valid = TRUE;
					}
				}else{
					$notification_values_are_valid = FALSE;
					break;
				}
			}

			if($notification_values_are_valid){
				if(empty($notification_entries)){
					return TRUE;
				}else{
					if($this->ci->notifications_m->insert_batch($notification_entries)){
						$this->send_batch_push_notification($notification_entries);
						return TRUE;
					}else{
						$this->ci->session->set_flashdata('error','Could not insert notification');
						return FALSE;
					}
				}
				
			}else{
				return FALSE;
			}
		}
	}

	public function mark_member_notification_as_read($unread_member_notifications_array = array(),$url = '',$member_id=0){
		if(isset($unread_member_notifications_array[trim($url)][$member_id])){
			$notification_id = $unread_member_notifications_array[trim($url)][$member_id];
			$input = array(
				'is_read'=>1,
				'modified_on'=>time()
			);
			if($result = $this->ci->notifications_m->update($notification_id,$input)){

			}else{
				$this->ci->session->set_flashdata('error','Notification could not be marked as read/');
			}
		}
	}


	function send_push_notification($user_id = 0,$group_id=0,$message='',$category=0,$invoice_id=0,$deposit_id=0,$transaction_alert_id = 0,$file_size=0,$file_path='',$file_type=1,$reference_number=0,$payment_request_status=0,$withdrawal_request_id=0,$withdrawal_approval_request_id=0,$loan_id = 0,$withdrawal_id=0){
		if($user_id&&$message){
			$url = 'https://fcm.googleapis.com/fcm/send';
			$registration_ids= json_encode(array(
					"e5uMAZvskHs:APA91bE1b858tIKWfz2YiLk0i8AsmVp8Ih5xtVHkhwbN4N8E4ZSzrP6IAAT3IVTwJ-mexEHJq_4JZKyrY9jzEJ6Pi_vkSlguXuBavRY6tHaqqCPiZyJ7gcICiXjIU-mqbnWixKmk2Esl"
				));
			$headers = array(
				'Authorization: key=' . FIREBASE_API_KEY,
				'Content-Type: application/json'
	        );

	        $payload = array(
				'message' 	=> strip_tags($message),
				'title'	=> $this->chamasoft_settings->application_name,
	            'icon_letter'	=> isset($this->category_options_letters[$category])?$this->category_options_letters[$category]:'C',
	            'sound' => 'mySound',
	            'user_id' => $user_id,
	            'id' => '',
	            'text_size' => 50,
	            'text_color' => '#ffffff',
	            'icon_color' => '#073050',
	            'category'=>$category,
	            'group_id'=> $group_id,
	            'group_name' => $this->ci->groups_m->get_group_name($group_id),
	            'invoice_id' => $invoice_id,
	            'deposit_id' => $deposit_id,
	            'transaction_alert_id' => $transaction_alert_id,
	            'file_size' => $file_size,
	            'file_path' => $file_path,
	            'file_type' => $file_type,
	            "reference_number" => $reference_number,
	            "payment_request_status" => $payment_request_status,
	            'withdrawal_request_id' => $withdrawal_request_id,
	            'withdrawal_approval_request_id' => $withdrawal_approval_request_id,
	            'loan_id' => $loan_id,
	            'withdrawal_id' => $withdrawal_id,
	         );

        	$fields = array(
				'to'		=> "/topics/".$this->chamasoft_settings->application_name,
				'data'	=> $payload

			);
        	$result = $this->ci->curl->push_notification($url,$headers,$fields);
        	file_put_contents("logs/push_notificationlog.txt","\n".'Date: '.date("d-M-Y h:i A")."\t fields: ".json_encode($fields)."\t KEY: ".FIREBASE_API_KEY."\t Response: ".$result."\n",FILE_APPEND);
		}
	}

	function send_batch_push_notification($notification_entries = array()){
		if($notification_entries){
			$headers = array(
				'Authorization: key=' . FIREBASE_API_KEY,
				'Content-Type: application/json'
	        );
	        $url = 'https://fcm.googleapis.com/fcm/send';
			$registration_ids= json_encode(array(
					"e5uMAZvskHs:APA91bE1b858tIKWfz2YiLk0i8AsmVp8Ih5xtVHkhwbN4N8E4ZSzrP6IAAT3IVTwJ-mexEHJq_4JZKyrY9jzEJ6Pi_vkSlguXuBavRY6tHaqqCPiZyJ7gcICiXjIU-mqbnWixKmk2Esl"
				));
			foreach ($notification_entries as $notification_entry) {
				$notification_entry = (object)$notification_entry;
				$payload = array(
					'message' 	=> strip_tags($notification_entry->message),
					'title'	=> $this->chamasoft_settings->application_name,
		            'icon_letter'	=> isset($this->category_options_letters[$notification_entry->category])?$this->category_options_letters[$notification_entry->category]:'C',
		            'sound' => 'mySound',
		            'user_id' => $notification_entry->to_user_id,
		            'id' => '',
		            'text_size' => 50,
		            'text_color' => '#ffffff',
		            'icon_color' => '#073050',
		            'category'=>$notification_entry->category,
		            'group_id'=> $notification_entry->group_id,
		            'group_name' => $this->ci->groups_m->get_group_name($notification_entry->group_id),
		            'invoice_id' => $notification_entry->invoice_id,
		            'deposit_id' => $notification_entry->deposit_id,
		            'transaction_alert_id' => $notification_entry->transaction_alert_id,
		            'file_size' => $notification_entry->file_size,
		            'file_path' => $notification_entry->file_path,
		            'file_type' => $notification_entry->file_type,
		            "reference_number" => $notification_entry->reference_number,
		            "payment_request_status" => $notification_entry->payment_request_status,
		            'withdrawal_request_id' => $notification_entry->withdrawal_request_id,
		            'withdrawal_approval_request_id' => $notification_entry->withdrawal_approval_request_id,
		            'loan_id' => $notification_entry->loan_id,
		            'withdrawal_id' => $notification_entry->withdrawal_id,
		         );
				$fields[] = array(
					'to'		=> "/topics/".$this->chamasoft_settings->application_name,
					'data'	=> $payload
				);
				$result[] = $this->ci->curl->push_notification($url,$headers,$fields);
			}
			file_put_contents("logs/push_notificationlog.txt","\n".'Date: '.date("d-M-Y h:i A")."\t fields: ".json_encode($fields)."\t KEY: ".FIREBASE_API_KEY."\t Response: ".json_encode($result)."\n",FILE_APPEND);
		}
	}
}