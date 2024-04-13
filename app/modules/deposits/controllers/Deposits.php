<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deposits extends Public_Controller{

	function __construct(){
        parent::__construct();
        // $this->output->enable_profiler(TRUE);
        $this->load->model('deposits_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->library('transactions');
        $this->load->library('contribution_invoices');
        $this->sms_template_default = $this->contribution_invoices->sms_template_default;
    }

    function index(){
    	echo 'here';
    }

    function record_contribution_payments(){
        $this->output->enable_profiler(TRUE);
    	$deposit_dates = array(
    		strtotime("10th Sept 2017"),
    	);
    	$member_ids = array(
    		'6730',
    	);
    	$contributions = array(
    		1068,
    	);
    	$amounts = array(
    		200,
    	);
    	$deposit_methods = array(
    		1
    	);

    	$account_ids = array(
    		'petty-134',
    	);
    	$descriptions = array(
    		''
    	);

    	$send_sms_notification = 0;
		$send_email_notification = 0;

    	$group_id = '1631';

    	$success = 0;
    	$fails = 0;
    	$errors = 0;

    	foreach ($deposit_dates as $key=>$deposit_date) {
    		if($deposit_date && $member_ids[$key] && $contributions[$key] && $amounts[$key] && $deposit_methods[$key] && $account_ids[$key]){
    			if($this->transactions->record_contribution_payment($group_id,$deposit_date,$member_ids[$key],$contributions[$key],$account_ids[$key],$deposit_methods[$key],$descriptions[$key],currency($amounts[$key]),$send_sms_notification,$send_email_notification)){
		            ++$success;
		        }else{
		            ++$fails;
		        }
    		} else{
    			++$errors;
    		}	
    	}

    	echo $success." successes <br/>";
    	echo $fails." fails <br/>";
    	echo $errors." errors <br/>";
    }

    function fix_date($id=0){
        $post = $this->deposits_m->get($id);
        if($post){
            $this->deposits_m->update($post->id,array(
                'deposit_date' => '1541518020',
                'created_on' => '1541518020',
            ));
            echo 'done';die;
        }
        echo 'no post';
    }

    function valid_time(){
        $date = '16-11-2020';
        if(valid_date($date)){
            echo "valid";
        }else{
            echo "invalid";
        }
    }

    function update_contribution_transfer($id=0,$group_id=0){
        $post = $this->deposits_m->get_group_contribution_transfer($id,$group_id);
        if($post){
            echo $post->amount;
            $update = array(
                'amount' => currency($post->amount),
                'modified_on' => time(),
            );
            $member_ids_array = array(
                $post->member_id,
                $post->member_to_id,
            );
            print_r($update);
            print_r($member_ids_array);
            if($this->deposits_m->update_contribution_transfer($id,$update)){
                $this->transactions->update_group_member_contribution_statement_balances(array($post->group_id),$member_ids_array);
                $this->transactions->update_group_member_contribution_statement_balances(array($post->group_id),$member_ids_array);
                echo 'done';
            }
        }
    }

    


}?>