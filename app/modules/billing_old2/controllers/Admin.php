<?php
class Admin extends Admin_Controller{

	protected $data = array();

	protected $package_validation_rules = array(
		array(
			'field' => 'name',
			'label' => 'Package Name',
			'rules' => 'required|trim|xss_clean'
		),
		array(
			'field' => 'slug',
			'label' => 'Package slug',
			'rules' => 'callback__is_unique_name'
		),
		array(
			'field' => 'billing_type',
			'label' => 'Biiling Package Type',
			'rules' => 'required|trim|xss_clean|numeric'
		),
		array(
			'field' => 'monthly_amount',
			'label' => 'Biiling Monthly Amount',
			'rules' => 'required|trim|xss_clean|currency'
		),
		array(
			'field' => 'quarterly_amount',
			'label' => 'Biiling Quarterly Amount',
			'rules' => 'required|trim|xss_clean|currency'
		),
		array(
			'field' => 'annual_amount',
			'label' => 'Biiling Annual Amount',
			'rules' => 'required|trim|xss_clean|currency'
		),
		array(
			'field' => 'enable_tax',
			'label' => 'Enable Tax',
			'rules' => 'trim|xss_clean'
		),
		array(
			'field' => 'percentage_tax',
			'label' => 'Percentage Tax',
			'rules' => 'trim|xss_clean'
		),
		array(
			'field' => 'enable_extra_member_charge',
			'label' => 'Enable Extra Charge',
			'rules' => 'trim|xss_clean'
		),
		array(
			'field' => 'monthly_pay_over',
			'label' => 'Monthly Amount',
			'rules' => 'trim|xss_clean'
		),
		array(
			'field' => 'quarterly_pay_over',
			'label' => 'Quarterly Amount',
			'rules' => 'trim|xss_clean'
		),
		array(
			'field' => 'annual_pay_over',
			'label' => 'Annual Pay',
			'rules' => 'trim|xss_clean'
		),
		array(
			'field' => 'members_limit',
			'label' => 'Members limit',
			'rules' => 'trim|xss_clean'
		),
	);

	public $invoice_validation_rules = array(
		array(
			'field' => 'group',
			'label' => 'Group',
			'rules' => 'required|trim|xss_clean|numeric'
		),
		array(
			'field' => 'billing_type',
			'label' => 'Billing package',
			'rules' => 'required|trim|xss_clean|numeric'
		),
		array(
			'field' => 'billing_date',
			'label' => 'Billing date',
			'rules' => 'required|trim'
		),
		array(
			'field' => 'due_date',
			'label' => 'Due date',
			'rules' => 'required|trim'
		),
		array(
			'field' => 'disable_prorating',
			'label' => 'Disable prorating',
			'rules' => 'trim|xss_clean'
		),
		array(
			'field' => 'billing_cycle',
			'label' => 'Billing cycle',
			'rules' => 'required|trim|xss_clean|numeric'
		),
		array(
			'field' => 'num_of_months',
			'label' => 'Number of months',
			'rules' => 'trim|xss_clean|numeric'
		),
		array(
			'field' => 'num_of_quarters',
			'label' => 'Number of quarters',
			'rules' => 'trim|xss_clean|numeric'
		),
		array(
			'field' => 'num_of_years',
			'label' => 'Number of years',
			'rules' => 'trim|xss_clean|numeric'
		),
	);

	function __construct(){
		parent::__construct();
		$this->load->model('billing_m');
		$this->load->model('groups/groups_m');
		$this->load->library('billing_settings');
	}


	function index(){

	}

	function _is_unique_name(){
		$slug = generate_slug($this->input->post('name'));
		$id = $this->input->post('id');
		if($this->billing_m->get_unique_package_by_slug($slug,$id)){
			$this->form_validation->set_message('_is_unique_name',"Package with such a name already exists");
		}
		return TRUE;
	}

	function _additional_settings(){
		if($this->input->post('enable_tax')){
			$this->package_validation_rules[] = 
				array(
					'field' => 'percentage_tax',
					'label' => 'Percentage Tax',
					'rules' => 'trim|xss_clean|required'
				);
		}

		if($this->input->post('enable_extra_member_charge')){
			$this->package_validation_rules[] = array(
				'field' => 'monthly_pay_over',
				'label' => 'Monthly Amount',
				'rules' => 'trim|xss_clean|required|currency'
			);
			$this->package_validation_rules[] = array(
				'field' => 'quarterly_pay_over',
				'label' => 'Quarterly Amount',
				'rules' => 'trim|xss_clean|required|currency'
			);
			$this->package_validation_rules[] = array(
				'field' => 'annual_pay_over',
				'label' => 'Annual Amount',
				'rules' => 'trim|xss_clean|required|currency'
			);
		}
	}

	function create_billing_package(){
		$post = new StdClass();
		$this->_additional_settings();
		$this->form_validation->set_rules($this->package_validation_rules);
		if($this->form_validation->run()){
			$input = array(
				'name' => $this->input->post('name'),
				'slug' => generate_slug($this->input->post('slug')),
				'billing_type' => $this->input->post('billing_type'),
				'monthly_amount' => currency($this->input->post('monthly_amount')),
				'quarterly_amount' => currency($this->input->post('quarterly_amount')),
				'annual_amount' => currency($this->input->post('annual_amount')),
				'enable_tax' => $this->input->post('enable_tax')?1:0,
				'percentage_tax' => currency($this->input->post('percentage_tax')),
				'enable_tax' => $this->input->post('enable_tax')?1:0,
				'enable_extra_member_charge' => $this->input->post('enable_extra_member_charge')?1:0,
				'monthly_pay_over' => currency($this->input->post('monthly_pay_over')),
				'quarterly_pay_over' => currency($this->input->post('quarterly_pay_over')),
				'annual_pay_over' =>currency($this->input->post('annual_pay_over')),
				'members_limit' =>currency($this->input->post('members_limit')),
				'active' => 1,
				'created_on' => time(),
				'created_by' => $this->user->id,
			);
			if($this->billing_m->insert_package($input)){
				$this->session->set_flashdata('success','Billing package successfully added');
				if($this->input->post('new_item')){
					redirect('admin/billing/create_billing_package');
				}
			}else{
				$this->session->set_flashdata('error','Error occurred and the billing package could not be created');
			}
			redirect('admin/billing/billing_packages');
		}
		foreach ($this->package_validation_rules as $key => $field) {
			$field_value = $field['field'];
            $post->$field_value = set_value($field['field']);
        }
        $this->data['id'] = '';
		$this->data['post'] = $post;
		$this->data['billing_types'] = array();
		$this->template->title('Create Billing Package')->build('admin/billing_package_form',$this->data);
	}

	function edit_billing_package($id=0){
		$id OR redirect('admin/billing/billing_packages');
		$post = $this->billing_m->get_package($id);
		$post OR redirect('admin/billing/billing_packages');
		$this->_additional_settings();
		$this->form_validation->set_rules($this->package_validation_rules);
		if($this->form_validation->run()){
			$update = array(
				'name' => $this->input->post('name'),
				'slug' => generate_slug($this->input->post('slug')),
				'billing_type' => $this->input->post('billing_type'),
				'monthly_amount' => currency($this->input->post('monthly_amount')),
				'quarterly_amount' => currency($this->input->post('quarterly_amount')),
				'annual_amount' => currency($this->input->post('annual_amount')),
				'enable_tax' => $this->input->post('enable_tax')?1:0,
				'percentage_tax' => currency($this->input->post('percentage_tax')),
				'enable_tax' => $this->input->post('enable_tax')?1:0,
				'enable_extra_member_charge' => $this->input->post('enable_extra_member_charge')?1:0,
				'monthly_pay_over' => currency($this->input->post('monthly_pay_over')),
				'quarterly_pay_over' => currency($this->input->post('quarterly_pay_over')),
				'annual_pay_over' =>currency($this->input->post('annual_pay_over')),
				'modified_on' => time(),
				'modified_by' => $this->user->id,
			);
			if($this->billing_m->update_package($id,$update)){
				$this->session->set_flashdata('success','Billing package successfully updated');
				if($this->input->post('new_item')){
					redirect('admin/billing/create_billing_package');
				}
			}else{
				$this->session->set_flashdata('error','Error occurred and the billing package could not be updated');
			}
			redirect('admin/billing/billing_packages');
		}else{
			foreach(array_keys($this->package_validation_rules) as $field){
                if (isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                }
            }
		}
		$this->data['id'] = $id;
		$this->data['post'] = $post;
		$this->data['billing_types'] = array();
		$this->template->title('Edit Billing Package')->build('admin/billing_package_form',$this->data);
	}

	function billing_packages(){
		$total_rows = $this->billing_m->count_all_packages();
        $pagination = create_pagination('admin/billing/billing_packages/pages',$total_rows);
        $posts = $this->billing_m->limit($pagination['limit'])->get_all_packages();
        $this->data['posts'] = $posts;
		$this->template->title('List billing packages')->build('admin/billing_packages',$this->data);
	}

	function create_billing_invoice(){
		$post = new StdClass();
		$this->form_validation->set_rules($this->invoice_validation_rules);
		
		if($this->form_validation->run()){
			$group = $this->input->post('group');
			$billing_type = $this->input->post('billing_type');
			$billing_date = $this->input->post('billing_date');
			$due_date = $this->input->post('due_date');
			$disable_prorating = $this->input->post('disable_prorating');
			$billing_cycle = $this->input->post('billing_cycle');
			$num_of_months = $this->input->post('num_of_months');
			$num_of_quarters = $this->input->post('num_of_quarters');
			$num_of_years = $this->input->post('num_of_years');
			
			$result = $this->billing_settings->create_invoice($group,$billing_type,$billing_date,$due_date,$disable_prorating,$billing_cycle,$num_of_months,$num_of_quarters,$num_of_years);

			if($result){

				$this->session->set_flashdata('success','Billing invoice successfully added');
				
				if($this->input->post('new_item')){
					redirect('admin/billing/create_billing_invoice');
				}

			}else{
				$this->session->set_flashdata('error','Error occurred and the billing invoice could not be added');
			}
		}

		foreach($this->invoice_validation_rules as $key => $field){
			$field_value = $field['field'];
            $post->$field_value = set_value($field['field']);
		}
		$this->data['id'] = '';
		$this->data['post'] = $post;
		$this->data['groups'] =  $this->groups_m->get_all_groups();
		$this->data['billing_packages'] = $this->billing_m->get_billing_packages_options();
		$this->data['billing_cycles'] = $this->billing_m->get_billing_cycles_options();
		$this->template->title('Create billing invoice')->build('admin/billing_invoice_form',$this->data);
	}

	function billing_invoices(){

	}

	function action(){
    	$action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_disable'){
            for($i=0;$i<count($action_to);$i++){
                $this->disable_billing_package($action_to[$i],FALSE);
            }
        }else if($action == 'bulk_activate'){
            for($i=0;$i<count($action_to);$i++){
                $this->activate_billing_package($action_to[$i],FALSE);
            }
        }else if($action == 'set_as_default'){
            if(count($action_to)==1){
                $this->set_default_billing_package($action_to[0],FALSE);
            }else if(count($action_to)==0){
                $this->session->set_flashdata('error','Please select one package.');
            }else{
                $this->session->set_flashdata('error','Please select one package. You cannot set more than one.');
            }
        }
        // else if($action=='bulk_void_invoice'){
        // 	for($i=0;$i<count($action_to);$i++){
        //         $this->void_invoice($action_to[$i],FALSE);
        //     }
        //     redirect('admin/billing/billing_invoices');
        // }else if($action=='bulk_void_payment'){
        // 	for($i=0;$i<count($action_to);$i++){
        //         $this->void_payment($action_to[$i],FALSE);
        //     }
        //     redirect('admin/billing/billing_payments');
        // }
        redirect('admin/billing/billing_packages');
    }

    function disable_billing_package($id=0,$redirect=TRUE){
    	$id OR redirect('admin/billing/billing_packages');
    	$post = $this->billing_m->get_package($id);
    	if(!$post){
    		$this->session->set_flashdata('error','Sorry, the package does not exist');
    		if($redirect){
    			redirect('admin/billing/billing_packages','refresh');
    		}
    		return FALSE;
    	}else{
    		if($post->active){
    			if($post->is_default){
    				$this->session->set_flashdata('error','Sorry, you cannot disable a default package. Change default then try again');
		    		if($redirect){
		    			redirect('admin/billing/billing_packages','refresh');
		    		}
		    		return FALSE;
    			}else{
    				$data = array(
		    				'active' => 0,
		    				'modified_by' => $this->user->id,
		    				'modified_on' =>time(),
	    				);
	    			if($this->billing_m->update_package($id,$data)){
	    				$this->session->set_flashdata('success','Package successfully disabled');
			    		if($redirect){
			    			redirect('admin/billing/billing_packages','refresh');
			    		}
			    		return TRUE;
	    			}
	    			else{
	    				$this->session->set_flashdata('error','Sorry, unable to disable the package');
			    		if($redirect){
			    			redirect('admin/billing/billing_packages','refresh');
			    		}
			    		return FALSE;
	    			}
    			}
    		}else{
    			$this->session->set_flashdata('error','Sorry, the package is already disabled');
	    		if($redirect){
	    			redirect('admin/billing/billing_packages','refresh');
	    		}
	    		return FALSE;
    		}
    	}
    }

    function activate_billing_package($id=0,$redirect=TRUE){
    	$id OR redirect('admin/billing/billing_packages');
    	$post = $this->billing_m->get_package($id);
    	if(!$post){
    		$this->session->set_flashdata('error','Sorry, the package does not exist');
    		if($redirect){
    			redirect('admin/billing/billing_packages','refresh');
    		}
    		return FALSE;
    	}else{
    		if(!$post->active){
    			$data = array(
	    				'active' => 1,
	    				'modified_by' => $this->user->id,
	    				'modified_on' =>time(),
    				);
    			if($this->billing_m->update_package($id,$data)){
    				$this->session->set_flashdata('success','Package successfully activated');
		    		if($redirect){
		    			redirect('admin/billing/billing_packages','refresh');
		    		}
		    		return TRUE;
    			}
    			else{
    				$this->session->set_flashdata('error','Sorry, unable to activate the package');
		    		if($redirect){
		    			redirect('admin/billing/billing_packages','refresh');
		    		}
		    		return FALSE;
    			}
    		}else{
    			$this->session->set_flashdata('error','Sorry, the package is already active');
	    		if($redirect){
	    			redirect('admin/billing/billing_packages','refresh');
	    		}
	    		return FALSE;
    		}
    	}
    }

    function set_default_billing_package($id=0,$redirect=TRUE){
    	$id OR redirect('admin/billing/billing_packages');
    	$post = $this->billing_m->get_package($id);
    	$default_package = $this->billing_m->get_default_package();
    	if(!$post){
    		$this->session->set_flashdata('error','Sorry, the package does not exist');
    		if($redirect){
    			redirect('admin/billing/billing_packages','refresh');
    		}
    		return FALSE;
    	}else{
    		if($post->active){
    			$data1 = array(
	    				'is_default' => 1,
	    				'modified_by' => $this->user->id,
	    				'modified_on' =>time(),
    				);
    			$data2 = array(
	    				'is_default' => '',
	    				'modified_by' => $this->user->id,
	    				'modified_on' =>time(),
    				);
    			if($default_package){
    				$this->billing_m->update_package($default_package->id,$data2);
    			}
    			if($this->billing_m->update_package($id,$data1)){
					$this->session->set_flashdata('success','Package successfully set as default');
		    		if($redirect){
		    			redirect('admin/billing/billing_packages','refresh');
		    		}
		    		return TRUE;
				}else{
					$this->session->set_flashdata('error','Sorry, unable to set as default package');
		    		if($redirect){
		    			redirect('admin/billing/billing_packages','refresh');
		    		}
		    		return FALSE;
				}
    		}else{
    			$this->session->set_flashdata('error','Sorry, the package is not active thus cannot be set as default');
	    		if($redirect){
	    			redirect('admin/billing/billing_packages','refresh');
	    		}
	    		return FALSE;
    		}
    	}
    }

    function receive_billing_payments(){
    	$this->data['groups'] = array();
    	$this->data['billing_packages'] = $this->billing_m->get_billing_packages_options();
    	$this->data['payment_methods'] = $this->billing_settings->payment_methods;
    	$this->template->title('Receive Billing Payments')->build('admin/receive_billing_payments',$this->data);
    }

    function billing_payments(){

    	$this->template->title('Receive Billing Payments')->build('admin/billing_payments',$this->data);
    }

}