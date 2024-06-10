<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//require_once "./assets/SMPP/protocol/smppclient.class.php";
//require_once "./assets/SMPP/protocol/gsmencoder.class.php";
//require_once "./assets/SMPP/transport/tsocket.class.php";

// require_once "./assets/AfricasTalkingGateway.php";
// require_once "./assets/AfricasTalking.php";
// require_once "./assets/Service.php";
// require_once "./assets/Application.php";
// use AfricasTalking\SDK\AfricasTalking;

class SMS_Gateway {

    public function __construct() {
      //parent::__construct();
  		$this->ci= & get_instance();
      $this->ci->load->library('curl');
    }


    function send_sms_via_equity_bank($phone_number = "",$message = "",$sender_id = "EazzyChama",$reference = "123456789",$user=array()){     
      $post_fields = json_encode(array(
          "reference" => $reference."",
          "recipient" => array(
            array(
              "to" => $phone_number,
              "platform" => 1,
            )
          ),
          "message" => $message,
          "source" => $sender_id,
          "productId" => "123",
          "tokens" => array(
            "{FirstName}" => !empty($user)?$user->first_name:'',
            "{LastName}" => !empty($user)?$user->last_name:'',
          ),
      ));
      return $this->ci->curl->equityBankRequests->send_sms($post_fields);  
    }

    function send_email_via_equity_bank($email = "",$message = "",$sender_id = "EazzyChama",$reference = "123456789",$user=array()){
      $post_fields = json_encode(array(
          "reference" => $reference."",
          "recipient" => array(
            array(
              "to" => $email,
              "platform" => 2,
            )
          ),
          "message" => $message,
          "source" => $sender_id,
          "productId" => "123",
          "tokens" => array(
            "{FirstName}" => !empty($user)?$user->first_name:'',
            "{LastName}" => !empty($user)?$user->last_name:'',
          ),
      ));
      return $this->ci->curl->equityBankRequests->send_sms($post_fields);  
    }



    public function send_sms_via_equity_bank_smsc($phone_number = "",$message = "",$sender_id = "EazzyChama"){

    	  if($sender_id&&$phone_number&&$message){
            $message = str_replace('&','and',$message);
			      $curl = curl_init();
      			curl_setopt_array($curl, array(
      			  CURLOPT_PORT => "8080",
      			  CURLOPT_URL => "http://196.216.242.150:8080/HTTP_Adaptor/Equitel_SMS",
      			  CURLOPT_RETURNTRANSFER => true,
      			  CURLOPT_ENCODING => "",
      			  CURLOPT_MAXREDIRS => 10,
      			  CURLOPT_TIMEOUT => 30,
      			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      			  CURLOPT_CUSTOMREQUEST => "POST",
      			  CURLOPT_POSTFIELDS => "<Request><username>eazzychama</username><password>Ch@m@123</password><commandId>sendSMS</commandId><oa>".$sender_id."</oa><da>".$phone_number."</da><seqno>15774</seqno><ud>".$message."</ud><pid>0</pid><servicetype>CMT</servicetype><validityperiod>100</validityperiod><registerdelivery>0</registerdelivery><dlruri></dlruri></Request>",
      			  CURLOPT_HTTPHEADER => array(
      			    "Cache-Control: no-cache",
      			    "Postman-Token: 7e34e5b7-75fc-4629-9de6-432ff87246d0"
      			  ),
      			));
            if($response = curl_exec($curl)){
                $xml = simplexml_load_string($response);
                if(isset($xml)){
                    if($xml->status == 0){
                        file_put_contents("logs/equity_bank_sms_gateway.log",date("d-M-Y h:i A")."\tSuccess: ".$xml->diaginfo." , Response: ".$response."\n",FILE_APPEND);
                        return TRUE;

                    }else{
                        file_put_contents("logs/equity_bank_sms_gateway_error.log",date("d-M-Y h:i A")."\tError: ".$xml->diaginfo." , Response: ".$response."\t Phone Number: ".$phone_number."\tMessage: ".$message."\n",FILE_APPEND);
                        return FALSE;
                    }
                }else{
                    file_put_contents("logs/equity_bank_sms_gateway_error.log",date("d-M-Y h:i A")."\tError: Could not parse XML. ".$response."\n",FILE_APPEND);
                    return FALSE;
                }
            }else{
                file_put_contents("logs/equity_bank_sms_gateway_error.log",date("d-M-Y h:i A")."\tError: No response received from Equity Bank.\n",FILE_APPEND);
                return FALSE;
            }
		    }else{
            //$this->sms_m->delete_sms_queue($queued_smsid);
            file_put_contents("logs/equity_bank_sms_gateway_error.log",date("d-M-Y h:i A")."\tError: Parameters missing.\t Phone: ".$phone_number."\t Message: ".$message."\n",FILE_APPEND);
			      return TRUE;
		    }
    }

    public function send_sms_via_africas_talking($recipients,$message = "EazzyClub",$sender_id = "EazzyClub"){
      $username = "eazzyclub";
      $apikey = "28144a74ffa9624f90fad36e093a28975efebabe6684b0b899f2c043f1cc0435";
      $gateway = new AfricasTalkingGateway($username,$apikey);
      try {
        $message = $message?:"EazzyClub";
        $results = $gateway->sendMessage($recipients,$message);
        $sms_result = TRUE;
        foreach($results as $result) {
          if($result->status == "Success"){

          }else{
            $sms_result = FALSE;
          }
        }
        if($sms_result){
          return TRUE;
        }else{
          return FALSE;
        }
      }catch ( AfricasTalkingGatewayException $e ){
        echo "Encountered an error while sending: ".$e->getMessage();
        return FALSE;
      }
    }
    public function send_sms_via_vaspro($messageData,$timestamp){
      $post_fields = json_encode(array(
        "apiKey" => "e7b4250b0b54c0723d7f952b52c0e7be",
        "shortCode"=>"VasPro",
        "uniqueId"=>$timestamp,
        "messageData"=>array(
        array("recipient"=>"254748974489","message"=>"Testing if I can send Bulk smses via Vaspro in one API call","uniqueId"=>$timestamp),
        // array("recipient"=>"254748974489","message"=>"Testing if I can send Bulk smses via Vaspro in one API call","uniqueId"=>$timestamp)
      ),
      "callbackURL"=>"http://vaspro.co.ke/dlr"
    ));
      $result= $this->ci->curl->post_json($post_fields,'https://api.vaspro.co.ke/v3/BulkSMS/bulk/nested');
      $result=json_decode($result);
      
      if($result->code=="Success"){
        return TRUE;
      }
      else{
        return FALSE;
      }
    }
    public function get_africas_talking_balance(){
      // Initialize the SDK
      $username = "eazzyclub";
      $apikey = "28144a74ffa9624f90fad36e093a28975efebabe6684b0b899f2c043f1cc0435";
      // Initialize the SDK
      $AT = new AfricasTalking($username, $apikey);

      // Get the application service
      $application = $AT->application();

      try {
          // Fetch the application data
          $data = $application->fetchApplicationData();
          return $data;
      } catch(Exception $e) {
          echo "Error: ".$e->getMessage();
          return FALSE;
      }
    }

}
