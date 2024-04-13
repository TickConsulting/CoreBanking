<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile extends Mobile_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model('group_roles/group_roles_m');
    }

    public function _remap($method, $params = array()){
       if(method_exists($this, $method)){
           return call_user_func_array(array($this, $method), $params);
       }
       $this->output->set_status_header('404');
       header('Content-Type: application/json');
       $file = file_get_contents('php://input')?(array)json_decode(file_get_contents('php://input')):array();
       $request = $_REQUEST+$file;
       echo encrypt_json_encode(
        array(
            'response' => array(
                    'status'    =>  404,
                    'message'       =>  '404 Method Not Found for URI: '.$this->uri->uri_string(),
                ),

        ));
    }

    function contribution_statement(){
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
                    $group_role_options = $this->group_roles_m->get_group_role_options($this->group->id);
                    if($this->group->enable_member_information_privacy){
                        $member_id = $this->member->id;
                    }else{
                        $member_id = $this->input->post('member_id')?:$this->member->id;
                    }
                    if($member_id){
                        if($group_member = $this->members_m->get_group_member($member_id)){
                            //$open_contribution_options = $this->contributions_m->get_group_open_contribution_options($this->group->id);
                            $open_contribution_options = $this->contributions_m->get_group_contribution_display_options($this->group->id,TRUE);
                            $contribution_id_list='';                                    
                            if($open_contribution_options){
                                $contribution_id_list = implode(',',array_keys($open_contribution_options));
                            }
                            $from = strtotime('-3 months');
                            $to = time();
                            $balance = $this->statements_m->get_member_contribution_balance($this->group->id,$member_id,$contribution_id_list,$from);
                            $amount_payable = $this->statements_m->get_member_contribution_amount_payable($this->group->id,$member_id,$contribution_id_list,$from);
                            $amount_paid = $this->statements_m->get_member_contribution_amount_paid($this->group->id,$member_id,$contribution_id_list,$from);

                            $member_datails = array(
                                'name' => $group_member->first_name.($group_member->middle_name?(' '.$group_member->middle_name.' '):' ').$group_member->last_name,
                                "role" => $group_member->is_admin?'Admin':(isset($group_roles[$group_member->group_role_id])?$group_roles[$group_member->group_role_id]:'Member'),
                                'email' => $group_member->email,
                                'phone' => valid_phone($group_member->phone),
                                'avatar' => $group_member->avatar,
                                'group' => $this->group->name,
                            );

                            $statement_details = array(
                                'statement_as_at' => timestamp_to_date(time(),TRUE),
                                'statement_period_from' => timestamp_to_date($from,TRUE),
                                'statement_period_to' => timestamp_to_date($to,TRUE),
                            );
                            

                            $statement_body = array();
                            $statement_header = array();
                            $statement_footer = array();
                            $total_amount_payable = $amount_payable;
                            $total_amount_paid = $amount_paid;
                            $balance = $balance;

                            if($this->group->disable_arrears){
                                $contribution_display_options = $this->contributions_m->get_group_contribution_display_options($this->group->id,TRUE);
                                $total_member_deposit_amount_by_contribution_array = $this->deposits_m->get_total_group_member_deposit_amount_by_contribution_array($member_id);
                                $member_contribution_transfers_to = $this->statements_m->get_group_member_contribution_transfers_to_per_contribution_array($member_id);
                                $member_contribution_transfers_from = $this->statements_m->get_group_member_contribution_transfers_from_per_contribution_array($member_id);
                                $opening_balances = $this->statements_m->get_member_deposits_opening_balance_by_contribution_array($this->group->id,$member_id,'',$from);
                                $posts = $this->statements_m->get_member_deposit_statement($member_id,'',$from,$to);
                                $group_member_options = $this->members_m->get_group_member_options($this->group->id);
                                $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id);
                                $fine_category_options = $this->fine_categories_m->get_group_options(FALSE,$this->group->id);
                                $closing_balance = 0; 
                                foreach($contribution_display_options as $contribution_id => $name): 
                                    $amount = isset($total_member_deposit_amount_by_contribution_array[$contribution_id])?$total_member_deposit_amount_by_contribution_array[$contribution_id]:0;
                                    $amount += $member_contribution_transfers_to[$contribution_id]-$member_contribution_transfers_from[$contribution_id];

                                    $opening_balance = isset($opening_balances[$contribution_id])?$opening_balances[$contribution_id]:0; 
                                    
                                    $closing_balance += $opening_balance; 
                                    $statement_header = array(
                                        'description' => 'Opening Balance',
                                        'date'  =>  timestamp_to_date($from,TRUE),
                                        'payable' => 0,
                                        'paid' => 0,
                                        'balance' => $opening_balance?:0,
                                    );
                                    if(isset($posts[$contribution_id])){
                                        foreach($posts[$contribution_id] as $post):
                                            $description = '';
                                            $date = '';
                                            $payable = 0;
                                            $paid = 0;
                                            if($post->transaction_type==25):
                                                $closing_balance -= $post->amount;
                                                if($post->member_id){
                                                    $description = "Share transfer from ".$group_member_options[$post->member_id].' to '.$group_member_options[$post->member_id];
                                                }else{
                                                     if($post->transfer_to==1){
                                                            $description = "Contribution transfer to ".$contribution_options[$post->contribution_to_id];
                                                        }else if($post->transfer_to==2){
                                                            if($post->contribution_to_id){
                                                                $description = "Contribution transfer to ".$contribution_options[$post->contribution_to_id];
                                                            }else if($post->fine_category_to_id){
                                                                $description = "Contribution transfer to ".$fine_category_options[$post->fine_category_to_id];
                                                            }
                                                            if($post->fine_category_to_id){
                                                                $description.= '- For '.$fine_category_options[$post->fine_category_to_id];
                                                            }else{
                                                                $description.=  ' - For '.$contribution_options[$post->contribution_to_id];
                                                            }
                                                        }else if($post->transfer_to==3){
                                                            $description=  ' To loan '.$this->loans_m->get_loan_details($post->loan_to_id);
                                                        }else if($post->transfer_to==4){
                                                            $description = $group_member_options[$post->member_id].' to '.$group_member_options[$post->share_transfer_recipient_member_id];
                                                        }
                                                }
                                                $payable = $opening_balance;
                                                $paid = $post->amount;
                                                $closing_balance = $closing_balance;

                                                $opening_balance -= $post->amount;
                                            elseif($post->transaction_type==26):
                                                $closing_balance += $post->amount;
                                                if($post->member_id){
                                                    $description = "Share transfer from ".$group_member_options[$post->member_id].' to '.$group_member_options[$post->member_id];
                                                }else{
                                                    $description = 'Contribution transfer from ';
                                                    if($post->transfer_to==1){
                                                        if($post->contribution_from_id=='loan'){
                                                            $description.= 'From loan -'.$this->loans_m->get_loan_details($post->loan_from_id);
                                                        }else{
                                                            $description.= "'".$contribution_options[$post->contribution_from_id];
                                                        }
                                                    }else if($post->transfer_to==2){
                                                        if($post->contribution_to_id){
                                                            if($post->contribution_from_id=='loan'){
                                                                $description.= 'From loan -'.$this->loans_m->get_loan_details($post->loan_from_id);
                                                            }else{
                                                                $description.= "'".$contribution_options[$post->contribution_from_id];
                                                            }
                                                        }else if($post->fine_category_to_id){
                                                            if($post->contribution_from_id=='loan'){
                                                                $description.= 'From loan -'.$this->loans_m->get_loan_details($post->loan_from_id);
                                                            }else{
                                                                $description.= "'".$contribution_options[$post->contribution_from_id];
                                                            }
                                                        }
                                                        if($post->fine_category_to_id){
                                                            $description.= '- For '.$fine_category_options[$post->fine_category_to_id];
                                                        }else{
                                                            $description.= ' - For '.$contribution_options[$post->contribution_to_id];
                                                        }
                                                    }else if($post->transfer_to==3){
                                                        $description.= ' To loan '.$this->loans_m->get_loan_details($post->loan_to_id);
                                                    }else if($post->transfer_to==4){
                                                        $description.= $group_member_options[$post->member_id].' to '.$group_member_options[$post->share_transfer_recipient_member_id];
                                                    }
                                                }
                                                $payable = $opening_balance;
                                                $paid = $post->amount;
                                                $closing_balance = $closing_balance;

                                                $opening_balance += $post->amount;
                                            else:
                                                if($post->transaction_type==21 || $post->transaction_type==22 || $post->transaction_type==23 || $post->transaction_type==24 ){
                                                    $closing_balance -= $post->amount;
                                                    $description = 'Contribution Refund';

                                                    $payable = $opening_balance;
                                                    $paid = $post->amount;
                                                    $closing_balance = $closing_balance;

                                                    $opening_balance -= $post->amount;
                                                }else if($post->transaction_type==30){
                                                    $closing_balance -= $post->amount;

                                                    $description = 'Contribution Transfer to Loan';
                                                    $payable = $opening_balance;
                                                    $paid = $post->amount;
                                                    $closing_balance = $closing_balance;

                                                    $opening_balance -= $post->amount;

                                                }else{
                                                    $closing_balance += $post->amount;
                                                    
                                                    $description = 'Contribution Deposit';
                                                    $payable = $opening_balance;
                                                    $paid = $post->amount;
                                                    $closing_balance = $closing_balance;

                                                    $opening_balance += $post->amount;
                                                }
                                            endif;
                                            $statement_body[] = array(
                                                'description' => $description,
                                                'date' => timestamp_to_date($post->transaction_date,TRUE),
                                                'payable' => $payable,
                                                'paid' => $paid,
                                                'balance' => $closing_balance,
                                                'invoice_id' => $post->invoice_id,
                                                'deposit_id' => $post->deposit_id,
                                            );
                                        endforeach;
                                    }
                                endforeach;
                                $statement_body = array_merge($statement_body , array(array(
                                    'description' => 'Total Member Savings - Closing Balance',
                                    'date'  =>  timestamp_to_date($to,TRUE),
                                    'payable' => 0,
                                    'paid' => 0,
                                    'balance' => $closing_balance?:0,
                                )));
                            }else{
                                $statement_header = array(
                                    'description' => 'Balance B/F',
                                    'date'  =>  timestamp_to_date($from,TRUE),
                                    'payable' => $amount_payable?:0,
                                    'paid' => $amount_paid?:0,
                                    'balance' => $balance?:0,
                                );
                                $posts = $this->statements_m->get_member_contribution_statement($member_id,$contribution_id_list,$from,$to,$this->group->id,'ASC');
                                $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id);
                                $statement_transaction_names = $this->transactions->statement_transaction_names;
                                foreach ($posts as $post) {
                                    $description = '';
                                    $date = '';
                                    $payable = 0;
                                    $paid = 0;
                                    
                                    if($post->transaction_type==1){ 
                                        $balance+=$post->amount;
                                        $total_amount_payable+=$post->amount;
                                        $description = $statement_transaction_names[$post->transaction_type].' '.$contribution_options[$post->contribution_id];
                                        $payable = $post->amount;
                                    }else if($post->transaction_type==21 ||$post->transaction_type==22 || $post->transaction_type==23 || $post->transaction_type==24){ 
                                        $balance+=$post->amount;
                                        $total_amount_paid-=$post->amount;
                                        $description=$statement_transaction_names[$post->transaction_type].' '.$contribution_options[$post->contribution_id].' refund';
                                        $paid = $post->amount;
                                    }else if($post->transaction_type==30){
                                        $balance+=$post->amount;
                                        $total_amount_paid-=$post->amount;
                                        $description=$statement_transaction_names[$post->transaction_type];
                                        $description.=$contribution_options[$post->contribution_from_id].' to '.(($post->loan_to_id)?'loan':'');
                                        $paid = ($post->amount);
                                    }else if($post->transaction_type==9||$post->transaction_type==10||$post->transaction_type==11||$post->transaction_type==15){ 
                                        $balance-=$post->amount;
                                        $total_amount_paid+=$post->amount;
                                        $description = $statement_transaction_names[$post->transaction_type].' '.$contribution_options[$post->contribution_id];
                                        $paid = $post->amount;
                                    }else if($post->transaction_type==25){  
                                        $balance+=$post->amount;
                                        $total_amount_payable+=$post->amount;
                                        $description = $statement_transaction_names[$post->transaction_type].' '.$contribution_options[$post->contribution_id]." to ".$contribution_options[$post->contribution_to_id];
                                        $paid = $post->amount;
                                    }else if($post->transaction_type==26){
                                        $balance-=$post->amount;
                                        $total_amount_paid+=$post->amount;  
                                        $description=$statement_transaction_names[$post->transaction_type];
                                        $paid = $post->amount;
                                    }else if($post->transaction_type==27){
                                        $balance+=$post->amount;
                                        $total_amount_paid-=$post->amount; 
                                        $description = $statement_transaction_names[$post->transaction_type];
                                        $paid = $post->amount;
                                    }
                                    if($description){
                                        $statement_body[] = array(
                                            'description' => $description,
                                            'date' => timestamp_to_date($post->transaction_date,TRUE),
                                            'payable' => $payable,
                                            'paid' => $paid,
                                            'balance' => $balance,
                                            'invoice_id' => $post->invoice_id,
                                            'deposit_id' => $post->deposit_id,
                                        );
                                    }
                                }
                                $statement_footer = array(
                                    'description' => 'Totals',
                                    'payable' => $total_amount_payable?:0,
                                    'paid' => $total_amount_paid?:0,
                                    'balance' => $balance?:0,
                                );
                            }
                            $response = array(
                                'status' => 1,
                                'message' => 'Contribution statement',
                                'time' => time(),
                                'member_datails' => $member_datails,
                                'statement_details' => $statement_details,
                                'statement_header' => $statement_header,
                                'statement_body' => $statement_body,
                                'statement_footer' => $statement_footer,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find group member details',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Member to view profile not found in the group',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function fine_statement(){
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
                    $group_role_options = $this->group_roles_m->get_group_role_options($this->group->id);
                    if($this->group->enable_member_information_privacy){
                        $member_id = $this->member->id;
                    }else{
                        $member_id = $this->input->post('member_id')?:$this->member->id;
                    }
                    if($member_id){
                        if($post = $this->members_m->get_group_member($member_id)){
                            $from = strtotime('-3 months');
                            $to = time();
                            $balance_bf = $this->statements_m->get_member_fine_balance($this->group->id,$post->id,'','',$from);
                            $amount_payable = 0;
                            $amount_paid = 0;

                            $member_datails = array(
                                'name' => $post->first_name.($post->middle_name?(' '.$post->middle_name.' '):' ').$post->last_name,
                                "role" => $post->is_admin?'Admin':(isset($group_roles[$post->group_role_id])?$group_roles[$group_member->group_role_id]:'Member'),
                                'email' => $post->email,
                                'phone' => valid_phone($post->phone),
                                'avatar' => $post->avatar,
                            );

                            $statement_details = array(
                                'statement_as_at' => timestamp_to_date(time(),TRUE),
                                'statement_period_from' => timestamp_to_date($from,TRUE),
                                'statement_period_to' => timestamp_to_date($to,TRUE),
                            );
                            $statement_header = array(
                                'description' => 'Balance B/F',
                                'date'  =>  timestamp_to_date($from,TRUE),
                                'payable' => $amount_payable?:0,
                                'paid' => $amount_paid?:0,
                                'balance' => $balance_bf?:0,
                            );
                            $posts = $this->statements_m->get_member_fine_statement($post->id,'','',$from,$to,$this->group->id);
                            $total_amount_payable = $amount_payable;
                            $total_amount_paid = $amount_paid;
                            $balance = $balance_bf;
                            $statement_body = array();
                            $contribution_fine_options = $this->contributions_m->get_group_contribution_fine_options($this->group->id);
                            $fine_category_options = $this->fine_categories_m->get_group_options(FALSE,$this->group->id);
                            $statement_transaction_names = $this->transactions->statement_transaction_names;
                            foreach ($posts as $post) {
                                $description = '';
                                $date = '';
                                $payable = 0;
                                $paid = 0;
                                
                                 if($post->transaction_type==2){ 
                                    $balance+=$post->amount;
                                    $total_amount_payable+=$post->amount;
                                    $description = $statement_transaction_names[$post->transaction_type].' '.$contribution_fine_options[$post->contribution_id];
                                    $payable = $post->amount;
                                }else if($post->transaction_type==3){
                                    $balance+=$post->amount;
                                    $total_amount_payable+=$post->amount;
                                    $description = $statement_transaction_names[$post->transaction_type].' Fine for '.(isset($fine_category_options[$post->fine_category_id])?$fine_category_options[$post->fine_category_id]:'');
                                    $payable = $post->amount;
                                }elseif($post->transaction_type==12||$post->transaction_type==13||$post->transaction_type==14||$post->transaction_type==16){
                                    $balance-=$post->amount;
                                    $total_amount_paid+=$post->amount;
                                    $description= $statement_transaction_names[$post->transaction_type];
                                    if($post->contribution_id){
                                        $description.=' for '.$contribution_fine_options[$post->contribution_id]; 
                                    }else if($post->fine_category_id){
                                        $description.=' for '.(isset($fine_category_options[$post->fine_category_id])?$fine_category_options[$post->fine_category_id]:''); 
                                    }
                                    $paid = $post->amount;
                                }else if($post->transaction_type==28){
                                    $balance-=$post->amount;
                                    $total_amount_paid+=$post->amount; 
                                    $description = $statement_transaction_names[$post->transaction_type];
                                    if($post->contribution_from_id=="loan"){
                                        $description.= " Loan repayment transfer to ";
                                    }else{
                                        $description.= $contribution_fine_options[$post->contribution_from_id]."' contribution transfer from ";
                                    }
                                    if($post->contribution_to_id){
                                        $description.= $contribution_fine_options[$post->contribution_to_id]." late payment fine";
                                    }else if($post->fine_category_to_id){
                                        $description.= $fine_category_options[$post->fine_category_to_id];
                                    }
                                    $paid = $post->amount;
                                }

                                if($description){
                                    $statement_body[] = array(
                                        'description' => $description,
                                        'date' => timestamp_to_date($post->transaction_date,TRUE),
                                        'invoice_id' => $post->invoice_id,
                                        'deposit_id' => $post->deposit_id,
                                        'payable' => $payable,
                                        'paid' => $paid,
                                        'balance' => $balance,
                                    );
                                }
                            }
                            
                            $statement_footer = array(
                                'description' => 'Totals',
                                'payable' => $total_amount_payable?:0,
                                'paid' => $total_amount_paid?:0,
                                'balance' => $balance?:0,
                            );

                            $response = array(
                                'status' => 1,
                                'message' => 'Contribution statement',
                                'time' => time(),
                                'member_datails' => $member_datails,
                                'statement_details' => $statement_details,
                                'statement_header' => $statement_header,
                                'statement_body' => $statement_body,
                                'statement_footer' => $statement_footer,
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find group member details',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Member to view profile not found in the group',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }

    function send_email_statement(){
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
                    $member_id = $this->member->id;
                    if($member_id){
                        if($post = $this->members_m->get_group_member($member_id)){
                            $from = str_replace(',','',$this->input->post('from'));
                            $from = $from?strtotime($from):strtotime('-3 months');
                            $to = str_replace(',','',$this->input->post('to'));
                            $to = $to?strtotime($to):time();
                            $statement_file_type = $this->input->post('file_type')?:1;
                            $type = $this->input->post('type')?:1;
                            $email = $this->input->post('email');
                            $loan_id = $this->input->post('loan_id');
                            $valid_email = FALSE;
                            if($email){
                                if(valid_email($email)){
                                    $valid_email = TRUE;
                                    if(!valid_email($this->user->email)){
                                        $update = array(
                                            'email' => $email,
                                            'modified_on' => time(),
                                            'modified_by' => $this->user->id,
                                        );
                                        if($this->ion_auth->identity_check($email)){
                                        }else{
                                            $this->ion_auth->update($this->user->id,$update);
                                        }
                                    }
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Kindly enter a valid email address',
                                        'time' => time(),
                                    );
                                }
                            }else{
                                if(valid_email($this->user->email)){
                                    $valid_email = TRUE;
                                    $email = $this->user->email;
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Kindly update profile and use a valid email address',
                                        'time' => time(),
                                    );
                                }
                            }

                            if($valid_email){
                                $input = array(
                                    'user_id' => $this->user->id,
                                    'member_id' => $this->member->id,
                                    'group_id' => $this->group->id,
                                    'statement_file_type' => $statement_file_type,
                                    'type'  => $type,
                                    'date_from' => $from,
                                    'date_to' => $to,
                                    'created_on' => time(),
                                    'created_by' => $this->user->id,
                                    'active' => 1,
                                    'action' => 1,
                                    'email' => $email,
                                    'loan_id' => $loan_id,
                                );
                                if($this->statements_m->insert_statement_request($input)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Request received. Statement will be sent to '.$this->user->email.'.',
                                        'time' => time()
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Error occured. Try again later',
                                        'time' => time()
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find group member details',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Member to view profile not found in the group',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }
    

    function download_statement(){
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
                    $member_id = $this->member->id;
                    if($member_id){
                        if($post = $this->members_m->get_group_member($member_id)){
                            $from = str_replace(',','',$this->input->post('from'));
                            $from = $from?strtotime($from):strtotime('-3 months');
                            $to = str_replace(',','',$this->input->post('to'));
                            $to = $to?strtotime($to):time();
                            $statement_file_type = $this->input->post('file_type')?:1;
                            $type = $this->input->post('type')?:1;
                            $loan_id = $this->input->post('loan_id');
                            $input = array(
                                'user_id' => $this->user->id,
                                'member_id' => $this->member->id,
                                'group_id' => $this->group->id,
                                'statement_file_type' => $statement_file_type,
                                'type'  => $type,
                                'date_from' => $from,
                                'date_to' => $to,
                                'created_on' => time(),
                                'created_by' => $this->user->id,
                                'active' => 1,
                                'action' => 2,
                                'email' => '',
                                'loan_id' => $loan_id,
                            );
                            if($this->statements_m->insert_statement_request($input)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Request received. You will receive a notification once the download is ready',
                                    'time' => time()
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Error occured. Try again later',
                                    'time' => time()
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find group member details',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Member to view profile not found in the group',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo encrypt_json_encode(array('response'=>$response));
    }
    function deposit_statement(){
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
                    $group_role_options = $this->group_roles_m->get_group_role_options($this->group->id);
                    if($this->group->enable_member_information_privacy){
                        $member_id = $this->member->id;
                    }else{
                        $member_id = $this->input->post('member_id')?:$this->member->id;
                    }
                    if($member_id){
                        if($group_member = $this->members_m->get_group_member($member_id)){
                            $from = $this->input->get('from')?strtotime($this->input->get('from')):strtotime('-12 months');
                            $to = $this->input->get('to')?strtotime($this->input->get('to')):strtotime('tomorrow');
                            $contribution_display_options = $this->contributions_m->get_group_contribution_display_options($this->group->id,TRUE);
                            $contribution_list = '0';
                            foreach ($contribution_display_options as $id=>$name) {
                                if($contribution_list){
                                    $contribution_list.=','.$id;
                                }else{
                                    $contribution_list=$id;
                                }
                            }
                            $total_member_deposit_amounts = $this->statements_m->get_group_member_total_paid_by_contribution_array($this->member->id,$this->group->id,$contribution_list);
                            $opening_balances = $this->statements_m->get_group_member_total_paid_by_contribution_array($this->member->id,$this->group->id,$contribution_list,$from);
                            $statement_transaction_names = $this->transactions->statement_transaction_names;
                            $group_currency = $this->group;
                            $account_options = $this->accounts_m->get_group_account_options(FALSE);
                            $posts = $this->statements_m->get_member_deposit_statement_array($this->member->id,$contribution_list,$from,$to);
                            $count = 0;
                            // print_r($posts); die;
                            $contribution_options = $this->contributions_m->get_group_contribution_options($this->group->id,FALSE);
                            if($posts){ 
                                $total_per_contribution = array();                                
                                foreach ($contribution_display_options as $contribution_id => $name):                                    
                                    $amount = isset($total_member_deposit_amounts[$contribution_id])?$total_member_deposit_amounts[$contribution_id]:0;
                                    $opening_balance = isset($opening_balances[$contribution_id])?$opening_balances[$contribution_id]:0; 
                                    $closing_balance = 0; 
                                    $closing_balance += $opening_balance;
                                    $contributions = isset($posts[$contribution_id])?$posts[$contribution_id]:array();
                                    foreach($contributions as $post):                                      
                                        
                                        if($post->transaction_type==25):
                                            $closing_balance -= $post->amount;
                                            if($post->member_id){
                                                $description = "Share transfer from ".$group_member_options[$post->member_id].' to '.$group_member_options[$post->member_id];
                                            }else{
                                                 if($post->transfer_to==1){
                                                        $description = "Contribution transfer to ".$contribution_options[$post->contribution_to_id];
                                                    }else if($post->transfer_to==2){
                                                        if($post->contribution_to_id){
                                                            $description = "Contribution transfer to ".$contribution_options[$post->contribution_to_id];
                                                        }else if($post->fine_category_to_id){
                                                            $description = "Contribution transfer to ".$fine_category_options[$post->fine_category_to_id];
                                                        }
                                                        if($post->fine_category_to_id){
                                                            $description.= '- For '.$fine_category_options[$post->fine_category_to_id];
                                                        }else{
                                                            $description.=  ' - For '.$contribution_options[$post->contribution_to_id];
                                                        }
                                                    }else if($post->transfer_to==3){
                                                        $description=  ' To loan '.$this->loans_m->get_loan_details($post->loan_to_id);
                                                    }else if($post->transfer_to==4){
                                                        $description = $group_member_options[$post->member_id].' to '.$group_member_options[$post->share_transfer_recipient_member_id];
                                                    }
                                            }
                                            $payable = $opening_balance;
                                            $paid = $post->amount;
                                            $closing_balance = $closing_balance;

                                            $opening_balance -= $post->amount;
                                        elseif($post->transaction_type==26):
                                            $closing_balance += $post->amount;
                                            if($post->member_id){
                                                $description = "Share transfer from ".(isset($group_member_options[$post->member_id])?$group_member_options[$post->member_id]:'').' to '.(isset($group_member_options[$post->member_id])?$group_member_options[$post->member_id]:'');
                                            }else{
                                                $description = 'Contribution transfer from ';
                                                if($post->transfer_to==1){
                                                    if($post->contribution_from_id=='loan'){
                                                        $description.= 'From loan -'.$this->loans_m->get_loan_details($post->loan_from_id);
                                                    }else{
                                                        $description.= "'".$contribution_options[$post->contribution_from_id];
                                                    }
                                                }else if($post->transfer_to==2){
                                                    if($post->contribution_to_id){
                                                        if($post->contribution_from_id=='loan'){
                                                            $description.= 'From loan -'.$this->loans_m->get_loan_details($post->loan_from_id);
                                                        }else{
                                                            $description.= "'".$contribution_options[$post->contribution_from_id];
                                                        }
                                                    }else if($post->fine_category_to_id){
                                                        if($post->contribution_from_id=='loan'){
                                                            $description.= 'From loan -'.$this->loans_m->get_loan_details($post->loan_from_id);
                                                        }else{
                                                            $description.= "'".$contribution_options[$post->contribution_from_id];
                                                        }
                                                    }
                                                    if($post->fine_category_to_id){
                                                        $description.= '- For '.$fine_category_options[$post->fine_category_to_id];
                                                    }else{
                                                        $description.= ' - For '.$contribution_options[$post->contribution_to_id];
                                                    }
                                                }else if($post->transfer_to==3){
                                                    $description.= ' To loan '.$this->loans_m->get_loan_details($post->loan_to_id);
                                                }else if($post->transfer_to==4){
                                                    $description.= $group_member_options[$post->member_id].' to '.$group_member_options[$post->share_transfer_recipient_member_id];
                                                }
                                            }
                                            $payable = $opening_balance;
                                            $paid = $post->amount;
                                            $closing_balance = $closing_balance;

                                            $opening_balance += $post->amount;
                                        else:
                                            if($post->transaction_type==21 || $post->transaction_type==22 || $post->transaction_type==23 || $post->transaction_type==24 ){
                                                $closing_balance -= $post->amount;
                                                $description = 'Contribution Refund';

                                                $payable = $opening_balance;
                                                $paid = $post->amount;
                                                $closing_balance = $closing_balance;

                                                $opening_balance -= $post->amount;
                                            }else if($post->transaction_type==30){
                                                $closing_balance -= $post->amount;

                                                $description = 'Contribution Transfer to Loan';
                                                $payable = $opening_balance;
                                                $paid = $post->amount;
                                                $closing_balance = $closing_balance;

                                                $opening_balance -= $post->amount;

                                            }else{
                                                $closing_balance += $post->amount;
                                                $description ='Contribution Payment';
                                                $payable = $opening_balance;
                                                $paid = $post->amount;
                                                $closing_balance = $closing_balance;
                                                $opening_balance += $post->amount;
                                            }
                                        endif;
                                        $statement_details[$contribution_id][] = array(
                                            'description' => $description,
                                            'date' => timestamp_to_date($post->transaction_date,TRUE),
                                            'payable' => $payable,
                                            'paid' => $paid,
                                            'balance' => $closing_balance,
                                        );
                                        
                                    endforeach;
                                    
                                endforeach;
                                $statement_header = array(
                                    'from'  =>  timestamp_to_date($from,TRUE),
                                    'to'=>timestamp_to_date($to,TRUE),
                                    'total_savings'=>array_sum($total_member_deposit_amounts)
                                );
                                foreach ($contribution_display_options as $contribution_id => $name):
                                    $opening_balance = isset($opening_balances[$contribution_id])?$opening_balances[$contribution_id]:0;
                                    $statement_body[] = array(
                                        'contribution_name' => isset($contribution_options[$contribution_id])?$contribution_options[$contribution_id]:array(),
                                        'opening_balance'=>$opening_balance,
                                        'totals' => isset($total_member_deposit_amounts[$contribution_id])?$total_member_deposit_amounts[$contribution_id]:array(),
                                        'statement_details' => isset($statement_details[$contribution_id])?$statement_details[$contribution_id]:array(),
                                    );
                                endforeach;
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Deposit statement',
                                    'time' => time(),
                                    'statement_header' => $statement_header,
                                    'statement_body' => $statement_body,
                                );

                            }else{
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Could not find deposit statement details',
                                    'time' => time(),
                                ); 
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find group member details',
                                'time' => time(),
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Member to view profile not found in the group',
                            'time' => time(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find member details',
                        'time' => time(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find group details',
                    'time' => time(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find user details',
                'time' => time(),
            );
        }
        echo json_encode(array('response'=>$response));
    }


}
