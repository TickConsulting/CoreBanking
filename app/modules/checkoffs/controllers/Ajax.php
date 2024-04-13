<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

    protected $validation_rules = array(
        array(
            'field' => 'checkoff_date',
            'label' => 'Checkoff Date',
            'rules' => 'required|trim',
        ),
        array(
            'field' => 'account_id',
            'label' => 'Account',
            'rules' => 'required|trim',
        ),
    );
    protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->model('deposits/deposits_m');
        $this->load->model('checkoffs_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('contributions/contributions_m');
    }

    function set_checkoff(){
        $response = array();
        if($_POST){
            $post = $_POST;
            $checkoff_amounts = $post['checkoff_amounts'];
            $result = TRUE;
            foreach($checkoff_amounts as $contribution_id => $members):
                foreach($members as $member_id => $amount):
                    if($this->contributions_m->delete_member_checkoff_contribution_amount_pairing($member_id,$contribution_id)){
                        $input = array(
                            'member_id' => $member_id,
                            'contribution_id' => $contribution_id,
                            'active' => 1,
                            'group_id' => $this->group->id,
                            'amount' => currency($amount),
                            'created_by' => $this->user->id,
                            'created_on' => time(),
                        );
                        if($this->contributions_m->insert_member_checkoff_contribution_amount_pairing($input)){
                            //continue
                        }else{
                            $result = FALSE;
                        }
                    }else{
                        $result = FALSE;
                    }
                endforeach;
            endforeach;
            if($result){
                $response = array(
                    'status' => 1,
                    'message' => 'Check off amounts saved successfully',
                    'refer' => site_url('group/checkoffs/listing'),
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Something went wrong when saving check off amounts',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'You have not submitted any records',
            );
        }
        echo json_encode($response);
    }

    function submit_checkoff(){
        if($_POST){
            set_time_limit(0);
            ini_set("memory_limit", "-1");
            $response = array();
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run()){
                $checkoff_date = strtotime($this->input->post('checkoff_date'));
                $account_id = $this->input->post('account_id');
                $input = array(
                    'checkoff_date' => $checkoff_date,
                    'group_id' => $this->group->id,
                    'account_id' => $account_id,
                    'active' => 1,
                    'amount' => 0,
                    'created_on' => time(),
                    'created_by' => $this->user->id,
                );
                if($checkoff_id = $this->checkoffs_m->insert($input)){
                    $checkoff_amounts = $this->input->post('checkoff_amounts');
                    // $result = TRUE;
                    $total_amount = 0;
                    $contribution_payments = array();
                    foreach($checkoff_amounts as $contribution_id => $members):
                        foreach($members as $member_id => $amount):
                            if($amount){
                                $amount = valid_currency($amount);
                                $contribution_payments[] = (object)array(
                                    'deposit_date' => $checkoff_date,
                                    'member_id' => $member_id,
                                    'contribution_id' => $contribution_id,
                                    'description' => '',
                                    'account_id' => $account_id,
                                    'amount' => $amount,
                                    'checkoff_id' => $checkoff_id,
                                    'deposit_method' => 1,
                                    'created_on' => time(),
                                    'send_sms_notification' => 0,
                                    'send_email_notification' => 0,
                                );
                                $total_amount+=$amount;
                                // if($this->transactions->record_contribution_payment($this->group->id,$checkoff_date,$member_id,$contribution_id,$account_id,1,'',$amount,FALSE,FALSE,0,FALSE,$checkoff_id)){
                                //     $total_amount+=$amount;
                                // }else{
                                //     $result = FALSE;
                                // }   
                            }
                        endforeach;
                    endforeach;
                    // if(count($contribution_payments) > 50){
                        // while(count($contribution_payments)){
                        //     if($this->transactions->record_group_contribution_payments($this->group->id,array_splice($contribution_payments, 0, 80))){ //processes in 50's //this removes the 50 from the original array
                        //         $input = array(
                        //             'amount' => $total_amount,
                        //             'modified_by' => $this->user->id,
                        //             'modified_on' => time()
                        //         );
                        //         if($this->checkoffs_m->update($checkoff_id,$input)){
                        //             $response = array(
                        //                 'status' => 1,
                        //                 'refer' => site_url('group/checkoffs/listing'),
                        //                 'message' => 'Check off submitted successfully',
                        //             );
                        //         }else{
                        //             $response = array(
                        //                 'status' => 0,
                        //                 'message' => 'Something went wrong when updating the checkoff entries',
                        //             );
                        //         }
                        //     }else{
                        //         $response = array(
                        //             'status' => 0,
                        //             'message' => 'Something went wrong when submitting the checkoff sheet',
                        //         );
                        //     }
                        // }
                    // }else{
                        if($this->transactions->record_group_contribution_payments($this->group->id,$contribution_payments)){
                            $input = array(
                                'amount' => $total_amount,
                                'modified_by' => $this->user->id,
                                'modified_on' => time()
                            );
                            if($this->checkoffs_m->update($checkoff_id,$input)){
                                $response = array(
                                    'status' => 1,
                                    'refer' => site_url('group/checkoffs/listing'),
                                    'message' => 'Check off submitted successfully',
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Something went wrong when updating the checkoff entries',
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Something went wrong when submitting the checkoff sheet',
                            );
                        }
                    // }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not save checkoff',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Please review the highlighted fields and try again',
                    'validation_errors' => $this->form_validation->error_array(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'You have not submitted any records',
            );
        }
        echo json_encode($response);
    }

    function get_monthly_checkoff_summary(){
        $response = array();
        $months_ago = 12;
        $from = strtotime("first day of -".$months_ago." months", time()); 
        $to = strtotime('tomorrow');
        $checkoffs = $this->checkoffs_m->get_group_checkoff_monthly_summary($this->group->id,$from,$to);
        $amounts = array();
        for($i=0;$i<=$months_ago;$i++){
            if(array_key_exists(date('Ym', $from),$checkoffs)){
                $months[] = date('M \'y',$from);
                $amounts[] = $checkoffs[date('Ym', $from)];
            }else{
                $months[] = date('M \'y',$from);
                $amounts[] = 0;
            }
            $from = strtotime('last day of +1 month',$from);
        }
        $response = array(
            'months' => $months,
            'amounts' => $amounts,
        );
        echo json_encode($response);
    }
}