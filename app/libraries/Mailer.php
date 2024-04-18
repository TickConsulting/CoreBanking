<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . 'third_party/vendor/autoload.php';
use Postmark\PostmarkClient;
use Postmark\Models\PostmarkException;
use Postmark\Models\PostmarkAttachment;
$headers = array();

class Mailer{
    protected $ci;
    public function __construct(){
        $this->ci= & get_instance();
        $this->ci->load->config('email', TRUE); 
        $this->config = $this->ci->config->item('email');
        $this->ci->load->library('email');
        $this->ci->load->model('emails/emails_m');
        $this->headers = 'From: Safaricom Files <notifications@chamasoft.com>' . "\r\n" .
                    'Reply-To: billing@chamasoft.com' . "\r\n".
                    'X-Mailer: PHP/' . phpversion();             
    }

    function send_email($email_array = array()){
        if($this->send_via_sendgrid($email_array)){
           return TRUE; 
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
                $client = new PostmarkClient($this->config['api_token']);
                // Send an email:
                try {
                    $attachment = '';
                    if(empty($email_object->attachments[0])){
                          
                    }else{
                       $attachment = PostmarkAttachment::fromFile(base_url($email_object->attachments[0]),$email_object->subject, 'application/pdf', 'cid:'.$email_object->subject);  
                    }         
                    $sendResult = $client->sendEmail(
                        $email_object->email_header,
                        $email_object->email_to,
                        $email_object->subject,
                        $email_object->message,
                        'Wallet Statement',
                        'E-Wallet Statement', true,'info@chamasoft.com', NULL, NULL,NULL, ([$attachment])?[$attachment]:NULL
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