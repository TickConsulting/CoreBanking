<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Curl{
	protected $ci;
	public $equityBankRequests;
	public $darajaRequests;

	public function __construct(){
		$this->ci= & get_instance();
		set_time_limit(0);
        ini_set('memory_limit','2048M');
        ini_set('max_execution_time', 1200);
		$this->equityBankRequests = new EquityBank();
		$this->darajaRequests = new DarajaRequests();
		$this->ci->load->library('messaging');
	}


	function test_mtn($headers = array(),$json_file= '',$url=''){
		$ch = curl_init();  
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, true );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $json_file);    
        $output=curl_exec($ch);
        $err = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if($err){
        	return $err;
        }elseif($info){
        	return $info;
        }else{
        	return $output;
        }
	}

	public function curl_post_xml($xml_file='',$url=''){
		if($xml_file && $url){
			$ch = curl_init();  
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt( $ch, CURLOPT_POST, true );
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_file);    
	        $output=curl_exec($ch);
	        curl_close($ch);
	        return  $output;
		}else{
			$this->ci->session->set_flashdata('error','Ensure all parameters are passed');
			return FALSE;
		}
	}

	public function curl_post_json_pdf($json_file='',$url='',$filename='Chamasoft Report')
    {
    	if($url && $json_file){
    		$ch = curl_init();  
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt($ch,CURLOPT_POST, true );
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	        curl_setopt($ch,CURLOPT_POSTFIELDS, $json_file);    
	        $output=curl_exec($ch);
	        header('Cache-Control: public'); 
			header('Content-type: application/pdf');
			header('Content-Disposition: attachment; filename="'.$filename.'".pdf"');
			header('Content-Length: '.strlen($output));
	        curl_close($ch);
	        return($output);
    	}
    	else{
    		$this->ci->session->set_flashdata('error','Ensure all parameters are passed');
			return FALSE;
    	}
    }

	public function post_json($json_file = '',$url = ''){
    	if($url && $json_file){
    		$ch = curl_init();  
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt($ch,CURLOPT_POST, true );
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	        curl_setopt($ch,CURLOPT_POSTFIELDS, $json_file);    
	        $output = curl_exec($ch);
	        curl_close($ch);
	        return($output);
    	}
    	else{
    		$this->ci->session->set_flashdata('error','Ensure all parameters are passed');
			return FALSE;
    	}
    }

    public function post_json_payment($json_file = '',$url = ''){
    	if($url && $json_file){
    		if($token = $this->get_authorization_token()){
				file_put_contents("logs/payment_request_payload.dat"," Timestamp: ".time().', URL : '.$url.' JSON data '. $json_file."\n",FILE_APPEND);
    			$ch = curl_init();  
		        curl_setopt($ch,CURLOPT_URL,$url);
		        curl_setopt($ch,CURLOPT_POST, true );
		        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		        curl_setopt($ch,CURLOPT_HTTPHEADER, array(
		        	'Authorization: '.$token,
		        	'Content-Type: application/json'
		        ));
		        curl_setopt($ch,CURLOPT_POSTFIELDS, $json_file);    
		        $output=curl_exec($ch);
		        curl_close($ch);
				file_put_contents("logs/payment_request_response.dat"," Timestamp: ".time().', JSON response '.json_encode($output)."\n",FILE_APPEND);
		        return($output);
    		}else{
    			$this->ci->session->set_flashdata('error',$this->ci->session->flashdata('error'));
    			return FALSE;
    		}
    	}
    	else{
    		$this->ci->session->set_flashdata('error','Ensure all parameters are passed');
			return FALSE;
    	}
    }

    function get_authorization_token(){
    	$curl = curl_init();
		// check the url to confirm it is prod or uat.
		$url = "";
		if(
			preg_match('/\.local/', $_SERVER['HTTP_HOST']) || 
			preg_match('/uat\.chamasoft\.com/', $_SERVER['HTTP_HOST']) ||
			preg_match('/chamasoftbeta/', $_SERVER['HTTP_HOST']) || 
			preg_match('/demo\.websacco\.com/', $_SERVER['HTTP_HOST']) || 
			preg_match('/uat\.websacco\.com/', $_SERVER['HTTP_HOST']) 
		){
			$url = "https://api-test.chamasoft.com:443/api/access_tokens/get_access_token";
		}else{
		   $url = "https://api.chamasoft.com:443/api/access_tokens/get_access_token"; 
		}
		curl_setopt_array($curl, array(
		  	CURLOPT_URL => $url,
		  	CURLOPT_RETURNTRANSFER => true,
		  	CURLOPT_ENCODING => "",
		  	CURLOPT_MAXREDIRS => 10,
		  	CURLOPT_TIMEOUT => 30,
		 	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		 	CURLOPT_SSL_VERIFYPEER => 0,
		  	CURLOPT_CUSTOMREQUEST => "GET",
		  	CURLOPT_HTTPHEADER => array(
		    	"Authorization: Basic Y2hhbWFzb2Z0OlI3c1B6THREVmNWV3BNcWZhYXhuUUFVRDNHeVRHN0F3",
		    	"Cache-Control: no-cache",
		    	"Content-Type: application/json"
		  	),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			$this->ci->session->set_flashdata('error',$err);
		  	return FALSE;
		} else {
		  	$res = json_decode($response);
		  	if($res){
		  		return $res->access_token_type.' '.$res->access_token;
		  	}
		}
    }

    function push_notification($url='',$headers='',$fields=''){
    	if($url && $headers && $fields){
    		$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL,$url);
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			return $result;
    	}else{
    		return FALSE;
    	}
    }

	public function recaptcha_verify($url='',$data=''){
		if($url && $data){
			$ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POST,true );
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($data));
            $result = curl_exec($ch);
            curl_close($ch);
			$status = json_decode($result,true);
			return $status;
		}else{
			return FALSE;
		}
	}

	public function post($post_data = '',$url = ''){
    	if($url && $post_data){
    		$ch = curl_init();  
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt($ch,CURLOPT_POST, true );
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	        //curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	        curl_setopt($ch,CURLOPT_POSTFIELDS, $post_data);    
	        $output=curl_exec($ch);
	        curl_close($ch);
	        return($output);
    	}
    	else{
    		$this->ci->session->set_flashdata('error','Ensure all parameters are passed');
			return FALSE;
    	}
    }

    function get_infobip_account_balance(){
        $url = "https://api.infobip.com/account/1/balance";
        $ch = curl_init();  
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, false );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Accept: application/json','Authorization: Basic ZGlnaXRhbHZpc2lvbjpEaWdpdGFsVmlzaW9uMzIx'));
        //curl_setopt($ch,CURLOPT_POSTFIELDS, $post_data);    
        $output=curl_exec($ch);
        curl_close($ch);
        if($response = json_decode($output)){ 
        	return $response->balance;
        }else{
        	return FALSE;
        }
    }

    function get_request($url = ''){
    	if($url){
    		$curl = curl_init();
			curl_setopt_array($curl, array(
			  	CURLOPT_URL => $url,
			  	CURLOPT_RETURNTRANSFER => true,
			  	CURLOPT_ENCODING => "",
			  	CURLOPT_MAXREDIRS => 10,
			  	CURLOPT_TIMEOUT => 30,
			 	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			 	CURLOPT_SSL_VERIFYPEER => 0,
			  	CURLOPT_CUSTOMREQUEST => "GET",
			));
			$output=curl_exec($curl);
	        curl_close($curl);
	       	return $output;
    	}	
    }

    public function download_file($file_path=''){
    	if($file_path){
	    	if(file_exists($file_path)) {
		        header('Content-Description: File Transfer');
		        header('Content-Type: application/octet-stream');
		        header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
		        header('Expires: 0');
		        header('Cache-Control: must-revalidate');
		        header('Pragma: public');
		        header('Content-Length: ' . filesize($file_path));
		        flush(); // Flush system output buffer
		        readfile($file_path);
		        exit;
		    }
		}
    }
}

class EquityBank{
	function __construct(){
		$this->ci= & get_instance();
		$this->ci->load->config('equity_bank');
		$this->ci->load->model('ipn_m');
		$this->ci->load->model('equity_bank/equity_bank_m');

		// print_r($_REQUEST);

		// die('we in');
	}

	public function _remap($method, $params = array()){
		// die('hehehe');
	}

	function generate_token($configuration = array()){
		$url = $this->ci->config->item('prod_url').$this->ci->config->item('token_url');	
		$grant_type = $this->ci->config->item('grant_type');
		$client_secret = $this->ci->config->item('client_secret');
		$client_id = $this->ci->config->item('client_id');
		$post_fieds = "client_id=".$client_id."&client_secret=".$client_secret."&grant_type=".$grant_type;
		$curl = curl_init();
		curl_setopt_array($curl, array(
  			CURLOPT_URL => $url,
  			CURLOPT_RETURNTRANSFER => true,
  			CURLOPT_ENCODING => "",
  			CURLOPT_MAXREDIRS => 10,
  			CURLOPT_TIMEOUT => 0,
  			CURLOPT_FOLLOWLOCATION => true,
  			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  			CURLOPT_CUSTOMREQUEST => "POST",
  			CURLOPT_POSTFIELDS => $post_fieds,
  			CURLOPT_HTTPHEADER => array(
    			"Content-Type: application/x-www-form-urlencoded"
  			))
		);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if($err){
			$this->ci->session->set_flashdata('error',"cURL Error #:" . $err);
        	return FALSE;
		}else{
			if($response){
				$result = json_decode($response);
	        	if($result){
	        		if(isset($result->access_token)){
	        			if($configuration){
		        			$update = array(
		        				'access_token' => $result->access_token,
		        				'access_token_expires_at' => strtotime('+'.$result->expires_in.' seconds',time()),
		        				'access_token_type' => "Bearer",
		        				'modified_on' => time(),
		        			);
		        			if($this->ci->equity_bank_m->update_configuration($configuration->id,$update)){
		        				return TRUE;
		        			}else{
		        				$this->ci->session->set_flashdata('error','Could not update token');
		        				return FALSE;
		        			}
		        		}else{//insert
		        			$insert = array(
		        				'grant_type' => $grant_type,
		        				'client_secret'=> $client_secret,
		        				'client_id' => $client_id,
		        				'access_token' => $result->access_token,
		        				'access_token_expires_at' => strtotime('+'.$result->expires_in.' seconds',time()),
		        				'access_token_type' => "Bearer",
		        				'created_on' => time(),
		        				'active' => 1,
		        				'is_default'=> 1,
		        			);
		        			if($this->ci->equity_bank_m->insert_configuration($insert)){
		        				return TRUE;
		        			}else{
		        				$this->ci->session->set_flashdata('error','Could not update token');
		        				return FALSE;
		        			}
		        		}
	        		}else{
	        			$this->ci->session->set_flashdata('error',$result->message);
	        			return FALSE;
	        		}
	        	}else{
	        		$this->ci->session->set_flashdata('error','Could not decode message from server');
	        		return FALSE;
	        	}
			}else{
				$this->ci->session->set_flashdata('error','There was an error. Could not get the response');
	        	return FALSE;
			}
		}
	}

	function client_account_token($configuration = array()){
		
		$curl = curl_init();		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $this->ci->config->item('prod_url').$this->ci->config->item('token_url'),
		  	CURLOPT_RETURNTRANSFER => true,
		  	CURLOPT_ENCODING => "",
		  	CURLOPT_MAXREDIRS => 10,
		  	CURLOPT_TIMEOUT => 0,
		  	CURLOPT_FOLLOWLOCATION => true,
		  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  	CURLOPT_CUSTOMREQUEST => "POST",
		  	//CURLOPT_POSTFIELDS => "client_id=".$this->ci->config->item('token_client_id')."&grant_type=".$this->ci->config->item('token_grant_type'),

		  	CURLOPT_POSTFIELDS => 'client_id='.$this->ci->config->item('client_id').'&grant_type='.$this->ci->config->item('token_grant_type').'&client_secret='.$this->ci->config->item('client_secret'),

		  	CURLOPT_HTTPHEADER => array("Content-Type: application/x-www-form-urlencoded")
		));
		$response = curl_exec($curl);
		curl_close($curl);			
		if($response){
			$res = json_decode($response);
			if($res){
				// $token_type = $res->token_type;
				// $access_token = $res->access_token;
				// return $token_type.' '.$access_token;
        		if(isset($res->access_token)){
        			if($configuration){
	        			$update = array(
	        				'access_token' => $res->access_token,
	        				'access_token_expires_at' => strtotime('+'.$res->expires_in.' seconds',time()),
	        				'access_token_type' => "Bearer",
	        				'modified_on' => time(),
	        			);
	        			if($this->ci->equity_bank_m->update_configuration($configuration->id,$update)){
	        				return TRUE;
	        			}else{
	        				$this->ci->session->set_flashdata('error','Could not update token');
	        				return FALSE;
	        			}
	        		}else{//insert
	        			$insert = array(
	        				'grant_type' => $this->ci->config->item('token_grant_type'),
	        				'client_id' => $this->ci->config->item('token_client_id'),
	        				'access_token' => $res->access_token,
	        				'access_token_expires_at' => strtotime('+'.$res->expires_in.' seconds',time()),
	        				'access_token_type' => "Bearer",
	        				'created_on' => time(),
	        				'active' => 1,
	        				'is_default'=> 1,
	        			);
	        			if($this->ci->equity_bank_m->insert_configuration($insert)){
	        				return TRUE;
	        			}else{
	        				$this->ci->session->set_flashdata('error','Could not update token');
	        				return FALSE;
	        			}
	        		}
        		}else{
        			if(isset($res->message)){
        				$this->ci->session->set_flashdata('error','You are not allowed to perform this request. IP is'.$res->message);
        			}else{
        				$this->ci->session->set_flashdata('error',$res->error_description);
        			}
        		}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function send_sms($post_fieds=''){		
		if($post_fieds){
			$token = $this->ci->equity_bank_m->get_token();
			if($token){
				$url = $this->ci->config->item('prod_url').$this->ci->config->item('notification_url');	
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS =>$post_fieds,
				  CURLOPT_HTTPHEADER => array(
				    "Authorization: ".$token,
				    "Content-Type: application/json"
				  ),
				));
				$response = curl_exec($curl);
				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$err = curl_error($curl);
				curl_close($curl);
				if ($err) {
					$this->ci->session->set_flashdata('error',"cURL Error #:" . $err);
					return FALSE;
				} else {
					if($httpcode!=200){
						return $this->ci->session->set_flashdata('error',"Http code error :".$httpcode);
					}else{
						return TRUE;
					}
				}
			}else{
				$this->ci->session->set_flashdata('error','error generating token');
	        	return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Form post fields are required');
			return FALSE;
		}
	}

	function generate_otp($post_fieds = ''){
		if($post_fieds){
			$token = $this->ci->equity_bank_m->get_token();
			if($token){
				$generate_otp_url = $this->ci->config->item('prod_url').$this->ci->config->item('generate_otp_url');
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $generate_otp_url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS =>$post_fieds,
				  CURLOPT_HTTPHEADER => array(
				    "Authorization: ".$token,
				    "Content-Type: application/json"
				  ),
				));
				$response = curl_exec($curl);

				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$err = curl_error($curl);

				curl_close($curl);
				if ($err) {
					$this->ci->session->set_flashdata('error',"cURL Error #:" . $err);
					return FALSE;
				} else {
					if($httpcode!=200){
						return $this->ci->session->set_flashdata('error',"Http code error :".$httpcode);
					}else{
						$decoded = json_decode($response);
						if(isset($decoded->statusCode)&&$decoded->statusCode == "00"){
							return TRUE;
						}
						return FALSE;
					}
				}
			}else{
				$this->ci->session->set_flashdata('error','error generating token');
	        	return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Form post fields are required');
			return FALSE;
		}
	}

	function verify_otp($post_fieds = ''){
		if($post_fieds){
			$token = $this->ci->equity_bank_m->get_token();
			if($token){
				$url = $this->ci->config->item('prod_url').$this->ci->config->item('verify_otp_url');
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS =>$post_fieds,
				  CURLOPT_HTTPHEADER => array(
				    "Authorization: ".$token,
				    "Content-Type: application/json"
				  ),
				));
				$response = curl_exec($curl);
				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$err = curl_error($curl);
				if ($err) {
					$this->ci->session->set_flashdata('error',"cURL Error #:" . $err);
					return FALSE;
				} else {
					if($httpcode!=200){
						if($decoded = json_decode($response)){
							if(isset($decoded->statusMessage)){
								$this->ci->session->set_flashdata('error',"Approval code error: ".$decoded->statusMessage);
							}else{
								$this->ci->session->set_flashdata('error',"Approval code error. Please use another approval code or resend to get a new one");
							}
						}else{
							$this->ci->session->set_flashdata('error',"Approval code error. Please use another approval code or resend to get a new one");
						}
						return FALSE;
					}else{
						$decoded = json_decode($response);
						if(isset($decoded->statusCode)&&$decoded->statusCode == "00"){
							return TRUE;
						}
						$this->ci->session->set_flashdata('error',$decoded->statusMessage);
						return FALSE;
					}
				}
			}else{
				$this->ci->session->set_flashdata('error','error generating token');
	        	return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Form post fields are required');
			return FALSE;
		}
	}

	function mobile_money_funds_transfer($reference='',$phone_number='',$amount=0,$source_account_number='',$country_code='',$withdrawal_request=array()){
		if($reference && $phone_number && $amount && $source_account_number&&$withdrawal_request){
			if($withdrawal_request->is_disbursed == '0'&&$withdrawal_request->status=="1"&&$withdrawal_request->is_approved=="1"&&$withdrawal_request->active=="1"){
				$phone_number = valid_phone($phone_number);
				$telco_source = substr($phone_number,0,3)==255?'Vodacom':'Mpesa';
				//$phone_number = substr($phone_number,0,-6).'000000';
				//$source_bank_id = ($source_currency=="TZS"?"55":"54");
				$post_fieds = json_encode(array(
					"reference" => $reference,
				    "phoneNumber" => $phone_number,
				    "telco" => $telco_source,
				    "amount" => $amount,
				    "sourceAccount" => $source_account_number,
				    "countryCode"=>$country_code,
				));
				if($token = $this->ci->equity_bank_m->get_token()){
					$url = $this->ci->config->item('prod_url').$this->ci->config->item('mobile_money_url');
					$curl = curl_init();
					curl_setopt_array($curl, array(
					  CURLOPT_URL => $url,
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => "",
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => "POST",
					  CURLOPT_POSTFIELDS =>$post_fieds,
					  CURLOPT_HTTPHEADER => array(
					    "Authorization: ".$token,
					    "Content-Type: application/json"
					  ),
					));
					$response = curl_exec($curl);
					$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
					$err = curl_error($curl);
					curl_close($curl);
					if ($err) {
						$this->ci->session->set_flashdata('error',"cURL Error #:" . $err);
						return FALSE;
					} else {					
						if($response){
							if($file = json_decode($response)){														
								if(isset($file->statusCode)){
									if($file->statusCode == '00'){
										return TRUE;
									}else{
										$this->ci->session->set_flashdata('error',$file->statusMessage);
										return FALSE;
									}
								}else{
									$this->ci->session->set_flashdata('error','The transaction could not be completed at the moment due to a server error');
									return FALSE;
								}
							}else{
								$this->ci->session->set_flashdata('error',"Http code error :".$httpcode.": Invalid response received from the server side");
								return FALSE;
							}
							//print_r($file);die;
						}else{
							$error = $err?:'';
							$code = $httpcode?:'';
							$this->ci->session->set_flashdata('error','Invalid response received from the server side. Error: '.$error.'. HttpCode: '.$code);
	        				return FALSE;
						}
					}
				}else{
					$this->ci->session->set_flashdata('error','error generating token');
		        	return FALSE;
				}
			}else{
				$this->ci->session->set_flashdata('error','This withdrawal request is not marked for disbursement. Please contact the admin');
		        return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Form post fields are required');
			return FALSE;
		}
	}

	function funds_transfer($reference_number='',$narration='',$source_account='',$amount=0,$source_currency='',$destination_account='',$destination_currency='',$withdrawal_request = array()){
		if($reference_number&&$narration&&$source_account&&$amount&&$source_currency&&$destination_account&&$destination_currency&&$withdrawal_request){
			if($withdrawal_request->is_disbursed == '0'&&$withdrawal_request->status=="1"&&$withdrawal_request->is_approved=="1"&&$withdrawal_request->active=="1"){
				$transaction_reference_number = $reference_number;
				$source_bank_id = ($source_currency=="TZS"?"55":"54");
				$destination_bank_id = ($destination_currency=="TZS"?"55":"54");

				$json_file = json_encode(array(
				    "reference" => $reference_number,
				    "sourceAccount" => $source_account,
				    "destinationAccount" => $destination_account,
				    "amount" => $amount,
				    "bankId" => $source_bank_id,
				    "paymentReason" => $narration
				));
				$token = $this->ci->equity_bank_m->get_token();
				if($token){
					$url = $this->ci->config->item('prod_url').$this->ci->config->item('internal_funds_transfer_url');
					$curl = curl_init();
					curl_setopt_array($curl, array(
					  CURLOPT_URL => $url,
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => "",
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => "POST",
					  CURLOPT_POSTFIELDS =>$json_file,
					  CURLOPT_HTTPHEADER => array(
					    "Authorization: ".$token,
					    "Content-Type: application/json"
					  ),
					));
					$response = curl_exec($curl);
					$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
					$err = curl_error($curl);
					curl_close($curl);
					if ($err) {
						$this->ci->session->set_flashdata('error',"cURL Error #:" . $err);
						return FALSE;
					} else {
						if($httpcode!=200){
							if($decode_file = json_decode($response)){
								if(isset($decode_file->errors)){
									$this->ci->session->set_flashdata('error',serialize($decode_file->errors));
								}elseif(isset($decode_file->message)){
									if($decode_file->message){
										$this->ci->session->set_flashdata('error',$decode_file->message);
									}else{
										$this->ci->session->set_flashdata('error',"Http code error :".$httpcode.' with empty message');
									}
								}elseif(isset($decode_file->statusMessage)){
									if($decode_file->statusMessage){
										$this->ci->session->set_flashdata('error',$decode_file->statusMessage);
									}else{
										$this->ci->session->set_flashdata('error',"Http code error :".$httpcode.' with empty status message');
									}
								}else{
									$this->ci->session->set_flashdata('error',"Http code error :".$httpcode.' with no errors');
								}
								return FALSE;
							}else{
								$this->ci->session->set_flashdata('error',"Http code error :".$httpcode);
								return FALSE;
							}
						}else{
							if($file = json_decode($response)){
								if(isset($file->transactionResult)){
									if($file->transactionResult == "0"){
										return TRUE;
									}else{
										$this->ci->session->set_flashdata('error',"Http code error :".$httpcode.' with transaction message');
									}
								}else{
									$message = $file->statusMessage;
									if($file->statusCode == '00'){
										return TRUE;
									}else{
										if($decode_file->message){
											$this->ci->session->set_flashdata('error',$decode_file->message);
										}else{
											$this->ci->session->set_flashdata('error',"Http code error :".$httpcode.' with empty message');
										}
									}
								}
							}else{
								$this->ci->session->set_flashdata('error',"Http code error :".$httpcode.": Invalid response received from the server side");
								return FALSE;
							}
						}
					}
				}else{
					$this->ci->session->set_flashdata('error','error generating token');
		        	return FALSE;
				}
			}else{
				$this->ci->session->set_flashdata('error','This withdrawal request is not marked for disbursement. Please contact the admin');
		        return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Form post fields are required');
			return FALSE;
		}
	}

	function generateHMACSignature($reference_number='',$accountNumber='',$amount=0,$currency='',$transaction_type=''){
		// $reference_number = "2312313339841";
		// $accountNumber = "1100194977404";
		// $amount = 145;
		// $currency = "KES";
		$encoded_reference = base64_encode($reference_number);
		$input = $accountNumber.'&'.$amount.'&'.strtoupper($currency).'&'.$transaction_type;
		return base64_encode(hash_hmac('sha256', $input,base64_decode($encoded_reference),true));
	}

	function generate_account_token(){
		$username = "denis.dev@equitybank.co.ke";
		$password = "denis123";
		$client_id = "44FEC6A5E3F5454";
		$url = "https://api-omnichannel-dev.azure-api.net/v2/oauth/token";
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  	CURLOPT_URL => $url,
		  	CURLOPT_RETURNTRANSFER => true,
		  	CURLOPT_ENCODING => "",
		  	CURLOPT_MAXREDIRS => 10,
		  	CURLOPT_TIMEOUT => 0,
		  	CURLOPT_FOLLOWLOCATION => true,
		  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  	CURLOPT_CUSTOMREQUEST => "POST",
		  	CURLOPT_POSTFIELDS => "username=".$username."&password=".$password."&client_id=".$client_id."&grant_type=password",
		  	CURLOPT_HTTPHEADER => array(
		    	"Content-Type: application/x-www-form-urlencoded"
		  	),
		));
		$response = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$err = curl_error($curl);
		curl_close($curl);
		if($err){
			$this->ci->session->set_flashdata('error',"Curl Error: ".$err);
			return FALSE;
		}else{
			if($httpcode == 200){
				if($file = json_decode($response)){
					return $file->token_type.' '.$file->access_token;
				}else{
					$this->ci->session->set_flashdata('error',"Invalid response");
					return FALSE;
				}
			}else{
				$this->ci->session->set_flashdata('error',"HTTP Code Error: ".$httpcode);
				return FALSE;
			}
		}
	}

	function account_lookup($account_number=0,$bankId=55){
		$url = $this->ci->config->item('prod_url').$this->ci->config->item('account_lookup');
		$url = $url.$account_number."?bankId=".$bankId;
		if($account_number){
			$token = $this->ci->equity_bank_m->get_token();
			if($token){
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
		  			CURLOPT_RETURNTRANSFER => true,
		  			CURLOPT_ENCODING => "",
				  	CURLOPT_MAXREDIRS => 10,
				  	CURLOPT_TIMEOUT => 0,
				  	CURLOPT_FOLLOWLOCATION => true,
				  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  	CURLOPT_CUSTOMREQUEST => "GET",
				  	CURLOPT_HTTPHEADER => array(
				    	"Content-Type: application/json",
				    	"Authorization: ".$token,
				  	),
				));
				$response = curl_exec($curl);
				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$err = curl_error($curl);
				curl_close($curl);
				if($err){
					$error = "cURL Error #:" . $err;
					$this->ci->session->set_flashdata('error',$error);
					$this->_alert_developer($url,$error,'Account Lookup');
					return FALSE;
				}else{
					if($file = json_decode($response)){
						if(isset($file->statusCode)){
							if($file->statusCode=='00'){
								$account_details = new StdClass;
								$res = $file->responseObject;
								$account_details->account_name = $res->accountName;
								$account_details->account_number = $res->accountNumber;
								$account_details->account_currency = $res->accountCurrency;
								$account_details->cif = $res->cif;
								return $account_details;
							}else{
								$this->ci->session->set_flashdata('error','Sorry we are experiencing some technical hitches at the moment. Try again later as this issue is resolved');
								$this->_alert_developer($url,$response,'Account Lookup');
								return FALSE;
							}
						}else{
							$error = 'The status from the server could not be decoded at the moment. Kindly try again later. File: '.$response;
							$this->ci->session->set_flashdata('error',$error);
							$this->_alert_developer($url,$error,'Account Lookup');
							return FALSE;
						}
					}else{
						$error = 'The response from the server could not be decoded at the moment. Kindly try again later';
						$this->ci->session->set_flashdata('error',$error);
						$this->_alert_developer($url,$error,'Account Lookup');
	        			return FALSE;
					}
				}
			}else{
				$error = 'error generating token';
				$this->ci->session->set_flashdata('error',$error);
				$this->_alert_developer($url,$error,'Account Lookup');
	        	return FALSE;
			}
		}else{
			$error = 'Account number is required';
			$this->ci->session->set_flashdata('error',$error);
			$this->_alert_developer($url,$error,'Account Lookup');
			return FALSE;
		}
	}

	function telco_lookup($phone_number=0,$account_type="Mpesa"){
		$url = $this->ci->config->item('prod_url').$this->ci->config->item('telco_lookup');
		if($phone_number){
			$token = $this->ci->equity_bank_m->get_token();
			if($token){
				$post_fieds = json_encode(array(
						"phoneNumber" => $phone_number,
						"telco" => $account_type,
				));
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS =>$post_fieds,
				  CURLOPT_HTTPHEADER => array(
				    "Content-Type: application/json",
				    "Authorization: ".$token,
				  ),
				));
				$response = curl_exec($curl);
				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$err = curl_error($curl);
				curl_close($curl);
				if($err){
					$error = "cURL Error #:" . $err;
					$this->ci->session->set_flashdata('error',$error);
					$this->_alert_developer($url,$error,'Telco Lookup');
					return FALSE;
				}else{
					if($file = json_decode($response)){
						if(isset($file->statusCode)){
							if($file->statusCode=="00"){
								$details = new StdClass;
								$res = $file->responseObject;
								$details->customer_name = $res->customerName;
								return $details;
							}else{
								if($file->statusMessage){
									$this->ci->session->set_flashdata('error','Sorry we are experiencing some technical hitches at the moment. Try again later as this issue is resolved');
								}else{
									$this->ci->session->set_flashdata('error','Sorry we are experiencing some technical hitches at the moment. Try again later as this issue is resolved');	
								}

								$this->_alert_developer($url,$response,'Telco Lookup');
								return FALSE;
							}
						}else{
							$error = 'The status from the server could not be decoded at the moment. Kindly try again later. File: '.$response;
							$this->ci->session->set_flashdata('error',$error);
							$this->_alert_developer($url,$error,'Telco Lookup');
							return FALSE;
						}
					}else{
						$error = 'The response from the server could not be decoded at the moment. Kindly try again later';
						$this->ci->session->set_flashdata('error',$error);
						$this->_alert_developer($url,$error,'Telco Lookup');
	        			return FALSE;
					}
				}
			}else{
				$error = 'error generating token';
				$this->ci->session->set_flashdata('error',$error);
				$this->_alert_developer($url,$error,'Telco Lookup');
	        	return FALSE;
			}
		}else{
			$error = 'Phone number is required';
			$this->ci->session->set_flashdata('error',$error);
			$this->_alert_developer($url,$error,'Telco Lookup');
			return FALSE;
		}
	}

	function get_account_balance($account_number=0,$bankId = 55){
		$url = $this->ci->config->item('prod_url').$this->ci->config->item('account_balance').$account_number.'?bankid='.$bankId;
		if($account_number && $bankId){
			$token = $this->ci->equity_bank_m->get_token();
			if($token){
				//'https://api-omnichannel-uat.azure-api.net/v1/account/balance/1180180500237?bankid=54',
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  	CURLOPT_URL =>  $url,    
				  	CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
				  	CURLOPT_MAXREDIRS => 10,
				  	CURLOPT_TIMEOUT => 0,
				  	CURLOPT_FOLLOWLOCATION => true,
				  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  	CURLOPT_CUSTOMREQUEST => 'GET',
				  	CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
					    "Authorization: ".$token,
					),
				));
				$response = curl_exec($curl);
				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$err = curl_error($curl);
				curl_close($curl);
				if($err){
					$error = "cURL Error #:" . $err;
					$this->ci->session->set_flashdata('error',$error);
					$this->_alert_developer($url,$error,'Get Account Balance');
					return FALSE;
				}else{
					$body = json_decode($response);
					if($body){
						if($body->statusCode == "00"){
							return currency($body->responseObject->ammount);
						}else{
							$this->ci->session->set_flashdata('error','Sorry we are experiencing some technical hitches at the moment. Try again later as this issue is resolved');
							$this->_alert_developer($url,$response,'Get Account Balance');
							return FALSE;
						}
					}else{
						$error = 'The response from the server could not be decoded at the moment. Kindly try again later';
						$this->ci->session->set_flashdata('error',$error);
						$this->_alert_developer($url,$error,'Get Account Balance');
						return FALSE;
					}
				}
			}else{
				$error = 'error generating token';
				$this->ci->session->set_flashdata('error',$error);
				$this->_alert_developer($url,$error,'Get Account Balance');
	        	return FALSE;
			}
		}else{
			$error = 'Account number is required';
			$this->ci->session->set_flashdata('error',$error);
			$this->_alert_developer($url,$error,'Get Account Balance');
			return FALSE;
		}
	}

	function general_curl_request($url='',$post_fieds=''){
		if($url){
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  	CURLOPT_URL => $url,
			  	CURLOPT_RETURNTRANSFER => true,
			  	CURLOPT_ENCODING => "",
			  	CURLOPT_MAXREDIRS => 10,
			  	CURLOPT_TIMEOUT => 0,
			  	CURLOPT_FOLLOWLOCATION => true,
			  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  	CURLOPT_CUSTOMREQUEST => "POST",
			  	CURLOPT_POSTFIELDS =>$post_fieds,
			  	CURLOPT_HTTPHEADER => array(
			    	"Content-Type: application/json"
			  	),
			));
			$response = curl_exec($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$err = curl_error($curl);
			curl_close($curl);
			if($err){
				$error = "cURL Error #:" . $err;
				$this->ci->session->set_flashdata('error',$error);
				$this->_alert_developer($url,$error,'General Curl Request');
				return FALSE;
			}else{
				return $response;
			}
		}else{
			$error = 'URL and Post Data is required';
			$this->ci->session->set_flashdata('error',$error);
			$this->_alert_developer($url,$error,'General Curl Request');
			return FALSE;
		}
	}

	function initiate_account_linkage($account_number=0,$countryCode="KE"){
		$url = $this->ci->config->item('prod_url').$this->ci->config->item('initiate_account_linkage');
		if($account_number&&$countryCode){
			$token = $this->ci->equity_bank_m->get_token();
			if($token){
				$post_fieds = json_encode(array(
						"idNumber" => $account_number,
						"countryCode" => $countryCode,
				));
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS =>$post_fieds,
				  CURLOPT_HTTPHEADER => array(
				    "Content-Type: application/json",
				    "Authorization: ".$token,
				  ),
				));
				$response = curl_exec($curl);
				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$err = curl_error($curl);
				curl_close($curl);
				if($err){
					$error = "cURL Error #:" . $err;
					$this->ci->session->set_flashdata('error',$error);
					$this->_alert_developer($url,$error,'Initiate account linkage');
					return FALSE;
				}else{
					if($file = json_decode($response)){
						if(isset($file->statusCode)){
							if($file->statusCode=="00"){
								return $file;
							}else if($file->statusCode=="94"){
								return $file;
							}else{
								if($file->statusMessage){
									$this->ci->session->set_flashdata('error','Sorry we are experiencing some technical hitches at the moment. Try again later as this issue is resolved');
								}else{
									$this->ci->session->set_flashdata('error','Sorry we are experiencing some technical hitches at the moment. Try again later as this issue is resolved');	
								}
								$this->_alert_developer($url,$response,'Initiate account linkage');
								return FALSE;
							}
						}else{
							$error = 'The status from the server could not be decoded at the moment. Kindly try again later. File: '.$response;
							$this->ci->session->set_flashdata('error',$error);
							$this->_alert_developer($url,$error,'Initiate account linkage');
							return FALSE;
						}
					}else{
						$error = 'The response from the server could not be decoded at the moment. Kindly try again later';
						$this->ci->session->set_flashdata('error',$error);
						$this->_alert_developer($url,$error,'Initiate account linkage');
	        			return FALSE;
					}
				}
			}else{
				$error = 'error generating token';
				$this->ci->session->set_flashdata('error',$error);
				$this->_alert_developer($url,$error,'Initiate account linkage');
	        	return FALSE;
			}
		}else{
			$error = 'Phone number is required';
			$this->ci->session->set_flashdata('error',$error);
			$this->_alert_developer($url,$error,'Initiate account linkage');
			return FALSE;
		}
	}

	function account_linkage_send_otp($account_number=0,$recipient_key=0,$countryCode="KE"){
		$url = $this->ci->config->item('prod_url').$this->ci->config->item('account_linkage_send_otp');
		if($account_number&&$recipient_key&&$countryCode){
			$token = $this->ci->equity_bank_m->get_token();
			if($token){
				$post_fieds = json_encode(array(
						"idNumber" => $account_number,
						"recipientKey" => $recipient_key,
						"countryCode" => $countryCode,
				));
				// print_r($post_fieds);die;
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS =>$post_fieds,
				  CURLOPT_HTTPHEADER => array(
				    "Content-Type: application/json",
				    "Authorization: ".$token,
				  ),
				));
				$response = curl_exec($curl);
				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$err = curl_error($curl);
				curl_close($curl);
				if($err){
					$error = "cURL Error #:" . $err;
					$this->ci->session->set_flashdata('error',$error);
					$this->_alert_developer($url,$error,'Account linkage - OTP');
					return FALSE;
				}else{
					if($file = json_decode($response)){
						if(isset($file->statusCode)){
							if($file->statusCode=="00"){
								return $file;
							}else{
								if($file->statusMessage){
									$this->ci->session->set_flashdata('error','Sorry we are experiencing some technical hitches at the moment. Try again later as this issue is resolved');
								}else{
									$this->ci->session->set_flashdata('error','Sorry we are experiencing some technical hitches at the moment. Try again later as this issue is resolved');	
								}
								$this->_alert_developer($url,$response,'Account linkage - OTP');
								return FALSE;
							}
						}else{
							$error = 'The status from the server could not be decoded at the moment. Kindly try again later. File: '.$response;
							$this->ci->session->set_flashdata('error',$error);
							$this->_alert_developer($url,$error,'Account linkage - OTP');
							return FALSE;
						}
					}else{
						$error = 'The response from the server could not be decoded at the moment. Kindly try again later';
						$this->ci->session->set_flashdata('error',$error);
						$this->_alert_developer($url,$error,'Account linkage - OTP');
	        			return FALSE;
					}
				}
			}else{
				$error = 'error generating token';
				$this->ci->session->set_flashdata('error',$error);
				$this->_alert_developer($url,$error,'Account linkage - OTP');
	        	return FALSE;
			}
		}else{
			$error = 'Phone number is required';
			$this->ci->session->set_flashdata('error',$error);
			$this->_alert_developer($url,$error,'Account linkage - OTP');
			return FALSE;
		}
	}

	function account_linkage_verify_otp($account_number=0,$otp=0,$countryCode="KE"){
		$url = $this->ci->config->item('prod_url').$this->ci->config->item('account_linkage_verify_otp');
		if($account_number&&$otp&&$countryCode){
			$token = $this->ci->equity_bank_m->get_token();
			if($token){
				$post_fieds = json_encode(array(
						"idNumber" => $account_number,
						"otp" => $otp,
						"countryCode" => $countryCode,
				));
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "PUT",
				  CURLOPT_POSTFIELDS =>$post_fieds,
				  CURLOPT_HTTPHEADER => array(
				    "Content-Type: application/json",
				    "Authorization: ".$token,
				  ),
				));
				$response = curl_exec($curl);
				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$err = curl_error($curl);
				curl_close($curl);
				if($err){
					$error = "cURL Error #:" . $err;
					$this->ci->session->set_flashdata('error',$error);
					$this->_alert_developer($url,$error,'Account linkage - Verify OTP');
					return FALSE;
				}else{
					if($file = json_decode($response)){
						if(isset($file->statusCode)){
							if($file->statusCode=="00"){
								return $file;
							}else{
								if($file->statusMessage){
									$this->ci->session->set_flashdata('error',$file->statusMessage);
								}else{
									$this->ci->session->set_flashdata('error','Sorry we are experiencing some technical hitches at the moment. Try again later as this issue is resolved');	
									$this->_alert_developer($url,$response,'Account linkage - Verify OTP');
								}
								return FALSE;
							}
						}else{
							$error = 'The status from the server could not be decoded at the moment. Kindly try again later';
							$this->ci->session->set_flashdata('error',$error);
							$this->_alert_developer($url,$error,'Account linkage - Verify OTP');
							return FALSE;
						}
					}else{
						$error = 'The response from the server could not be decoded at the moment. Kindly try again later';
						$this->ci->session->set_flashdata('error',$error);
						$this->_alert_developer($url,$error,'Account linkage - Verify OTP');
	        			return FALSE;
					}
				}
			}else{
				$error = 'error generating token';
				$this->ci->session->set_flashdata('error',$error);
				$this->_alert_developer($url,$error,'Account linkage - Verify OTP');
	        	return FALSE;
			}
		}else{
			$error = 'Phone number is required';
			$this->ci->session->set_flashdata('error',$error);
			$this->_alert_developer($url,$error,'Account linkage - Verify OTP');
			return FALSE;
		}
	}

	function account_linkage_status($account_number=0,$countryCode="KE"){
		$url = $this->ci->config->item('prod_url').$this->ci->config->item('check_linked_account');
		if($account_number&&$countryCode){
			$token = $this->ci->equity_bank_m->get_token();
			if($token){
				$post_fieds = json_encode(array(
						"idNumber" => $account_number,
						"countryCode" => $countryCode,
				));
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $url,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS =>$post_fieds,
				  CURLOPT_HTTPHEADER => array(
				    "Content-Type: application/json",
				    "Authorization: ".$token,
				  ),
				));
				$response = curl_exec($curl);
				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$err = curl_error($curl);
				curl_close($curl);
				if($err){
					$error = "cURL Error #:" . $err;
					$this->ci->session->set_flashdata('error',$error);
					$this->_alert_developer($url,$error,'Account linkage - Verify OTP');
					return FALSE;
				}else{
					if($file = json_decode($response)){
						if(isset($file->statusCode)){
							if($file->statusCode=="00"){
								return $file;
							}else{
								if($file->message){
									$this->ci->session->set_flashdata('error',$file->message);
								}else{
									$this->ci->session->set_flashdata('error','Sorry we are experiencing some technical hitches at the moment. Try again later as this issue is resolved');	
									$this->_alert_developer($url,$response,'Account linkage - Verify OTP');
								}
								return FALSE;
							}
						}else{
							$error = 'The status from the server could not be decoded at the moment. Kindly try again later';
							$this->ci->session->set_flashdata('error',$error);
							$this->_alert_developer($url,$error,'Account linkage - Verify OTP');
							return FALSE;
						}
					}else{
						$error = 'The response from the server could not be decoded at the moment. Kindly try again later';
						$this->ci->session->set_flashdata('error',$error);
						$this->_alert_developer($url,$error,'Account linkage - Verify OTP');
	        			return FALSE;
					}
				}
			}else{
				$error = 'error generating token';
				$this->ci->session->set_flashdata('error',$error);
				$this->_alert_developer($url,$error,'Account linkage - Verify OTP');
	        	return FALSE;
			}
		}else{
			$error = 'Phone number is required';
			$this->ci->session->set_flashdata('error',$error);
			$this->_alert_developer($url,$error,'Account linkage - Verify OTP');
			return FALSE;
		}
	}


	function _alert_developer($url='',$errorMessage='',$topic=''){
		if(preg_match('/(\.local)/',$_SERVER['HTTP_HOST'])){
			return;
		}
		$message = "Hey dev, there is an error. Please check the API for ".$topic.". This is the error ".$errorMessage." The URL ".$url.' over the server host '.$_SERVER['HTTP_HOST'];
		$to='ongidigeofrey@gmail.com';
		$subject='Eazzykikundi Api Error - '.$topic;
		$this->ci->messaging->send_developer_alert_mail(
			$to,
			$subject,
			$message,$topic,'info@chamasoft.com','info@chamasoft.com',
			array(
				// 'aggrey.kiprotich@digitalvision.co.ke',
				// 'brian.mwangi@digitalvision.co.ke'
			)
		);
	}
}

class DarajaRequests{
	function __construct(){
		$this->ci= & get_instance();
		$this->ci->load->model('ipn_m');
	}

	function generate_token($configuration=array()){
		 
		$url = isset($configuration->endpoint)?$configuration->endpoint:"https://sandbox.safaricom.co.ke";
		$url=$url.'/oauth/v1/generate?grant_type=client_credentials';
		 
		$this->api_key = $configuration->api_key;
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  	CURLOPT_URL => $url,
		  	CURLOPT_RETURNTRANSFER => true,
		  	CURLOPT_ENCODING => "",
		  	CURLOPT_MAXREDIRS => 10,
		  	CURLOPT_TIMEOUT => 30,
		  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  	CURLOPT_CUSTOMREQUEST => "GET",
		  	CURLOPT_HTTPHEADER => array(
		    	"Authorization: ".$this->api_key,
		  	),
		));
		$output = curl_exec($curl);
		 
		$err = curl_error($curl);
		curl_close($curl);
        if($output){
        	$result = json_decode($output);
        	if($result){
			 
        		if(isset($result->access_token)){
        			$update = array(
        				'access_token' => $result->access_token,
        				'access_token_expires_at' => strtotime('+'.$result->expires_in.' seconds',time()),
        				'access_token_type' => "Bearer",
        			);
        			if($this->ci->ipn_m->update_configuration($configuration->id,$update)){
        				return TRUE;
        			}else{
        				$this->ci->session->set_flashdata('error','Could not update token');
        				return FALSE;
        			}
        		}else{
        			$this->ci->session->set_flashdata('error',$result->message);
        			return FALSE;
        		}
        	}else{
        		$this->ci->session->set_flashdata('error','Could not decode message from server');
        		return FALSE;
        	}
        }else{
        	$this->ci->session->set_flashdata('error',"cURL Error #:" . $err);
        	return FALSE;
        }
	}

	function process_request($post_data = '',$url='',$shortcode = 0){
		$token = $this->ci->ipn_m->get_token($shortcode);
		if($token){
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  	CURLOPT_URL => $url,
			  	CURLOPT_RETURNTRANSFER => true,
			  	CURLOPT_ENCODING => "",
			  	CURLOPT_MAXREDIRS => 10,
			  	CURLOPT_TIMEOUT => 30,
			  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  	CURLOPT_CUSTOMREQUEST => "POST",
			  	CURLOPT_POSTFIELDS => $post_data,
			  	CURLOPT_HTTPHEADER => array(
			    	"Authorization: ".$token,
			    	"Content-Type: application/json",
			  	),
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				$this->ci->session->set_flashdata('error',"cURL Error #:" . $err);
				return FALSE;
			} else {
			 	return $response;
			}
		}else{
			$this->ci->session->set_flashdata('error','error generating token');
        	return FALSE;
		}
	}

	function query_stk_payment_status($post_data ='',$shortcode=0){
		if($post_data){
			$token = $this->ci->ipn_m->get_token($shortcode);
			if($token){
				$url = 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query';
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  	CURLOPT_URL => $url,
				  	CURLOPT_RETURNTRANSFER => true,
				  	CURLOPT_ENCODING => "",
				  	CURLOPT_MAXREDIRS => 10,
				  	CURLOPT_TIMEOUT => 30,
				  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  	CURLOPT_CUSTOMREQUEST => "POST",
				  	CURLOPT_POSTFIELDS => $post_data,
				  CURLOPT_HTTPHEADER => array(
				    "Authorization: ".$token,
				    "Content-Type: application/json",
				  ),
				));
				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
				if ($err) {
				  	$this->ci->session->set_flashdata('error',"cURL Error #:" . $err);
					return FALSE;
				} else {
				  	return $response;
				}
			}else{
				$this->ci->session->set_flashdata('error','error generating token');
        		return FALSE;
			}
		}else{
			$this->ci->session->set_flashdata('error','Missing post data');
        	return FALSE;
		}
		
	}
}

?>