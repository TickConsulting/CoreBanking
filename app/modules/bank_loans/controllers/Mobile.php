<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Mobile extends Mobile_Controller{
function __construct(){
		parent::__construct();
		$this->load->model('bank_loans_m');
	}

	function get_group_bank_loan_options(){
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
                    $base_where = array('is_fully_paid'=>0,'group_id'=>$this->group->id);
                    $ongoing_bank_loans = $this->bank_loans_m->get_group_bank_loans($base_where,$group_id);
                    $loans = array();
                    foreach ($ongoing_bank_loans as $ongoing_bank_loan){
                        $loans[] = array(
                            'id' => $ongoing_bank_loan->id,
                            'amount' => $ongoing_bank_loan->amount_loaned,
                            'description' => $ongoing_bank_loan->description,
                            'balance' => $ongoing_bank_loan->loan_balance,
                        );
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Group bank loans',
                        'time' => time(),
                        'loans' => $loans,
                    );
                }else{
                    $response = array(
                        'status' => 6,
                        'message' => 'Could not find member details',
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

}?>
