<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('countries_m');
	}

	public function _remap($method, $params = array()){
       if(method_exists($this, $method)){
           return call_user_func_array(array($this, $method), $params);
       }
       $this->output->set_status_header('404');
       header('Content-Type: application/json');
       $file = file_get_contents('php://input')?(array)json_decode(file_get_contents('php://input')):array();
       $request = $_REQUEST+$file;
       echo encrypt_json_encode(
       	array(
       		'response' => array(
		       		'status'	=>	404,
		       		'message'		=>	'404 Method Not Found for URI: '.$this->uri->uri_string(),
       			),

       	)
       	);
	}

	function get_country_options(){
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
            $country_options = $this->countries_m->get_country_options();
            $countries = array();
            foreach ($country_options as $id => $name) {
                $countries[] = array(
                    'id' => $id,
                    'name' => $name,
                );
            }
            $response = array(
                'status' => 1,
                'message' => 'successful',
                'countries' => $countries,
            );
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
	}


    function get_currency_options(){
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
            $currency_options = $this->countries_m->get_currency_options();
            $currencies = array();
            foreach ($currency_options as $id => $name) {
                $currencies[] = array(
                    'id' => $id,
                    'name' => $name,
                );
            }
            $response = array(
                'status' => 1,
                'message' => 'successful',
                'currencies' => $currencies,
            );
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

}?>