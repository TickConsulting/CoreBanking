<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Loan_applications_m extends MY_Model{

	protected $_table = 'loan_applications';

	function __construct(){
		parent::__construct();
		//$this->install();
	}

	function install(){
    $this->db->query('
      create table if not exists loan_applications(
          `id` int not null auto_increment primary key,
            `member_id` blob,
            `group_id` blob,
            `loan_type_id` blob,
            `loan_amount` blob,
            `repayment_period` blob,
            `is_approved` blob,
            `description` blob,
            `active` blob,
            `created_by` blob,
            `created_on` blob,
            `modified_on` blob,
            `modified_by` blob,
            `status` blob,
           `agree_to_rules` blob

          )'
    );
    
    $this->db->query('
        create table if not exists loan_approval_requests(
          id int not null auto_increment primary key,
          `loan_application_id` blob,
          `loan_type_id` blob,
          `member_id` blob,
          `user_id` blob,  
          `group_role_id` blob,
          `group_id` blob,
          `signatory_member_id` blob,
          `signatory_user_id` blob,
          `level` blob,
          `is_last_level` blob,
          `active` blob,
          `is_deleted` blob,
          `is_approved` blob,
          `is_declined` blob,
          `comments` blob,
          `created_on` blob,  
          `created_by` blob,  
          `modified_on` blob,
          `modified_by` blob
      )'
    );
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('loan_applications',$input);
	}

	function update($id,$input,$val=FALSE){
	   return $this->update_secure_data($id,'loan_applications',$input);
	}

	function get($id=0){
      $this->select_all_secure('loan_applications');
      $this->db->where('id',$id);
      return $this->db->get('loan_applications')->row();
	}

	   function get_all(){
	    	$this->select_all_secure('loan_applications');
	    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);	
	    	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);	
        $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
        $this->db->order_by($this->dx('status'),'ASC',FALSE);
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
	    	return $this->db->get('loan_applications')->result();
	}

  function get_member_loan_application($id=0,$group_id=0,$member_id=0){
      $this->select_all_secure('loan_applications');
      $this->db->where('id',$id);
      $this->db->where($this->dx('active').'="1"',NULL,FALSE);  
      if($member_id){
        $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE); 
      }else{
        $this->db->where($this->dx('member_id').'="'.$this->member->id.'"',NULL,FALSE); 
      }
      if($group_id){
        $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE); 
      }else{
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
      return $this->db->get('loan_applications')->row();
  }

  function get_pending_member_loan_applications($group_id=0,$member_id=0){
    $this->select_all_secure('loan_applications');
    $this->db->where($this->dx('active').'="1"',NULL,FALSE);  
    if($group_id){
      $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
    }else{
      $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
    }
    if($member_id){
      $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE); 
    }else{
      $this->db->where($this->dx('member_id').'="'.$this->member->id.'"',NULL,FALSE); 
    }
    $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    $this->db->where($this->dx('is_approved').' IS NULL',NULL,FALSE);
    $this->db->order_by($this->dx('status'),'ASC',FALSE);
    $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    return $this->db->get('loan_applications')->result();
  }

	 function count_all(){
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);	
    	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    	return $this->count_all_results('loan_applications');
    }

    function get_member_loan_applications($group_id=0,$member_id=0){
    	$this->select_all_secure('loan_applications');
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);	
      if($group_id){
        $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
    	if($member_id){
        $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE); 
      }else{
        $this->db->where($this->dx('member_id').'="'.$this->member->id.'"',NULL,FALSE); 
      }
      $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
      $this->db->order_by($this->dx('status'),'ASC',FALSE);
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
	    return $this->db->get('loan_applications')->result();
    }

    function get_member_loans($group_id=0,$member_id=0,$is_fully_paid = FALSE){
        $this->select_all_secure('loan_applications'); 
        if($group_id){
            $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
        }else{
            $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
        }
        if($member_id){
            $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE); 
        }else{
            $this->db->where($this->dx('member_id').'="'.$this->member->id.'"',NULL,FALSE); 
        }
        $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
        $this->db->order_by($this->dx('status'),'ASC',FALSE);
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        return $this->db->get('loan_applications')->result();
    }
    function get_application_by_reference_number($reference_number = 0){
      $this->select_all_secure('loan_applications');
      $this->db->where($this->dx('reference_number').' = "'.$reference_number.'"',NULL,FALSE);
      return $this->db->get('loan_applications')->row();
    }
    // function get_pending_group_loan_applications($group_id=0){
    //   $this->select_all_secure('loan_applications'); 
    //   if($group_id){
    //       $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
    //   }else{
    //       $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
    //   }
    //   $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
    //   $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    //   return $this->db->get('loan_applications')->result();
    // }



    // function get_pending_loan_applications($group_id=0){
    //   $this->select_all_secure('loan_applications'); 
    //   if($group_id){
    //       $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
    //   }else{
    //       $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
    //   }
    //   $this->db->where($this->dx('is_approved').'="0"',NULL,FALSE); 
    //   $this->db->where($this->dx('is_declined').'="0"',NULL,FALSE); 
    //   $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    //   return $this->db->get('loan_applications')->result();
    // }

    function get_group_pending_loan_applications($group_id=0){
      $this->select_all_secure('loan_applications'); 
      if($group_id){
          $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('is_approved').'="0"',NULL,FALSE); 
      $this->db->where($this->dx('is_declined').'="0"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_approved_loan_applications_pending_disbursements(){
      $this->select_all_secure('loan_applications'); 
      $this->db->where($this->dx('is_approved').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('status').'="0"',NULL,FALSE); 
      $this->db->where($this->dx('is_declined').'="0"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_approved_group_loan_applications_pending_disbursement($group_id=0){
      $this->select_all_secure('loan_applications'); 
      if($group_id){
          $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('status').'="0"',NULL,FALSE); 
      $this->db->where($this->dx('is_approved').'="1"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_group_approved_loan_applications($group_id=0){
      $this->select_all_secure('loan_applications'); 
      if($group_id){
          $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('is_approved').'="1"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_group_disbursed_loan_applications($group_id=0){
      $this->select_all_secure('loan_applications'); 
      if($group_id){
          $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('is_approved').'="1"',NULL,FALSE);
      $this->db->where($this->dx('status').'="1"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }


    function get_member_disbursed_loan_applications($member_id = 0,$group_id=0){
      $this->select_all_secure('loan_applications'); 
      $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
      if($group_id){
          $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('is_approved').'="1"',NULL,FALSE);
      $this->db->where($this->dx('status').'="1"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_member_disbursement_failed_loan_applications($member_id = 0,$group_id=0){
      $this->select_all_secure('loan_applications'); 
      $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
      if($group_id){
          $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('is_approved').'="1"',NULL,FALSE);
      $this->db->where($this->dx('status').'="2"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }


    function get_group_disbursement_failed_loan_applications($group_id=0){
      $this->select_all_secure('loan_applications'); 
      if($group_id){
          $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('is_approved').'="1"',NULL,FALSE);
      $this->db->where($this->dx('status').'="2"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_group_declined_loan_applications($group_id=0){
      $this->select_all_secure('loan_applications'); 
      if($group_id){
          $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('is_declined').'="1"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_member_pending_loan_applications($member_id=0){
      $this->select_all_secure('loan_applications'); 
      $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
      $this->db->where($this->dx('is_approved').'="0"',NULL,FALSE); 
      $this->db->where($this->dx('is_declined').'="0"',NULL,FALSE); 
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_member_loan_application_in_progress($member_id=0){
      $this->select_all_secure('loan_applications'); 
      $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
      //$this->db->where($this->dx('status').'="0"',NULL,FALSE);  
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_member_approved_loan_applications($member_id=0){
      $this->select_all_secure('loan_applications'); 
      $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
      $this->db->where($this->dx('is_approved').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_member_approved_loan_applications_pending_disbursement($member_id=0){
      $this->select_all_secure('loan_applications'); 
      $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
      $this->db->where($this->dx('is_approved').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('status').'="0"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_member_declined_loan_applications($member_id=0){
      $this->select_all_secure('loan_applications'); 
      $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
      $this->db->where($this->dx('is_declined').'="1"',NULL,FALSE); 
      $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }
    
    function get_pending_bank_loan_applications(){
      $this->select_all_secure('loan_applications');
      $this->db->where($this->dx('status').'="4"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function get_all_pending_loan_applications(){
      $this->select_all_secure('loan_applications');
      $this->db->where($this->dx('status').'="3"',NULL,FALSE); 
      $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
      return $this->db->get('loan_applications')->result();
    }

    function count_all_member_loan_applications(){
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);	
    	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
    	$this->db->where($this->dx('member_id').'="'.$this->member->id.'"',NULL,FALSE);	
      $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    	return $this->count_all_results('loan_applications');
    }

    function count_all_group_loan_applications(){
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);	
    	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    	return $this->count_all_results('loan_applications');
    }

    function count_pending_loan_applications($loan_type_id=0,$group_id=0,$member_id=0){
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);	
      if($group_id){
          $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      if($member_id){
          $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('member_id').'="'.$this->member->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('loan_type_id').'="'.$loan_type_id.'"',NULL,FALSE);		
      $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
      $this->db->where($this->dx('status').'<3',NULL,FALSE);	
    	return $this->count_all_results('loan_applications')?:0;
    }

    function count_group_active_loan_applications(){
    	$this->db->where($this->dx('active').'="1"',NULL,FALSE);	
    	$this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      $this->db->where($this->dx('status').'<3',NULL,FALSE);	
      $this->db->where($this->dx('is_deleted').' IS NULL',NULL,FALSE);
    	return $this->count_all_results('loan_applications')?:0;
    }

    function safe_delete($id=0,$group_id=0){
      $this->db->where('id',$id);
      if($group_id){
        $this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
      }else{
        $this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
      }
      return $this->update_secure_data($id,'loan_applications',array('is_deleted'=>1,'modified_on'=>time()));
    }

    function count_group_loan_types($id = 0,$group_id=0){
      $this->select_all_secure('loan_applications');
      $this->db->where($this->dx('loan_applications.loan_type_id'). ' = "'.$id.'"',NULL,FALSE);
      if($group_id){
          $this->db->where($this->dx('loan_applications.group_id'). ' = "'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('loan_applications.group_id'). ' = "'.$this->group->id.'"',NULL,FALSE);
      }
      return $this->db->count_all_results('loan_applications');
    }

    function get_option_objects_array(){
      $arr = array();
      $this->select_all_secure('loan_applications');
      $this->db->where($this->dx('group_id')." = '".$this->group->id."' ",NULL,FALSE);
      $this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
      $loan_applications = $this->db->get('loan_applications')->result();
      foreach($loan_applications as $loan_application):
        $arr[$loan_application->id] = $loan_application;
      endforeach;
      return $arr;
    }

    function count_all_pending_member_loan_applications($group_id=0,$member_id=0){
      $this->select_all_secure('loan_applications');
      if($group_id){
          $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
      }
      if($member_id){
          $this->db->where($this->dx('member_id').'="'.$member_id.'"',NULL,FALSE);
      }else{
          $this->db->where($this->dx('member_id').'="'.$this->member->id.'"',NULL,FALSE);
      }
      $this->db->where($this->dx('is_approved').'="0"',NULL,FALSE); 
      $this->db->where($this->dx('is_declined').'="0"',NULL,FALSE); 
      return $this->db->count_all_results('loan_applications');
    }

  function get_average_loan_application_amounts_per_month($group_id = 0,$from = '',$to = '',$loan_type_id = 0){
    $this->db->select(array(
      'SUM('.$this->dx('loan_amount').') as loan_amount',
      "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month "
    ));
    $this->db->where($this->dx('active'). ' = "1"',NULL,FALSE);
    if($group_id){
      $this->db->where($this->dx('group_id'). ' = "'.$group_id.'"',NULL,FALSE);
    }else{
      $this->db->where($this->dx('group_id'). ' = "'.$this->group->id.'"',NULL,FALSE);
    }

    if($from){
      $this->db->where($this->dx('created_on'). ' >= "'.$from.'"',NULL,FALSE);
    }

    if($to){
      $this->db->where($this->dx('created_on'). ' <= "'.$to.'"',NULL,FALSE);
    }

    $this->db->group_by(
      array(
        'month',
      )
    );
    $result = $this->db->get('loan_applications')->result();
    $average = $result?array_sum(array_column($result, 'loan_amount'))/count($result):0;
    return $average;
  }

  function count_average_loan_application_per_month($group_id = 0,$from = '',$to = '',$loan_type_id = 0){
    $this->db->select(array(
      'COUNT('.$this->dx('loan_amount').') as loan_applications',
      "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%c') as month "
    ));
    $this->db->where($this->dx('active'). ' = "1"',NULL,FALSE);
    if($group_id){
      $this->db->where($this->dx('group_id'). ' = "'.$group_id.'"',NULL,FALSE);
    }else{
      $this->db->where($this->dx('group_id'). ' = "'.$this->group->id.'"',NULL,FALSE);
    }

    if($from){
      $this->db->where($this->dx('created_on'). ' >= "'.$from.'"',NULL,FALSE);
    }

    if($to){
      $this->db->where($this->dx('created_on'). ' <= "'.$to.'"',NULL,FALSE);
    }

    $this->db->group_by(
      array(
        'month',
      )
    );
    $result = $this->db->get('loan_applications')->result();
    $count = $result?array_sum(array_column($result, 'loan_applications'))/count($result):0;
    return $count;
  }

  function get_loan_application_approval_requests($loan_application_id = '',$group_id=0){
    $this->select_all_secure('loan_approval_requests'); 
    if($group_id){
        $this->db->where($this->dx('group_id').'="'.$group_id.'"',NULL,FALSE);
    }else{
        $this->db->where($this->dx('group_id').'="'.$this->group->id.'"',NULL,FALSE);
    }
    $this->db->where($this->dx('loan_application_id').'="'.$loan_application_id.'"',NULL,FALSE);
    $this->db->where($this->dx('active').'="1"',NULL,FALSE); 
    $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    return $this->db->get('loan_approval_requests')->result();
  }

  function update_loan_application_approval_request($id,$input,$val=FALSE){
    return $this->update_secure_data($id,'loan_approval_requests',$input);
  }
}
?>