<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Group extends Group_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('accounts/accounts_m');
		$this->load->model('deposits/deposits_m');
		$this->load->model('withdrawals/withdrawals_m');
		$this->load->model('statements/statements_m');
	}


	function uri_not_found(){
        $this->template->title('Page not found')->build('group/uri_not_found');
	}

	function index(){
		$this->benchmark->mark('code_start');
		$data['total_group_fines'] = $this->deposits_m->get_group_total_fines();
		$data['total_group_contributions'] = $this->deposits_m->get_group_total_contributions();
		$data['total_group_contribution_refunds'] = $this->withdrawals_m->get_group_total_contribution_refunds();
        $data['total_group_contribution_transfers_from_loan_to_contribution'] = $this->statements_m->get_group_total_contribution_transfers_from_loan_to_contribution($this->member->id);
		$data['total_group_contributions_transfers_to_fines'] = $this->statements_m->get_group_total_contribution_transfers_to_fines();
		$data['total_group_contributions_by_month_array'] = $this->deposits_m->get_group_total_contributions_by_month_array();
		$data['total_group_fines_by_month_array'] = $this->deposits_m->get_group_total_fines_by_month_array();
		$data['total_group_miscellaneous_payments_by_month_array'] = $this->deposits_m->get_group_total_miscellaneous_payments_by_month_array();
		$data['total_group_income_by_month_array'] = $this->deposits_m->get_group_total_income_by_month_array();
		$data['total_group_expenses_by_month_array'] = $this->withdrawals_m->get_group_total_expenses_by_month_array();
	
		$data['total_group_expenses'] = $this->withdrawals_m->get_group_total_expenses();
		$data['total_cash_at_bank'] = $this->accounts_m->get_group_total_cash_at_bank();
		$data['total_actual_balance'] = $this->accounts_m->get_group_total_actual_bank_balance();
		$data['total_cash_at_hand'] = $this->accounts_m->get_group_total_cash_at_hand();
		$data['bank_accounts'] = $this->bank_accounts_m->get_group_bank_accounts();
		$data['membership_numbers'] = $this->members_m->get_membership_number();
		$data['bank_account'] = $this->bank_accounts_m->get_group_verified_partner_bank_account();
		$data['total_group_contributions_transfers_to_loans'] = $this->statements_m->get_group_total_contribution_transfers_to_loans();
        $this->template->set_layout('dashboard.html')->title(translate('Group Dashboard'))->build('group/index',$data);
	}

	function activate(){
		$activation_code = $this->input->get('activation_code');
		$this->form_validation->set_rules('activation_code', 'Activation Code', 'required|numeric');
		if($this->form_validation->run()||$activation_code){
			if(($this->group->activation_code==$this->input->post('activation_code'))||($this->group->activation_code==$activation_code)){
				$data = array(
					'lock_access'=>0,
					'modified_on'=>time(),
					'modified_by'=>$this->user->id
				);
				if($result = $this->groups_m->update($this->group->id,$data)){
					$this->session->set_flashdata('success','Group activated successfully');
				}else{
					$this->session->set_flashdata('error','Something went wrong during group activation');
				}
				if($this->application_settings->enforce_group_setup_tasks){
					redirect('group/setup_tasks/accounts');
				}else{
					redirect('group');
				}
			}else{
				$this->session->set_flashdata('error','The activation code you entered is does not match the code sent to you');
				redirect($this->agent->referrer());
			}
		}else{
			//do nothing
			if($this->group->lock_access==0){
				if($this->application_settings->enforce_group_setup_tasks){
					redirect('group/setup_tasks/accounts');
				}else{
					redirect('group');
				}
			}
		}
		$this->template->set_layout('authentication.html')->title('Activate Group Account')->build('group/activate');
	}

	function resend_activation_code(){
		$channel = $this->input->get('channel');
		if($this->messaging->send_activation_code($this->user,$this->group->activation_code,$this->group->slug,$this->group->name)){
			if($channel=='email'||$this->user->email){
				$this->session->set_flashdata('success','Group activation email sent to <strong>'.$this->user->email.'</strong>, please check your email inbox, if you don\'t find it there check your spam or junk folder.');
				if($this->agent->referrer()){
					redirect($this->agent->referrer(),'refresh');
				}else{
					redirect('group/activate','refresh');
				}
			}else if($channel=='sms'||$this->user->phone){
				$this->session->set_flashdata('success','Group activation SMS sent to <strong>'.$this->user->phone.'</strong>');
				if($this->agent->referrer()){
					redirect($this->agent->referrer(),'refresh');
				}else{
					redirect('group/activate','refresh');
				}
			}else{
				$this->session->set_flashdata('error','Something went wrong while sending the activation code');
			}
		}else{
			//$this->session->set_flashdata('error','Something went wrong while sending the activation code');
		}
		if($this->agent->referrer()){
			redirect($this->agent->referrer());
		}else{
			redirect('group/activate?channel='.$channel);
		}
	}

	function change_email_address(){
		$channel = $this->input->get('channel');
		$change_email_address_rules = array(
			array(
                'field' =>  'email',
                'label' =>  'Email Address',
                'rules' =>  'required|trim|valid_email|callback__unique_email_identity',
            ),
		);
		if(valid_email($this->user->email)){
			$this->form_validation->set_rules($change_email_address_rules);
			if($this->form_validation->run()){
				$data = array(
					'email' => $this->input->post('email'),
                    'modified_on'   => time(),
                    'modified_by'   => $this->ion_auth->get_user()->id,
                );
				$result = $this->ion_auth->update($this->user->id,$data);
				if($result){
					$this->user = $this->ion_auth->get_user($this->user->id);
					if($this->user){
						$this->resend_activation_code();
					}else{
						$this->session->set_flashdata('error','User not found');
					}
				}else{
					$this->session->set_flashdata('error','User email could not be changed');
				}
			}else{
				//do nothing validation failed
			}
			$this->template->set_layout('authentication.html')->title('Change Email Address')->build('group/change_email_address');
		}else{
			redirect('group/activate?channel='.$channel);
		}
	}

	function _unique_email_identity(){
		$email = $this->input->post('email');
		if($this->user->email==$email){
			$this->form_validation->set_message('_unique_email_identity','Enter another email address, you have entered the current email address');
            return FALSE;
		}else{
			if($this->ion_auth->get_user_by_email($email)){
				$this->form_validation->set_message('_unique_email_identity','The email you entered is already registered, please enter another one');
            	return FALSE;
			}else{
            	return TRUE;
			}
		}
	}

	function change_phone_number(){
		$channel = $this->input->get('channel');
		$change_phone_number_rules = array(
			array(
                'field' =>  'phone',
                'label' =>  'Phone Number',
                'rules' =>  'required|trim|callback__unique_phone_number_identity',
            ),
		);
		if(valid_phone($this->user->phone)){
			$this->form_validation->set_rules($change_phone_number_rules);
			if($this->form_validation->run()){
				$data = array(
					'phone' => valid_phone($this->input->post('phone')),
                    'modified_on'   => time(),
                    'modified_by'   => $this->ion_auth->get_user()->id,
                );
				$result = $this->ion_auth->update($this->user->id,$data);
				if($result){
					$this->user = $this->ion_auth->get_user($this->user->id);
					if($this->user){
						$this->resend_activation_code();
					}else{
						$this->session->set_flashdata('error','User not found');
					}
				}else{
					$this->session->set_flashdata('error','User phone number could not be changed');
				}
			}else{
				//do nothing validation failed
			}
			$this->template->set_layout('authentication.html')->title('Change Phone Number')->build('group/change_phone_number');
		}else{
			redirect('group/activate?channel='.$channel);
		}
	}

	function _unique_phone_number_identity(){
		if($phone = valid_phone($this->input->post('phone'))){
			if($this->user->phone==$phone){
				$this->form_validation->set_message('_unique_phone_number_identity','Enter another phone number, you have entered the current phone number');
	            return FALSE;
			}else{
				if($user = $this->ion_auth->get_user_by_phone($phone)){
					$this->form_validation->set_message('_unique_phone_number_identity','The phone number you entered is already registered, please enter another one');
	            	return FALSE;
				}else{
	            	return TRUE;
				}
			}
		}else{
			$this->form_validation->set_message('_unique_phone_number_identity','The phone number you entered is invalid');
	        return FALSE;
		}
	}

}