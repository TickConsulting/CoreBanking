<?php  defined('BASEPATH') OR exit('No direct script access allowed');
class Mobile extends Mobile_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->library('loan');
        $this->load->model('loan_applications_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('loan_types/loan_types_m');
    }


    function get_pending_member_loan_applications(){        
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
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->active){
                    	$application_details = array();
                    	$loan_types = $this->loan_types_m->get_options();
                        $pending_loans = $this->loan_applications_m->get_member_pending_loan_applications($this->member->id);
                        foreach ($pending_loans as $key => $pending_loan):
                        	$application_details[] = array(
                        		'id'=>$pending_loan->id,
                        		'name'=>$loan_types[$pending_loan->loan_type_id],
                        		'amount'=>$pending_loan->loan_amount,
                        		'duration' =>$pending_loan->repayment_period,
                        		'application_date'=>timestamp_to_mobile_report_time($pending_loan->created_on),
                        	);
                        endforeach;
                        $response = array(
                            'status'=>1,
                            'time'=>time(),
                            'application_details'=>$application_details,
                        );                                                                       
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }    
        echo json_encode(array('response'=>$response));
    }

    function get_member_loan_applications(){

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
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->active){
                        $application_details = array(); 
                         $loan_types = $this->loan_types_m->get_options();                       
                        $pending_loans = $this->loan_applications_m->get_member_pending_loan_applications($this->member->id);  
                        if($pending_loans){
                            foreach ($pending_loans as $key => $pending_loan):
                                $application_details[] = array(
                                    'id'=>$pending_loan->id,
                                    'name'=>$loan_types[$pending_loan->loan_type_id],
                                    'amount'=>$pending_loan->loan_amount,
                                    'duration' =>$pending_loan->repayment_period,
                                    'status'=>$pending_loan->status,
                                    'application_date'=>timestamp_to_mobile_report_time($pending_loan->created_on),
                                    'status_flag'=>0, //pending approvals
                                );
                            endforeach;
                        }
                        $approved_loan_status = array();
                        $approved_applications = $this->loan_applications_m->get_member_approved_loan_applications($this->member->id);
                        if($approved_applications){
                            foreach($approved_applications as $key => $approved_application):
                                if($approved_application->status == 0){
                                    $approved_loan_status[] = array(
                                        'id'=>$approved_application->id,
                                        'status'=>$approved_application->status,
                                        'name'=>$loan_types[$approved_application->loan_type_id],
                                        'amount'=>$approved_application->loan_amount,
                                        'duration' =>$approved_application->repayment_period,
                                        'application_date'=>timestamp_to_mobile_report_time($approved_application->created_on),
                                        'description'=>'Disbursment in progress' ,
                                        'status_flag'=>1, //approved in progress                                  
                                    );
                                }else if($approved_application->status == 1){
                                    /*$approved_loan_status[] = array(
                                        'id'=>$approved_application->id,
                                        'name'=>$loan_types[$approved_application->loan_type_id],
                                        'amount'=>$approved_application->loan_amount,
                                        'duration' =>$approved_application->repayment_period,
                                        'status'=>$approved_application->status,
                                        'application_date'=>timestamp_to_mobile_report_time($approved_application->created_on),
                                        'description'=>'Loan disbursed',
                                        'status_flag'=>2, //approved disbursed 
                                    );*/
                                }elseif ($approved_application->status == 2){
                                    $approved_loan_status[] = array(
                                        'id'=>$approved_application->id,
                                        'name'=>$loan_types[$approved_application->loan_type_id],
                                        'amount'=>$approved_application->loan_amount,
                                        'duration' =>$approved_application->repayment_period,
                                        'status'=>$approved_application->status,
                                        'application_date'=>timestamp_to_mobile_report_time($approved_application->created_on),
                                        'description'=>'Disbursment failed',
                                        'status_flag'=>3, //approved disbursment failed 
                                    );
                                }
                            endforeach;

                        }
                        $declined_details = array();
                        $declined_applications = $this->loan_applications_m->get_member_declined_loan_applications($this->member->id);
                        if($declined_applications){
                            foreach ($declined_applications as $key => $declined_application):
                                if(array_key_exists($declined_application->loan_type_id, $loan_types)){
                                    $declined_details[] = array(
                                        'id'=>$declined_application->id,
                                        'name'=>$loan_types[$declined_application->loan_type_id],
                                        'amount'=>$declined_application->loan_amount,
                                        'duration' =>$declined_application->repayment_period,
                                        'status'=>$declined_application->status,
                                        'application_date'=>timestamp_to_mobile_report_time($declined_application->created_on),
                                        'status_flag'=>4, //loan declined
                                    );
                                }
                            endforeach;
                        }
                        $response = array(
                            'status'=>1,
                            'time'=>time(),
                            'loans'=>array_merge($declined_details,array_merge($approved_loan_status,$application_details)),                          
                        );                                                             
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }    
        echo json_encode(array('response'=>$response));
    }


    function cancel_member_loan_application(){
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
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->active){
                        $loan_application_id = $this->input->post('loan_application_id');
                        if($this->loan_application = $this->loan_applications_m->get($loan_application_id)){
                            if($this->loan_application->active == 1){
                                $guarantorship_requests = $this->loans_m->get_loan_application_guarantorship_requests($this->loan_application->id);
                                $signatory_requests = $this->loans_m->get_loan_application_signatory_requests($this->loan_application->id);
                                if($guarantorship_requests){
                                    foreach($guarantorship_requests as $key => $guarantorship_request):
                                        $guarantor_input = array(
                                            'active'=>0,
                                            'modified_by'=>$this->user->id,
                                            'modified_on'=>time()
                                        );
                                        $this->loans_m->update_loan_guarantors($guarantorship_request->id,$guarantor_input);
                                    endforeach;
                                }
                                if($signatory_requests){
                                    foreach($signatory_requests as $key => $signatory_request):
                                        $signatory_input = array(
                                            'active'=>0,
                                            'modified_by'=>$this->user->id,
                                            'modified_on'=>time()
                                        );
                                        $this->loans_m->update_loan_signatories($signatory_request->id,$signatory_input);
                                    endforeach;
                                }
                                $input = array(
                                    'active'=>0,
                                    'modified_by'=>$this->user->id,
                                    'modified_on'=>time()
                                );
                                if($this->loan_applications_m->update($loan_application_id,$input)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Loan application cancelled successfully',
                                        'time' => time(),
                                    ); 
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Loan application could not be cancelled',
                                        'time' => time(),
                                    ); 
                                }
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Loan application already cancelled',
                                    'time' => time(),
                                );    
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not get loan application details',
                                'time' => time(),
                            );
                        }                                                             
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }    
        echo json_encode(array('response'=>$response));
    }

    function get_loan_application_status(){
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
            $group_id = $this->input->post('group_id');
            if($this->group = $this->groups_m->get($group_id)){
                if($this->member = $this->members_m->get_group_member_by_user_id($this->group->id,$this->user->id)){
                    if($this->member->active){
                        $loan_application_id = $this->input->post('loan_application_id');
                        if($this->loan_application = $this->loan_applications_m->get($loan_application_id)){
                            if($this->loan_application->active == 1){
                                $guarantorship_requests = $this->loans_m->get_loan_application_guarantorship_requests($this->loan_application->id);
                                $signatory_requests = $this->loans_m->get_loan_application_signatory_requests($this->loan_application->id);
                                $group_members = $this->members_m->get_group_member_options();
                                $guarantorship_status = array();
                                $signatory_status = array();
                                if($guarantorship_requests){                                    
                                    foreach($guarantorship_requests as $key => $guarantorship_request):
                                        $reason = $guarantorship_request->decline_reason?:'';
                                        if($guarantorship_request->is_approved){
                                            $status = 1 ;
                                            $description = 'Approved';
                                        }else if($guarantorship_request->is_declined){
                                            $status = 2;
                                            $description = 'Declined';                                            
                                        }else{
                                            if($this->loan_application->is_declined){
                                                $description = 'Did not respond';
                                            }else{
                                                $description = 'Pending';
                                            }
                                            $status = '0';
                                            $description = $description;
                                        }
                                        if(array_key_exists($guarantorship_request->guarantor_member_id, $group_members)){
                                            $member = $group_members[$guarantorship_request->guarantor_member_id];
                                        }else{
                                            $member = '';
                                        }
                                        $guarantorship_status[] = array(
                                            'name'=>$member,
                                            'status'=>$status,
                                            'reason'=>$reason,
                                            'description'=>$description
                                        );
                                    endforeach;
                                }
                                if($signatory_requests){
                                    foreach($signatory_requests as $key => $signatory_request):
                                        $reason = $signatory_request->decline_reason?:'';
                                        if($signatory_request->is_approved){
                                                $status = 1 ;
                                                $description = 'Approved';
                                            }else if($signatory_request->is_declined){
                                                $status = 2;
                                                $description = 'Declined';
                                            }else{
                                                if($this->loan_application->is_declined){
                                                    $description = 'Did not respond';
                                                }else{
                                                    $description = 'Pending';
                                                }
                                                $status = '0';
                                                $description = $description;
                                            }
                                            if(array_key_exists($signatory_request->signatory_member_id, $group_members)){
                                                $member = $group_members[$signatory_request->signatory_member_id];
                                            }else{
                                                $member = '';
                                            }
                                            $signatory_status[] = array(
                                                'name'=>$member,
                                                'status'=>$status,
                                                'reason'=>$reason,
                                                'description'=>$description
                                            );
                                    endforeach;
                                }
                                if($this->loan_application->is_declined){
                                    $declined_by = $this->loan_application->declined_by?$this->ion_auth->get_user($this->loan_application->declined_by):'';
                                    $decline_name = $declined_by?$declined_by->first_name.' '.$declined_by->last_name:' Automatically declined by the system';
                                }else{
                                    $decline_name = '';
                                }

                                $response = array(
                                    'status'=>1,
                                    'time'=>time(),
                                    'guarantorship_status'=>$guarantorship_status,
                                    'signatory_status'=>$signatory_status,
                                    'declined_by'=>$decline_name,
                                    'decline_reason'=>$this->loan_application->decline_reason,

                                ); 
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Loan application already cancelled',
                                    'time' => time(),
                                );    
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not get loan application details',
                                'time' => time(),
                            );
                        }                                                             
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not proceed. Account is suspended',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find Applicant Details
',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 5,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 4,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }    
        echo json_encode(array('response'=>$response));
    }
}