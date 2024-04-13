<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emails extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('emails_m');
        $this->load->model('groups/groups_m');
    }

    public function send_queued_emails($limit = 5){
        if(preg_match('/(chamasoft)/i',$this->application_settings->application_name)){
           $email_from = "info@chamasoft.com";          
        }else if(preg_match('/(eazzyclub)/i',$this->application_settings->application_name)){
            $email_from = "info@eazzyclub.co.ug";
        }else if(preg_match('/(eazzychama)/i',$this->application_settings->application_name)){
            $email_from ="info@eazzychama.co.ke";
        }else if(preg_match('/(eazzykikundi)/i',$this->application_settings->application_name)){
            $email_from ="info@eazzykikundi.com";
        }else if(preg_match('/(websacco)/i',$this->application_settings->application_name)){
            $email_from ="info@websacco.com";
        }else{
            $email_from = "info@chamasoft.com";
        }
    	$queued_emails = $this->emails_m->get_emails_to_send($limit);
        // print_r($queued_emails);
        // die;
    	$successes = 0;
    	$failures = 0;
        foreach ($queued_emails as $queued_email){
            if($queued_email->email_to){
                $email_array = array(
                    'email_from'=>$email_from,
                    'to'=>$queued_email->email_to,
                    // 'to'=>"geoffrey.githaiga@digitalvision.co.ke",
                    'cc'=> $queued_email->cc,
                    'bcc'=>$queued_email->bcc,
                    'subject'=> $queued_email->subject,
                    'message'=>$queued_email->message,
                    'sending_email'=>$queued_email->sending_email,
                    'attachments'=>unserialize($queued_email->attachments),
                );
                if($result_id = $this->emails_manager->send_email_via_postmark_api($email_array)){   
                    $input = array(
                        'email_to'      =>  $queued_email->email_to,
                        'subject'       =>  $queued_email->subject,
                        'message'       =>  $queued_email->message,
                        'email_from'    =>  $email_from,
                        'sending_email' =>  $queued_email->sending_email,
                        'group_id'      =>  $queued_email->group_id?:0,
                        'member_id'     =>  $queued_email->member_id,
                        'user_id'       =>  $queued_email->user_id,
                        'attachments'   =>  $queued_email->attachments,
                        'cc'            =>  $queued_email->cc,
                        'bcc'           =>  $queued_email->bcc,
                        'embeded_attachments'=> $queued_email->embeded_attachments,
                        'created_on'    =>  time(),
                        'created_by'    =>  $queued_email->created_by,
                        'email_header'  =>  $queued_email->email_header,
                    );
                    if($this->emails_m->insert($input)){
                        $this->emails_m->delete_email_queue($queued_email->id);
                        ++$successes; 
                    }else{
                        $failures++;
                        $this->emails_m->delete_email_queue($queued_email->id);
                    }
                }else{
                    $failures++;
                    $this->emails_m->delete_email_queue($queued_email->id);
                }
            }else{
                $failures++;
                $this->emails_m->delete_email_queue($queued_email->id);
            }
        }
        echo $successes.' Successes.<br/>';
        echo $failures.' Failures.<br/>';
    }

    function delete_old_emails(){
        $this->emails_m->delete_old_emails();
    }

    function delete_old_queued_emails(){
        $this->emails_m->delete_old_queued_emails();
    }

    function check_number_of_users_added($date = 0){
        if($date){

        }else{
            $date = date("d-m-Y",strtotime("-3 days"));
        }
        $email_type = 2 ;
        $groups = $this->groups_m->groups_registered_after_certain_days($date);
        if($groups){
            $owner_ids = array();
            foreach ($groups as $key => $group):
                if($group->active_size < $group->size){
                    $owner_ids[] = $group->owner;
                }
            endforeach;
            if($owner_ids){
                $users = $this->users_m->get_users_array($owner_ids);
                $emails_queued_today = $this->emails_m->get_emails_queued_today($email_type);       
                $email_array = array();
                foreach ($users as $key => $user):
                    $member = $this->members_m->get_group_member_by_group_user_id($groups[$user->id]->id,$user->id);
                    if(empty($emails_queued_today)){
                        $email_array[] = array(
                            'user_id'=>$user->id,
                            'first_name'=>$user->first_name,
                            'last_name'=>$user->last_name,
                            'email'=>$user->email,
                            'member_id'=>$member->id,
                            'group_id'=>$groups[$user->id]->id,
                            'group_name'=>$groups[$user->id]->name,
                            'size'=>$groups[$user->id]->size,
                            'active_size'=>$groups[$user->id]->active_size,
                        );
                    }else{
                        if(array_key_exists($member->id, $emails_queued_today[$user->id][$groups[$user->id]->id])){
                            echo 'could not sent email already queued'; 
                        }else{
                            $email_array[] = array(
                                'user_id'=>$user->id,
                                'first_name'=>$user->first_name,
                                'last_name'=>$user->last_name,
                                'email'=>$user->email,
                                'member_id'=>$member->id,
                                'group_id'=>$groups[$user->id]->id,
                                'group_name'=>$groups[$user->id]->name,
                                'size'=>$groups[$user->id]->size,
                                'active_size'=>$groups[$user->id]->active_size,
                            );
                        }
                    }
                endforeach;
            }
            if($no_sent = $this->messaging->send_group_size_email_reminder($email_array)){
                echo $no_sent .' emails reminders sent ';
            }
        }else{
            echo " no groups created yesterdy";
        }
    }

    function check_last_time_logged_in($limit = 100){   
        if($limit){

        }else{     
            $limit = 100;
        }  
        $date = date("d-m-Y",strtotime("-15 days"));
        $email_type= 3;
        $count_users = $this->users_m->count_users_not_logged_in_certain_days($date);
        $size = 0;   
        $emails_already_queued = 0;    
        $step_size = 100;
        $pagination = create_pagination('emails/check_last_time_logged_in/'.$limit.'/',$count_users,$step_size,4,TRUE);
        $users = $this->users_m->get_users_not_logged_in_certain_days($date);
        if($users){
            $emails_queued_today = $this->emails_m->get_reminder_emails_queued_today($email_type);           
            $user_array = array();
            foreach ($users as $user):
                $size++;
                $member = $this->members_m->get_member_by_user_id($user->id);
                if($member){
                    if(empty($emails_queued_today)){
                        $user_array[] = array(
                            'user_id'=>$user->id,
                            'member_id'=>$member->id,
                            'group_id'=>$member->group_id,
                            'first_name'=>$user->first_name,
                            'last_name'=>$user->last_name,
                            'email'=>$user->email,
                            'last_login'=>$user->last_login
                        );
                    }else{
                        if(array_key_exists($user->id, $emails_queued_today)){
                            $emails_already_queued++;                            
                        }else{
                            $user_array[] = array(
                                'user_id'=>$user->id,
                                'member_id'=>$member->id,
                                'group_id'=>$member->group_id,
                                'first_name'=>$user->first_name,
                                'last_name'=>$user->last_name,
                                'email'=>$user->email,
                                'last_login'=>$user->last_login
                            );
                        }
                    }

                    
                }
            endforeach;
            if($no_sent = $this->messaging->send_user_last_login_reminder($user_array)){
                echo $no_sent ."emails queued<br>";
            }else{
               echo " could not queue email messages<br>"; 
            }
            echo $emails_already_queued. 'already queued<br>'; 
        }


    }

    function test_email(){
        $array = array(
            '1'=>1,);
        $email = "geoffrey.githaiga@digitalvision.co.ke";
        $message = "This is a test email";
        $reference_number = random_string(1,10);
        $result = $this->sms_gateway->send_email_via_equity_bank($email,$message,'Eazzykikundi',$reference_number);
        print_r($result); die();
    }

    function test_invitation($group_id = 0){
        $this->messaging->send_member_first_time_invitation_message($group_id);
    }


 }