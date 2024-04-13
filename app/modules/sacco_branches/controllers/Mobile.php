<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile extends Mobile_Controller{

  function __construct(){
        parent::__construct();
        $this->load->model('sacco_branches_m');
    }

    public function _remap($method, $params = array()){
        if(method_exists($this, $method)){
           return call_user_func_array(array($this, $method), $params);
        }
        $this->output->set_status_header('404');
        header('Content-Type: application/json');
        $file = file_get_contents('php://input')?(array)json_decode(file_get_contents('php://input')):array();
        $request = $_REQUEST+$file;
        echo json_encode(
        array(
            'response' => array(
                'status'    =>  404,
                'message'       =>  '404 Method Not Found for URI: '.$this->uri->uri_string(),
            ))
        );
    }


    function get_sacco_branches_options(){
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
                    $sacco_id = $this->input->post('sacco_id');
                    if(is_numeric($sacco_id)){
                        $sacco_branches = array();
                        $posts = $this->sacco_branches_m->get_sacco_branch_options_by_sacco_id($sacco_id);
                        foreach ($posts as $id => $name) {
                           $sacco_branches[] = array(
                                'id' => $id,
                                'name' => $name,
                           );
                        }
                        $response = array(
                            'status' => 1,
                            'message' => 'successful',
                            'sacco_branches' => $sacco_branches,
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Request submitted is invalid',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
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

}