<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms extends Public_Controller{

    protected $sms_send_to_options = array(
        1 => 'All Users',
        2 => 'Multiple User Segments e.g. Paying Group Owners',
        3 => 'Specific Groups',
    );

    protected $sms_multiple_segment_options = array(
        'Paying Groups' => array(
            1 => ' All Paying Group Owners & Administrators ',
            2 => ' All Paying Group Members( Owners & Administrators Included) ',
        ),
        'Paying Groups (In Arrears)' => array(
            3 => ' All In Arrears Paying Group Owners & Administrators ',
            4 => ' All In Arrears Paying Group Members( Owners & Administrators Included) ',
        ),
        'On Trial' => array(
            5 => ' All On Trial Group Owners & Administrators ',
            6 => ' All On Trial Group Members( Owners & Administrators Included) ',
        ),
        'Trial Expired' => array(
            7 => ' All Trial Expired Group Owners & Administrators ',
            8 => ' All Trial Expired Group Members( Owners & Administrators Included) ',
        )
    );

    public function __construct(){
        parent::__construct();
        $this->load->model('sms_m');
        $this->load->model('users/users_m');
        $this->load->model('groups/groups_m');
        $this->load->model('members/members_m');
        $this->load->library('messaging');
    }

    public function send_queued_smses($limit = 5){
        if($this->application_settings->disable_smses){
            echo "SMS delivery disabled by Super Admin.";
        }else{
            $queued_smses = $this->sms_m->get_smses_to_send($limit);
        	$successes = 0;
        	$failures = 0;
        	foreach($queued_smses as $queued_sms){
                if(!$this->check_if_notifications_enabled($queued_sms->group_id)) {
                    $this->sms_m->delete_sms_queue($queued_sms->id); 
                    continue;
                }
                if(valid_phone($queued_sms->sms_to)){          
                    if($queued_sms->system_sms){
                        if($this->sms_m->send_sms(valid_phone($queued_sms->sms_to),$queued_sms->message,$queued_sms->member_id,$queued_sms->user_id,$queued_sms->group_id,$queued_sms->created_by,$queued_sms->system_sms)){
                            //delete the sms
                            $this->sms_m->delete_sms_queue($queued_sms->id);
                            $successes++;
                        }else{
                            $failures++;
                        }
                    }else{
                        $group_sms_balance = $this->group_current_balance_sms($queued_sms->group_id);
                        if(preg_match('/eazzy/i', $this->application_settings->application_name)){ 
                            $group_sms_balance = 100;
                        }
                        if($group_sms_balance){
                            if($this->sms_m->send_sms($queued_sms->sms_to,$queued_sms->message,$queued_sms->member_id,$queued_sms->user_id,$queued_sms->group_id,$queued_sms->created_by,$queued_sms->system_sms)){
                                    //delete the sms
                                $this->sms_m->delete_sms_queue($queued_sms->id);
                                $this->reduce_group_sms($queued_sms->group_id,$group_sms_balance);
                                $successes++;
                            }else{
                                $failures++;
                            }
                        }
                    }
                }else{
                    $failures++;
                    $this->sms_m->delete_sms_queue($queued_sms->id);
                }

        	}
            // $this->update_group_insufficient_sms_balance();
        	echo $successes.' Successes.<br/>';
        	echo $failures.' Failures.<br/>';
        }
    }

    function check_if_notifications_enabled($group_id=0){
        $group =  $this->groups_m->get($group_id);
        if($group){
            return $group->disable_notifications?0:1;
        }
        return TRUE;
    }

    function group_current_balance_sms($group_id=0){
        $group = $this->groups_m->get_group_owner($group_id);
        if($group->sms_balance>0){
            if($group->sms_balance==10){
                $this->messaging->notify_admin_sms_balance($group);
            }
            if($group->sms_balance == 2){
                //also notify the admin
                $this->messaging->notify_admin_sms_balance($group);
            }
            return $group->sms_balance;

        }else{
            return FALSE;
        }
    }

    function reduce_group_sms($group_id=0,$sms_balance=0){
        if($sms_balance){
            $new_sms_balance = $sms_balance - 1;
            $data = array('sms_balance'=>$new_sms_balance,'modified_on'=>time());
            return ($this->groups_m->update($group_id,$data));
        }else{
            //do nothing
        }
    }

    function update_group_insufficient_sms_balance(){
        $smses = $this->sms_m->get_all_smses_with_insufficient_error();
        foreach ($smses as $sms){
            $sms = (object)$sms;
            if($this->group_current_balance_sms($sms->group_id)){
                $this->sms_m->update_insufficient_where_group_id($sms->group_id);
            }else{
                continue;
            }
        }
    }

    function sms_send_to_options(){
        echo json_encode($this->sms_send_to_options);
    }

    function sms_multiple_segment_options(){
        echo json_encode($this->sms_multiple_segment_options);
    }

    function sms_send_to_group_and_user_lists(){
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        if($username=='chamasoft'&&$password=='-PrAZ8m9F!DEnd*'){
            $send_to_segments = $this->input->post('send_to_segments');
            $send_to_segments_array = unserialize($send_to_segments);
            $arr = array();

            $arr['groups'] = array();
            $arr['users'] = array();

            $arr['total_group_count'] = $this->groups_m->count_all();
            $arr['total_user_count'] = $this->members_m->count_all();
            $arr['recipients_string'] = '';
            $count = 1;
            foreach ($send_to_segments_array as $segment) {
                # code...
                if($segment==1){
                    if($count==1){
                        $arr['recipients_string'] .= " All Paying Group Owners & Administrators";
                    }else{
                        $arr['recipients_string'] .= ", All Paying Group Owners & Administrators";
                    }
                    // All Paying Group Owners & Administrators
                    $arr['paying_groups'] = $this->groups_m->get_paying_groups();
                    $arr['paying_group_owners_and_administrators'] = $this->members_m->get_paying_group_owners_and_administrators();
                }else if($segment==2){
                    // All Paying Group Members ( Owners & Administrators )
                    if($count==1){
                        $arr['recipients_string'] .= " All Paying Group Members ( Owners & Administrators Included ) ";
                    }else{
                        $arr['recipients_string'] .= ", All Paying Group Members ( Owners & Administrators Included )";
                    }
                    $arr['paying_groups'] = $this->groups_m->get_paying_groups();
                    $arr['paying_group_owners_administrators_and_members'] = $this->members_m->get_paying_group_owners_administrators_and_members();
                }else if($segment==3){
                    //All In Arrears Paying Group Owners & Administrators
                    if($count==1){
                        $arr['recipients_string'] .= " All In Arrears Paying Group Owners & Administrators";
                    }else{
                        $arr['recipients_string'] .= ", All In Arrears Paying Group Owners & Administrators";
                    }
                    $arr['in_arrears_paying_groups'] = $this->groups_m->get_in_arrears_paying_groups();
                    $arr['in_arrears_paying_group_owners_and_administrators'] = $this->members_m->get_in_arrears_paying_group_owners_and_administrators();
                }else if($segment==4){
                    //All In Arrears Paying Group Members( Owners & Administrators )
                    if($count==1){
                        $arr['recipients_string'] .= " All In Arrears Paying Group Members( Owners & Administrators Included )";
                    }else{
                        $arr['recipients_string'] .= ", All In Arrears Paying Group Members( Owners & Administrators Included )";
                    }
                    $arr['in_arrears_paying_groups'] = $this->groups_m->get_in_arrears_paying_groups();
                    $arr['in_arrears_paying_group_owners_administrators_and_members'] = $this->members_m->get_in_arrears_paying_group_owners_administrators_and_members();
                }else if($segment==5){
                    //All On Trial Group Owners & Administrators
                    if($count==1){
                        $arr['recipients_string'] .= " All On Trial Group Owners & Administrators";
                    }else{
                        $arr['recipients_string'] .= ", All On Trial Group Owners & Administrators";
                    }
                    $arr['on_trial_groups'] = $this->groups_m->get_groups_on_trial();
                    $arr['on_trial_group_owners_and_administrators'] = $this->members_m->get_on_trial_group_owners_and_administrators();
                }else if($segment==6){
                    # code...
                    //All On Trial Group Members( Owners & Administrators )
                    if($count==1){
                        $arr['recipients_string'] .= " All On Trial Group Members ( Owners & Administrators Included)";
                    }else{
                        $arr['recipients_string'] .= ", All On Trial Group Members ( Owners & Administrators Included)";
                    }
                    $arr['on_trial_groups'] = $this->groups_m->get_groups_on_trial();
                    $arr['on_trial_group_owners_administrators_and_members'] = $this->members_m->get_on_trial_group_owners_administrators_and_members();
                }else if($segment==7){
                    //All Trial Expired Group Owners & Administrators
                    if($count==1){
                        $arr['recipients_string'] .= " All Trial Expired Group Owners & Administrators";
                    }else{
                        $arr['recipients_string'] .= ", All Trial Expired Group Owners & Administrators";
                    }
                    $arr['trial_expired_groups'] = $this->groups_m->get_groups_trial_expired();
                    $arr['trial_expired_group_owners_and_administrators'] = $this->members_m->get_trial_expired_group_owners_and_administrators();
                }else if($segment==8){
                    if($count==1){
                        $arr['recipients_string'] .= " All Trial Expired Group Members( Owners & Administrators Included)";
                    }else{
                        $arr['recipients_string'] .= ", All Trial Expired Group Members( Owners & Administrators Included)";
                    }
                    $arr['trial_expired_groups'] = $this->groups_m->get_groups_trial_expired();
                    $arr['trial_expired_group_owners_administrators_and_members'] = $this->members_m->get_trial_expired_group_owners_administrators_and_members();
                }
                $count++;
            }
            echo json_encode($arr);
        }
    }

    function sms_all_group_and_user_lists(){
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        if($username=='chamasoft'&&$password=='-PrAZ8m9F!DEnd*'){
            $arr = array();

            $arr['groups'] = $this->groups_m->get_all_groups();
            $arr['users'] = $this->members_m->get_all_members();

            $arr['total_group_count'] = $this->groups_m->count_all();
            $arr['total_user_count'] = $this->members_m->count_all_members();

            echo json_encode($arr);
        }
    }

    function sms_group_lists(){
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        if($username=='chamasoft'&&$password=='-PrAZ8m9F!DEnd*'){
            $groups = $this->groups_m->get_all_groups();
            echo json_encode($groups);
        }
    }

    function sms_group_and_user_lists(){
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        if($username=='chamasoft'&&$password=='-PrAZ8m9F!DEnd*'){
            $arr = array();
            $group_options = $this->input->post('group_options');
            $group_options_array = unserialize($group_options);
            $group_id_list = "0";
            $count = 1;
            foreach($group_options_array as $group_id):
                if($count==1){
                    $group_id_list = $group_id;
                }else{
                    $group_id_list .= ','.$group_id;
                }
                $count++;
            endforeach;

            $arr['groups'] = $this->groups_m->get_group_options_by_group_id_list($group_id_list);
            $arr['users'] = $this->members_m->get_member_options_by_group_id_list($group_id_list);

            $arr['total_group_count'] = $this->groups_m->count_all();
            $arr['total_user_count'] = $this->members_m->count_all_members();
            echo json_encode($arr);
        }
    }

    function get_infobip_account_balance(){
        echo $balance = $this->curl->get_infobip_account_balance();
    }

    function toggle_sms_delivery(){
        if($this->application_settings->sender_id == "Chamasoft"||$this->application_settings->sender_id == "EazzyClub"){
            if($this->application_settings->disable_smses){
                $this->_disable_sms_delivery(TRUE);
            }else{
                if($this->application_settings->sender_id == "EazzyClub"){
                    if($result = $this->sms_gateway->get_africas_talking_balance()){
                        $balance = isset($result['data']->UserData->balance)?$result['data']->UserData->balance:'';
                        $balance = str_replace("KES",'',trim($balance));
                        if($balance<500){
                            $this->_disable_sms_delivery();
                        }else{
                            $this->_enable_sms_delivery();
                        }
                    }else{
                        $this->_disable_sms_delivery();
                    }
                }else{
                    if($balance = $this->curl->get_infobip_account_balance()){
                        if($balance<25){
                            $this->_disable_sms_delivery();
                        }else{
                            $this->_enable_sms_delivery();
                        }
                    }else{
                        $this->_disable_sms_delivery();
                    }
                }
            }
        }else{
            echo "Toggle SMS disabled for the Sender ID: ".$this->application_settings->sender_id;
        }
    }

    function _disable_sms_delivery($ignore_email = FALSE){
        if($this->application_settings->sms_delivery_enabled==1||$this->application_settings->sms_delivery_enabled==NULL){
            $input = array(
                'sms_delivery_enabled' => 0,
                'modified_on' => time(),
                'modified_by' => 1
            );
            if($this->settings_m->update(1,$input)){
                if($ignore_email){

                }else{
                    if($this->messaging->send_sms_delivery_toggle_email(FALSE)){

                    }else{
                        echo "Could not send message to Finance Team";
                    }
                }
            }else{
                echo "Could not update SMS delivery setting";
            }
        }else{
            echo "SMS delivery already disabled";
        }
    }

    function _enable_sms_delivery(){
        if($this->application_settings->sms_delivery_enabled==0){
            $input = array(
                'sms_delivery_enabled' => 1,
                'modified_on' => time(),
                'modified_by' => 1
            );
            if($this->settings_m->update(1,$input)){
                if($this->messaging->send_sms_delivery_toggle_email(TRUE)){

                }else{
                    echo "Could not send message to Finance Team";
                }
            }else{
                echo "Could not update SMS delivery setting";
            }
        }else{
            echo "SMS delivery already enabled";
        }
    }


    function delete_old_queued_smses(){
        $this->sms_m->delete_old_queued_smses();
    }

    function test($phone = "254728747061"){
        $reference_number = rand(100000000,999999999);
        $from = isset($this->application_settings->sender_id)?$this->application_settings->sender_id:'Eazzykikundi';
        $message = "This is a simple test from {FirstName} on ".$from." portal";
        $user = (object)array(
            'id' => 1,
            'first_name' => 'Geoffrey',
            'last_name' => 'Githaiga',
        );
        $result = $this->sms_gateway->send_sms_via_africas_talking(valid_phone($phone),$message,$from,$reference_number,$user);
        print_r($result);
    }

 }
