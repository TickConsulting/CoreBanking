<?php if(!defined('BASEPATH')) exit('You are not allowed to viewe this script');
class Partner extends Partner_Controller{

	protected $group_status_options = array(
		1 => "On trial",
		2 => "Paying",
		3 => "Suspended",
	);

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Group_members');
		$this->load->library('contribution_invoices');


	}

	function index(){
		
	}

	function search($id = 0){
		if($id){
			$group = $this->groups_m->get($id);
			if($group){
				$this->data['group_id'] = $group->id;
				$this->data['group'] = $group;
			}else{
				redirect('admin/groups/search');
			}
		}else{
			$this->data['group_id'] = "";
		}
		$this->template->title('Group Search')->build('partner/search',$this->data);
	}

    function ajax_search_options(){
        $this->groups_m->get_search_options();

    }

	function _is_unique_slug()
	{
		$slug = $this->input->post('slug');
		$id = $this->input->post('id');

		if($this->groups_m->get_by_slug($slug,$id))
		{
			$this->form_validation->set_message('_is_unique_slug','The slug already exists. Ensure it is unique');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	function _unique_check_account(){
		$account_number = $this->input->post('account_number');
		$id = $this->input->post('id');

		if($this->groups_m->count_groups_by_account($account_number,$id))
		{
			$this->form_validation->set_message('_unique_check_account','The account number already exists with another group');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	function listing(){	
		$group_ids_arr=array();
		$name = $this->input->get('name');
		$owner = $this->input->get('owner');
		$phone = $this->input->get('phone');
		$email = $this->input->get('email');
		$status = $this->input->get('status');
		$bank_ids = $this->input->get('group_bank_options');
		$total_rows = $this->groups_m->count_all($name,$owner,$phone,$email,$status,$group_ids_arr);
        $pagination = create_pagination('admin/groups/listing/pages',$total_rows,50,5);
        $posts = $this->groups_m->limit($pagination['limit'])->get_all($name,$owner,$phone,$email,$status,$group_ids_arr);
        $this->data['group_bank_options'] = $this->banks_m->get_admin_bank_options();
        $this->data['group_status_options'] = $this->group_status_options;
		$this->data['posts'] = $posts;
		$this->data['pagination'] = $pagination;
		$this->data['billing_packages'] = $this->billing_m->billing_packages_options();
		$this->data['billing_cycles'] = $this->billing_settings->billing_cycle;
		$this->template->title('Group List')->build('partner/listing',$this->data);



		if($this->input->get('generate_excel') == 1){
			foreach ($posts as $post) {
				$post->user = $this->ion_auth->get_user($post->owner);
			}
			$this->data['posts'] = $posts;
			$this->data['application_name'] = $this->application_settings->application_name;
			$this->data['generate_all_records'] = 0;
			$json_file = json_encode($this->data);
		    $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/group_listing',$this->data['application_name'].' Groups Listing Report');
		    print_r($response);die;
		}
		if($this->input->get('generate_excel') == 2){
			$posts = $this->groups_m->get_all($name,$owner,$phone,$email,$status,$group_ids_arr);
			foreach ($posts as $post) {
				$post->user = $this->ion_auth->get_user($post->owner);
			}
			$this->data['posts'] = $posts;
			$this->data['application_name'] = $this->application_settings->application_name;
			$this->data['generate_all_records'] = 1;
			$json_file = json_encode($this->data);
		    $response = $this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/reports/group_listing',$this->data.' Groups Listing Report');
		    print_r($response);die;
		}
	}

	function edit($id=0){
		$id OR redirect('admin/groups/listing');
		$post = new StdClass();
		$post = $this->groups_m->get($id);
		if(!$post){
			$this->session->set_flashdata('error','The group does not exist');
			return FALSE;
		}
		$this->form_validation->set_rules($this->validation_rules);
		if($this->form_validation->run()){
			$update = $this->groups_m->update($post->id,
				array(
						'name'			=>	$this->input->post('name'),
						'slug'			=>	strtolower($this->input->post('slug')),
						'size'			=>	$this->input->post('size'),
						'country_id'	=>	$this->input->post('country_id'),
						'currency_id'	=>	$this->input->post('currency_id'),
						'trial_days'	=>	$this->input->post('trial_days')?:0,
						'billing_package_id'=>	$this->input->post('billing_package_id'),
						'lock_access'	=>	$this->input->post('lock_access'),
						'activation_code'=>	$this->input->post('activation_code'),
						'billing_date'  =>  strtotime($this->input->post('billing_date')),
						'billing_cycle' => 	$this->input->post('billing_cycle'),
						'status'		=>	$this->input->post('status'),
						'sms_balance'	=>	$this->input->post('sms_balance'),
						'account_number'	=>	$this->input->post('account_number'),
						"online_banking_enabled" => $this->input->post('online_banking_enabled'),
						"notify_members_on_withdrawals" => $this->input->post('notify_members_on_withdrawals')?1:0,
						'modified_by'	=>	$this->user->id,
						'modified_on'	=>	time(),
						'active'		=>	1,
						'is_sacco'      => $this->input->post('is_sacco')
					));
			if($update)
			{
				$this->group_members->set_active_group_size($post->id);
				$this->session->set_flashdata('success',$post->name.' successfully updated');
			}
			else
			{
				$this->session->set_flashdata('error','Unable to update '.$post->name);
			}
			redirect('admin/groups/listing','refresh');
		}
		else
		{
			foreach (array_keys($this->validation_rules) as $field)
	        {
	             if (isset($_POST[$field]))
	            {
	                $post->$field = $this->form_validation->$field;
	            }
	        }
		}

		$this->data['post'] = $post;
		$this->data['group_status']	=	$this->groups_m->get_group_status();
        $this->data['country_options'] = $this->countries_m->get_country_options();
        $this->data['billing_cycles'] = $this->billing_settings->billing_cycle;
        $this->data['currency_options'] = $this->currency_options;
        $this->data['billing_packages'] = $this->billing_m->billing_packages_options();
		$this->data['id'] = $id;

		$this->template->title('Edit '.$post->name)->build('admin/form',$this->data);

	}

	function view($id=0){
		$id OR redirect('admin/groups/listing');

		$post = new stdClass();

		$post = $this->groups_m->get($id);

		if(!$post)
		{
			$this->session->set_flashdata('error','The group does not exist');
			return FALSE;
		}
		$members= $this->members_m->get_group_members_by_id_for_admin($post->id);
		$member_options = array();
		if($members){
			foreach ($members as $key => $value) {
				$member_options[$value->id] = $value->first_name.' '.$value->last_name;
			}
		}

		$this->data['member_options'] = $member_options;
		$this->data['members'] = $members;
		$this->data['post'] = $post;
		$this->data['country'] = $this->countries_m->get($post->country_id);
		$this->data['currency'] =  $this->currency_options[$post->currency_id?:$this->default_country->id];

		$this->data['bank_accounts'] = $this->bank_accounts_m->get_group_bank_accounts($post->id);
		$this->data['sacco_accounts'] = $this->sacco_accounts_m->get_group_sacco_accounts($post->id);
		$this->data['petty_cash_accounts'] = $this->petty_cash_accounts_m->get_all($post->id);
		$this->data['mobile_money_accounts'] = $this->mobile_money_accounts_m->get_group_mobile_money_accounts($post->id);

		$this->data['contributions'] = $this->contributions_m->get_group_contributions($post->id);
		$this->data['regular_contribution_settings_array'] = $this->contributions_m->get_group_regular_contribution_settings_array($post->id);
        $this->data['one_time_contribution_settings_array'] = $this->contributions_m->get_group_one_time_contribution_settings_array($post->id);
        $this->data['selected_group_members_array'] = $this->contributions_m->get_all_contribution_member_pairings_array($post->id);
        $this->data['contribution_fine_settings_array'] = $this->contributions_m->get_all_contribution_fine_settings_array($post->id);

        $this->data['invoice_days'] = $this->contribution_invoices->invoice_days;
        $this->data['month_days'] = $this->contribution_invoices->month_days;
        $this->data['week_days'] = $this->contribution_invoices->week_days;
        $this->data['days_of_the_month'] = $this->contribution_invoices->days_of_the_month;
        $this->data['every_two_week_days'] = $this->contribution_invoices->every_two_week_days;
        $this->data['months'] = $this->contribution_invoices->months;
        $this->data['starting_months'] = $this->contribution_invoices->starting_months;
        $this->data['week_numbers'] = $this->contribution_invoices->week_numbers;
        $this->data['contribution_frequency_options'] = $this->contribution_invoices->contribution_frequency_options;
        $this->data['contribution_type_options'] = $this->contribution_invoices->contribution_type_options;
        $this->data['fine_types'] = $this->contribution_invoices->fine_types;
        $this->data['fine_chargeable_on_options'] = $this->contribution_invoices->fine_chargeable_on_options['Frequently used options']+$this->contribution_invoices->fine_chargeable_on_options['Other options'];
        $this->data['fine_frequency_options'] = $this->contribution_invoices->fine_frequency_options;
        $this->data['fine_mode_options'] = $this->contribution_invoices->fine_mode_options;
        $this->data['fine_limit_options'] = $this->contribution_invoices->fine_limit_options;
        $this->data['percentage_fine_on_options'] = $this->contribution_invoices->percentage_fine_on_options;
        $this->data['billing_cycles'] = $this->billing_settings->billing_cycle;
        $this->data['arrears'] = $this->billing_m->get_group_account_arrears($post->id);
        $this->data['group_currency'] = $this->groups_m->get_this_group_currency($post->id);
        $this->data['invoices'] = $this->billing_m->get_all_active_group_invoices($post->id);
		$this->template->title($post->name)->build('partner/view',$this->data);
	}

	function ajax_view($id=0){
		$post = new stdClass();
		$post = $this->groups_m->get($id);
		if($post){
			if(!$post){
				$this->session->set_flashdata('error','The group does not exist');
				return FALSE;
			}
			$members= $this->members_m->get_group_members_by_id_for_admin($post->id);
			$member_options = array();
			if($members){
				foreach ($members as $key => $value) {
					$member_options[$value->id] = $value->first_name.' '.$value->last_name;
				}
			}

			$member_options = $member_options;
			$members = $members;
			$post = $post;
			$country = $this->countries_m->get($post->country_id);
			$currency =  $this->currency_options[$post->currency_id?:$this->default_country->id];

			$bank_accounts = $this->bank_accounts_m->get_group_bank_accounts($post->id);
			$sacco_accounts = $this->sacco_accounts_m->get_group_sacco_accounts($post->id);
			$petty_cash_accounts = $this->petty_cash_accounts_m->get_all($post->id);
			$mobile_money_accounts = $this->mobile_money_accounts_m->get_group_mobile_money_accounts($post->id);

			$contributions = $this->contributions_m->get_group_contributions($post->id);
			$regular_contribution_settings_array = $this->contributions_m->get_group_regular_contribution_settings_array($post->id);
	        $one_time_contribution_settings_array = $this->contributions_m->get_group_one_time_contribution_settings_array($post->id);
	        $selected_group_members_array = $this->contributions_m->get_all_contribution_member_pairings_array($post->id);
	        $contribution_fine_settings_array = $this->contributions_m->get_all_contribution_fine_settings_array($post->id);

	        $invoice_days = $this->contribution_invoices->invoice_days;
	        $month_days = $this->contribution_invoices->month_days;
	        $week_days = $this->contribution_invoices->week_days;
	        $days_of_the_month = $this->contribution_invoices->days_of_the_month;
	        $every_two_week_days = $this->contribution_invoices->every_two_week_days;
	        $months = $this->contribution_invoices->months;
	        $starting_months = $this->contribution_invoices->starting_months;
	        $week_numbers = $this->contribution_invoices->week_numbers;
	        $contribution_frequency_options = $this->contribution_invoices->contribution_frequency_options;
	        $contribution_type_options = $this->contribution_invoices->contribution_type_options;
	        $fine_types = $this->contribution_invoices->fine_types;
	        $fine_chargeable_on_options = $this->contribution_invoices->fine_chargeable_on_options['Frequently used options']+$this->contribution_invoices->fine_chargeable_on_options['Other options'];
	        $fine_frequency_options = $this->contribution_invoices->fine_frequency_options;
	        $fine_mode_options = $this->contribution_invoices->fine_mode_options;
	        $fine_limit_options = $this->contribution_invoices->fine_limit_options;
	        $percentage_fine_on_options = $this->contribution_invoices->percentage_fine_on_options;
	        $billing_cycles = $this->billing_settings->billing_cycle;
	        $arrears = $this->billing_m->get_group_account_arrears($post->id);
	        $group_currency = $this->groups_m->get_this_group_currency($post->id);
	        $invoices = $this->billing_m->get_all_active_group_invoices($post->id);
	        $active_size=$post->active_size?:1;
	        echo '
	        <div>
	        	<strong> Quick Actions: </strong>
		        <a href="'.site_url('admin/groups/edit/'.$post->id).'" class="btn btn-xs default">
	                <i class="fa fa-edit"></i> Edit &nbsp;&nbsp;
	            </a>';

	            echo '
            	<a href="'.site_url('admin/groups/reset_group/'.$post->id).'" class="btn btn-xs btn-warning prompt_confirmation_message_link" data-title="Enter the reset code to reset the group data." >
	                <i class="fa fa-trash"></i> Reset Group Data &nbsp;&nbsp;
	            </a>';

            	echo '
            	<a href="'.site_url('admin/groups/delete/'.$post->id).'" class="btn btn-xs red prompt_confirmation_message_link" data-title="Enter the delete code to delete the group and its data permanently." >
	                <i class="fa fa-trash"></i> Delete &nbsp;&nbsp;
	            </a>';
                
	        	echo '
	            <a target="_blank" href="'.$this->application_settings->protocol.$post->slug.'.'.$this->application_settings->url.'" class="btn btn-xs btn-default">
	                <i class="fa fa-user-secret"></i> Login as Admin &nbsp;&nbsp;
	            </a>
	        </div>
	        <hr/>
	        ';
	        echo '
	        	<div class="mt-element-list">
	                <div class="mt-list-head list-todo red">
	                    <div class="list-head-title-container">
	                        <div class="list-head-count">
	                            <div class="list-head-count-item">
	                                <i class="fa fa-users"></i> Group Membership Size(Members Registered): '.$post->size.' ('.$active_size.')'.' </div>
	                            <div class="list-head-count-item">
	                                <i class="fa fa-hand-paper-o"></i> Contributions: '.count($contributions).'</div>
	                            <div class="list-head-count-item">
	                                <i class="fa fa-institution"></i> Bank Accounts: '.count($bank_accounts).'</div>
	                        </div>
	                    </div>
	                </div>
	                <div class="mt-list-container list-todo">
	                    <div class="list-todo-line"></div>
	                    <ul>
	                        <li class="mt-list-item">
	                            <div class="list-todo-icon bg-white">
	                                <i class="fa fa-info"></i>
	                            </div>
	                            <div class="list-todo-item dark">
	                                <a class="list-toggle-container" data-toggle="collapse" href="#task-1" aria-expanded="false">
	                                    <div class="list-toggle done uppercase">
	                                        <div class="list-toggle-title bold">Group Information</div>
	                                        <div class="badge badge-default pull-right bold"></div>
	                                    </div>
	                                </a>
	                                <div class="task-list panel-collapse collapse in" id="task-1">
	                                    <ul>
	                                        <li class="task-list-item done">
	                                            <div class="task-icon">
	                                                <a href="javascript:;">
	                                                    <i class="fa fa-users"></i>
	                                                </a>
	                                            </div>
	                                            <div class="task-content">
	                                                <h4 class="uppercase bold">
	                                                    <a href="javascript:;">'.$post->name.'</a>
	                                                </h4>
	                                                <ul class="">
	                                                    ';
	                                                        $owner = $this->ion_auth->get_user($post->owner);
	                                                        $created_by = $this->ion_auth->get_user($post->created_by);
	                                                    echo '
	                                                    <li><strong>Group Number: </strong>'.$post->account_number.'</li>
	                                                    <li><strong>Signed Up On: </strong>'.timestamp_to_date($post->created_on).'</li>
	                                                    <li><strong>Group Registered By: </strong>'.$owner->first_name.' '.$owner->last_name.'</li>
	                                                    <li><strong>E-mail: </strong>'.$owner->email.'</li>
	                                                    <li><strong>Phone: </strong>'.$owner->phone.'</li>   
	                                                    <li><strong>Created By: </strong>'.$created_by->first_name.' '.$created_by->last_name.'</li>
	                                                    <li><strong>Group E-mail: </strong>'.$post->email?:$owner->email?:$created_by->email.'</li>
	                                                    <li><strong>Group Phone: </strong>'.$post->phone.'</li>        
	                                                </ul>
	                                            </div>
	                                        </li>
	                                        <li class="task-list-item">
	                                            <div class="task-icon">
	                                                <a href="javascript:;">
	                                                    <i class="fa fa-money"></i>
	                                                </a>
	                                            </div>
	                                            <div class="task-content">
	                                                <h4 class="uppercase bold">
	                                                    <a href="javascript:;">Group Billing Information</a>
	                                                </h4>
	                                                Subscription Status: 
	                                                ';
	                                                    $status = $post->status;
	                                                    if($status == 1)
	                                                    {
	                                                        echo '<span class="label label-xs label-success">Subscribed</span>';
	                                                        if($arrears>0){
	                                                            echo '&nbsp;<span class="label label-xs label-warning">In Arrears</span>';
	                                                            echo '&nbsp;'.$this->default_country->currency_code.' '.number_to_currency($arrears).' In Arrears ';
	                                                        }else{
	                                                            echo '&nbsp;<span class="label label-xs label-info">Subscription Payments Upto Date</span>';
	                                                        }
	                                                        if(empty($invoices)){
	                                                echo '
	                                                    <p>
	                                                        <div class="alert alert-info">
	                                                            <strong>Info!</strong> No billing invoices to display 
	                                                        </div>
	                                                    </p>';
	                                                        }else{
	                                                    echo '
	                                                    <hr/>
	                                                    <p>
	                                                        <table class="table table-striped table-bordered table-advance table-condensed table-hover">
	                                                            <thead>
	                                                                <tr>
	                                                                    <th width="8px">#</th>
	                                                                    <th width="">Billing Date </th>
	                                                                    <th class="text-right">Amount Payable </th>
	                                                                    <th class="text-right">Amount Paid </th>
	                                                                    <th class="text-right">Balance </th>
	                                                                </tr>
	                                                            </thead>
	                                                            <tbody>';
	                                                                $i = 0; foreach($invoices as $invoice):
	                                                                echo '
	                                                                <tr>
	                                                                    <td>'.++$i.'</td>
	                                                                    <td>'.timestamp_to_date($invoice->billing_date).'</td>
	                                                                    <td class="text-right">'.number_to_currency($subscription=$invoice->amount).'</td>
	                                                                    <td class="text-right">'.number_to_currency($amount=$invoice->amount_paid).'</td>
	                                                                    <td class="text-right">'.number_to_currency($subscription-$amount).'</td>
	                                                                </tr>';
	                                                                endforeach;
	                                                            echo '
	                                                            </tbody>
	                                                        </table>
	                                                    </p>';
	                                                        }
	                                                    }
	                                                    else if($status == 2)
	                                                    {
	                                                        echo '<span class="label label-xs label-danger">Suspended</span>';
	                                                    }
	                                                    else
	                                                    {
	                                                        if($post->trial_days){
	                                                            echo '<span class="label label-sx label-primary">On Trial</span>';
	                                                        }else{
	                                                            echo '<span class="label label-sx label-default">Expired on '.timestamp_to_date($post->trial_days_end_date).'</span>';
	                                                        }
	                                                    }
	                                                echo '   
	                                                <p></p>
	                                            </div>
	                                        </li>
	                                    </ul>
	                                    <div class="task-footer bg-grey">
	                                        <div class="row">
	                                            <div class="col-xs-6">
	                                                <a class="task-trash" href="'.site_url("admin/groups/edit/".$post->id).'">
	                                                    <i class="fa fa-edit"></i>
	                                                </a>
	                                            </div>                                                
	                                            <div class="col-xs-6">
	                                                <a class="task-trash" target="_blank" href="'.$this->application_settings->protocol.$post->slug.'.'.$this->application_settings->url.'">
	                                                    <i class="fa fa-user-secret"></i>
	                                                </a>
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                        </li>
	                        <li class="mt-list-item">
	                            <div class="list-todo-icon bg-white">
	                                <i class="fa fa-users"></i>
	                            </div>
	                            <div class="list-todo-item dark">
	                                <a class="list-toggle-container" data-toggle="collapse" href="#task-2" aria-expanded="false">
	                                    <div class="list-toggle done uppercase">
	                                        <div class="list-toggle-title bold">Group Members</div>
	                                        <div class="badge badge-default pull-right bold">'.$post->active_size.'</div>
	                                    </div>
	                                </a>
	                                <div class="task-list panel-collapse collapse" id="task-2">
	                                    <ul>
	                                        <li class="task-list-item done">
	                                            <div class="task-icon">
	                                                <a href="javascript:;">
	                                                    <i class="fa fa-list-alt"></i>
	                                                </a>
	                                            </div>
	                                            <div class="task-content">
	                                                <h4 class="uppercase bold">
	                                                    <a href="javascript:;">Members List</a>
	                                                </h4>
	                                                <p>';
	                                                    if($members):
	                                                    	echo '
	                                                        <table class="table table-striped table-bordered table-advance table-condensed table-hover ">
	                                                            <thead>
	                                                                <tr>
	                                                                    <th>
	                                                                        #
	                                                                    </th>
	                                                                    <th>
	                                                                       Member Name
	                                                                    </th>
	                                                                    <th>
	                                                                        Contact
	                                                                    </th>
	                                                                    <th>
	                                                                        Join Date
	                                                                    </th>
	                                                                    <th>
	                                                                        Last Login
	                                                                    </th>
	                                                                    <th>
	                                                                        Status
	                                                                    </th>
	                                                                </tr>
	                                                            </thead>
	                                                            <tbody>';
	                                                                
	                                                                $i=1;
	                                                                foreach($members as $member):
	                                                                echo '
	                                                                    <tr>
	                                                                        <td>'.$i.'</td>
	                                                                        <td>'.$member->first_name.' '.$member->last_name.'</td>
	                                                                        <td>
	                                                                            ';
	                                                                               echo $member->email?$member->email.'</br>':'';
	                                                                               echo $member->phone;
	                                                                        echo '
	                                                                        </td>                                
	                                                                        <td >'.str_replace(',','<br/>',timestamp_to_date_and_time($member->created_on)).'</td>
	                                                                        <td >';
	                                                                        if($member->last_login){
	                                                                            echo str_replace(',','<br/>',timestamp_to_date_and_time($member->last_login));
	                                                                        }else{
	                                                                            echo 'Never';
	                                                                        }
	                                                                        echo'
	                                                                        </td>
	                                                                        <td class="actions">';
	                                                                            $status = $member->active;
	                                                                                if($status):
	                                                                                    echo '<span class="label label-xs label-primary"> Active </span>';
	                                                                                else:
	                                                                                    if($member->is_deleted):
	                                                                                        echo '<span class="label label-xs label-danger"> Deleted </span>';
	                                                                                    else:
	                                                                                        echo '<span class="label label-xs label-warning"> Suspended </span>';
	                                                                                    endif;
	                                                                                endif;
	                                                                        echo'  
	                                                                        </td>
	                                                                    </tr>';
	                                                                $i++; endforeach; 
	                                                                echo '
	                                                            </tbody>
	                                                        </table>';
	                                                    else:
	                                                    	echo'
	                                                        <div class="alert alert-info">
	                                                            <h4 class="block">Information! No records to display</h4>
	                                                            <p>
	                                                                Sorry, there are no members registered within this group.
	                                                            </p>
	                                                        </div>';
	                                                    endif;
	                                                echo '
	                                                </p>
	                                            </div>
	                                        </li>
	                                    </ul>
	                                </div>
	                            </div>
	                        </li>
	                        <li class="mt-list-item">
	                            <div class="list-todo-icon bg-white">
	                                <i class="fa fa-money"></i>
	                            </div>
	                            <div class="list-todo-item dark">
	                                <a class="list-toggle-container font-white" data-toggle="collapse" href="#task-3" aria-expanded="false">
	                                    <div class="list-toggle done uppercase">
	                                        <div class="list-toggle-title bold">Contribution Settings</div>
	                                        <div class="badge badge-default pull-right bold">'.count($contributions).'</div>
	                                    </div>
	                                </a>
	                                <div class="task-list panel-collapse collapse" id="task-3">
	                                    <ul>
	                                        <li class="task-list-item done">
	                                            <div class="task-icon">
	                                                <a href="javascript:;">
	                                                    <i class="fa fa-navicon"></i>
	                                                </a>
	                                            </div>
	                                            <div class="task-content">
	                                                <h4 class="uppercase bold">
	                                                    <a href="javascript:;">Contribution Settings Listing</a>
	                                                </h4>
	                                                <p>';
	                                                    if(empty($contributions)):
	                                                    	echo'
	                                                        <hr/>
	                                                        <div class="alert alert-info">
	                                                            <strong>Info!</strong> Group doesn\'t have any contribution settings created yet.
	                                                        </div>';
	                                                    else:
	                                                    	echo '
	                                                        <table class="table table-striped table-bordered table-advance table-condensed table-hover">
	                                                            <thead>
	                                                                <tr>
	                                                                    <th width=\'2%\'>
	                                                                        #
	                                                                    </th>
	                                                                    <th>
	                                                                        Name
	                                                                    </th>
	                                                                    <th>
	                                                                        Contribution Particulars
	                                                                    </th>
	                                                                    <th width="15%" class=\'text-right\'>
	                                                                        Amount ('.$group_currency.')
	                                                                    </th>
	                                                                    <th>
	                                                                        Status
	                                                                    </th>
	                                                                </tr>
	                                                            </thead>
	                                                            <tbody>';
	                                                                $i = $this->uri->segment(5, 0); foreach($contributions as $contribution): 
	                                                                    if($contribution->type==1){ 
	                                                                    	echo '
	                                                                        <tr>
	                                                                            <td>'.($i+1).'</td><td>'.$contribution->name.'</td>
	                                                                            <td>'; 
	                                                                                if($contribution->regular_invoicing_active){
	                                                                                    $regular_contribution_setting = isset($regular_contribution_settings_array[$contribution->id])?$regular_contribution_settings_array[$contribution->id]:'';
	                                                                                    if($regular_contribution_setting){ 
	                                                                                        echo '<strong>Contribution Type: </strong>'.$contribution_type_options[$contribution->type];
	                                                                                        echo '<br/><strong>Contribution Details: </strong>'; 
	                                                                                        if($regular_contribution_setting->contribution_frequency==1){
	                                                                                            //Once a month
	                                                                                            echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$days_of_the_month[$regular_contribution_setting->month_day_monthly].' '.$month_days[$regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0];
	                                                                                        }else if($regular_contribution_setting->contribution_frequency==6){
	                                                                                            //Weekly
	                                                                                            echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$week_days[$regular_contribution_setting->week_day_weekly];
	                                                                                        }else if($regular_contribution_setting->contribution_frequency==7){
	                                                                                            //Fortnight or every two weeks
	                                                                                            echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$every_two_week_days[$regular_contribution_setting->week_day_fortnight].' '.$week_numbers[$regular_contribution_setting->week_number_fortnight];
	                                                                                        }else if($regular_contribution_setting->contribution_frequency==2||$regular_contribution_setting->contribution_frequency==3||$regular_contribution_setting->contribution_frequency==4||$regular_contribution_setting->contribution_frequency==5){
	                                                                                            //Multiple months
	                                                                                            echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].',
	                                                                                            '.$days_of_the_month[$regular_contribution_setting->month_day_multiple].'
	                                                                                            '.$month_days[$regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0].', 
	                                                                                            '.$starting_months[$regular_contribution_setting->start_month_multiple];
	                                                                                        }
	                                                                                        echo '<br/><strong>Invoice Date: </strong>'.timestamp_to_date($regular_contribution_setting->invoice_date);
	                                                                                        echo '<br/><strong>Contribution Date: </strong>'.timestamp_to_date($regular_contribution_setting->contribution_date);
	                                                                                        echo '<br/><strong>SMS Notifications: </strong>'; echo $regular_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
	                                                                                        echo '<br/><strong>Email Notifications: </strong>';echo $regular_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
	                                                                                        echo '<br/><strong>Fines: </strong>';echo $regular_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
	                                                                                        echo '<br/>';
	                                                                                        if($regular_contribution_setting->enable_contribution_member_list){
	                                                                                            echo '<hr/>';
	                                                                                            if(isset($selected_group_members_array[$contribution->id])){
	                                                                                                $group_members = $selected_group_members_array[$contribution->id];
	                                                                                                $count = 1;
	                                                                                                echo '<strong>Members to be invoiced: </strong><br/>';
	                                                                                                foreach($group_members as $member_id){
	                                                                                                    if($count==1){
	                                                                                                        echo $member_options[$member_id];
	                                                                                                    }else{
	                                                                                                        echo ','.$member_options[$member_id];
	                                                                                                    }
	                                                                                                    $count++;
	                                                                                                }
	                                                                                            }
	                                                                                        }else{
	                                                                                            echo '<strong>All members to be invoiced </strong><br/>';
	                                                                                        }

	                                                                                        if($regular_contribution_setting->enable_fines){
	                                                                                            if(isset($contribution_fine_settings_array[$contribution->id])){
	                                                                                                echo '<strong>Contribution fine settings: </strong><br/>';
	                                                                                                $contribution_fine_settings = $contribution_fine_settings_array[$contribution->id];
	                                                                                                $count = 1;
	                                                                                                foreach ($contribution_fine_settings as $contribution_fine_setting) {
	                                                                                                    if($count>1){
	                                                                                                        echo '<br/>';
	                                                                                                    }
	                                                                                                    echo '<strong>Fine setting #'.$count.'<br/></strong>';
	                                                                                                    echo '<strong>Fine Date</strong> '.timestamp_to_date($contribution_fine_setting->fine_date).'<br/>';
	                                                                                                    if($contribution_fine_setting->fine_type==1){
	                                                                                                        echo $fine_types[$contribution_fine_setting->fine_type];
	                                                                                                        echo ' '.$group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
	                                                                                                        echo ' '.$fine_mode_options[$contribution_fine_setting->fixed_fine_mode];
	                                                                                                        echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->fixed_fine_chargeable_on];
	                                                                                                        echo ' '.$fine_frequency_options[$contribution_fine_setting->fixed_fine_frequency];
	                                                                                                        echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
	                                                                                                    }else if($contribution_fine_setting->fine_type==2){
	                                                                                                        echo $fine_types[$contribution_fine_setting->fine_type];
	                                                                                                        echo ' '.$contribution_fine_setting->percentage_rate.' % ';
	                                                                                                        echo ' '.$percentage_fine_on_options[$contribution_fine_setting->percentage_fine_on];
	                                                                                                        echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_chargeable_on];
	                                                                                                        echo ' '.$fine_mode_options[$contribution_fine_setting->percentage_fine_mode];
	                                                                                                        echo ' '.$fine_frequency_options[$contribution_fine_setting->percentage_fine_frequency];
	                                                                                                        echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
	                                                                                                    }
	                                                                                                    echo '<br/><strong>SMS Notifications: </strong>'; echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
	                                                                                                    echo '<br/><strong>Email Notifications: </strong>';echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span><br/>";
	                                                                            
	                                                                                                    $count++;
	                                                                                                }
	                                                                                            }
	                                                                                        }
	                                                                                    }else{
	                                                                                        echo "<span class='label label-default'>Regular Contribution Setting not Available</span>";
	                                                                                    }

	                                                                                }else{
	                                                                                    echo "<span class='label label-default'>Invoicing Disabled</span>";
	                                                                                }
	                                                                            echo '
	                                                                            </td>
	                                                                            <td class=\'text-right\'>'.number_to_currency($contribution->amount).'</td>
	                                                                            <td>';
	                                                                                    if($contribution->is_hidden){
	                                                                                        echo "<span class='label label-default'>Hidden</span>";
	                                                                                    }else{
	                                                                                        echo "<span class='label label-success'>Visible</span>";
	                                                                                    } 
	                                                                                    if($contribution->regular_invoicing_active){
	                                                                                        echo "<span class='label label-success'>Invoicing Active</span>";
	                                                                                    }else{
	                                                                                        echo "<span class='label label-default'>Invoicing Disabled</span>";
	                                                                                    }
	                                                                            echo '
	                                                                            </td>
	                                                                        </tr>';
	                                                                    }else if($contribution->type==2){
	                                                                    	echo '
	                                                                        <tr>
	                                                                            <td>'.($i+1).'</td>
	                                                                            <td>
	                                                                                '.$contribution->name.'
	                                                                            </td>
	                                                                            <td>';
	                                                                                if($contribution->one_time_invoicing_active){
	                                                                                    $one_time_contribution_setting = isset($one_time_contribution_settings_array[$contribution->id])?$one_time_contribution_settings_array[$contribution->id]:'';
	                                                                                    if($one_time_contribution_setting){ 
	                                                                                        echo '<strong>Contribution Type: </strong>'.$contribution_type_options[$contribution->type];
	                                                                                        echo '<br/><strong>Invoice Date: </strong>'.timestamp_to_date($one_time_contribution_setting->invoice_date);
	                                                                                        echo '<br/><strong>Contribution Date: </strong>'.timestamp_to_date($one_time_contribution_setting->contribution_date);
	                                                                                        echo '<br/><strong>SMS Notifications: </strong>'; echo $one_time_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
	                                                                                        echo '<br/><strong>Email Notifications: </strong>';echo $one_time_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
	                                                                                        echo '<br/><strong>Fines: </strong>';echo $one_time_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
	                                                                                        echo '<br/>';
	                                                                                        if($one_time_contribution_setting->enable_contribution_member_list){
	                                                                                            if(isset($selected_group_members_array[$contribution->id])){
	                                                                                                echo '<hr/>';
	                                                                                                $group_members = $selected_group_members_array[$contribution->id];
	                                                                                                $count = 1;
	                                                                                                echo '<strong>Members to be invoiced: </strong><br/>';
	                                                                                                foreach($group_members as $member_id){
	                                                                                                    if($count==1){
	                                                                                                        echo $member_options[$member_id];
	                                                                                                    }else{
	                                                                                                        echo ','.$member_options[$member_id];
	                                                                                                    }
	                                                                                                    $count++;
	                                                                                                }
	                                                                                            }
	                                                                                        }else{
	                                                                                            echo '<strong>All members to be invoiced </strong><br/>';
	                                                                                        }

	                                                                                        if($one_time_contribution_setting->enable_fines){
	                                                                                            if(isset($contribution_fine_settings_array[$contribution->id])){
	                                                                                                echo '<strong>Contribution fine settings: </strong><br/>';
	                                                                                                $contribution_fine_settings = $contribution_fine_settings_array[$contribution->id];
	                                                                                                $count = 1;
	                                                                                                foreach ($contribution_fine_settings as $contribution_fine_setting) {
	                                                                                                    if($count>1){
	                                                                                                        echo '<br/>';
	                                                                                                    }
	                                                                                                    echo '<strong>Fine setting #'.$count.'<br/></strong>';
	                                                                                                    if($contribution_fine_setting->fine_type==1){
	                                                                                                        echo $fine_types[$contribution_fine_setting->fine_type];
	                                                                                                        echo ' '.$group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
	                                                                                                        echo ' '.$fine_mode_options[$contribution_fine_setting->fixed_fine_mode];
	                                                                                                        echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->fixed_fine_chargeable_on];
	                                                                                                        echo ' '.$fine_frequency_options[$contribution_fine_setting->fixed_fine_frequency];
	                                                                                                        echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
	                                                                                                    }else if($contribution_fine_setting->fine_type==2){
	                                                                                                        echo $fine_types[$contribution_fine_setting->fine_type];
	                                                                                                        echo ' '.$contribution_fine_setting->percentage_rate.' % ';
	                                                                                                        echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_on];
	                                                                                                        echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_chargeable_on];
	                                                                                                        echo ' '.$fine_mode_options[$contribution_fine_setting->percentage_fine_mode];
	                                                                                                        echo ' '.$fine_frequency_options[$contribution_fine_setting->percentage_fine_frequency];
	                                                                                                        echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
	                                                                                                    }
	                                                                                                    echo '<br/><strong>SMS Notifications: </strong>'; echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
	                                                                                                    echo '<br/><strong>Email Notifications: </strong>';echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span><br/>";
	                                                                            
	                                                                                                    $count++;
	                                                                                                }
	                                                                                            }
	                                                                                        }
	                                                                                    }else{
	                                                                                        echo "<span class='label label-default'>One Time Contribution Setting not Available</span>";
	                                                                                    }

	                                                                                }else{
	                                                                                    echo "<span class='label label-default'>Invoicing Disabled</span>";
	                                                                                }
	                                                                            echo '   
	                                                                            </td>
	                                                                            <td class=\'text-right\'>'.number_to_currency($contribution->amount).'</td>
	                                                                            <td>'; 
	                                                                                if($contribution->is_hidden){
	                                                                                    echo "<span class='label label-default'>Hidden</span>";
	                                                                                }else{
	                                                                                    echo "<span class='label label-success'>Visible</span>";
	                                                                                }
	                                                                            
	                                                                                if($contribution->one_time_invoicing_active){
	                                                                                    echo "<span class='label label-success'>Invoicing Active</span>";
	                                                                                }else{
	                                                                                    echo "<span class='label label-default'>Invoicing Disabled</span>";
	                                                                                }
	                                                                            echo '    
	                                                                            </td>
	                                                                        </tr>';
	                                                                    }else if($contribution->type==3){
	                                                                    	echo '
	                                                                        <tr>
	                                                                            <td>'.($i+1).'</td>
	                                                                            <td>
	                                                                                '.$contribution->name.'
	                                                                            </td>
	                                                                            <td>';
	                                                                                echo '<strong>Contribution Type: </strong>'.$contribution_type_options[$contribution->type];
	                                                                            echo '
	                                                                            </td>
	                                                                            <td class=\'text-right\'>'.number_to_currency($contribution->amount).'</td>
	                                                                            <td>';
	                                                                                    if($contribution->is_hidden){
	                                                                                        echo "<span class='label label-default'>Hidden</span>";
	                                                                                    }else{
	                                                                                        echo "<span class='label label-success'>Visible</span>";
	                                                                                    }
	                                                                                    if($contribution->regular_invoicing_active){
	                                                                                        echo "<span class='label label-success'>Invoicing Active</span>";
	                                                                                    }else{
	                                                                                        echo "<span class='label label-default'>Invoicing Disabled</span>";
	                                                                                    }
	                                                                                echo '
	                                                                            </td>
	                                                                        </tr> ';
	                                                                    } 
	                                                                    $i++;
	                                                                    endforeach;
	                                                               	echo ' 
	                                                            </tbody>
	                                                        </table>';
	                                                    endif;
	                                                    echo '
	                                                </p>
	                                            </div>
	                                        </li>
	                                    </ul>
	                                </div>
	                            </div>
	                        </li>
	                        <li class="mt-list-item">
	                            <div class="list-todo-icon bg-white">
	                                <i class="fa fa-institution"></i>
	                            </div>
	                            <div class="list-todo-item dark">
	                                <a class="list-toggle-container font-white" data-toggle="collapse" href="#task-4" aria-expanded="false">
	                                    <div class="list-toggle done uppercase">
	                                        <div class="list-toggle-title bold">Group Accounts</div>
	                                        <div class="badge badge-default pull-right bold">'.count($bank_accounts).'</div>
	                                    </div>
	                                </a>
	                                <div class="task-list panel-collapse collapse" id="task-4">
	                                    <ul>
	                                        <li class="task-list-item done">
	                                            <div class="task-icon">
	                                                <a href="javascript:;">
	                                                    <i class="fa fa-navicon"></i>
	                                                </a>
	                                            </div>
	                                            <div class="task-content">
	                                                <h4 class="uppercase bold">
	                                                    <a href="javascript:;">Group Accounts Listing</a>
	                                                </h4>
	                                                <p>';
	                                                    if($bank_accounts){
	                                                    	echo '
	                                                        <h5>Bank Accounts</h5>
	                                                        <ol class="">';
	                                                        foreach ($bank_accounts as $bank_account):
	                                                            echo '<li>'; echo'<strong>'.$bank_account->bank_name.'</strong> | '.$bank_account->bank_branch.' branch | '.$bank_account->account_name.' | '.$bank_account->account_number; echo '</li>';
	                                                        endforeach;
	                                                        echo '
	                                                        </ol>';

	                                                    } if($sacco_accounts){
	                                                    	echo '
	                                                        <h5>sacco Accounts</h5>
	                                                        <ol>';
	                                                        foreach ($sacco_accounts as $sacco_account):
	                                                            echo '<li>'; echo '<strong>'.$sacco_account->sacco_name.'</strong> | '.$sacco_account->sacco_branch.' | '.$sacco_account->account_name.' | '.$sacco_account->account_number; echo '</li>';
	                                                        endforeach;
	                                                        echo '
	                                                        </ol>';

	                                                    }if($mobile_money_accounts){
	                                                    	echo '
	                                                        <h5>Mobile Money Accounts</h5>
	                                                        <ol>';
	                                                        foreach ($mobile_money_accounts as $mobile_money_account):
	                                                            echo '<li>'; echo '<strong>'.$mobile_money_account->mobile_money_provider_name.'</strong> | '.$mobile_money_account->account_name.' | '.$mobile_money_account->account_number; echo '</li>';
	                                                        endforeach;
	                                                        echo '
	                                                        </ol>';

	                                                    }if($petty_cash_accounts){
	                                                    	echo '
	                                                        <h5>Petty Cash Accounts</h5>
	                                                        <ol>';
	                                                        foreach ($petty_cash_accounts as $petty_cash_account):
	                                                            echo '<li>';  echo '<strong>'.$petty_cash_account->account_name.'</strong>'; echo '</li>';
	                                                        endforeach;
	                                                        echo '
	                                                        </ol>';
	                                                    }
	                                                    if(empty($bank_accounts) && empty($petty_cash_accounts) && empty($sacco_accounts) && empty($mobile_money_accounts)){
	                                                        echo '<hr/>
	                                                        <div class="alert alert-info">
	                                                            <strong>Info!</strong> Group doesn\'t have any bank accounts created yet.
	                                                        </div>';
	                                                    }
	                                                echo'
	                                                </p>
	                                            </div>
	                                        </li>
	                                    </ul>
	                                </div>
	                            </div>
	                        </li>
	                    </ul>
	                </div>
	            </div>
	        ';
	    }else{
	    	echo '<hr/>
	            <div class="alert alert-info">
	                <strong>Info!</strong> Could not find group profile.
	            </div>
	       	';
	    }
	}

	function delete($group_id = 0,$confirmation_code = '',$redirect = TRUE){
			$confirmation_code = $this->input->get('confirmation_code')?$this->input->get('confirmation_code'):$confirmation_code;
			$referrer  = $this->agent->referrer();
			if($confirmation_code=="thecheese"){
				set_time_limit(0);
				$this->_backup_group($group_id,1);
				if($this->groups_m->delete($group_id)){
					$result = TRUE;
					$members = $this->members_m->get_group_members($group_id);
					foreach ($members as $member) {
						# code...
						if($this->members_m->delete($member->id)){
							$member_group_count = $this->groups_m->count_current_user_groups($member->user_id) + 1;
							if($member_group_count==1){
								if($this->users_m->delete($member->user_id)){

								}else{
									$result = FALSE;
								}
							}
						}else{
							$result = FALSE;
						}
					}
					if(preg_match('/(sandbox\.co)/',$_SERVER['HTTP_HOST'])){
						$database = 'eazzychama_uganda';
					}else if(preg_match('/(eazzyclub\.co)/',$_SERVER['HTTP_HOST'])){
						$database = 'eazzyclub';
					}else if(preg_match('/173\.255\.205\.7/',$_SERVER['SERVER_ADDR'])||preg_match('/45\.56\.79\.118/',$_SERVER['SERVER_ADDR'])){
						$database = 'eazzychama';
					}else{
						$database = 'chamasoft';
					}
					$tables=$this->db->query("SELECT t.TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES AS t WHERE t.TABLE_SCHEMA = '".$database."' ")->result_array();    
						$count = 1;
						$ignore_tables = array('equity_bank_transaction_alerts','transaction_alerts');
						foreach($tables as $key => $val) {
							$table_name = $val['table_name'];
						    if($this->db->field_exists('group_id',$table_name)){
						    	if(in_array($table_name,$ignore_tables)){
						    		if($table_name=='transaction_alerts'){
						    			$this->migrate_m->unset_group_transaction_alerts($group_id);
						    		}
						    	}else{
						    		if($this->migrate_m->delete_group_data($group_id,$table_name)){
						    			//do nothing for now
						    		}else{
						    			$result = FALSE;
						    		}
						    	}
						    }
						    if($result){
						    	$this->session->set_flashdata('success','All went well during the deletion of the group and group data');
						    }else{
						    	$this->session->set_flashdata('warning','Something went wrong during the deletion of the group and group data');
						    }
						}
				}
			}else{
				$this->session->set_flashdata('warning','You entered the wrong confirmation code');
			}	
			if($redirect){
				redirect('admin/groups/listing');	    	
			}
	}

	function reset_group($group_id = 0,$confirmation_code = '',$redirect = TRUE){
			$confirmation_code = $this->input->get('confirmation_code')?$this->input->get('confirmation_code'):$confirmation_code;
			$referrer  = $this->agent->referrer();
			if($confirmation_code=="thecheese"){
				set_time_limit(0);
				$this->_backup_group($group_id,0,1);
				$result = TRUE;
				$members= $this->members_m->get_group_members_by_id_for_admin($group_id);
				foreach ($members as $member) {
					if($member->active){
					}else{
						if($this->members_m->delete($member->id)){
							$member_group_count = $this->groups_m->count_current_user_groups($member->user_id) + 1;
							if($member_group_count==1){
								if($this->users_m->delete($member->user_id)){

								}else{
									$result = FALSE;
								}
							}
						}else{
							$result = FALSE;
						}
					}
				}
				if(preg_match('/(sandbox\.co)/',$_SERVER['HTTP_HOST'])){
					$database = 'eazzychama_uganda';
				}else if(preg_match('/(eazzyclub\.co)/',$_SERVER['HTTP_HOST'])){
					$database = 'eazzyclub';
				}else if(preg_match('/173\.255\.205\.7/',$_SERVER['SERVER_ADDR'])||preg_match('/45\.56\.79\.118/',$_SERVER['SERVER_ADDR'])){
					$database = 'eazzychama';
				}else{
					$database = 'chamasoft';
				}
				$tables=$this->db->query("SELECT t.TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES AS t WHERE t.TABLE_SCHEMA = '".$database."' ")->result_array();    
					$count = 1;
					$ignore_tables = array('equity_bank_transaction_alerts','transaction_alerts','investment_groups','members','bank_accounts');
					foreach($tables as $key => $val) {
						$table_name = $val['table_name'];
					    if($this->db->field_exists('group_id',$table_name)){
					    	if(in_array($table_name,$ignore_tables)){
					    		if($table_name=='transaction_alerts'){
					    			$this->migrate_m->unset_group_transaction_alerts($group_id);
					    		}
					    	}else{
					    		if($this->migrate_m->delete_group_data($group_id,$table_name)){
					    			//do nothing for now
					    		}else{
					    			$result = FALSE;
					    		}
					    	}
					    }
					    if($result){
					    	$this->session->set_flashdata('success','All went well during the deletion of the group and group data');
					    }else{
					    	$this->session->set_flashdata('warning','Something went wrong during the deletion of the group and group data');
					    }
					}
					if(in_array('bank_accounts', $ignore_tables)){
						$this->reset_group_bank_account_balances($group_id);
					}
			}else{
				$this->session->set_flashdata('warning','You entered the wrong confirmation code');
			}	
			if($redirect){
				redirect('admin/groups/listing');	    	
			}
	}

 	function reset_group_bank_account_balances($group_id = 0){
 		$bank_accounts = $this->bank_accounts_m->get_group_bank_accounts($group_id);
 		foreach ($bank_accounts as $bank_account) {
 			$update = array(
 				'initial_balance' => 0,
 				'current_balance' => 0,
 			);
 			$this->bank_accounts_m->update($bank_account->id,$update);
 		}
 	}

	function action(){
		$action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_delete'){
            for($i=0;$i<count($action_to);$i++){
                $this->delete($action_to[$i],"galileo",FALSE);
            }
        }
        if($this->agent->referrer()){
            redirect($this->agent->referrer());
        }else{
            redirect('admin/groups/listing');
        }
	}
	

	function contribution_statistics(){
		$params = array(

			);
		$total_rows = $this->groups_m->count_all_paying_groups($params);
        $pagination = create_pagination('admin/contribution_statistics/listing/pages',$total_rows,250,5);
        $posts = $this->groups_m->limit($pagination['limit'])->get_all_paying_groups($params);

        $billing_packages = $this->billing_m->get_all_packages(TRUE);

        $param_options = array(
        		'lock_access' => TRUE,
        		'status' => 1
        	);

        $total_deposits = array();
        $total_withdrawals = array();
        $contributions = array();
        $total_contributions = array();
		$average_contributions=array();
		$billing=array();
		$current_billing= array();
		$post_groups = array();
        foreach($posts as $post){
        	$deposits = $this->deposits_m->group_total_deposits($post->id);
        	$total_withdrawals[$post->id] = $this->withdrawals_m->group_total_withdrawals($post->id);

        	$contribution = $this->contributions_m->get_group_regular_contributions($post->id);
        	$contributions = $this->_monthly_contributions($contribution);
        	$total_contributions[$post->id] = $contributions->total_monthly;
        	$average_contributions[$post->id] = $contributions->average_amount;

        	
        	$current_billin = $this->billing_settings->get_amount_payable($post->billing_package_id,3,$post->id,'','',TRUE);
        	foreach($billing_packages as $package){
        		$billing[$post->id][$package->id] = $this->billing_settings->get_amount_payable($package->id,3,$post->id,'','',TRUE);
        	}

        	$account_arrears = $this->billing_m->get_group_account_arrears($post->id);
        	if($account_arrears<=0 && $current_billin->amount>0 && $deposits>0){
        		$post_groups[] = $post;
        	}
        	$current_billing[$post->id] = $current_billin;
        	$total_deposits[$post->id] = $deposits;
        }

        $this->data['group_options'] = $this->groups_m->get_options($param_options);
        $this->data['total_deposits'] = $total_deposits;
        $this->data['total_withdrawals'] = $total_withdrawals;
        $this->data['total_contributions'] = $total_contributions;
        $this->data['average_contributions'] = $average_contributions;
        $this->data['billing_packages'] = $billing_packages;
        $this->data['posts'] = $post_groups;
        $this->data['billing'] = $billing;
        $this->data['current_billing'] = $current_billing;
        $this->data['account_arrears'] = $account_arrears;
        $this->template->title('Group Contribution Statistics')->build('admin/contribution_statistics',$this->data);
	}


	function group_distribution($lower=0,$upper=20){
		$paying = $this->input->get_post('paying')?1:0;
		print_r($this->groups_m->count_groups_with_members_between($lower,$upper,$paying));

		print_r('<pre>');
		print_r($this->groups_m->get_groups_billing_cycle($lower,$upper,$paying));
		print_r('</pre>');
	}


	function _monthly_contributions($contributions=array()){
		$total_amount = 0;
        $total_members = 0;
        $average_members=0;
        $i = 0;
		if($contributions){
            
            //average per member contribution
            foreach ($contributions as $value) {
                $value = (object)$value;
                $total_members+= $value->members;
                if($value->frequency==1){
                    $total_amount+= ($value->amount)*$value->members;
                }else if($value->frequency==2){
                    $total_amount+= (($value->amount)/2)*$value->members;
                }else if($value->frequency==3){
                    $total_amount+= (($value->amount)/3)*$value->members;
                }else if($value->frequency==4){
                    $total_amount+= (($value->amount)/6)*$value->members;
                }else if($value->frequency==5){
                    $total_amount+= (($value->amount)/12)*$value->members;
                }else if($value->frequency==6){
                    $total_amount+= (($value->amount)*4)*$value->members;
                }
                ++$i;
            }
        }
        if($total_members){
        	$average_members = $total_members/$i;
        }
        if($total_amount){
        	$average_amount = $total_amount/$average_members;
        }else{
        	$average_amount = 0;
        }
        
        $result = array(
        		'total_monthly' => $total_amount?:0,
        		'average_amount' => $average_amount,
        	);

        return (object)$result;
	}

	function switch_all_group_contribution_ids($group_id = 15,$contribution_id = 44){
		$tables=$this->db->query("SELECT t.TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES AS t WHERE t.TABLE_SCHEMA = 'chamasoft' ")->result_array();    
		$count = 1;
		foreach($tables as $key => $val) 
		{			
			$table_name = $val['table_name'];
		    if($this->db->field_exists('group_id',$table_name)&&$this->db->field_exists('contribution_id',$table_name)){
		    	$result = $this->groups_m->get_group_table_data($table_name,$group_id);
		    	foreach($result as $row):
		    		if($row->contribution_id=="47"||$table_name=="contribution_fine_settings"||$table_name=="regular_contribution_settings"){

		    		}else{
		    			if($row->contribution_id){
		    				$input = array(
				    			'contribution_id' => $contribution_id,
				    			'modified_on' => time(),
				    			'modified_by' => $this->user->id
				    		);
				    		$this->groups_m->update_group_table_data($row->id,$table_name,$input);
		    			}else{
		    				
		    			}
			    	}
		    	endforeach;
		    	echo $table_name." - ".count($result)."<br/>";
		    }
		}
	}

	function subscribed_groups(){
		$this->data['posts'] = $this->groups_m->get_subscribed_groups();
		$this->data['user_options'] = $this->users_m->get_options(TRUE);
		$this->data['group_bank_account_options'] = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options();
		$this->template->title('Subscribed Group List')->build('admin/subscribed_groups',$this->data);
	}

	function paying_groups(){
		$generate_excel = $this->input->get('generate_excel');
		$from = 0;
		$to = 0;
		$paying_group_ids = $this->billing_m->get_paying_group_id_array($from,$to);
		$groups_billing_payable_amounts = $this->billing_m->get_groups_billing_payable_amounts_array($paying_group_ids);
		$groups_billing_paid_amounts = $this->billing_m->get_groups_billing_paid_amounts_array($paying_group_ids);
		$groups_billing_last_payment_dates_array = $this->billing_m->get_groups_billing_last_payment_dates_array($paying_group_ids);
		$groups_billing_first_payment_dates_array = $this->billing_m->get_groups_billing_first_payment_dates_array($paying_group_ids);
		$paying_groups_arrears_array = array();
		foreach($paying_group_ids as $group_id):
			$payable = isset($groups_billing_payable_amounts[$group_id])?$groups_billing_payable_amounts[$group_id]:0;
			$paid = isset($groups_billing_paid_amounts[$group_id])?$groups_billing_paid_amounts[$group_id]:0;
			$paying_groups_arrears_array[$group_id] = $payable - $paid;
			if($paying_groups_arrears_array[$group_id]>0):
				$key = array_search($group_id,$paying_group_ids);
				//unset($paying_group_ids[$key]);
			endif;
		endforeach;
        $this->data['bank_branch_options'] = $this->bank_branches_m->get_bank_branch_options_by_bank_id(1);
        $this->data['group_bank_branch_pairing_arrays'] = $this->reports_m->get_group_bank_branch_pairing_arrays();
		$this->data['paying_groups_arrears_array'] = $paying_groups_arrears_array;
		$this->data['groups_billing_last_payment_dates_array'] = $groups_billing_last_payment_dates_array;
		$this->data['groups_billing_first_payment_dates_array'] = $groups_billing_first_payment_dates_array;
		$this->data['posts'] = $this->groups_m->get_paying_groups($paying_group_ids);
		$this->data['user_options'] = $this->users_m->get_options(TRUE);
		$this->data['user_last_login_options'] = $this->users_m->get_user_last_login_options();
		$this->data['billing_cycle_options'] = $this->billing_settings->billing_cycle;
		$this->data['group_bank_account_options'] = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options(FALSE);
        $this->data['referrer_options'] = $this->referrers_m->get_admin_referrer_options();
		if($generate_excel){
			//echo json_encode($this->data);
			//die;
			$response = $this->curl_post_data->curl_post_json_excel((json_encode($this->data)),'https://excel.chamasoft.com/groups/paying_groups',$this->application_settings->application_name.' Paying Groups ');
            print_r($response);die;
		}
		$this->template->title('Paying Group List')->build('admin/paying_groups',$this->data);
	}

	function paying_group_members(){
		$generate_excel = $this->input->get('generate_excel');
		$from = strtotime('-12 months');
		$to = strtotime('today');
		$paying_group_ids = $this->billing_m->get_paying_group_id_array($from,$to);
		$this->data['posts'] = $this->members_m->get_paying_group_members($paying_group_ids);
		$this->data['group_options'] = $this->groups_m->get_options();
		if($generate_excel){
			//echo json_encode($this->data);
			//die;
			$response = $this->curl_post_data->curl_post_json_excel((json_encode($this->data)),'https://excel.chamasoft.com/groups/paying_group_members',$this->application_settings->application_name.' Paying Group Members ');
            print_r($response);die;
		}
		$this->template->title('Paying Group Members List')->build('admin/paying_group_members',$this->data);
	}





	function groups_in_arrears(){
		$generate_excel = $this->input->get('generate_excel');
		$paying_group_ids = $this->billing_m->get_paying_group_id_array();
		$groups_billing_payable_amounts = $this->billing_m->get_groups_billing_payable_amounts_array($paying_group_ids);
		$groups_billing_paid_amounts = $this->billing_m->get_groups_billing_paid_amounts_array($paying_group_ids);
		$groups_billing_last_payment_dates_array = $this->billing_m->get_groups_billing_last_payment_dates_array($paying_group_ids);
		$groups_billing_first_payment_dates_array = $this->billing_m->get_groups_billing_first_payment_dates_array($paying_group_ids);
		$paying_groups_arrears_array = array();
		foreach($paying_group_ids as $group_id):
			$payable = isset($groups_billing_payable_amounts[$group_id])?$groups_billing_payable_amounts[$group_id]:0;
			$paid = isset($groups_billing_paid_amounts[$group_id])?$groups_billing_paid_amounts[$group_id]:0;
			$paying_groups_arrears_array[$group_id] = $payable - $paid;
			if($paying_groups_arrears_array[$group_id]<=0):
				$key = array_search($group_id,$paying_group_ids);
				unset($paying_group_ids[$key]);
			endif;
		endforeach;

        $this->data['bank_branch_options'] = $this->bank_branches_m->get_bank_branch_options_by_bank_id(1);
        $this->data['group_bank_branch_pairing_arrays'] = $this->reports_m->get_group_bank_branch_pairing_arrays();
		$this->data['groups_billing_first_payment_dates_array'] = $groups_billing_first_payment_dates_array;
		$this->data['paying_groups_arrears_array'] = $paying_groups_arrears_array;
		$this->data['groups_billing_last_payment_dates_array'] = $groups_billing_last_payment_dates_array;
		$this->data['posts'] = $this->groups_m->get_paying_groups($paying_group_ids);
		$this->data['user_options'] = $this->users_m->get_options(TRUE);
		$this->data['billing_cycle_options'] = $this->billing_settings->billing_cycle;
		$this->data['group_bank_account_options'] = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options(FALSE);
		$this->data['referrer_options'] = $this->referrers_m->get_admin_referrer_options();
		if($generate_excel){
			//echo json_encode($this->data);
			//die;
			$response = $this->curl_post_data->curl_post_json_excel((json_encode($this->data)),'https://excel.chamasoft.com/groups/groups_in_arrears',$this->application_settings->application_name.' Groups in Arrears ');
            print_r($response);die;
		}
		$this->template->title('Paying Group List')->build('admin/groups_in_arrears',$this->data);
	}

	function paying_groups_csv(){
		header('Content-Type: application/excel');
		header('Content-Disposition: attachment; filename="paying_groups.csv"');
		$paying_group_ids = $this->billing_m->get_paying_group_id_array();
		$groups_billing_payable_amounts = $this->billing_m->get_groups_billing_payable_amounts_array($paying_group_ids);
		$groups_billing_paid_amounts = $this->billing_m->get_groups_billing_paid_amounts_array($paying_group_ids);
		$paying_groups_arrears_array = array();
		foreach($paying_group_ids as $group_id):
			$payable = isset($groups_billing_payable_amounts[$group_id])?$groups_billing_payable_amounts[$group_id]:0;
			$paid = isset($groups_billing_paid_amounts[$group_id])?$groups_billing_paid_amounts[$group_id]:0;
			$paying_groups_arrears_array[$group_id] = $payable - $paid;
			if($paying_groups_arrears_array[$group_id]>0):
				$key = array_search($group_id,$paying_group_ids);
				unset($paying_group_ids[$key]);
			endif;
		endforeach;
		$posts = $this->groups_m->get_paying_groups($paying_group_ids);
		$user_options = $this->users_m->get_options();
		$user_contact_options = $this->users_m->get_contact_options();
		$group_bank_account_options = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options(FALSE);

		$fp = fopen('php://output', 'w');
		foreach ($posts as $post) {
			$group_data[] = $post->name; 
			$group_data[] = $user_options[$post->owner].'- ('.$user_contact_options[$post->owner].') '; 
            if(array_key_exists($post->id,$group_bank_account_options)){
                $bank_accounts = $group_bank_account_options[$post->id];
                $bank_account_string = "";
                $count = 1;
                foreach($bank_accounts as $bank_account):
                	if($count==1){
    					$group_data[] = '"'.$bank_account.'"';
                		$bank_account_string.='('.$count.'). '.$bank_account;
                	}else{
                		$bank_account_string.=' ('.$count.'). '.$bank_account;
                	}
                	$count++;
                endforeach;
                $group_data[] = $bank_account_string;
            }else{
            	$groups_data[] = "No bank account create on the group profile";
            	$group_data[] = "No bank account create on the group profile";
            }
		    fputcsv($fp,$group_data);
		    $group_data = array();
		}
		fclose($fp);
	}

	function groups_on_trial(){
		$generate_excel = $this->input->get('generate_excel');
		$total_rows = $this->groups_m->count_groups_on_trial();
        $pagination = create_pagination('admin/groups/groups_on_trial/pages',$total_rows,50,5);
        if($generate_excel){
			$this->data['posts'] = $this->groups_m->get_groups_on_trial();
        }else{
			$this->data['posts'] = $this->groups_m->limit($pagination['limit'])->get_groups_on_trial();
		}
		$this->data['user_options'] = $this->users_m->get_options(TRUE);
		$this->data['group_bank_account_options'] = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options(FALSE);
		$this->data['pagination'] = $pagination;
		if($generate_excel){
			$response = $this->curl_post_data->curl_post_json_excel((json_encode($this->data)),'https://excel.chamasoft.com/groups/groups_on_trial',$this->application_settings->application_name.' Groups on Trial');
            print_r($response);die;

		}
		$this->template->title('On Trial Group List')->build('admin/groups_on_trial',$this->data);
	}

	function locked_groups(){
		$total_rows = $this->groups_m->count_locked_groups();
        $pagination = create_pagination('admin/groups/locked_groups/pages',$total_rows,50,5);
		$this->data['posts'] = $this->groups_m->limit($pagination['limit'])->get_locked_groups();
		$this->data['user_options'] = $this->users_m->get_options(TRUE);
		$this->data['group_bank_account_options'] = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options();
		$this->data['pagination'] = $pagination;
		$this->template->title('Locked Group List')->build('admin/locked_groups',$this->data);
	}

	function groups_trial_elapsed(){
		$generate_excel = $this->input->get('generate_excel');
		$total_rows = $this->groups_m->count_groups_trial_expired();
        $pagination = create_pagination('admin/groups/groups_trial_elapsed/pages',$total_rows,50,5);
        if($generate_excel){
			$this->data['posts'] = $this->groups_m->get_groups_trial_expired();
        }else{
			$this->data['posts'] = $this->groups_m->limit($pagination['limit'])->get_groups_trial_expired();
        }
		$this->data['user_options'] = $this->users_m->get_options(TRUE);
		$this->data['group_bank_account_options'] = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options(FALSE);
		$this->data['pagination'] = $pagination;
		if($generate_excel){
			$response = $this->curl_post_data->curl_post_json_excel((json_encode($this->data)),'https://excel.chamasoft.com/groups/groups_trial_elapsed',$this->application_settings->application_name.' Groups Trial Elapsed ');
            print_r($response);die;
		}
		$this->template->title('Groups with Elapsed Trial')->build('admin/groups_trial_elapsed',$this->data);
	}

	function groups_listing(){
		$this->data['posts'] = $this->groups_m->get_groups_trial_expired();
		$this->data['user_options'] = $this->users_m->get_options(TRUE);
		$this->data['group_bank_account_options'] = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options();
		//print_r($this->data['group_bank_account_options']);
		//die;
		$this->template->title('Expired Groups List')->build('admin/groups_listing',$this->data);
	}

	function groups_with_connected_accounts(){
		$this->data['posts'] = $this->groups_m->get_groups_with_connected_accounts();
		$this->data['user_options'] = $this->users_m->get_options(TRUE);
		$this->data['group_bank_account_options'] = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options();
		//print_r($this->data['group_bank_account_options']);
		//die;
		$this->template->title('On Trial Group List')->build('admin/groups_with_connected_accounts',$this->data);
	}

	function groups_with_accounts(){
		$this->data['posts'] = $this->groups_m->get_groups_with_accounts();
		$this->data['user_options'] = $this->users_m->get_options(TRUE);
		$this->data['group_bank_account_options'] = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options();
		//print_r($this->data['group_bank_account_options']);
		//die;
		$this->template->title('On Trial Group List')->build('admin/groups_with_accounts',$this->data);
	}

	function groups_with_accounts_csv(){
		header('Content-Type: application/excel');
		header('Content-Disposition: attachment; filename="groups_with_accounts.csv"');
		$posts = $this->groups_m->get_groups_with_accounts();
		$user_options = $this->users_m->get_options();
		$user_contact_options = $this->users_m->get_contact_options();
		$group_bank_account_options = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options();

		$fp = fopen('php://output', 'w');
		foreach ($posts as $post) {
			$group_data[] = $post->name; 
			$group_data[] = $user_options[$post->owner].'- ('.$user_contact_options[$post->owner].') '; 
            if(array_key_exists($post->id,$group_bank_account_options)){
                $bank_accounts = $group_bank_account_options[$post->id];
                $bank_account_string = "";
                $count = 1;
                foreach($bank_accounts as $bank_account):
                	if($count==1){
                		$bank_account_string.='('.$count.'). '.$bank_account;
                	}else{
                		$bank_account_string.=' ('.$count.'). '.$bank_account;
                	}
                	$count++;
                endforeach;
                $group_data[] = $bank_account_string;
            }else{
            	$group_data[] = "No bank account create on the group profile";
            }
		    fputcsv($fp,$group_data);
		    $group_data = array();
		}
		fclose($fp);
	}
	
	function groups_with_connected_accounts_csv(){
		header('Content-Type: application/excel');
		header('Content-Disposition: attachment; filename="groups_with_connected_accounts.csv"');
		$posts = $this->groups_m->get_groups_with_connected_accounts();
		$user_options = $this->users_m->get_options();
		$user_contact_options = $this->users_m->get_contact_options();
		$group_bank_account_options = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options();

		$fp = fopen('php://output', 'w');
		foreach ($posts as $post) {
			$group_data[] = $post->name; 
			$group_data[] = $user_options[$post->owner].'- ('.$user_contact_options[$post->owner].') '; 
            if(array_key_exists($post->id,$group_bank_account_options)){
                $bank_accounts = $group_bank_account_options[$post->id];
                $bank_account_string = "";
                $count = 1;
                foreach($bank_accounts as $bank_account):
                	if($count==1){
                		$bank_account_string.='('.$count.'). '.$bank_account;
                	}else{
                		$bank_account_string.=' ('.$count.'). '.$bank_account;
                	}
                	$count++;
                endforeach;
                $group_data[] = $bank_account_string;
            }else{
            	$group_data[] = "No bank account create on the group profile";
            }
		    fputcsv($fp,$group_data);
		    $group_data = array();
		}
		fclose($fp);
	}
	
	function subscribed_groups_csv(){
		header('Content-Type: application/excel');
		header('Content-Disposition: attachment; filename="subscribed_groups.csv"');
		$posts = $this->groups_m->get_paying_groups();
		$user_options = $this->users_m->get_options();
		$user_contact_options = $this->users_m->get_contact_options();
		$group_bank_account_options = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options();

		$fp = fopen('php://output', 'w');
		foreach ($posts as $post) {
			$group_data[] = $post->name; 
			$group_data[] = $user_options[$post->owner].'- ('.$user_contact_options[$post->owner].') '; 
            if(array_key_exists($post->id,$group_bank_account_options)){
                $bank_accounts = $group_bank_account_options[$post->id];
                $bank_account_string = "";
                $count = 1;
                foreach($bank_accounts as $bank_account):
                	if($count==1){
                		$bank_account_string.='('.$count.'). '.$bank_account;
                	}else{
                		$bank_account_string.=' ('.$count.'). '.$bank_account;
                	}
                	$count++;
                endforeach;
                $group_data[] = $bank_account_string;
            }else{
            	$group_data[] = "No bank account create on the group profile";
            }
		    fputcsv($fp,$group_data);
		    $group_data = array();
		}
		fclose($fp);
	}
	
	function groups_on_trial_csv(){
		header('Content-Type: application/excel');
		header('Content-Disposition: attachment; filename="groups_on_trial.csv"');
		$posts = $this->groups_m->get_groups_on_trial();
		$user_options = $this->users_m->get_options();
		$user_contact_options = $this->users_m->get_contact_options();
		$group_bank_account_options = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options();

		$fp = fopen('php://output', 'w');
		foreach ($posts as $post) {
			$group_data[] = $post->name; 
			$group_data[] = $user_options[$post->owner].'- ('.$user_contact_options[$post->owner].') '; 
            if(array_key_exists($post->id,$group_bank_account_options)){
                $bank_accounts = $group_bank_account_options[$post->id];
                $bank_account_string = "";
                $count = 1;
                foreach($bank_accounts as $bank_account):
                	if($count==1){
                		$bank_account_string.='('.$count.'). '.$bank_account;
                	}else{
                		$bank_account_string.=' ('.$count.'). '.$bank_account;
                	}
                	$count++;
                endforeach;
                $group_data[] = $bank_account_string;
            }else{
            	$group_data[] = "No bank account create on the group profile";
            }
		    fputcsv($fp,$group_data);
		    $group_data = array();
		}
		fclose($fp);
	}

	function transaction_summary($val1=0,$val2=0){
		$paying_group_ids = $this->billing_m->get_paying_group_id_array();
		$total_number_of_transactions = $this->groups_m->get_group_total_transactions($paying_group_ids);
		$total_groups = 0;
		$total_deposits = 0;
		$totalDepositAmount = 0;
		$groupscount=0;
		print_r($total_number_of_transactions);
		foreach ($total_number_of_transactions as $key => $value) {
			$value = (object)$value;
			//$total_deposits +=$value->deposits_per_month;
			if($value->totalAmount<10000){
				$totalDepositAmount +=$value->totalAmount;
				
			}

			if($value->totalAmount>=$val1 && $value->totalAmount<$val2){
					++$groupscount;
				} 
			
			$total_groups +=1;
		}

		echo $total_deposits.'<br/>';
		echo $total_groups.'<br/>';
		echo $totalDepositAmount.'<br/>';
		echo "Groups ".$groupscount.'<br/>';
	}

	function group_registration_per_month(){
		$end_month = strtotime("first day of next month");
		$last_month = strtotime("first day of August 2013");

		$months = round(($end_month - $last_month)/(24*60*60*30));
		$groups = array();
		$users = array();
		for($i=0;$i<$months;$i++){
			$from = strtotime("+ ".$i." months",$last_month);
			$to = strtotime("+ ".($i+1)." months",$last_month);
			$groups[] = array(
					date("M, Y",$from),
					$this->groups_m->count_all('','','','','',$from,$to),
					$this->groups_m->count_subscribed_groups($from,$to),
					$this->users_m->count_from_date($from,$to),
					$this->deposits_m->count_from_date($from,$to),
					number_to_currency($this->deposits_m->value_of_deposits_where($from,$to)),
					$this->withdrawals_m->count_from_date($from,$to),
					number_to_currency($this->withdrawals_m->value_of_deposits_where($from,$to)),
				);
		}
		$this->data['groups'] = $groups;
		print_r($groups);die;
		//print_r(json_encode($this->data));die;
		$response = $this->curl_post_data->curl_post_json_excel((json_encode($this->data)),'https://excel.chamasoft.com/groups/group_registration_per_month_summary',$this->application_settings->application_name.' Summary');
            print_r($response);die;

	}

	function member_distribution($lower_range=0,$upper_range=0){
		$ids = $this->groups_m->get_groups_with_members($lower_range,$upper_range);
		$years_ago = $this->input->get_post('years_ago')?:5.;
		$count = count($ids);
		
		echo 'count '.$count.'<br/>';

		$arr = array();
		foreach ($ids as $id) {
			$arr[] = $id->id;
		}
		$members = $this->members_m->count_mmebers_in_groups($arr);
		echo 'members '.$members.'<br/>';
		$res = $this->groups_m->get_group_total_transactions($arr);
		//print_r($res);die;
		$amount = 0;
		$items = 0;
		foreach ($res as $key => $value) {
			if($value['totalAmount']>=1000 && $value['totalAmount']<=15000){
				$amount += $value['totalAmount'];
				++$items;
			}
		}

		$fines = $this->groups_m->member_fines_per_group($arr);

		$total_fine_amount = 0;
		$total_fine_items = 0;

		foreach ($fines as $fine) {
			if($fine['totalAmount']<10000){
				$total_fine_amount+=$fine['totalAmount'];
				++$total_fine_items;
			}
		}


		$inactive = $this->groups_m->get_inactive_members($arr);
		$active = $this->groups_m->get_active_members($arr);
		$suspesion_rate = round(((count($inactive)/count($active) )* 100),2);

		$loans = $this->groups_m->average_loans_by_group($arr,$years_ago);
		$loan_average = $this->groups_m->average_loan_amount_by_group($arr,$years_ago);

		$stocks = $this->groups_m->get_groups_doing_stocks($arr);
		$stocks_value = $this->groups_m->get_total_value_for_groups_doing_stocks($arr);
		$assets = $this->groups_m->get_groups_doing_assets($arr);
		$assets_value = $this->groups_m->get_total_value_for_groups_doing_assets($arr);
		$money_markets = $this->groups_m->get_groups_doing_money_market($arr);
		$money_markets_value = $this->groups_m->get_total_value_for_groups_doing_money_market($arr);

		echo 'Total Amount '.number_to_currency($amount).'<br/>';
		echo 'Items '.$items.'<br/>';

		echo 'Number '.number_to_currency($amount/$items).'<br/>';

		echo 'Average Penalty Size '.number_to_currency($total_fine_amount/$total_fine_items).'<br/>';
		echo 'Average Rate of Penalization '.round((($total_fine_items/$count)*100),2).'% <br/>';
		echo 'Average suspession rate '.$suspesion_rate.'% <br/>';
		echo 'Average Loans by Group '.$loans.' <br/>';
		echo 'Average Loan Size amount Ksh '.number_to_currency($loan_average).' <br/>';
		echo 'Stocks '.$stocks.' <br/>';
		echo 'Assets '.$assets.' <br/>';
		echo 'Money Markets '.$money_markets.' <br/>';
		echo 'Stock value Ksh '.number_to_currency($stocks_value/$stocks).' <br/>';
		echo 'Assets value Ksh '.number_to_currency($assets_value/$assets).' <br/>';
		echo 'Money Market value Ksh '.number_to_currency($money_markets_value/$money_markets).' <br/>';
		
	}

	function group_life_cycle_data(){
		$paying_group_ids = $this->billing_m->get_paying_group_id_array();
		$groups_in_package = $this->groups_m->get_groups_in_package("2,4,7");
		$ids = array_merge($paying_group_ids,$groups_in_package);

		$one_year_ago = strtotime("-1 years");
		
		$one_year = $this->groups_m->groups_m->count_groups_less_than_one_year($one_year_ago,$ids);
		$more_than_one_year = $this->groups_m->groups_m->count_groups_more_than_one_year($one_year_ago,$ids);
		$withdrawals = $this->withdrawals_m->count_withdrawals_based_on_options("29,30,31,32");
		echo 'one year '.$one_year.'<br/>';
		echo 'one year older '.$more_than_one_year.'<br/>';
		echo 'withdrawals '.$withdrawals.'<br/>';
	}

	function contribution_frequency(){
		$frequencies = $this->contribution_invoices->contribution_frequency_options;
		foreach ($frequencies as $key => $frequency) {
			echo $frequency." count ".$this->contributions_m->count_contributions_based_on_frequecy($key).' and an average of KSH '.number_to_currency($this->contributions_m->average_contribution_amount_in_frequency($key)).' <br/>';
		}
	}

	function update_group_size(){
		$groups = $this->groups_m->get_all();
		foreach ($groups as $group) {
			$this->group_members->set_active_group_size($group->id);
		}
	}
	 function get_numbers($date){
        if($date){
            $date = strtotime($date);
        }else{
            $date =  time();
        }
        $from = $date;
        $to = time();
        $paying_group_ids = $this->billing_m->get_paying_group_id_array($from,$to);        
        $registered_users = $this->users_m->count_all_active_users();
        $registered_partners = $this->partners_m->count_all();
        $this->data['total_deposits'] = $this->deposits_m->get_total_deposits_by_month_array_tests($from,$to,$paying_group_ids);
       //print_r($total_deposits);
        $deposits = 0;

        // foreach ($total_deposits as $key => $total_deposit) {              	
        //     for($month = 1; $month <= count($total_deposit) ;$month++){
        //     	$total_deposit[$month] = 0;
        //     }
        // }
        // foreach ($total_deposits as $key => $total_deposit) {
        //     for($month = 1; $month <= count($total_deposit) ;$month++){
        //     	$total_deposit[$month]  += $total_deposit[$month];
        //     	$tdeposits[] = $total_deposit[$month];
        //     	//print_r( $tdeposits );
        //     }
        // }
        $this->data['number_of_loans']  = $this->loans_m->count_all_paying_groups_loans($from,$to,$paying_group_ids);
        $this->data['number_of_active_loans']  = $this->loans_m->count_all_active_loans($from,$to,$paying_group_ids);
        $this->data['total_amount_loans'] = $this->loans_m->get_total_amount_by_month_array_tests($from,$to,$paying_group_ids);
        $this->data['total_withdrawals']  = $this->withdrawals_m->get_total_withdrawals_by_month_array_tests($from,$to,$paying_group_ids);
        $this->data['user_countries'] = $this->groups_m->get_group_countries_no();
        $this->data['currency_option'] = $this->countries_m->get_currency_options();
        $this->template->title('Numbers')->build('admin/numbers',$this->data);
    }
    function withdrawal_list($date){
    	$day_strings = explode('-', $date);
    	$from = mktime(0, 0, 0, $day_strings[0] , 1, $day_strings[1]);
    	$lastday = date('t',strtotime($from));
    	$to = mktime(0, 0, 0, $day_strings[0] ,$lastday, $day_strings[1]);
        $paying_group_ids = $this->billing_m->get_paying_group_id_array(); 
        $this->data['withdrawals'] = $this->withdrawals_m->get_total_withdrawals_of_that_month($from,$to,$paying_group_ids);
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->data['member_options'] = $this->members_m->get_options();
        $this->template->title('Numbers')->build('admin/withdrawal_numbers',$this->data);

    }
    function deposit_list($date){
    	$day_strings = explode('-', $date);
    	$from = mktime(0, 0, 0, $day_strings[0] , 1, $day_strings[1]);
    	$lastday = date('t',strtotime($from));
    	$to = mktime(0, 0, 0, $day_strings[0] ,$lastday, $day_strings[1]);
        $paying_group_ids = $this->billing_m->get_paying_group_id_array(); 
        $this->data['deposits'] = $this->deposits_m->get_total_deposits_of_that_month($from,$to,$paying_group_ids);
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->data['member_options'] = $this->members_m->get_options();
        $this->template->title('Numbers')->build('admin/deposit_numbers',$this->data);
    }
    function loan_list($date){
    	$day_strings = explode('-', $date);
    	$from = mktime(0, 0, 0, $day_strings[0] , 1, $day_strings[1]);
    	$lastday = date('t',strtotime($from));
    	$to = mktime(0, 0, 0, $day_strings[0] ,$lastday, $day_strings[1]);
        $paying_group_ids = $this->billing_m->get_paying_group_id_array();
        $this->data['loans'] = $this->loans_m->get_total_loans_of_that_month($from,$to,$paying_group_ids);
        $this->data['group_options'] = $this->groups_m->get_options();
        $this->data['member_options'] = $this->members_m->get_options();
        $this->template->title('Numbers')->build('admin/loan_numbers',$this->data);
    }


    function _backup_group($group_id = 0,$delete = 0,$reset=0){
    	$this->migrate_m->backup_group($group_id,$delete,$reset);
    }

    function get_users($user_id){
        return $this->ion_auth->get_user($user_id);
    }

    function get_group($group_id = 0){
        $this->select_all_secure('investment_groups');
        $this->db->where('id',$group_id);
        return $this->db->get('investment_groups')->row();
    }

    function get_data_by_group_id($group_id=0,$table_name=''){
        $this->select_all_secure($table_name);
        $this->db->where($this->dx($table_name.'.group_id').' = "'.$group_id.'"',NULL,FALSE);
        return $this->db->get($table_name)->result();
    }

}
?>