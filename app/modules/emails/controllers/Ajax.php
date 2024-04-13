<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

    protected $data = array();
    
    function __construct(){
        parent::__construct();
    }
    
    function count_mails(){
        $draft_emails = $this->emails_m->draft_group_emails_count();
        $outbox_emails = $this->emails_m->queued_group_emails_count();
        $inbox_emails = $this->emails_m->inbox_unread_group_emails_count();

        $counts = (object)array('outbox_emails'=>$outbox_emails,'inbox_emails'=>$inbox_emails,'draft_emails'=>$draft_emails);

        echo json_encode($counts);
    }
}