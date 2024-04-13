<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . 'third_party/vendor/autoload.php';
use Postmark\PostmarkClient;
use Postmark\Models\PostmarkException;
use Postmark\Models\PostmarkAttachment;

class Emails_manager{
    protected $ci;
    public function __construct(){
        $this->ci= & get_instance();
        $this->ci->load->config('email', TRUE); 
        $this->config = $this->ci->config->item('email');
        $this->ci->load->library('email');
        $this->ci->load->model('emails/emails_m');              
    }

    function send_email($email_array = array()){
        if($this->send_email_via_postmark_api($email_array)){
           return TRUE; 
        }else{
           return FALSE;  
        }
        /*if(empty($email_array)){
            $this->ci->session->set_flashdata('warning','Email particulars array is empty');
            return FALSE;
        }else{
            $email_object = (object)$email_array;
            if($email_object){
                $this->ci->email->from($email_object->email_from);
                $this->ci->email->to($email_object->to);                
                $this->ci->email->cc($email_object->cc);
                $this->ci->email->bcc($email_object->bcc);
                $this->ci->email->subject($email_object->subject);
                $this->ci->email->message($email_object->message);
                if($this->ci->email->send()){
                    return TRUE;
                }else{
                    $this->ci->session->set_flashdata('warning');
                    return FALSE;   
                }
            }else{
               return FALSE;  
            }
        }*/
    }

    function send_email_via_sendgrid_api($email_array= array()){
        if(empty($email_array)){
            $this->ci->session->set_flashdata('warning','Email particulars array is empty');
            return FALSE;
        }else{

            $email_object = (object)$email_array;
            if($email_object){
                $email = new \SendGrid\Mail\Mail(); 
                $email->setFrom($email_object->email_from);
                $email->setSubject($email_object->subject);
                $email->addTo($email_object->to);
                $email->addContent(
                    "text/html", $email_object->message
                );               
                $sendgrid = new \SendGrid($this->config['apiKey']);
                try {
                    $response = $sendgrid->send($email);
                    $status_code = $response->statusCode(); 
                    if($status_code == '202' || $status_code == '200'){
                        return TRUE;
                    }else{
                        return FALSE;
                    }  
                } catch (Exception $e) {
                    echo 'Caught exception: '. $e->getMessage() ."\n";
                }
            }else{
               return FALSE;  
            }
        }        

    }

    function send_via_sendgrid($to='',$subject='',$message='',$topic='',$from='info@chamasoft.com',$reply_to="info@chamasoft.com",$cc='',$bcc='',$attachments=array()){
        if($to&&$subject&&$message){
            $attachment_files = $this->_get_attachments($attachments);
            
            $data = array(
                'personalizations' => array(
                    array(
                        'to' => $this->_generate_recipients($to),
                        'subject' => $subject,
                    )
                ),
                'content' => array(
                    array(
                        'type' => 'text/html',
                        'value' => $message,
                    )
                ),
                'from' => array(
                    'email' => $from,
                    'name' => $topic,
                ),
                'reply_to' => array(
                    'email' => $reply_to,
                    'name' => $reply_to,
                ),
            );
            if($attachments){
                $data['attachments'] = $attachment_files;
            }
            if($cc_recipients=$this->_generate_recipients($cc)){
                $data['personalizations'][0]['cc'] = $cc_recipients;
            }
            if($bcc_recipients=$this->_generate_recipients($bcc)){
                $data['personalizations'][0]['bcc'] = $bcc_recipients;
            }
            $post_data =  json_encode($data);
            $url = 'https://api.sendgrid.com/v3/mail/send';
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POST, true );
            curl_setopt($ch,CURLOPT_ENCODING, "" );
            curl_setopt($ch,CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch,CURLOPT_TIMEOUT, 60);
            curl_setopt($ch,CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
            curl_setopt($ch,CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch,CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                "Authorization: Bearer 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",
            ));
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $output=curl_exec($ch);
            curl_close($ch);
            if($output){
                return FALSE;
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }
    

    function send_email_via_postmark_api($email_array= array()){        
        if(empty($email_array)){
            $this->ci->session->set_flashdata('warning','Email particulars array is empty');
            return FALSE;
        }else{
            $email_object = (object)$email_array;
            if($email_object){
                if(preg_match('/(chamasoft)/',$_SERVER['HTTP_HOST'])){
                   $api_token = $this->config['api_token'];
                   $from_email = "info@chamasoft.com";          
                }else if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){
                    $api_token = $this->config['eazzyclub_token'];
                    $from_email = "info@eazzyclub.co.ug";
                }else if(preg_match('/(eazzychama)/',$_SERVER['HTTP_HOST'])){
                    $from_email ="info@eazzychama.co.ke";
                    $api_token = $this->config['eazzychama_token'];
                }else{
                    $from_email = "info@chamasoft.com";
                    $api_token = $this->config['api_token'];
                }
                $client = new PostmarkClient($api_token);
                // Send an email:
                try {
                    $attachment = '';
                    if(empty($email_object->attachments[0])){
                          
                    }else{
                       $attachment = PostmarkAttachment::fromFile(base_url($email_object->attachments[0]),$email_object->subject, 'application/pdf', 'cid:'.$email_object->subject);                        
                    } 
                    $sendResult = $client->sendEmail(
                        $email_object->email_from,
                        $email_object->to,
                        $email_object->subject,
                        $email_object->message,
                        $email_object->subject,
                        NULL,true, $from_email, $email_object->cc, $email_object->bcc, NULL, ([$attachment])?[$attachment]:NULL
                    );
                }

                catch (Exception $e) {
                    
                    $sendResult = FALSE;
                }
                if($sendResult){
                    return TRUE;
                }else{
                    $this->ci->session->set_flashdata('warning','could not send email');
                    return FALSE;   
                }
            }else{
               return FALSE;  
            }
        }
    }

    function _send_postmark_attachments($email_array=array()){
        $email_object = (object)$email_array;
        $attachment = $email_object->attachments[0];
        $attachment_segments = explode('/',$attachment);
        if(isset($attachment_segments[4])){
            $filename = $attachment_segments[4];
        }
        $file_content = file_get_contents(base_url($email_object->attachments[0]));
        $result = array(
            'content' => base64_encode($file_content),
            'type' => 'application/pdf',
            'filename' => $filename,
            'disposition' => 'attachment',
        );        
        $content = file_get_contents(base_url($email_object->attachments[0]));
        $content = chunk_split(base64_encode($content));
         
        $data = array(
            'From' => $email_object->email_from,
            'To' => $email_object->to,
            'Subject' => $email_object->subject,
            'HtmlBody' => $email_object->message,
            'Attachments' => array(
            array(
                    "Name" => $filename,
                    "Content"=> $content,
                    "ContentType"=> "application/octet-stream",
                    "disposition" => "attachment",
                )
            )
        );
        $data_string = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.postmarkapp.com/email');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $headers = [ 
            'X-Postmark-Server-Token: '.$this->config['api_token'],
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $result = curl_exec($ch);
        curl_close ($ch);
        $response = json_decode($result); 
        //print_r($response); die();       
        if($response->Message == 'OK' || $response->ErrorCode == 0) {
            return TRUE;
        } else {
           return FALSE;
        }
    }

    function _get_attachments($serialized_attachments =''){
        $attachment_files = array();        
        if($serialized_attachments){
            $attachments = unserialize($serialized_attachments);   
            if(is_array($attachments)){
                $result = array();
                foreach ($attachments as $attachment) {
                    if(is_file($attachment)){
                        $file_content = file_get_contents($attachment);
                        $attachment_segments = explode('/',$attachment);                         
                        if(isset($attachment_segments[3])){
                            $filename = $attachment_segments[3];
                        }
                        $result[] = array(
                            'content' => base64_encode($file_content),
                            'type' => 'application/pdf',
                            'filename' => $filename,
                            'disposition' => 'attachment',
                        );
                    }else{
                        $file_content = file_get_contents(base_url($attachment));
                        $attachment_segments = explode('/',$attachment);                         
                        if(isset($attachment_segments[3])){
                            $filename = $attachment_segments[3];
                        }
                        $result[] = array(
                            'content' => base64_encode($file_content),
                            'type' => 'application/pdf',
                            'filename' => $filename,
                            'disposition' => 'attachment',
                        );
                        //return FALSE;
                    }
                }
                $attachment_files = $result;
            }else{
                if(is_file($attachments)){
                    $attachment = file_get_contents($attachments);
                    $attachment_segments = explode('/',$attachments);
                    $filename = '';
                    if(isset($attachment_segments[3])){
                        $filename = $attachment_segments[3];
                    }
                    $attachment_files = array(
                        array(
                            'content' => base64_encode($attachment),
                            'type' => 'application/pdf',
                            'filename' => $filename,
                            'disposition' => 'attachment',
                        )
                    );
                }else{
                    //return FALSE;
                }
            }
        }else{

        }
        return $attachment_files;
    }


    function _generate_recipients($to=''){
        $recipients = array();
        if($to){
            if(is_array($to)){

            }else{
                $to = array($to);
            }
            foreach ($to as $email) {
                $recipients[] = array(
                    'email' => $email,
                    'name' => '',
                );
            }
        }else{

        }
        return $recipients;
    }


}