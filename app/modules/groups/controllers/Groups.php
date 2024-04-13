<?php if(!defined('BASEPATH')) exit('You are not allowed to viewe this script');
class Groups extends CI_Controller{
	protected $data = array();

	function __construct(){
		parent::__construct();
		$this->load->model('migrate_m');
		$this->load->model('groups_m');
		$this->load->model('users/users_m');
		$this->load->model('settings/settings_m');
		$this->load->model('members/members_m');
		$this->load->model('sms/sms_m');
		$this->load->model('emails/emails_m');
		$this->load->library('curl');
		$this->load->library('ion_auth');
		$this->load->library('billing_settings');
		if(!isset($_SESSION['chamasoft_settings'])){
        	$sessions = $this->settings_m->get_settings();
           	$this->session->set_userdata('chamasoft_settings',$sessions);
        }
        $this->chamasoft_settings = $this->session->userdata('chamasoft_settings');
	}

	function disable_group_notifications(){
		$posts = $this->groups_m->get_inactive_group_disable_notifications();
		$i = 0;
		foreach ($posts as $key => $post) {
			$update = array(
	            'disable_notifications' => 1,
	            'modified_on' => time(),
	        );
	        if($this->groups_m->update($post->id,$update)){
	        	++$i;
	        }
		}

		echo "Groups disabled notifications are :".$i;
	}

	function archive_groups($deleted = 0){
		// if(!preg_match('/(chamasoft)/',$_SERVER['HTTP_HOST'])){
		// 	die;
		// }
		$total_rows = $this->groups_m->count_potential_archive_groups();
		//echo ($total_rows);die;
        $step_size = 5;
        $pagination = create_pagination('groups/archive_groups/'.$deleted,$total_rows,$step_size,4,TRUE);
        $posts = $this->groups_m->limit($pagination['limit'])->get_potential_archive_groups();
        $deleted =$this->_loop_through_groups($posts,$deleted);
        if($pagination){
            $total = $pagination['total'];
            $current_page = $pagination['current_page'];
            $next_page = ($current_page+$step_size);
            if($next_page<$total){
            	$url = site_url('groups/archive_groups/'.$deleted.'/'.($next_page));
                // echo '
                //     <script>
                //         window.location = "'.$url.'";
                //     </script>

                // ';
                redirect($url,'refresh');
            }
        }
        print_r($pagination);
        echo $deleted.' groups archived';
        die;
    }

    function _loop_through_groups($posts=array(),$deleted){
    	if($posts){
        	set_time_limit(0);
        	ini_set('memory_limit','-1');
        	ini_set('max_execution_time', 24000);
            foreach ($posts as $post) {
            	if($post->active_size>5){
            		$this->migrate_m->backup_group($post->id,1,0,1);
            	}
				if($this->groups_m->delete($post->id)){
					$result = TRUE;
					$members = $this->members_m->get_group_members($post->id);
					foreach ($members as $member) {
						if($this->members_m->delete($member->id)){
							$member_group_count = $this->groups_m->count_current_user_groups($member->user_id) + 1;
							if($member_group_count==1){
								if($this->ion_auth->is_admin($member->user_id)){
								}else{
									if($this->users_m->delete($member->user_id)){
									}else{
										$result = FALSE;
									}
								}
								
							}else{}
						}else{
							$result = FALSE;
						}
					}
					$database = 'chamasoft';
					$tables=$this->db->query("SELECT t.TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES AS t WHERE t.TABLE_SCHEMA = '".$database."' ")->result_array();    
						$count = 1;
						$ignore_tables = array(
							'equity_bank_transaction_alerts',
							'transaction_alerts',
							'group_deletions'
						);
						foreach($tables as $key => $val) {
							$table_name = $val['table_name'];
						    if($this->db->field_exists('group_id',$table_name)){
						    	if(in_array($table_name,$ignore_tables)){
						    		if($table_name=='transaction_alerts'){
						    			$this->migrate_m->unset_group_transaction_alerts($post->id);
						    		}
						    	}else{
						    		if($this->migrate_m->delete_group_data($post->id,$table_name)){
						    			//do nothing for now
						    		}else{
						    			$result = FALSE;
						    		}
						    	}
						    }
						}
				}
				++$deleted;
            }
        }
        return $deleted;
    }

    function update_group_subscription_status(){
    	$groups = $this->groups_m->get_all();
    	foreach ($groups as $key => $group) {
    		$this->billing_settings->update_group_subscription_status($group->id);
            $i = $key;
    	}
        echo $i.' groups';
    }
    

    function group_segementation(){
    	$segments = $this->billing_settings->subscription_statuses;
    	$group_segments = $this->groups_m->count_group_segments($segments);
    	print_r($group_segments);die;
    }

    function group_by_segment($segment=0){
    	print_r($this->groups_m->get_group_by_segment($segment));
    }

    function get_group($group_id=0){
        print_r($this->groups_m->get($group_id));
    }


    function export_group_data(){
		$allowed_ips = array(
			'197.237.145.98',
			'127.0.0.1',
		);
		$file = file_get_contents('php://input');
        $input = json_decode($file);
        $slug = isset($input->slug)?$input->slug:'';
		$user_name = isset($input->user_name)?$input->user_name:'';
		$password = isset($input->password)?$input->password:'';
		if(in_array($_SERVER['REMOTE_ADDR'],$allowed_ips) ||($user_name=='chamasoft'&&$password=='thecheese')){
			if($slug){
				$group_details = $this->groups_m->get_by_slug($slug);
				if($group_details){
					$group_id = $group_details->id;
					$ignore_tables = array("activity_log","email_templates","sms_templates","bank_menus","admin_menus","admin_quick_action_menus","ara_sessions","equity_bank_transaction_alerts","ipns","t24_transaction_alerts","contribution_invoicing_queue","contribution_fine_invoicing_queue","loan_invoicing_queue","email_queue","sms_result","user_bank_pairings","transaction_alert_forwarders","login_attempts","emails","voided_statements","group_deletions");
			        $table_name = array();
			        $tables = $this->db->list_tables();
			        foreach ($tables as $table) {
			            if(in_array($table, $ignore_tables)){

			            }else{
			                $table_name[$table] = $this->db->list_fields($table);
			            }
			            
			        }
			        $success=0;
			        $fails = 0;
			        $updated_tables = array();
			        $table_names = array();
			        $group_data = array();
			        foreach ($table_name as $key => $value) {
			            $updated_tables[$key] = $key;
			            if(in_array('group_id', $value)){
			                $table_names[$key] = $value;
			                $data = $this->migrate_m->get_data_by_group_id($group_id,$key);
			                if($data){
			                    $group_data[$key] = $data;
			                    if($key == 'members'){
			                        foreach ($data as $data_key => $data_value) {
			                            $user_data[] =  $this->migrate_m->get_users($data_value->user_id);
			                        }
			                        $group_data['users'] = $user_data;
			                    }
			                }
			            }
			        }
			        $group = $this->migrate_m->get_group($group_id);
			        $group_data['investment_groups'] = $group;
			        echo json_encode(array(
						'result_code' => 200,
						'group_data' => $group_data,
						'message' => 'Success'
					));
				}else{
					echo json_encode(array(
						'result_code' => 503,
						'message' => 'Group details not found in production'
					));
				}
				
			}else{
				echo json_encode(array(
					'result_code' => 503,
					'message' => 'Please provide group slug'
				));
			}
		}else{
			echo json_encode(array(
				'result_code' => 503,
				'message' => 'You do not have permission to access this resource'
			));
		}
    }

    function get_transaction_statistics(){
    	$group_with_highest_membership_count = $this->groups_m->get_group_with_highest_membership_count();
    	
    	print_r($group_with_highest_membership_count);
    }

	function get_groups_created_specific_number_of_days_ago($number_of_days){
		$posts = $this->groups_m->get_groups_created_specific_number_of_days_ago($number_of_days);
		$groups = [];
		$users = [];

		foreach ($posts as $key => $post) {
			if( $post->active_size < 3 ) {
				$users[] = $post->owner;
				$groups[] = $post;
			}
		}
		
		$options = $this->users_m->get_users_by_ids_options($users);
		$sms_array = [];
		$emails_array = [];

		foreach($groups as $key => $group) {
			$user = isset($options[$group->owner]) ? $options[$group->owner] : "";

			if($user) {
				if($number_of_days == 3) {
					// Check if number is valid
					if(valid_phone($user->phone)){
						$message = $this->sms_m->build_sms_message('complete-member-setup-notification',array(
								'FIRST_NAME' => $user->first_name,
								'GROUP_NAME' => $group->name,
							)
						);
						$sms_array[] = array(
							'sms_to'=>$user->phone,
							'group_id'=>$group->id,
							'member_id'=>$user->id,
							'user_id'=>$user->id,
							'message'=>$message,
							'created_on'=>time(),
							'created_by'=>$user->id
						);
					}
					// Check if email is valid
					if(valid_email($user->email)){
						$message = $this->emails_m->build_email_message('complete-member-setup-notification',array(
								'FIRST_NAME' => $user->first_name,
								'LAST_NAME' => $user->last_name,
								'GROUP_NAME' => $group->name,
								'APPLICATION_NAME'	=>	$this->chamasoft_settings->application_name,
								'APPLICATION_LOGO'=>$this->chamasoft_settings?site_url('/uploads/logos/'.$this->chamasoft_settings->logo):'', 
							)
						);
						
						$emails_array[] = array(
							'email_to'=>$user->email,
							'subject'=>$user->first_name.' '.$user->last_name.' setup notification for '.$group->name.' group on '.$this->chamasoft_settings->application_name,
							'email_from'=>'',
							'group_id'=>$group->id,
							'member_id'=>$user->id,
							'user_id'=>$user->id,
							'message'=>$message,
							'created_on'=>time(),
							'created_by'=>$user->id
						);
					}
				}else if($number_of_days == 8) {
					// Check if number is valid
					if(valid_phone($user->phone)){
						$message = $this->sms_m->build_sms_message('group-archive-notification',array(
								'FIRST_NAME' => $user->first_name,
								'GROUP_NAME' => $group->name,
							)
						);
						$sms_array[] = array(
							'sms_to'=>$user->phone,
							'group_id'=>$group->id,
							'member_id'=>$user->id,
							'user_id'=>$user->id,
							'message'=>$message,
							'created_on'=>time(),
							'created_by'=>$user->id
						);
					}
					// Check if email is valid
					if(valid_email($user->email)){
						$message = $this->emails_m->build_email_message('group-archive-notification',array(
								'FIRST_NAME' => $user->first_name,
								'LAST_NAME' => $user->last_name,
								'GROUP_NAME' => $group->name,
								'APPLICATION_NAME'	=>	$this->chamasoft_settings->application_name,
								'APPLICATION_LOGO'=>$this->chamasoft_settings?site_url('/uploads/logos/'.$this->chamasoft_settings->logo):'', 
							)
						);
						
						$emails_array[] = array(
							'email_to'=>$user->email,
							'subject'=>$user->first_name.' '.$user->last_name.' setup notification for '.$group->name.' group on '.$this->chamasoft_settings->application_name,
							'email_from'=>'',
							'group_id'=>$group->id,
							'member_id'=>$user->id,
							'user_id'=>$user->id,
							'message'=>$message,
							'created_on'=>time(),
							'created_by'=>$user->id
						);
					}
				}
				
			}
		}

		// Batch insert
		if(!empty($sms_array)) {
			$sms_result = $this->sms_m->insert_batch_to_queue($sms_array);
		}

		if(!empty($emails_array)) {
			$email_result = $this->emails_m->insert_chunk_emails_queue($emails_array);
		}
	}


	function get_latest_signups($period =0 ){
		$this->response = array();
        set_time_limit(0);
        ini_set('memory_limit','1536M');
        //error_reporting(1);
        if(isset($_REQUEST)){
           $data = file_get_contents('php://input');
           $json_data = json_decode($data);
           if(empty($json_data)){
                $this->response = [
                    'responseCode'=>0,
                    'message'=> "Json data empty"
                ];
            }else{ 
            	$group_ids = $json_data->groupIds; 
            	$period = isset($json_data->period)?$json_data->period:'';
                $groups = $this->groups_m->get_group_by_bank_panel_ids($group_ids);
                $groups_array = array();
                if($groups){
                    $ids_ = [];
                    foreach ($groups as $key => $group) {
                        $ids_[] = $group->id;
                    }
                    $from = strtotime(" 1st ".date('M Y',strtotime("-11 months",time())));
			        if($period){
			            if($period == 'today'){
			                $from = time();
			            }else if($period == 'last_7'){
			                $from = strtotime("-7 days",time());
			            }else if ($period == 'last_1') {
			                $from = strtotime(" 1st ".date('M Y',strtotime("-1 month",time())));
			            }else if($period == 'last_3'){
			                $from = strtotime(" 1st ".date('M Y',strtotime("-2 months",time())));
			            }else if($period == 'last_6'){
			                $from = strtotime(" 1st ".date('M Y',strtotime("-5 months",time())));
			            }else if($period == 'last_10'){
			                $from = strtotime(" 1st day of ".date('Y',strtotime("-10 years",time())));
			            }else if($period == 'all'){
			                $from = strtotime(" 1st day of ".date('Y',strtotime("-20 years",time())));
			            }
			        }
			        $from = $from?$from:strtotime("-7 days");
			        $to = time();
			        
			        $groups_ = $this->groups_m->get_latest_signups($from,$to,$ids_);
			        
			        if($groups_){         
			            foreach ($groups_ as $key => $group) {
			                $groups_array[] = array(
			                    'id'=>$group->id,
			                    'name'=>$group->name,
			                    'activity'=>'Signed up',
			                    'active_size'=>$group->active_size,
			                    'size'=>$group->size,
			                    'elapse_time'=>daysAgo($group->created_on)
			                );
			            }
			        }
                }
	        	
		        $this->response = array(
		            'responseCode' => 1,
		            'message' => "success",
		            'signups' => $groups_array
		        );
		    }
	    }else{
	    	$this->response = [
                'responseCode'=>0,
                'message'=> "Blank Request"
            ];
        }
        echo json_encode($this->response);
    }
}
?>