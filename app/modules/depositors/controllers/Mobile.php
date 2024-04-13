<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{

	protected $data=array();

     protected $validation_rules=array(
            array(
                    'field' =>  'name',
                    'label' =>  'Depositor Name',
                    'rules' =>  'trim|required|callback__is_depositor_name_unique',
                ),
            array(
                    'field' =>   'email',
                    'label' =>   'Depositor Email',
                    'rules' =>   'trim|valid_email',
                ),
            array(
                    'field' =>   'phone',
                    'label' =>   'Depositor Phone',
                    'rules' =>   'trim|valid_phone',
                ),
            array(
                    'field' =>  'description',
                    'label' =>  'Depositor Description',
                    'rules' =>  'trim',
                ),
        );

    public function __construct(){
        parent::__construct();
        $this->load->model('depositors_m');
    }

	function create(){
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
                    $this->form_validation->set_rules($this->validation_rules);
                    if($this->form_validation->run()){
                        $id = $this->depositors_m->insert(
                            array(
                                'name'  =>  $this->input->post('name'),
                                'email'  =>  $this->input->post('email'),
                                'phone'  =>  $this->input->post('phone'),
                                'description'  =>  $this->input->post('description'),
                                'group_id'  =>  $this->group->id,
                                'active'    =>  1,
                                'created_by'    =>  $this->user->id,
                                'created_on'    =>  time(),
                            )
                        );
                        if($id){
                            $response = array(
                                'status' => 1,
                                'time' => time(),
                                'success' => 'Depositor successfully created',
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'time' => time(),
                                'error' => 'Unable to create a new Depositor',
                            );
                        }
                    }else{
                        $post = array();
                        $form_errors = $this->form_validation->error_array();
                        foreach ($form_errors as $key => $value) {
                            $post[$key] = $value;
                        }
                        $response = array(
                                'status' => 0,
                                'time' => time(),
                                'error' => 'Form validation failed',
                                'validation_errors' => $post,
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
        echo encrypt_json_encode(array('response'=>$response));
	}

    function get_group_depositor_options(){
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
                    $depositors = $this->depositors_m->get_all_group_depositors($this->group->id);;
                    $posts = array();
                    foreach ($depositors as $depositor) {
                       $posts[] = array(
                            'id' => $depositor->id,
                            'name' => $depositor->name,
                            'identity' =>   valid_email($depositor->email)?$depositor->email:$depositor->phone, 
                       );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Depositors',
                        'time' => time(),
                        'depositors' => $posts,
                    );
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
        echo encrypt_json_encode(array('response'=>$response));
    }

    function _is_depositor_name_unique(){
        $name = $this->input->post('name');
        $group_id = $this->input->post('group_id');
        if($group_id && $name){
            if(!$this->depositors_m->is_name_unique($name,$group_id)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_is_depositor_name_unique','Depositor already exists in the Group');
                return FALSE;
            }
        }else{
            $this->form_validation->set_message('_is_depositor_name_unique','Essential parameters missing');
            return FALSE;
        }
    }
}
?>