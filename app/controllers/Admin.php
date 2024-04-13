<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Admin extends Admin_Controller{

	function __construct(){
		parent::__construct();
        $this->load->model('groups/groups_m');
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('activity_log/activity_log_m');
        $this->load->model('transaction_alerts/transaction_alerts_m');
        $this->load->model('sms/sms_m');
        $this->load->model('emails/emails_m');
        // $this->load->model('billing/billing_m');
        $this->load->model('invoices/invoices_m');
        $this->load->model('loan_invoices/loan_invoices_m');
	}


	function index(){
     
     
        $this->$data['account_number_count'] = 100;
        $this->$data['total_transactions_amount'] = 250;
        $this->data['total_deposit_transactions_amount'] =80000;
        $this->data['total_withdrawal_transactions_amount'] = 9000;
        $this->data['total_deposit_transactions_amounts_by_group_bank_account_number_array'] = 10000;
        $this->data['total_withdrawal_transactions_amounts_by_group_bank_account_number_array'] = 3000;
        $this->data['deposit_percentage'] = 700;
        $this->data['partners'] = 30;
        $this->data['withdrawal_percentage'] = 10000;
        $this->data['total_deposits_by_month_array'] = 2000;
        $this->data['total_withdrawals_by_month_array'] = 800;
        $this->template->title('Admin Dashboard')->build('admin/index',$this->data);
	}
    function index_old(){
       
        $this->data['account_number_count'] = 100;
        $this->data['total_transactions_amount'] = 250;
        $this->data['total_deposit_transactions_amount'] =80000;
        $this->data['total_withdrawal_transactions_amount'] = 9000;
        $this->data['total_deposit_transactions_amounts_by_group_bank_account_number_array'] = 10000;
        $this->data['total_withdrawal_transactions_amounts_by_group_bank_account_number_array'] = 3000;
        $this->data['deposit_percentage'] = 700;
        $this->data['withdrawal_percentage'] = 10000;
        $this->data['total_deposits_by_month_array'] = 2000;
        $this->data['total_withdrawals_by_month_array'] = 800;
        $this->template->set_layout('bank_dashboard.html')->title('Admin Dashboard')->build('bank/index',$this->data);
        }

	function _valid_identity(){
		$identity = $this->input->post('identity');
		if(!valid_email($identity))
		{
			if(!valid_phone($identity))
			{
				$this->form_validation->set_message('_valid_identity','Enter a valid Email or Phone Number');
                 return FALSE;
			}
			return TRUE;
		}
		else
		{
			return TRUE;
		}
	}

	public function login()
	{
		if($this->ion_auth->logged_in())
		{      
            redirect('admin');
        }

        $em = $this->input->get('l');

        //validate form input
        $this->form_validation->set_rules('identity', 'Email / Phone Number', 'required|callback__valid_identity');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $messages = '';

        if ($this->form_validation->run() == true)
        { //check to see if the user is logging in
            //check for "remember me"
     
            $remember = (bool) $this->input->post('remember');

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
            { 
            	$refer = $this->input->post('refer');

                $this->session->set_flashdata('message', $this->ion_auth->messages());
                //redirect them to their default chama
                $this->user = $this->ion_auth->get_user();
                $user_id = $this->user->id;

                if($refer)
                {
                    redirect($refer,'refresh');
                }
                else
                {
                    redirect('admin','refresh');
                }
            }
            else
            { //if the login was un-successful
                //redirect them back to the login page
                //print_r($this->ion_auth->errors());die;
                $this->session->set_flashdata('error', $this->ion_auth->errors()); 
                $messages = $this->ion_auth->errors();
                redirect('admin/login'); //use redirects instead of loading views for compatibility with MY_Controller libraries
            }
        }
        else
        {  //the user is not logging in so display the login page
            //set the flash data error message if there is one
            $this->data['messages'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['identity'] = array('name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
            $this->data['password'] = array('name' => 'password',
                'id' => 'password',
                'type' => 'password',
            );

        }
        
        $this->data['refer'] = $this->input->get_post('refer');

        $this->template->set_layout('login.html')->title('Admin Login')->build('admin/login',$this->data);
    }

    function forgot_password()
    {
        if($this->ion_auth->logged_in())
        {      
            redirect('admin');
        }

        $this->form_validation->set_rules('identity', 'Email / Phone Number', 'required|callback__valid_identity');
        if($this->form_validation->run())
        {
            $identity = $this->input->post('identity');
            $forgotten = $this->ion_auth->forgotten_password($identity);
            if ($forgotten) 
            { //if there were no errors
                $forgotten = (object)$forgotten;
                $this->session->set_flashdata('success', $this->ion_auth->messages());
                if(valid_email($identity))
                {
                    $this->session->set_flashdata('success','Recovery Email sent, please check your email address');
                    redirect('admin/login','refresh');
                }
                else
                {
                    redirect("admin/confirm_code", 'refresh'); //we should display a confirmation page here instead of the login page
                }
            }
            else 
            {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect("admin/forgot_password", 'refresh');
            }

        }
        else
        {
            $this->data['messages'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['identity'] = array('name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
        }

        $this->template->set_layout('login.html')->title('Forgot Password')->build('admin/forgot_password');
    }

    function confirm_code ()
    {
        if($this->ion_auth->logged_in())
        {      
            redirect('admin');
        }

        $this->form_validation->set_rules('identity', 'Email / Phone Number', 'required|callback__valid_identity');
        $this->form_validation->set_rules('confirmation_code', 'Confirmation Code', 'required|numeric');
        if($this->form_validation->run())
        {
            $this->load->library('ion_auth');
            $confirmation_code = $this->input->post('confirmation_code');
            $identity = $this->input->post('identity');
            $forgot_password_code = $this->ion_auth->confirm_code($identity,$confirmation_code);

            if($forgot_password_code)
            {
                redirect('admin/reset_password?code='.$forgot_password_code,'refresh');
            }
            else
            {
                $this->session->set_flashdata('error', $this->ion_auth->errors());
                redirect("admin/confirm_code", 'refresh');
            }

        }
        else
        {
            $this->data['messages'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['identity'] = array('name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
            $this->data['confirmation_code'] = array('name' => 'confirmation_code',
                'id' => 'confirmation_code',
                'type' => 'text',
                'value' => $this->form_validation->set_value('confirmation_code'),
            );
        }
        $this->template->set_layout('login.html')->title('Confirm Code')->build('admin/confirm_code');
    }

    public function reset_password()
    {

        if($this->ion_auth->logged_in())
        {      
            redirect('admin');
        }

        $code = $this->input->post_get('code');

        $profile = $this->ion_auth->forgotten_password_check($code);
        
       
       if($profile)
       {
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
           $this->form_validation->set_rules('conf_password', 'Confirm Password', 'required|matches[password]');
           if($this->form_validation->run())
           {
                $password = $this->input->post('password');
                $object = $this->ion_auth->forgotten_password_complete($code,$password);
                if($object)
                {
                    $object = (object)$object;
                    if($this->ion_auth->reset_password($object->identity,$password)){
                        $this->ion_auth->clear_forgotten_password_code($code);

                        if($this->ion_auth->login($object->identity, $object->new_password,1))
                        {
                            $this->session->set_flashdata('success', $this->ion_auth->messages());
                            redirect('admin');
                        }
                        else
                        {
                            $this->session->set_flashdata('error', $this->ion_auth->errors());
                            redirect('admin/login');
                        }
                           
                    }
                     else
                    {
                        $this->session->set_flashdata('error', $this->ion_auth->errors());
                        redirect('admin/login','refresh');
                    }
                    
                }
                else
                {
                    $this->session->set_flashdata('error', $this->ion_auth->errors());
                    redirect('admin/login','refresh');
                }

           }
           else{
                 $this->data['messages'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                $this->data['password'] = array('name' => 'password',
                    'id' => 'password',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('password'),
                );
                $this->data['conf_password'] = array('name' => 'conf_password',
                    'id' => 'conf_password',
                    'type' => 'text',
                    'value' => $this->form_validation->set_value('conf_password'),
                );
           }

           $this->template->set_layout('login.html')->title('Reset Password')->build('admin/reset_password');


       }
       else
       {
            $this->session->set_flashdata('error', 'sorry the code does not exist');
            redirect("admin/forgot_password", 'refresh');
            return false;
       }
    }

	public function logout()
	{
		//log the user out
        $this->ion_auth->logout();
        unset($_SESSION);
        $this->session->set_flashdata('success', 'You have Successfully Logged Out');
        //redirect them back to the page they came from
        redirect('admin/login','refresh');
	}

    

    
}

?>