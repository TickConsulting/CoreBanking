<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{

	protected $data=array();

     protected $validation_rules=array(
            array(
                    'field' =>  'name',
                    'label' =>  'Recipient Name',
                    'rules' =>  'trim|required|callback__is_recipient_name_unique',
                ),
            array(
                    'field' =>   'email',
                    'label' =>   'Recipient Email',
                    'rules' =>   'trim|valid_email',
                ),
            array(
                    'field' =>   'phone',
                    'label' =>   'Recipient Phone',
                    'rules' =>   'trim|valid_phone',
                ),
            array(
                    'field' =>  'description',
                    'label' =>  'Recipient Description',
                    'rules' =>  'trim',
                ),
        );

    public function __construct(){
        parent::__construct();
        $this->load->model('recipients_m');
    }

	function create(){
		$file = file_get_contents('php://input');
        $response = array();
        $request = array();
        header('Content-Type: application/json');
        $time = time();
        if($file){
            $result = json_decode($file);
            $request = $result;
            if($result){
                $user_id = isset($result->current_user_id)?$result->current_user_id:0;
                $group_id = isset($result->group_id)?$result->group_id:0;
                if($user_id&&$group_id){
                    $user = $this->ion_auth->get_user($user_id);
                    if($user){
                        $group = $this->groups_m->get($group_id);
                        if($group){
                            foreach ($result as $result_key => $result_value) {
                                $_POST[$result_key] = $result_value;
                            }
                            $_POST['group_id'] = $group_id;
                            $this->form_validation->set_rules($this->validation_rules);
                            if($this->form_validation->run()){
                                $id = $this->recipients_m->insert(
                                    array(
                                        'name'  =>  $this->input->post('name'),
                                        'email'  =>  $this->input->post('email'),
                                        'phone'  =>  $this->input->post('phone'),
                                        'description'  =>  $this->input->post('description'),
                                        'group_id'  =>  $group->id,
                                        'active'    =>  1,
                                        'created_by'    =>  $user->id,
                                        'created_on'    =>  time(),
                                    )
                                );
                                if($id){
                                    $response = array(
                                        'status' => 1,
                                        'time' => time(),
                                        'success' => 'Recipient successfully created',
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'time' => time(),
                                        'error' => 'Unable to create a new Recipient',
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
                                'status' => 5,
                                'time' => time(),
                                'error' => 'Group details not found',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 4,
                            'time' => time(),
                            'error' => 'User details not found',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'time' => time(),
                        'error' => 'essential values missing',
                    );
                }
            }else{
                $response = array(
                    'status' => 3,
                    'time' => time(),
                    'error' => 'Invalid file sent',
                );
            }
        }else{
            $response = array(
                    'status' => 2,
                    'time' => time(),
                    'error' => 'No file sent',
                );
        }
        echo json_encode(array('response'=>$response,'request'=>$request));
	}

    function _is_recipient_name_unique(){
        $name = $this->input->post('name');
        $group_id = $this->input->post('group_id');
        if($group_id && $name){
            if(!$this->recipients_m->is_name_unique($name,$group_id)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_is_recipient_name_unique','Recipient already exists in the Group');
                return FALSE;
            }
        }else{
            $this->form_validation->set_message('_is_recipient_name_unique','Essential parameters missing');
            return FALSE;
        }
    }
}
?>