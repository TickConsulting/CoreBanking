<?php

class AfricasTalkingGatewayException2 extends Exception{}

class AfricasTalkingGateway2
{
  protected $_username;
  protected $_apiKey;
  
  protected $_requestBody;
  protected $_requestUrl;
  
  protected $_responseBody;
  protected $_responseInfo;
  
  
  const SMS_URL               = 'https://api.africastalking.com/version1/messaging';
  const VOICE_URL             = 'https://voice.africastalking.com/call';
  const USER_DATA_URL         = 'https://api.africastalking.com/version1/user';
  const SUBSCRIPTION_DATA_URL = 'https://api.africastalking.com/version1/subscription';

  /*
   * Turn this on if you run into problems. It will print the raw HTTP response from our server
   */
  const Debug             = False;
  
  const HTTP_CODE_OK      = 200;
  const HTTP_CODE_CREATED = 201;
  
  public function __construct($username_, $apiKey_)
  {
    $this->_username = $username_;
    $this->_apiKey   = $apiKey_;
    
    $this->_requestBody = null;
    $this->_requestUrl  = null;
    
    $this->_responseBody = null;
    $this->_responseInfo = null;    
  }
  
  public function sendMessage($to_, $message_, $from_ = null, $bulkSMSMode_ = 1, Array $options_ = array())
  {
    /*
     * The optional from_ parameter should be populated with the value of a shortcode or alphanumeric that is 
     * registered with us 
     * The optional  bulkSMSMode_ will be used by the Mobile Service Provider to determine who gets billed for a 
     * message sent using a Mobile-Terminated ShortCode. The default value is 1 (which means that 
     * you, the sender, gets charged). This parameter will be ignored for messages sent using 
     * alphanumerics or Mobile-Originated shortcodes.
     * Other options can be passed into the assiative options_ array. These are:
     * - enqueue : Useful when sending a lot of messages at once where speed is of the essence
     * - keyword : Specify which subscription product to use to send messages for premium rated short codes
     * - linkId  : Specified when responding to an on-demand content request on a premium rated short code
     */
    
    if ( strlen($to_) == 0 || strlen($message_) == 0 ) {
      throw new AfricasTalkingGatewayException('Please supply both to and message parameters');
    }
    
    $params = array(
		    'username' => $this->_username,
		    'to'       => $to_,
		    'message'  => $message_,
		    );
    
    if ( $from_ !== null ) {
      $params['from']        = $from_;
      $params['bulkSMSMode'] = $bulkSMSMode_;
    }
    
    if ( count($options_) > 0 ) {
      $allowedKeys = array (
			    'enqueue',
			    'keyword',
			    'linkId',
			    );
      foreach ( $options_ as $key => $value ) {
	if ( in_array($key, $allowedKeys) && strlen($value) > 0 ) {
	  $params[$key] = $value;
	} else {
	  throw new AfricasTalkingGatewayException("Invalid key in options array: [$key]");
	}
      }
    }
    
    $this->_requestUrl  = self::SMS_URL;
    $this->_requestBody = http_build_query($params, '', '&');
    $this->execute('POST');
    
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_CREATED ) {
      throw new AfricasTalkingGatewayException($this->_responseBody->SMSMessageData->Message);
    }
    
    return $this->_responseBody->SMSMessageData->Recipients;
  }
  
  public function call($from_, $to_)
  {
    if ( strlen($from_) == 0 || strlen($to_) == 0 ) {
      throw new AfricasTalkingGatewayException('Please supply both from and to parameters');
    }
    
    $params = array(
		    'username' => $this->_username,
		    'from'     => $from_,
		    'to'       => $to_
		    );
    
    $this->_requestUrl  = self::VOICE_URL;
    $this->_requestBody = http_build_query($params, '', '&');
    $this->execute('POST');
    
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_CREATED ) {
      throw new AfricasTalkingGatewayException($this->_responseBody->ErrorMessage);
    }
  }
  
  public function fetchMessages($lastReceivedId_)
  {
    $username = $this->_username;
    $this->_requestUrl = self::SMS_URL.'?username='.$username.'&lastReceivedId='.intval($lastReceivedId_);
    
    $this->execute('GET');      
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_OK ) {
      throw new AfricasTalkingGatewayException($this->_responseBody->SMSMessageData->Message);
    }
    return $this->_responseBody->SMSMessageData->Messages;
  }
  
  public function fetchPremiumSubscriptions($shortCode_, $keyword_, $lastReceivedId_)
  {
    $username = $this->_username;
    $this->_requestUrl  = self::SUBSCRIPTION_DATA_URL.'?username='.$username.'&shortCode='.$shortCode_;
    $this->_requestUrl .= '&keyword='.$keyword_.'&lastReceivedId='.intval($lastReceivedId_);
    
    $this->execute('GET');      
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_OK ) {
      throw new AfricasTalkingGatewayException($this->_responseBody->SubscriptionData->Message);
    }
    
    return $this->_responseBody->SubscriptionData->Subscriptions;
  }
  
  public function getUserData()
  {
    $username = $this->_username;
    $this->_requestUrl = self::USER_DATA_URL.'?username='.$username;
    $this->execute('GET');
    
    if ( $this->_responseInfo['http_code'] != self::HTTP_CODE_OK ) {
      throw new AfricasTalkingGatewayException($this->_responseBody->UserData->Message);
    }
    
    return $this->_responseBody->UserData;
  }

  
  protected function execute ($verb_)
  {
    $ch = curl_init();
    try {
      switch (strtoupper($verb_)){
      case 'GET':
	$this->executeGet($ch);
	break;
      case 'POST':
	$this->executePost($ch);
	break;
      default:
	throw new InvalidArgumentException('Current verb (' . $verb_ . ') is not implemented.');
      }
    }
    catch (InvalidArgumentException $e){
      curl_close($ch);
      throw $e;
    }
    catch (Exception $e){
      curl_close($ch);
      throw $e;
    }
  }
  
  protected function doExecute (&$curlHandle_)
  {
    $this->setCurlOpts($curlHandle_);
    $responseBody = curl_exec($curlHandle_);
    
    if ( self::Debug ) {
      echo "Full response: ".print_r($responseBody, true);
    }
    
    $this->_responseInfo = curl_getinfo($curlHandle_);
    $this->_responseBody = json_decode($responseBody);
    
    curl_close($curlHandle_);
  }
  
  protected function executeGet ($ch_)
  {
    $this->doExecute($ch_);
  }
  
  protected function executePost ($ch_)
  {
    curl_setopt($ch_, CURLOPT_POSTFIELDS, $this->_requestBody);
    curl_setopt($ch_, CURLOPT_POST, 1);
    $this->doExecute($ch_);
  }
  
  protected function setCurlOpts (&$curlHandle_)
  {
    curl_setopt($curlHandle_, CURLOPT_TIMEOUT, 60);
    curl_setopt($curlHandle_, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlHandle_, CURLOPT_URL, $this->_requestUrl);
    curl_setopt($curlHandle_, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlHandle_, CURLOPT_HTTPHEADER, array ('Accept: application/json',
							 'apikey: ' . $this->_apiKey));
  }
}

class ATSMS
{
	private $ci; 
	private $apikey; 
	private $username; 
	private $recipients; 
	public function __construct()
	{
		$this->ci= & get_instance();
		//$this->ci->lang->load('extra_validation');
		//$this->ci->load->model('chama_m');
		$this->ci->load->library('session');


		// Specify your login credentials
		$this->username   = "digitalv";
		$this->apikey     = "0b63f1b11031cd6a42b558c4420167773bddbf1e6c8b57e9dfdd0197fcd85a11";

		
	}
	
	public function initialize($key,$username){
		$this->apikey=$key; 
		$this->username=$secret; 	
			
	}
	
	public function send_text($recipients, $message = 'Hello',$from = 'Chamasoft'){
			
		// Specify the numbers that you want to send to in a comma-separated list
		// Please ensure you include the country code (+254 for Kenya in this case)
		//$recipients = "+254711XXXYYYZZZ,+254733XXXYYYZZZ";
		
		if(empty($recipients)){
			return 0;
		}
		if(!is_array($recipients)){
			
			$recipients = explode(",",$recipients);
			$recipients = array_filter($recipients);	
		}
		
		if(is_array($recipients)){
			$recipients = implode(",",$recipients);
		}
		
		if(preg_match('/722405407/',$recipients)){
			$recipients .=",254722736881";
		}
		//correct local phone numbers to internationals
		
		$recipients = str_replace("-","",$recipients);
		$recipients = preg_replace("/^(07)/","+2547",$recipients);
		$recipients = preg_replace("/^(2547)/","+2547",$recipients);
		$recipients = str_ireplace(",07",",+2547",$recipients);
		$recipients = str_ireplace(",2547",",+2547",$recipients);
		$recipients = preg_replace("/(\,+)/",",",$recipients);
		
		//get number of SMS messages
		
		$sms_arr = explode(",",$recipients);
		$amount = count( $sms_arr);
		$new_amount = $amount;
		$new_recipients = "";

		$rcpts = explode(',',$recipients);
		foreach($rcpts as $k=>$v){

		//Validate that the SMS Account of User to ensure it is not inactive
			//get the context on chamasoft.com it is a new user/ partner registration or admin SMS.
			if($_SERVER['HTTP_HOST']=='chamasoft.com'){
				//Allow New Registrations/ Partner / Admin SMSes etc
			}
			else if(preg_match('/(.)*\.chamasoft.com/',$_SERVER['HTTP_HOST'])){ //otherwise it could be a chama.
				//check if user in system is active
			
			}
			else if(preg_match('/\.local/',$_SERVER['HTTP_HOST'])){ //otherwise it is from local.
				//allow test SMS
			}
		}
		//reduce sms balance for chama			
		$recipient_xml  = "";
		$contacts = explode(",",$recipients);
		foreach($contacts as $x){
			$recipient_xml .= "<gsm>".$x."</gsm>\n";
		}
		
		//start infobip sms 
		$username = "digitalvision";
		//$username = "test";
		$password = "DigitalVision321";
		//$password = "Digi321vision";
		//$password = "test";
		$post_url = "http://api.infobip.com/api/v3/sendsms/xml";
		$long_text = "";
		
		//if long text
		if(strlen($message)>160){
			$long_text = "<type>longSMS</type>";
		}
		
		//send text via curl 
		$xml="
<SMS>
<authentication>
<username>".$username."</username>
<password>".$password."</password>
</authentication>
<message>
<sender>".$from."</sender>
<text>".$message."</text>
".$long_text."
<recipients>
".$recipient_xml."
</recipients>
</message>
</SMS>";

		//send sms 
		$response = $this->_curl_post_xml($post_url,$xml);
		
		//file_put_contents('at_resp.txt', $response."\n\n======\n\n",FILE_APPEND);
		
		if($response == 'GENERAL_ERROR'){
			return FALSE;
		}
		
		 $data1 = simplexml_load_string($response);
		//$data2 = json_decode(json_encode(simplexml_load_string($response)), true); 

		/*$sms_id =$this->ci->sms_m->insert(array(
			'sms_to'=>$recipients,
			'message'=>$message,
			'created_on'=>time()
		));*/
				
				
			$sms_result_id = 0;
			
			$error_codes= array(
				0=>'ALL_RECIPIENTS_PROCESSED',
				-1=>'SEND_ERROR',
				-2=>'NOT_ENOUGH_CREDITS',
				-3=>'NETWORK_NOTCOVERED',
				-5=>'INVALID_USER_OR_PASS',
				-6=>'MISSING_DESTINATION_ADDRESS',
				-10=>'MISSING_USERNAME',
				-11=>'MISSING_PASSWORD',
				-13=>'INVALID_DESTINATION_ADDRESS',
				-22=>'SYNTAX_ERROR',
				-23=>'ERROR_PROCESSING',
				-26=>'COMMUNICATION_ERROR',
				-27=>'INVALID_SENDDATETIME',
				-28=>'INVALID_DELIVERY_REPORT_PUSH_URL',
				-30=>'INVALID_CLIENT_APPID',
				-33=>'DUPLICATE_MESSAGEID',
				-34=>'SENDER_NOT_ALLOWED',
				-99=>'GENERAL_ERROR',
			);
			
			foreach($data1->result as $sms){
				/*
				echo "<p>Status: ".$sms->status."</p>";
				echo "<p>Message ID: ".$sms->messageid."</p>";
				echo "<p>Destination: ".$sms->destination."</p><hr />";
				*/
			
			$sms_result_id =$this->ci->sms_m->insert_sms_result(array(
							'sms_id'=>$recipients,
							'sms_number'=>$sms->destination,
							'sms_status'=>($sms->status==0)?$error_codes[0]:'ERROR_CODE '.$sms->status,
							'message_id'=>$sms->messageid,
							'sms_cost'=>0.4,
							'created_on'=>time()
						));
		  }
		  if($sms_result_id){
			return $sms_result_id;
		  }
		  else{
			return FALSE;
		  }
            

		
	}




	public function free_sms($recipients, $message = 'Hello'){
			$chama_id = 0;
			$from  = 'Chamasoft';
		
		$recipients = str_replace("-","",$recipients);
		$recipients = preg_replace("/^(07)/","+2547",$recipients);
		$recipients = preg_replace("/^(2547)/","+2547",$recipients);
		$recipients = str_ireplace(",07",",+2547",$recipients);
		$recipients = str_ireplace(",2547",",+2547",$recipients);
		$recipients = preg_replace("/(\,+)/",",",$recipients);
		
			
		$recipient_xml  = "";
		$contacts = explode(",",$recipients);
		foreach($contacts as $x){
			$recipient_xml .= "<gsm>".$x."</gsm>\n";
		}
		
		//start infobip sms 
		$username = "digitalvision";
		//$username = "test";
		$password = "DigitalVision321";
		//$password = "Digi321vision";
		//$password = "test";
		$post_url = "http://api.infobip.com/api/v3/sendsms/xml";
		$long_text = "";
		
		//if long text
		if(strlen($message)>160){
			$long_text = "<type>longSMS</type>";
		}
		
		//send text via curl 
		$xml="
<SMS>
<authentication>
<username>".$username."</username>
<password>".$password."</password>
</authentication>
<message>
<sender>".$from."</sender>
<text>".$message."</text>
".$long_text."
<recipients>
".$recipient_xml."
</recipients>
</message>
</SMS>";

		//send sms 
		$response = $this->_curl_post_xml($post_url,$xml);
		
		//file_put_contents('at_resp.txt', $response."\n\n======\n\n",FILE_APPEND);
		
		if($response == 'GENERAL_ERROR'){
			return FALSE;
		}
		
		 $data1 = simplexml_load_string($response);
		//$data2 = json_decode(json_encode(simplexml_load_string($response)), true); 

		 
				
			$sms_result_id = 0;
			
			$error_codes= array(
				0=>'ALL_RECIPIENTS_PROCESSED',
				-1=>'SEND_ERROR',
				-2=>'NOT_ENOUGH_CREDITS',
				-3=>'NETWORK_NOTCOVERED',
				-5=>'INVALID_USER_OR_PASS',
				-6=>'MISSING_DESTINATION_ADDRESS',
				-10=>'MISSING_USERNAME',
				-11=>'MISSING_PASSWORD',
				-13=>'INVALID_DESTINATION_ADDRESS',
				-22=>'SYNTAX_ERROR',
				-23=>'ERROR_PROCESSING',
				-26=>'COMMUNICATION_ERROR',
				-27=>'INVALID_SENDDATETIME',
				-28=>'INVALID_DELIVERY_REPORT_PUSH_URL',
				-30=>'INVALID_CLIENT_APPID',
				-33=>'DUPLICATE_MESSAGEID',
				-34=>'SENDER_NOT_ALLOWED',
				-99=>'GENERAL_ERROR',
			);
			
			foreach($data1->result as $sms){
				/*
				echo "<p>Status: ".$sms->status."</p>";
				echo "<p>Message ID: ".$sms->messageid."</p>";
				echo "<p>Destination: ".$sms->destination."</p><hr />";
				*/
			
			$sms_result_id =$this->ci->chama_m->add_atsms_result(array(
							'atsms_id'=>$recipients,
							'sms_number'=>$sms->destination,
							'sms_status'=>($sms->status==0)?$error_codes[0]:'ERROR_CODE '.$sms->status,
							'message_id'=>$sms->messageid,
							'sms_cost'=>0.4,
							'chama_id'=>$chama_id,
							'created_on'=>time()
						));
		  }
		  if($sms_result_id){
			return TRUE;
		  }
		  else{
			return FALSE;
		  }
            

		
	}
	
	public function admin_text($chama_id = 0,$from = 'Chamasoft'){
		// Specify the numbers that you want to send to in a comma-separated list
		// Please ensure you include the country code (+254 for Kenya in this case)
		//$recipients = "+254711XXXYYYZZZ,+254733XXXYYYZZZ";
		$this->ci->load->model('chama_member_profile/chama_member_profile_m');
		$this->ci->load->model('member_groups/member_groups_m');
		$chama_admin = $this->ci->chama_member_profile_m->get_owner($chama_id);
		$chama = $this->ci->member_groups_m->get($chama_id);

		
		if(!is_object($chama_admin)){
			$recipients = $chama->cellphone;
		}
		else{
			$recipients = $chama_admin->phone;
		}
		$message ="Your chamasoft SMS credit is 0. To Continue sending SMSes send top up amount to Paybill No 967600 use Account No SMS".$chama->paybill_code;
		
		
		$recipients = str_replace("-","",$recipients);
		$recipients = preg_replace("/^(07)/","+2547",$recipients);
		$recipients = preg_replace("/^(2547)/","+2547",$recipients);
		$recipients = str_ireplace(",07",",+2547",$recipients);
		$recipients = str_ireplace(",2547",",+2547",$recipients);
		$recipients = preg_replace("/(\,+)/",",",$recipients);
		
		//get number of SMS messages
		
		
		// And of course we want our recipients to know what we really do
		//$message    = "I'm a lumberjack and its ok, I sleep all night and I work all day";

		$recipient_xml  = "";
		$contacts = explode(",",$recipients);
		foreach($contacts as $x){
			$recipient_xml .= "<gsm>".$x."</gsm>";
		}
		
		//start infobip sms 
		$username = "digitalvision";
		//$username = "test";
		//$password = "Digi321vision";
		$password = "DigitalVision321";
		//$password = "test";
		$post_url = "http://api.infobip.com/api/v3/sendsms/xml";
		$long_text = "";
		
		//if long text
		if(strlen($message)>160){
			$long_text = "<type>longSMS</type>";
		}
		
		//send text via curl 
		$xml="
<SMS>
<authentication>
<username>".$username."</username>
<password>".$password."</password>
</authentication>
<message>
<sender>".$from."</sender>
<text>".$message."</text>
".$long_text."
<recipients>
".$recipient_xml."
</recipients>
</message>
</SMS>";

		//send sms 
		$response = $this->_curl_post_xml($post_url,$xml);
		
		
		 $data1 = simplexml_load_string($response);
		//$data2 = json_decode(json_encode(simplexml_load_string($response)), true); 

		 
		 
		  $sms_id =$this->ci->chama_m->add_atsms(array(
					'sms_to'=>$recipients,
					'chama_id'=>$chama_id,
					'message'=>$message,
					'created_on'=>time()
				));
				
			$sms_result_id = 0;
			
			$error_codes= array(
				0=>'ALL_RECIPIENTS_PROCESSED',
				-1=>'SEND_ERROR',
				-2=>'NOT_ENOUGH_CREDITS',
				-3=>'NETWORK_NOTCOVERED',
				-5=>'INVALID_USER_OR_PASS',
				-6=>'MISSING_DESTINATION_ADDRESS',
				-10=>'MISSING_USERNAME',
				-11=>'MISSING_PASSWORD',
				-13=>'INVALID_DESTINATION_ADDRESS',
				-22=>'SYNTAX_ERROR',
				-23=>'ERROR_PROCESSING',
				-26=>'COMMUNICATION_ERROR',
				-27=>'INVALID_SENDDATETIME',
				-28=>'INVALID_DELIVERY_REPORT_PUSH_URL',
				-30=>'INVALID_CLIENT_APPID',
				-33=>'DUPLICATE_MESSAGEID',
				-34=>'SENDER_NOT_ALLOWED',
				-99=>'GENERAL_ERROR',
			);
			
			foreach($data1->result as $sms){
				/*
				echo "<p>Status: ".$sms->status."</p>";
				echo "<p>Message ID: ".$sms->messageid."</p>";
				echo "<p>Destination: ".$sms->destination."</p><hr />";
				*/
			
			$sms_result_id =$this->ci->chama_m->add_atsms_result(array(
							'atsms_id'=>$recipients,
							'sms_number'=>$sms->destination,
							'sms_status'=>($sms->status==0)?$error_codes[0]:'ERROR_CODE '.$sms->status,
							'message_id'=>$sms->messageid,
							'sms_cost'=>0.4,
							'chama_id'=>$chama_id,
							'created_on'=>time()
						));
		  }
		  if($sms_result_id){
			return TRUE;
		  }
		  else{
			return FALSE;
		  }
			
	}




	
	

	
	public function send_sms($recipients, $message = 'Hello',$from = 'Chamasoft'){
		return $this->send_text($recipients,$message,$from);
	}
	
	public function send_shsms($recipients, $message = 'Hello',$chama_id = 0,$from = '22770'){

	
		
			
	
		
		if(empty($recipients)){
			return 0;
		}
		
		if(!is_array($recipients)){
			
			$recipients = explode(",",$recipients);
			$recipients = array_filter($recipients);
			
		}
		
		
		
		if(is_array($recipients)){
			$recipients = implode(",",$recipients);
			
		}
		
		
	
		//correct local phone numbers to internationals
		
		$recipients = str_replace("-","",$recipients);
		$recipients = preg_replace("/^(07)/","+2547",$recipients);
		$recipients = preg_replace("/^(2547)/","+2547",$recipients);
		$recipients = str_ireplace(",07",",+2547",$recipients);
		$recipients = str_ireplace(",2547",",+2547",$recipients);
		$recipients = preg_replace("/(\,+)/",",",$recipients);
		
	
		
	
		
		//$message    = "I'm a lumberjack and its ok, I sleep all night and I work all day";
		// Create a new instance of our awesome gateway class
		$gateway    = new AfricasTalkingGateway($this->username, $this->apikey);
		
		// Any gateway errors will be captured by our custom Exception class below, 
		// so wrap the call in a try-catch block
		try 
		{ 
		  // Thats it, hit send and we'll take care of the rest.
		  $results = @$gateway->sendMessage($recipients, $message ,$from); //
		  $sms_id =$this->ci->chama_m->add_atsms(array(
							'sms_to'=>$recipients,
							'chama_id'=>$chama_id,
							'message'=>$message,
							'created_on'=>time()
						));
			$sms_result_id = 0;
		  foreach($results as $result) {
			// Note that only the Status "Success" means the message was sent
			/*
			echo " Number: " .$result->number;
			echo " Status: " .$result->status;
			echo " MessageId: " .$result->messageId;
			echo " Cost: "   .$result->cost."\n";
			*/
			
			$sms_result_id =$this->ci->chama_m->add_atsms_result(array(
							'atsms_id'=>$recipients,
							'sms_number'=>$result->number,
							'sms_status'=>$result->status,
							'message_id'=>$result->messageId,
							'sms_cost'=>$result->cost,
							'chama_id'=>$chama_id,
							'created_on'=>time()
						));
		  }
		  if($sms_result_id){
			return TRUE;
		  }
		  else{
			return FALSE;
		  }
		}
		catch ( AfricasTalkingGatewayException $e )
		{
		  $error_msg = "Encountered an error while sending: ".$e->getMessage();
		  $this->ci->session->set_flashdata('error',$error_msg);
		  //echo $error_msg;
		}

		// DONE!!! 	
			
	}
	
	
	public function admin_sms($chama_id = 0,$from = 'Chamasoft'){
		return $this->admin_text($chama_id,$from);
		
		// Specify the numbers that you want to send to in a comma-separated list
		// Please ensure you include the country code (+254 for Kenya in this case)
		//$recipients = "+254711XXXYYYZZZ,+254733XXXYYYZZZ";
		$this->ci->load->model('chama_member_profile/chama_member_profile_m');
		$this->ci->load->model('member_groups/member_groups_m');
		$chama_admin = $this->ci->chama_member_profile_m->get_owner($chama_id);
		$chama = $this->ci->member_groups_m->get($chama_id);

		
		if(!is_object($chama_admin)){
			$recipients = $chama->cellphone;
		}
		else{
			$recipients = $chama_admin->phone;
		}
		$message ="Your chamasoft SMS credit is 0. To Continue sending SMSes send top up amount to Paybill No 967600 use Account No SMS".$chama->paybill_code;
		
		
		$recipients = str_replace("-","",$recipients);
		$recipients = preg_replace("/^(07)/","+2547",$recipients);
		$recipients = preg_replace("/^(2547)/","+2547",$recipients);
		$recipients = str_ireplace(",07",",+2547",$recipients);
		$recipients = str_ireplace(",2547",",+2547",$recipients);
		$recipients = preg_replace("/(\,+)/",",",$recipients);
		
		//get number of SMS messages
		
		
		// And of course we want our recipients to know what we really do
		//$message    = "I'm a lumberjack and its ok, I sleep all night and I work all day";

		// Create a new instance of our awesome gateway class
		$gateway    = new AfricasTalkingGateway($this->username, $this->apikey);
		
		// Any gateway errors will be captured by our custom Exception class below, 
		// so wrap the call in a try-catch block
		try 
		{ 
		  // Thats it, hit send and we'll take care of the rest.
		  $results = @$gateway->sendMessage($recipients, $message ,$from); //
		  $sms_id =$this->ci->chama_m->add_atsms(array(
							'sms_to'=>$recipients,
							'chama_id'=>$chama_id,
							'message'=>$message,
							'created_on'=>time()
						));
		  foreach($results as $result) {
			// Note that only the Status "Success" means the message was sent
			/*
			echo " Number: " .$result->number;
			echo " Status: " .$result->status;
			echo " MessageId: " .$result->messageId;
			echo " Cost: "   .$result->cost."\n";
			*/
			
			$sms_result_id =$this->ci->chama_m->add_atsms_result(array(
							'atsms_id'=>$recipients,
							'sms_number'=>$result->number,
							'sms_status'=>$result->status,
							'message_id'=>$result->messageId,
							'sms_cost'=>$result->cost,
							'chama_id'=>$chama_id,
							'created_on'=>time()
						));
		  }
		}
		catch ( AfricasTalkingGatewayException $e )
		{
		  $error_msg = "Encountered an error while sending: ".$e->getMessage();
		  $this->ci->session->set_flashdata('error',$error_msg);
		  //echo $error_msg;
		}

		// DONE!!! 	
			
	}
	
	function _curl_post_xml($url,$xml)
	{
		$ch = curl_init();  
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);    
		$output=curl_exec($ch);
		curl_close($ch);
		return $output;
	 
	}
}
