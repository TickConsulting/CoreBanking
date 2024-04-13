<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{

	function __construct(){
        parent::__construct();
        $this->load->model('transaction_statements_m');
    }

    
    public function index(){
        $data = array();
        $this->template->title('Group Transaction Statement')->build('group/index',$data);
    }

    public function ajax_set_cut_off_date(){
    	$validation_rules = array(
    		array(
                'field' =>  'cut_off_date',
                'label' =>  'Back-dating cut off date',
                'rules' =>  'trim|required',
            ),
            array(
                'field' =>  'group_start_date',
                'label' =>  'Group start date',
                'rules' =>  'trim|required',
            ),
    	);
    	$this->form_validation->set_rules($validation_rules);
    	if($this->form_validation->run()){
    		$this->transaction_statements_m->disable_group_cut_off_date();
            $cut_off_date = strtotime($this->input->post('cut_off_date'));
            $group_start_date = strtotime($this->input->post('group_start_date'));
    		$input = array(
                'group_start_date' => $group_start_date,
    			'cut_off_date' => $cut_off_date,
    			'group_id' => $this->group->id,
    			'created_on' => time(),
    			'created_by' => $this->user->id,
    			'active' => 1,
    		);
            if($this->transactions->update_back_dating_records_cut_off_date($this->group->id,$cut_off_date)){
        		if($this->transaction_statements_m->insert_group_cut_off_date($input)){
        			$group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        			if($group_cut_off_date){
        				echo json_encode($group_cut_off_date);
        			}else{
        				echo "Could not find group cut off date";
        			}
        		}else{
        			echo "Could not add cut off date";
        		}
            }else{
                echo "Could not update new cut off date for the records";
            }
    	}else{
    		echo validation_errors();
    	}
    }

    public function ajax_get_group_cut_off_date(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        if($group_cut_off_date){
            echo json_encode($group_cut_off_date);
        }else{
            echo "No group cut off date set";
        }
    }

    function check_for_deleted_members_transaction_statement_entries(){
        $transaction_statements = $this->transaction_statements_m->get_group_transaction_statements();
        $loan_options = $this->loans_m->get_group_loan_options();

        $count = 0;
        $accounted_for_count = 0;
        $amount = 0;
        foreach($transaction_statements as $transaction_statement):
            if($transaction_statement->member_id){
                if(array_key_exists($transaction_statement->member_id,$this->group_member_options)){

                }else{
                    $count++;
                    //echo number_to_currency($transaction_statement->amount)."<br/>";
                    if(in_array($transaction_statement->transaction_type,$this->transactions->contribution_payment_transaction_types)){
                        $amount += $transaction_statement->amount;
                        $accounted_for_count++;
                        echo number_to_currency($transaction_statement->amount).'<br/>';

                        // $post = $this->deposits_m->get_group_deposit($transaction_statement->deposit_id);
                        // if($post){
                        //     if($this->transactions->void_group_deposit($post->id,$post,TRUE,$this->group->id,$this->user)){
                        //         echo "Deposit voided.<br/>";
                        //     }else{
                        //         echo "Could not void deposit.<br/>";
                        //     }  
                        // }else{
                        //     echo "Could not find deposit.<br/>";
                        // }
                        
                    }
                    if(in_array($transaction_statement->transaction_type,$this->transactions->statement_contribution_refund_withdrawal_transaction_types)){
                        echo number_to_currency($transaction_statement->amount).'<br/>';
                        $amount -= $transaction_statement->amount;
                        $accounted_for_count++;
                    }

                }
            }
        endforeach;
        foreach($transaction_statements as $transaction_statement):
            if($transaction_statement->loan_id){
                if(array_key_exists($transaction_statement->loan_id,$loan_options)){

                }else{
                    $count++;

                    if(in_array($transaction_statement->transaction_type,$this->transactions->loan_repayment_transaction_types)){
                        $amount += $transaction_statement->amount;
                        $accounted_for_count++;
                        $post = $this->deposits_m->get_group_deposit($transaction_statement->deposit_id);
                        if($post){
                            if($this->transactions->void_group_deposit($post->id,$post,TRUE,$this->group->id,$this->user)){
                                echo "Deposit voided.<br/>";
                            }else{
                                echo number_to_currency($transaction_statement->amount)."<br/>";
                                echo "Could not void deposit.<br/>";
                            }  
                        }else{
                            echo "Could not find deposit.<br/>";
                        }
                    }else{
                        
                    }
                    //$amount += $transaction_statement->amount;
                    //echo number_to_currency($transaction_statement->amount)."<br/>";

                }
            }
        endforeach;
        echo $accounted_for_count.'/'.$count."<br/>";
        echo number_to_currency($amount);
    }

    function check_loan_repayments(){
        $deposits = $this->deposits_m->get_group_active_loan_repayment_deposits($this->group->id);

        $deposits_array = array();

        foreach($deposits as $deposit):
            $deposits_array[$deposit->id] = $deposit->id;
        endforeach;

        echo count($deposits)."<br/>";
        $loan_repayments = $this->loan_repayments_m->get_group_active_loan_repayments($this->group->id);
        // $loan_repayments_array = array();
        // foreach($loan_repayments as $loan_repayment):
        //     $loan_repayments_array[$loan_repayment->id] = $loan_repayment->id;
        // endforeach;
        // foreach($deposits_array as $loan_repayment_id):
        //     unset($loan_repayments_array[$loan_repayment_id]);
        // endforeach;


        echo count($loan_repayments)."<br/>";
        $transaction_statements = $this->transaction_statements_m->get_group_active_loan_repayment_deposit_transaction_statements($this->group->id);
        $transaction_statements_array = array();
        foreach($transaction_statements as $transaction_statement):
            $transaction_statements_array[$transaction_statement->deposit_id] = $transaction_statement->deposit_id;
        endforeach;
        echo count($transaction_statements)."<br/>";

        foreach($deposits_array as $deposit_id):
            unset($transaction_statements_array[$deposit_id]);
        endforeach;


        //print_r($transaction_statements_array);

        foreach($transaction_statements_array as $deposit_id){
            $deposit = $this->deposits_m->get($deposit_id);
            echo "Loan ID : #".$deposit->loan_id." | Amount : ".number_to_currency($deposit->amount)."<br/>";   
            // $post = $this->deposits_m->get_group_deposit($deposit->id);
            // if($post){
            //     if($this->transactions->void_group_deposit($post->id,$post,TRUE,$this->group->id,$this->user)){
            //         echo "Deposit voided.<br/>";
            //     }else{
            //         echo number_to_currency($transaction_statement->amount)."<br/>";
            //         echo "Could not void deposit.<br/>";
            //     }  
            // }else{
            //     echo "Could not find deposit.<br/>";
            // }     

        }

        die;
    }

    function check_all_transaction_statements(){
        $transaction_statements = $this->transaction_statements_m->get_group_transaction_statements($this->group->id);
        echo count($transaction_statements)."<br/>";
        $deposits_array = array();
        $deposits = $this->deposits_m->get_group_deposits($this->group->id);
        foreach($deposits as $deposit):
            $deposits_array[$deposit->id] = $deposit;
        endforeach;
        echo count($deposits)."<br/>";
        $withdrawals = $this->withdrawals_m->get_group_withdrawals($this->group->id);
        foreach($withdrawals as $withdrawal):
            $withdrawals_array[$withdrawal->id] = $withdrawal;
        endforeach;
        echo count($withdrawals);
        $balance = 0;
        foreach($transaction_statements as $transaction_statement):
            if($transaction_statement->deposit_id){
                if(isset($deposits_array[$transaction_statement->deposit_id])){
                    if($deposits_array[$transaction_statement->deposit_id]->amount == $transaction_statement->amount){
                        $balance += $transaction_statement->amount;
                    }else{
                        echo "Am in";
                    }
                }else{
                    echo "We have a major problem";
                }
            }else if($transaction_statement->withdrawal_id){
                if(isset($withdrawals_array[$transaction_statement->withdrawal_id])){
                    if($withdrawals_array[$transaction_statement->withdrawal_id]->amount == $transaction_statement->amount){
                        $balance -= $transaction_statement->amount;
                    }else{
                        echo "Am in";
                    }
                }else{
                    echo "We have a major problem";
                }
            }else{
                echo "We have a problem Houston";
            }

        endforeach;
        echo "<br/>".number_to_currency($balance);
    }


}