<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Emails_m extends MY_Model {

protected $_table = 'emails';
protected $empty_list = "0,'',' '";

	function __construct(){
		$this->load->dbforge();
		$this->load->library('Pmailer');
		$this->load->library('Emails_manager');
		
		//$this->install();
		$this->load->model('email_templates/email_templates_m');
	}

	/**
	email_to = The email of the recipient
	subject = Email Subject
	message = The email body
	email_from = Email of the sender:: if Empty, it assumes is the system/admin side who sent
	group_id = The group_id that sent the email,
	member_id = The id of the sender if from the group side,
	user_id = The id of the user sending the email,
	*/

	function install(){
		$this->db->query("
		create table if not exists emails(
			id int not null auto_increment primary key,
		 	`email_to` blob,
		  	`subject` blob,
		  	`message` blob,
		  	`email_from` blob,
		  	`sending_email` blob,
		  	`group_id` blob,
		  	`member_id` blob,
		  	`user_id` blob,
		  	`attachments` blob,
		  	`is_read` blob,
		  	`is_draft` blob,
		  	`cc` blob,
		  	`bcc` blob,
		  	`email_header` blob,
		  	`embeded_attachments` blob,
		  	created_by blob,
			created_on blob
		)");

		$this->db->query("
		create table if not exists email_queue(
			id int not null auto_increment primary key,
		 	`email_to` blob,
		  	`subject` blob,
		  	`message` blob,
		  	`email_from` blob,
		  	`sending_email` blob,
		  	`group_id` blob,
		  	`member_id` blob,
		  	`user_id` blob,
		  	`attachments` blob,
		  	`cc` blob,
		  	`bcc` blob,
		  	`email_header` blob,
		  	`embeded_attachments` blob,
		  	`is_draft` blob,
			created_on blob,
			created_by blob
		)");
	}

	function send_email($to='',$subject='',$message='',$email_from='',$sending_email='',$attachments='',$cc='',$bcc='',$embeded_attachments='',$group_id='',$member_id='',$user_id='',$created_by=0,$header = "",$donor_id=0)
	{
		if($group_id){
			$group = $this->groups_m->get($group_id);
			if($group){
				if($group->disable_notifications){
					return TRUE;
				}else{
					//continue
				}
			}else{
				$this->session->set_flashdata('error','Group record not found');
				return FALSE;
			}
		}

		$id = '';
		$sender = preg_match('/(eazzychama)/i',$this->application_settings->sender_id) ? 'info@eazzychama.co.ke' : 'info@chamasoft.com';
		$email_array = array(
			'to'=>$to,
			'email_from'	=>	$email_from?$email_from:$sender,
			'subject'=>$subject,
			'message'=>$message,
			'sending_email'=>$sending_email,
			'attachments'=>unserialize($attachments),
			'cc'=>$cc,
			'bcc'=>$bcc,
			'header'=>$header,
		);
		$mailer = $this->emails_manager->send_email($email_array);
		//$mailer = $this->pmailer->send_mail($to,$subject,$message,$sending_email,unserialize($attachments),$cc,$bcc,array(),$header);
		if($mailer){
			$input = array(
					'email_to'		=>	$to,
					'subject'		=>	$subject,
					'message'		=>	$message,
					'email_from'	=>	$email_from?$email_from:'System Admin',
					'sending_email'	=>	$sending_email,
					'group_id'		=>	$group_id?$group_id:0,
					'member_id'		=>	$member_id,
					//'donor_id'		=>	$donor_id,
					'user_id'		=>	$user_id,
					'attachments'	=>	$attachments,
					'cc'			=>	$cc,
					'bcc'			=>	$bcc,
					'embeded_attachments'=> $embeded_attachments,
					'created_on'	=>	time(),
					'created_by'	=>	$created_by,
					'email_header'  =>	$header
			);
			$id = $this->insert($input);
			if($id)
			{
				return $mailer;
			}
		}
		else
		{
			return $mailer;
		}
	}


	function insert($input = array(),$key=FALSE){
        return $this->insert_secure_data('emails', $input);
	}

	function insert_chunk_emails_queue($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_chunked_batch_secure_data('email_queue',$input);
	}

	function get($id = 0){
		$this->select_all_secure('emails');
		$this->db->where('id',$id);
		return $this->db->get('emails')->row();
	}

	function get_emails_to_send($limit = 5){
		$this->select_all_secure('email_queue');
		//$this->db->where($this->dx('is_draft').' IS NULL ',NULL,FALSE);
		$this->db->where($this->dx('message').'!=""',NULL,FALSE);
		$this->db->limit($limit);
		$this->db->order_by('id','ASC');
		return $this->db->get('email_queue')->result();
	}

	function insert_email_queue($input = array()){
    	return $this->insert_secure_data('email_queue',$input);
    }

	function get_email_queue(){
		$this->select_all_secure('email_queue');
		$this->db->order_by('created_on','DESC');
		return $this->db->get('email_queue')->result();
	}

	function get_email_from_queue($id=0){
		$this->select_all_secure('email_queue');
		$this->db->where('id',$id);
		return $this->db->get('email_queue')->row();
	}

	function email_queue_count(){
		return $this->db->count_all_results('email_queue');
	}

	function delete_email_queue($id = 0){
		$this->db->where('id',$id);
		return $this->db->delete('email_queue');
	}

	function sent_emails_count($group_id = 0,$from = 0,$to = 0){
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		return $this->db->count_all_results('emails');
	}

	function todays_sent_emails_count(){
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
		return $this->db->count_all_results('emails');
	}



	function get_all($group_id = 0,$from = 0,$to = 0){
		$this->select_all_secure('emails');
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->order_by($this->dx('created_on'), 'DESC', FALSE);
		return $this->db->get('emails')->result();
	}

	function build_email_message($slug = '',$fields,$passed_values=''){ 
		$data = '';
		$email_template = $this->email_templates_m->get_by_slug($slug);
		if($email_template){
			$data = $email_template->content;
		}
        foreach ($fields as $k => $v)
        {
            $data= preg_replace('/\[' . $k . '\]/', $v, $data);
        }
        if($passed_values){

        }
        return $data;
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'emails',$input);
    }

    function update_queue($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'email_queue',$input);
    }

    function get_emails_queued_today($email_type =0){
		$this->select_all_secure('email_queue');
		$this->db->where($this->dx('email_type').' >= "'.$email_type.'"',NULL,FALSE);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
		$arr = array();
		$results =$this->db->get('email_queue')->result();
		foreach($results as $key => $result):
			$arr[$result->user_id][$result->group_id][$result->member_id] = $result->member_id;
		endforeach;
		return $arr;
	}

	function get_reminder_emails_queued_today($email_type =0){
		$this->select_all_secure('email_queue');
		$this->db->where($this->dx('email_type').' >= "'.$email_type.'"',NULL,FALSE);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
		$arr = array();
		$results =$this->db->get('email_queue')->result();
		foreach($results as $key => $result):
			$arr[$result->user_id] = $result->member_id;
		endforeach;
		return $arr;
	}

	/************************************Group Emails *********************/


	function get_all_group_emails(){
		$this->select_all_secure('emails');
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);	
		return $this->db->get('emails')->result();
	}

	function get_all_draft_emails(){
		$this->select_all_secure('email_queue');
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('email_from').'="'.$this->user->email.'"',NULL,FALSE);
		$this->db->where($this->dx('is_draft').'="1"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);	
		return $this->db->get('email_queue')->result();
	}


	function draft_group_emails_count(){
		$this->db->where($this->dx('email_from').'="'.$this->user->email.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('is_draft').'="1"',NULL,FALSE);
		return $this->db->count_all_results('email_queue')?:0;
	}

	function get_all_queued_emails(){
		$this->select_all_secure('email_queue');
		$this->db->where($this->dx('email_from').'="'.$this->user->email.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('is_draft').' = "0" ',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);	
		return $this->db->get('email_queue')->result();
	}

	function get_queued_emails(){
		$this->select_all_secure('email_queue');
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);	
		return $this->db->get('email_queue')->result();
	}

	function get_all_sent_emails(){
		$this->select_all_secure('emails');
		$this->db->where($this->dx('email_from').'="'.$this->user->email.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('emails')->result();

	}

	function queued_group_emails_count(){
		$this->db->where($this->dx('email_from').'="'.$this->user->email.'"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('is_draft').' = "0" ',NULL,FALSE);

		//$this->db->where('('.$this->dx('is_draft').'IS NULL OR '.$this->dx('is_draft').' ="" OR '.$this->dx('is_draft').' = "0" OR '.$this->dx('is_draft').'=" ")',NULL,FALSE);
		return $this->db->count_all_results('email_queue')?:0;
	}

	function inbox_unread_group_emails_count(){

		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('is_read').' IS NULL ',NULL,FALSE);

		//$this->db->where('('.$this->dx('is_read').'="" OR '.$this->dx('is_read').'IS NULL OR '.$this->dx('is_read').'=0)',NULL,FALSE);
		$this->db->where($this->dx('email_to').'="'.$this->user->email.'"',NULL,FALSE);
		//$this->db->join('users',$this->dx('users.email').'='.$this->dx('emails.email_to'));
		return $this->db->count_all_results('emails')?:0;
	}

	function get_unread_member_emails_array(){
		/**
		$this->select_all_secure('emails');
		$this->db->select(array(
				$this->dx('users.avatar').' as sender_avatar',
				$this->dx('users.first_name').' as sender_first_name',
				$this->dx('users.middle_name').' as sender_middle_name',
				$this->dx('users.last_name').' as sender_last_name',
				$this->dx('users.email').' as sender_email',
			));
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		//$this->db->where('('.$this->dx('is_read').'="" OR '.$this->dx('is_read').'IS NULL OR '.$this->dx('is_read').'=0)',NULL,FALSE);
		$this->db->join('users',$this->dx('users.email').'='.$this->dx('emails.email_to'));
		$this->db->order_by($this->dx('emails.created_on'),'DESC',FALSE);
		return $this->db->get('emails')->result();
		**/
		return FALSE;
	}
	

	function inbox_group_emails($limit = 0){
		$this->select_all_secure('emails');
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('email_to').'="'.$this->user->email.'"',NULL,FALSE);
		if($limit){
			$this->db->limit($limit);
		}
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('emails')->result();
	}

	function get_mail($id=0){
		$this->select_all_secure('emails');
		$this->db->where('id',$id);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		//$this->db->where($this->dx('member_id').'="'.$this->member->id.'"',NULL,FALSE);
		return $this->db->get('emails')->row();
	}

	function get_queued_mail($id=0){
		$this->select_all_secure('email_queue');
		$this->db->where('id',$id);
		$this->db->where($this->dx('is_draft').' = " 0 " ',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		return $this->db->get('email_queue')->row();
	}

	function get_queued_email($id=0){
		$this->select_all_secure('email_queue');
		$this->db->where('id',$id);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		return $this->db->get('email_queue')->row();
	}

	function get_draft_mail($id=0){
		$this->select_all_secure('email_queue');
		$this->db->where('id',$id);
		$this->db->where($this->dx('is_draft').'="1"',NULL,FALSE);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		return $this->db->get('email_queue')->row();
	}

	function get_sent_mail($id=0){
		$this->select_all_secure('emails');
		$this->db->where('id',$id);
		$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
		return $this->db->get('emails')->row();
	}

	function get_this_group_currency($group_id=0)
    {
       $this->db->select(array(
                $this->dx('countries.currency_code').' as currency_code'
            ));
        $this->db->where('investment_groups.id',$group_id?:$this->group_id);
        $this->db->join('countries','countries.id = '.$this->dx('currency_id'));
        return $this->db->get('investment_groups')->row()->currency_code;
    }

    
	function delete_old_emails(){
		$this->db->where($this->dx('created_on')." < '".strtotime('-3 month')."' ",NULL,FALSE);
		return $this->db->delete('emails');
	}

	function delete_old_queued_emails(){
		$this->db->where($this->dx('created_on')." < '".strtotime('-3 days')."' ",NULL,FALSE);
		return $this->db->delete('email_queue');
	}


}