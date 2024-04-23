<?php  defined('BASEPATH') OR exit('No direct script access allowed');
class Mobile extends Mobile_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->model('notifications_m');
    }

    public function mark_all_as_read(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $mark_all_as_read = $this->input->post('mark_all_notifications_as_read')?:0;
                    if($mark_all_as_read){
                        if($this->notifications_m->mark_all_member_notifications_as_read($this->member->id,$this->group->id)){
                            $response = array(
                                'status' => 1,
                                'time' => time(),
                                'success' => 'success.',
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'error' => 'Error occured while updating notifications.',
                            );
                        }
                    }else{
                       $response = array(
                            'status' => 0,
                            'time' => time(),
                            'error' => 'Kindly try again later. No operation performed.',
                        ); 
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function mark_all_as_unread(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $mark_all_as_read = $this->input->post('mark_all_notifications_as_unread')?:0;
                    if($mark_all_as_read){
                        if($this->notifications_m->mark_all_member_notifications_as_unread($this->member->id,$this->group->id)){
                            $response = array(
                                'status' => 1,
                                'time' => time(),
                                'success' => 'success.',
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'error' => 'Error occured while updating notifications.',
                            );
                        }
                    }else{
                       $response = array(
                            'status' => 0,
                            'time' => time(),
                            'error' => 'Kindly try again later. No operation performed.',
                        ); 
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function mark_as_read(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $notification_id = $this->input->post('notification_id')?:'';
                    if($notification_id && $notification = $this->notifications_m->get($notification_id,$group_id)){
                        if($this->notifications_m->update($notification->id,array(
                                'is_read' => 1,
                                'modified_on' => time(),
                            ))){
                            $response = array(
                                'status' => 1,
                                'time' => time(),
                                'success' => 'success',
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'error' => 'Error updating notification',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'time' => time(),
                            'error' => 'Notification not available',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));       
    }

    function get_all_group_member_notifications(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $lower_limit = $this->input->post('lower_limit')?:0;
                    $upper_limit = $this->input->post('upper_limit')?:100;
                    $records_per_page = $upper_limit - $lower_limit;
                    if($lower_limit>$upper_limit){
                        $records_per_page = 100;
                    }
                    $total_rows = $this->notifications_m->count_member_notifications('',$this->group->id,$this->member->id);
                    $pagination = create_custom_pagination('group/reports/listing/pages',$total_rows,$records_per_page,$lower_limit,TRUE);
                    $posts = $this->notifications_m->limit($pagination['limit'])->get_member_notifications('',$this->group->id,$this->member->id);
                    $list_notifications = array();
                    $count = $lower_limit;
                    foreach ($posts as $key => $post) {
                       $list_notifications[] = array_merge((array)$post,array(
                            'count' => $count+($key+1),
                            'id' => $post->id,
                            'subject' => $post->subject,
                            'message' => clean_notification($post->message),
                            'category' => $post->category,
                            'time_ago' => timestamp_to_time_elapsed($post->created_on),
                            'is_read' => $post->is_read,
                       ));
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Member notifications',
                        'time' => time(),
                        'all_notification_counts' => count($list_notifications),
                        'notifications' => $list_notifications,
                        'notification_count' => $this->notifications_m->count_unread_member_notifications($this->group->id,$this->member->id),
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function get_group_member_notification_count(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    $response = array(
                        'status' => 1,
                        'message' => 'Member notifications',
                        'time' => time(),
                        'notification_count' => $this->notifications_m->count_unread_member_notifications($this->group->id,$this->member->id),
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }

    function create_test(){
        foreach ($this->request as $key => $value) {
            if(preg_match('/phone/', $key)){
                $_POST[$key] = valid_phone($value);
            }else{
                $_POST[$key] = $value;
            }
        }
        $user_id = $this->input->post('user_id');
        if($this->user = $this->ion_auth->get_user($user_id)){
            $this->ion_auth->update_last_login($this->user->id);
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }
}?>