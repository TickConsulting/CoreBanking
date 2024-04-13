<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

    protected $data = array();
    protected $package_billing_rules = array(
			    	array(
			    		'field' => 'name',
			    		'label' => 'Billing Package Name',
			    		'rules' => 'trim',
			    	),
			    	array(
			    		'field' => 'slug',
			    		'label' => 'Billing Package Name',
			    		'rules' => 'required|trim|callback__is_unique_package_name',
			    	),
			    	array(
			    		'field' => 'billing_type',
			    		'label' => 'Billing Package Type',
			    		'rules' => 'required|trim',
			    	),
			    	array(
			    		'field' => 'rate',
			    		'label' => 'Percentage Rate',
			    		'rules' => 'trim',
			    	),
			    	array(
			    		'field' => 'rate_on',
			    		'label' => 'Percetage Rate On',
			    		'rules' => 'trim',
			    	),
			    	array(
			    		'field' => 'monthly_amount',
			    		'label' => 'Monthly Amount',
			    		'rules' => 'trim|currency',
			    	),
			    	array(
			    		'field' => 'quarterly_amount',
			    		'label' => 'Quarterly Amount',
			    		'rules' => 'trim|currency',
			    	),
			    	array(
			    		'field' => 'annual_amount',
			    		'label' => 'Annual Amount',
			    		'rules' => 'trim|currency',
			    	),
			    	// array(
			    	// 	'field' => 'monthly_smses',
			    	// 	'label' => 'Monthly SMSes',
			    	// 	'rules' => 'trim|required|numeric',
			    	// ),
			    	// array(
			    	// 	'field' => 'quarterly_smses',
			    	// 	'label' => 'Quarterly SMSes',
			    	// 	'rules' => 'trim|required|numeric',
			    	// ),
			    	// array(
			    	// 	'field' => 'annual_smses',
			    	// 	'label' => 'Annual SMSes',
			    	// 	'rules' => 'trim|required|numeric',
			    	// ),
			    	array(
			    		'field' => 'enable_tax',
			    		'label' => 'Enable VAT Tax',
			    		'rules' => 'trim',
			    	),
			    	array(
			    		'field' => 'percentage_tax',
			    		'label' => 'Percentage Tax',
			    		'rules' => 'trim',
			    	),
                    array(
                        'field' => 'enable_extra_member_charge',
                        'label' => 'Extra Members Charge',
                        'rules' => 'trim',
                    ),
                    array(
                        'field' => 'monthly_pay_over',
                        'label' => 'Monthly Pay Over',
                        'rules' => 'trim',
                    ),
                    array(
                        'field' => 'quarterly_pay_over',
                        'label' => 'Quarterly Pay Over',
                        'rules' => 'trim',
                    ),
                    array(
                        'field' => 'annual_pay_over',
                        'label' => 'Annual Pay Over',
                        'rules' => 'trim',
                    ),
                    array(
                        'field' => 'display_reports',
                        'label' => 'Display in Reports',
                        'rules' => 'trim',
                    ),
                    
			    );

      protected $configuration_rules = array(
        array(
            'label' => 'Username',
            'field' => 'username',
            'rules' => 'required|trim|callback__is_unique_username',
        ),
        array(
            'label' => 'Password',
            'field' => 'password',
            'rules' => 'required',
        ),
        array(
            'label' => 'Shortcode',
            'field' => 'shortcode',
            'rules' => 'required|numeric',
        ),
        array(
            'label' => 'Access Token',
            'field' => 'access_token',
            'rules' => '',
        )
    );

    function __construct()
    {
        parent::__construct();
        $this->load->model('billing_m');
        $this->load->model('menus/menus_m');
        $this->load->model('ipn_m');
        $this->load->model('quick_action_menus/quick_action_menus_m');
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->library('billing_settings');
    }

    function index(){

    }

    function _is_unique_package_name(){
    	$slug = $this->input->post('slug');
    	$id = $this->input->post('id');
    	if($this->billing_m->is_unique_slug_package($slug,$id)){
    		$this->form_validation->set_message('_is_unique_package_name','The billing package name already exists. Change the name.');
    		return FALSE;
    	}else{
    		return TRUE;
    	}
    }

    function create_billing_package(){
    	$post = new StdClass();
    	if($this->input->post('billing_type')==1){
    		$this->package_billing_rules[] = array(
		    		'field' => 'monthly_amount',
		    		'label' => 'Monthly Amount',
		    		'rules' => 'trim|currency|required',
		    	);
			$this->package_billing_rules[] = array(
		    		'field' => 'quarterly_amount',
		    		'label' => 'Quarterly Amount',
		    		'rules' => 'trim|currency|required',
		    	);
			$this->package_billing_rules[] = array(
		    		'field' => 'annual_amount',
		    		'label' => 'Annual Amount',
		    		'rules' => 'trim|currency|required',
		    	);
    	}
    	else if($this->input->post('billing_type')==2){
    		$this->package_billing_rules[] = array(
			    		'field' => 'rate',
			    		'label' => 'Percentage Rate',
			    		'rules' => 'trim|required|numeric|greater_than[0]|less_than[100]',
			    	);
			$this->package_billing_rules[] = array(
			    		'field' => 'rate_on',
			    		'label' => 'Percetage Rate On',
			    		'rules' => 'trim|required',
			    	);
    	}
    	if($this->input->post('enable_tax')){
    		$this->package_billing_rules[] = array(
			    		'field' => 'percentage_tax',
			    		'label' => 'Percentage Tax',
			    		'rules' => 'trim|required|numeric|greater_than[0]|less_than[100]',
			    	);
    	}
        if($this->input->post('enable_extra_member_charge')){
                    $this->package_billing_rules[] = array(
                                'field' => 'monthly_pay_over',
                                'label' => 'Monthly pay over 20',
                                'rules' => 'trim|required|currency',
                            );
                    $this->package_billing_rules[] = array(
                                'field' => 'quarterly_pay_over',
                                'label' => 'Quarterly pay over 20',
                                'rules' => 'trim|required|currency',
                            );
                    $this->package_billing_rules[] = array(
                                'field' => 'annual_pay_over',
                                'label' => 'Annual pay over 20',
                                'rules' => 'trim|required|currency',
                            );
        }
    	$this->form_validation->set_rules($this->package_billing_rules);
    	if($this->form_validation->run()){
    		$billing_type = $this->input->post('billing_type');
    		$monthly_amount = '';
    		$quarterly_amount = '';
    		$annual_amount = '';
    		$rate = '';
    		$rate_on = '';
    		if($billing_type==1){
    			$monthly_amount = $this->input->post('monthly_amount')? $this->input->post('monthly_amount'):0;
    			$quarterly_amount = $this->input->post('quarterly_amount')?$this->input->post('quarterly_amount'):0;
    			$annual_amount = $this->input->post('annual_amount')?$this->input->post('annual_amount'):0;
    		}else if($billing_type==2){
    			$rate = $this->input->post('rate');
    			$rate_on = $this->input->post('rate_on');
    		}
    		$percentage_tax='';
    		$enable_tax = $this->input->post('enable_tax');
    		if($enable_tax){
    			$percentage_tax = $this->input->post('percentage_tax');
    		}
            $enable_extra_member_charge = $this->input->post('enable_extra_member_charge');
            $monthly_pay_over=0;
            $quarterly_pay_over=0;
            $annual_pay_over=0;
            if($enable_extra_member_charge){
                $monthly_pay_over = $this->input->post('monthly_pay_over')?$this->input->post('monthly_pay_over'):0;
                $quarterly_pay_over = $this->input->post('quarterly_pay_over')?$this->input->post('quarterly_pay_over'):0;
                $annual_pay_over = $this->input->post('annual_pay_over')?$this->input->post('annual_pay_over'):0;
            }
    		if($this->billing_m->count_default()){
    			$default='';
    		}else{
    			$default = 1;
    		}

    		$data = array(
    				'name' => $this->input->post('name'),
    				'slug' => $this->input->post('slug'),
    				'billing_type' => $billing_type,
    				'rate' => $rate,
    				'rate_on' => $rate_on,
    				'monthly_amount' => $monthly_amount,
    				'quarterly_amount' => $quarterly_amount,
    				'annual_amount' => $annual_amount,
    				'monthly_smses' => $this->input->post('monthly_smses'),
    				'quarterly_smses' => $this->input->post('quarterly_smses'),
    				'annual_smses' => $this->input->post('annual_smses'),
    				'enable_tax' => $enable_tax,
    				'percentage_tax' => $percentage_tax,
                    'enable_extra_member_charge'=>$enable_extra_member_charge,
                    'monthly_pay_over'=>$monthly_pay_over,
                    'quarterly_pay_over'=>$quarterly_pay_over,
                    'annual_pay_over'=>$annual_pay_over,
                    'display_reports' => $this->input->post('display_reports'),
    				'active' => 1,
    				'is_default' => $default,
    				'created_by' => $this->user->id,
    				'created_on' => time(),
    			);
    		$id = $this->billing_m->insert_package($data);
    		if($id){
    			$this->session->set_flashdata('success','Billing package successfully created');
    			if($this->input->post('new_item')){
    				redirect('admin/billing/create_billing_package');
    			}else{
    				redirect('admin/billing/billing_packages');
    			}
    		}else{
    			$this->session->set_flashdata('error','Unable to create the billing package');
    			redirect('admin/billing/billing_packages');
    		}

    	}

    	foreach ($this->package_billing_rules as $key => $field) {
             $field_value = $field['field'];
             $post->$field_value = set_value($field['field']);
        }

    	$this->data['post'] = $post;
    	$this->data['id'] = '' ;
    	$this->data['billing_type'] = $this->billing_settings->billing_type;
    	$this->data['billing_percentage_on'] = $this->billing_settings->billing_percentage_on;
        $this->template->title('Create Billing Package')->build('admin/create_billing_package_form',$this->data);
    }

    function edit_billing_package($id=0){
    	$id OR redirect('admin/billing/billing_packages');
    	$post = new StdClass();
    	if($this->input->post('billing_type')==1){
    		$this->package_billing_rules[] = array(
		    		'field' => 'monthly_amount',
		    		'label' => 'Monthly Amount',
		    		'rules' => 'trim|currency|required',
		    	);
			$this->package_billing_rules[] = array(
		    		'field' => 'quarterly_amount',
		    		'label' => 'Quarterly Amount',
		    		'rules' => 'trim|currency|required',
		    	);
			$this->package_billing_rules[] = array(
		    		'field' => 'annual_amount',
		    		'label' => 'Annual Amount',
		    		'rules' => 'trim|currency|required',
		    	);
    	}
    	else if($this->input->post('billing_type')==2){
    		$this->package_billing_rules[] = array(
			    		'field' => 'rate',
			    		'label' => 'Percentage Rate',
			    		'rules' => 'trim|required|greater_than[0]|less_than[100]',
			    	);
			$this->package_billing_rules[] = array(
			    		'field' => 'rate_on',
			    		'label' => 'Percetage Rate On',
			    		'rules' => 'trim|required',
			    	);
    	}
    	if($this->input->post('enable_tax')){
    		$this->package_billing_rules[] = array(
			    		'field' => 'percentage_tax',
			    		'label' => 'Percentage Tax',
			    		'rules' => 'trim|required|greater_than[0]|less_than[100]',
			    	);
    	}
        if($this->input->post('enable_extra_member_charge')){
                    $this->package_billing_rules[] = array(
                                'field' => 'monthly_pay_over',
                                'label' => 'Monthly pay over 20',
                                'rules' => 'trim|required|currency',
                            );
                    $this->package_billing_rules[] = array(
                                'field' => 'quarterly_pay_over',
                                'label' => 'Quarterly pay over 20',
                                'rules' => 'trim|required|currency',
                            );
                    $this->package_billing_rules[] = array(
                                'field' => 'annual_pay_over',
                                'label' => 'Annual pay over 20',
                                'rules' => 'trim|required|currency',
                            );
        }
    	$this->form_validation->set_rules($this->package_billing_rules);
    	if($this->form_validation->run()){
    		$billing_type = $this->input->post('billing_type');
    		$monthly_amount = '';
    		$quarterly_amount = '';
    		$annual_amount = '';
    		$rate = '';
    		$rate_on = '';
    		if($billing_type==1){
    			$monthly_amount = $this->input->post('monthly_amount')?$this->input->post('monthly_amount'):0;
    			$quarterly_amount = $this->input->post('quarterly_amount')?$this->input->post('quarterly_amount'):0;
    			$annual_amount = $this->input->post('annual_amount')?$this->input->post('annual_amount'):0;
    		}else if($billing_type==2){
    			$rate = $this->input->post('rate');
    			$rate_on = $this->input->post('rate_on');
    		}
    		$percentage_tax='';
    		$enable_tax = $this->input->post('enable_tax');
    		if($enable_tax){
    			$percentage_tax = $this->input->post('percentage_tax');
    		}
            $enable_extra_member_charge = $this->input->post('enable_extra_member_charge');
            $monthly_pay_over=0;
            $quarterly_pay_over=0;
            $annual_pay_over=0;
            if($enable_extra_member_charge){
                $monthly_pay_over = $this->input->post('monthly_pay_over')?$this->input->post('monthly_pay_over'):0;
                $quarterly_pay_over = $this->input->post('quarterly_pay_over')? $this->input->post('quarterly_pay_over'):0;
                $annual_pay_over = $this->input->post('annual_pay_over')? $this->input->post('annual_pay_over'):0;
            }
    		$data = array(
    				'name' => $this->input->post('name'),
    				'slug' => $this->input->post('slug'),
    				'billing_type' => $billing_type,
    				'rate' => $rate,
    				'rate_on' => $rate_on,
    				'monthly_amount' => $monthly_amount,
    				'quarterly_amount' => $quarterly_amount,
    				'annual_amount' => $annual_amount,
    				'monthly_smses' => $this->input->post('monthly_smses'),
    				'quarterly_smses' => $this->input->post('quarterly_smses'),
    				'annual_smses' => $this->input->post('annual_smses'),
                    'enable_extra_member_charge'=>$enable_extra_member_charge,
                    'monthly_pay_over'=>$monthly_pay_over,
                    'quarterly_pay_over'=>$quarterly_pay_over,
                    'annual_pay_over'=>$annual_pay_over,
    				'enable_tax' => $enable_tax,
                    'display_reports' => $this->input->post('display_reports'),
    				'percentage_tax' => $percentage_tax,
    				'modified_by' => $this->user->id,
    				'modified_on' => time(),
    			);

    		$update = $this->billing_m->update_package($id,$data);
    		if($update){
    			$this->session->set_flashdata('success','Billing package successfully created');
    			if($this->input->post('new_item')){
    				redirect('admin/billing/create_billing_package');
    			}else{
    				redirect('admin/billing/billing_packages');
    			}
    		}else{
    			$this->session->set_flashdata('error','Unable to create the billing package');
    			redirect('admin/billing/billing_packages');
    		}

    	}
    	else{
    		foreach(array_keys($this->package_billing_rules) as $field)
            {
                if (isset($_POST[$field]))
                {
                    $post->$field = $this->form_validation->$field;
                }
            }
    	}

    	$post = $this->billing_m->get_package($id);
    	$this->data['post'] = $post;
    	$this->data['id'] = $id;
    	$this->data['billing_type'] = $this->billing_settings->billing_type;
    	$this->data['billing_percentage_on'] = $this->billing_settings->billing_percentage_on;
        $this->template->title('Create Billing Package')->build('admin/create_billing_package_form',$this->data);
    }

    function billing_packages(){
    	$posts = $this->billing_m->get_all_packages();

    	$this->data['posts'] = $posts;
    	$this->data['billing_type'] = $this->billing_settings->billing_type;
    	$this->data['billing_percentage_on'] = $this->billing_settings->billing_percentage_on;
    	$this->template->title('List Billing  Packages')->build('admin/billing_packages',$this->data);
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
		    				'active' => NULL,
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

                    if($this->billing_m->update_package($default_package->id,$data2)){
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
        }else if($action=='bulk_void_invoice'){
        	for($i=0;$i<count($action_to);$i++){
                $this->void_invoice($action_to[$i],FALSE);
            }
            redirect('admin/billing/billing_invoices');
        }else if($action=='bulk_void_payment'){
        	for($i=0;$i<count($action_to);$i++){
                $this->void_payment($action_to[$i],FALSE);
            }
            redirect('admin/billing/billing_payments');
        }
        redirect('admin/billing/billing_packages');
    }


    /***************************Billing Invoices *************************/

    function _test_due_date(){
    	$billing_date = $this->input->post('billing_date');
    	$due_date = $this->input->post('due_date');
    	if(strtotime($billing_date)>strtotime($due_date)){
    		$this->form_validation->set_message('_test_due_date','The due date cannot be less that the billing date');
    		return FALSE;
    	}else{
    		return TRUE;
    	}
    }

    protected $billing_invoices_rules = array(
			    	array(
			    		'field' => 'group_id',
			    		'label' => 'Group Name',
			    		'rules' => 'trim|required|numeric',
			    	),
			    	array(
			    		'field' => 'billing_package_id',
			    		'label' => 'Billing Package Name',
			    		'rules' => 'trim|required|numeric',
			    	),
			    	array(
			    		'field' => 'billing_date',
			    		'label' => 'Billing Date',
			    		'rules' => 'trim|required',
			    	),
			    	array(
			    		'field' => 'due_date',
			    		'label' => 'Billing Due Date',
			    		'rules' => 'trim|required|callback__test_due_date',
			    	),
			    	array(
			    		'field' => 'billing_cycle',
			    		'label' => 'Billing Cycle',
			    		'rules' => 'trim|required|numeric',
			    	),
			    	array(
			    		'field' => 'amount_paid',
			    		'label' => 'Amount Paid',
			    		'rules' => 'trim|currency',
			    	),
			    	
			    );

    function create_billing_invoice(){
    	$post = new StdClass();
    	$this->form_validation->set_rules($this->billing_invoices_rules);
    	if($this->form_validation->run()){
    		$billing_cycle = $this->input->post('billing_cycle');
    		$billing_package_id = $this->input->post('billing_package_id');
    		$group_id = $this->input->post('group_id');
            $due_date = strtotime($this->input->post('due_date'));
            $billing_date = strtotime($this->input->post('billing_date'));
            $amount_paid = $this->input->post('amount_paid');
            $amount = $this->billing_settings->get_amount_payable($billing_package_id,$billing_cycle,$group_id,'','',TRUE,$billing_date);
    		if($amount){
                $billing_package_id = $amount->billing_package_id;
    			$id = $this->billing_settings->create_invoice($group_id,$billing_date,$due_date,$amount,$billing_cycle,$this->user,$amount_paid,1);
    			if($id){
    				if($this->input->post('new_item')){
    					redirect('admin/billing/create_billing_invoice','redirect');
    				}else{
    					redirect('admin/billing/billing_invoices','redirect');
    				}
    			}
    		}else{
    			$this->session->set_flashdata('error','There was an error generating amount payable');
    		}

    		redirect('admin/billing/billing_invoices');
    	}

    	foreach ($this->billing_invoices_rules as $key => $field) {
             $field_value = $field['field'];
             $post->$field_value = set_value($field['field']);
        }

        $this->data['post'] = $post;
        $this->data['billing_cycle'] = $this->billing_settings->billing_cycle;
        $this->data['groups'] = $this->groups_m->get_group_options_for_billing();
        $this->data['billing_packages'] = $this->billing_m->billing_packages_options();

        $this->template->title('Create Billing Invoice')->build('admin/create_billing_invoice',$this->data);
    }

    function billing_invoices()
    {
        if($this->input->get('filter')){
            $group_id = $this->input->get('group_id');
            $billing_cycle = $this->input->get('billing_cycle');
            $billing_date = strtotime($this->input->get('billing_date'));
            $due_date = strtotime($this->input->get('due_date'));
            $invoice_status = $this->input->get('invoice_status');
            $total_rows = $this->billing_m->count_all_active_invoices($group_id,$billing_cycle,$billing_date,$due_date,$invoice_status);
            $pagination = create_pagination('admin/billing/billing_invoices/pages', $total_rows,'',5,TRUE);
            $posts = $this->billing_m->limit($pagination['limit'])->get_all_active_invoices('',$group_id,$billing_cycle,$billing_date,$due_date,$invoice_status);
        }else{
            $total_rows = $this->billing_m->count_all_active_invoices();
            $pagination = create_pagination('admin/billing/billing_invoices/pages', $total_rows,'',5,TRUE);
            $posts = $this->billing_m->limit($pagination['limit'])->get_all_active_invoices();
        }
    	$this->data['posts'] = $posts;
    	$this->data['pagination'] = $pagination;
    	$this->data['billing_cycle'] = $this->billing_settings->billing_cycle;
        $this->data['groups'] = $this->groups_m->get_options();
        $this->data['billing_packages'] = $this->billing_m->billing_packages_options();
    	$this->template->title('List Billing Invoices')->build('admin/billing_invoices',$this->data);	
    }


    function void_invoice($id=0,$redirect=TRUE){
    	$id OR redirect('admin/billing/billing_invoices');
    	$post = $this->billing_m->get_invoice($id);
    	if(!$post){
    		$this->session->set_flashdata('error','Sorry, the invoice is not available');
    		if($redirect){
    			redirect('admin/billing/billing_invoices');
    		}
    		return FALSE;
    	}else{
    		if($post->active){
    			$this->void_payment('',FALSE,$id);
    			$invoice_data = array(
    					'active' => NULL,
    					'amount_paid'=>0,
    					'status'=>'',
    					'modified_on'=>time(),
    					'modified_by'=>$this->user->id,
    				);
    			$update = $this->billing_m->update_invoice($post->id,$invoice_data);
    			$this->billing_settings->update_invoices($post->group_id);
    			if($update){
    				$this->session->set_flashdata('success','Invoice successfully voided');
	    			if($redirect){
		    			redirect('admin/billing/billing_invoices');
		    		}
    			}else{
    				$this->session->set_flashdata('error','Unable to void the invoice');
	    			if($redirect){
		    			redirect('admin/billing/billing_invoices');
		    		}
    			}
    		}else{
    			$this->session->set_flashdata('error','Sorry, invoice already voided');
    			if($redirect){
	    			redirect('admin/billing/billing_invoices');
	    		}
    		}
    	}
    }


    function edit_billing_invoice($id=0){
    	$id OR redirect('admin/billing/billing_invoices');
    	$post = new StdClass();
    	$post = $this->billing_m->get_invoice($id);
    	if(!$post){
    		$this->session->set_flashdata('error','Billing invoice not found');
    		redirect('admin/billing/billing_invoices');
    		return FALSE;
    	}

    	$this->form_validation->set_rules($this->billing_invoices_rules);
    	if($this->form_validation->run()){
    		$billing_cycle = $this->input->post('billing_cycle');
    		$billing_package_id = $this->input->post('billing_package_id');
    		$group_id = $this->input->post('group_id');
            $billing_date = strtotime($this->input->post('billing_date'));
            $amount = $this->billing_settings->get_amount_payable($billing_package_id,$billing_cycle,$group_id,'','',TRUE,$billing_date);
            $amount_paid = $this->input->post('amount_paid');
    		if($amount){
                $update = array(
                    'group_id'=> $group_id,
                    'billing_date'=> $billing_date,
                    'due_date'=> strtotime($this->input->post('due_date')),
                    'amount'=> round($amount->amount+$amount->tax+$amount->prorated_amount,2),
                    'tax' => $amount->tax,
                    'prorated_amount' => $amount->prorated_amount,
                    'billing_cycle' => $billing_cycle,
                    'billing_package_id'=>$billing_package_id,
                    'modified_by' => $this->user->id,
                    'modified_on' => time(),
                    'amount_paid' => ($amount_paid==number_to_currency($post->amount_paid))?0:$amount_paid,
                );
    			if($this->billing_m->update_invoice($post->id,$update)){
    				$this->void_payment('',FALSE,$id);
    				if($amount_paid && ($amount_paid!=number_to_currency($post->amount_paid))){
    					$payment = $this->billing_settings->record_billing_payments($amount_paid,$group_id,$amount->billing_package_id,$id,time(),1,'','Created from a invoice generated manually',$this->user->id);
    					if($payment){
    						$this->session->set_flashdata('success','Payment successfully recorded');
    					}else{
    						$this->session->set_flashdata('error','Unable to record payment');
    					}
    				}
    				$this->billing_settings->update_invoices($group_id);
    				$this->session->set_flashdata('success','Billing invoice successfully updated');
    				if($this->input->post('new_item')){
    					redirect('admin/billing/create_billing_invoice','redirect');
    				}else{
    					redirect('admin/billing/billing_invoices','redirect');
    				}
    			}
    		}else{
    			$this->session->set_flashdata('error','There was an error generating amount payable');
    		}

    		redirect('admin/billing/billing_invoices');
    	}
    	else{
    		foreach(array_keys($this->billing_invoices_rules) as $field)
            {
                if (isset($_POST[$field]))
                {
                    $post->$field = $this->form_validation->$field;
                }
            }
    	}


    	$this->data['post'] = $post;
        $this->data['billing_cycle'] = $this->billing_settings->billing_cycle;
        $this->data['groups'] = $this->groups_m->get_group_options_for_billing();
        $this->data['billing_packages'] = $this->billing_m->billing_packages_options();

        $this->template->title('Create Billing Invoice')->build('admin/create_billing_invoice',$this->data);
    }

    function invoice_export_pdf($id=0,$group_id=0){
        $post = $this->billing_m->get_group_billing_invoice($id,$group_id);
        if(empty($post)){
            return FALSE;
        }
        $this->data['package'] = $this->billing_m->get_package($post->billing_package_id);
        $this->data['post'] = $post;
        $this->data['billing_cycles'] = $this->billing_settings->billing_cycle;
        $this->data['group'] = $this->groups_m->get_group_owner($post->group_id);
        $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        $this->data['application_settings'] = $this->application_settings;
        $arrears= $this->billing_m->get_group_account_arrears($post->group_id);
        if($post->amount_paid){
            if($post->amount_paid > $post->amount){
                $this->data['balance'] = $arrears - $post->amount;
            }elseif($post->amount_paid >= $post->amount) {
                $this->data['balance'] = $arrears;
            }          
        }elseif($post->amount_paid == 0){
            $this->data['balance'] = $arrears - $post->amount;
        }else{
            $this->data['balance'] = 0;
        }
        $json_file = json_encode($this->data);
        $response = $this->curl_post_data->curl_post_json_pdf($json_file,'https://pdfs.chamasoft.com/billing_invoices',$this->data['group']->name.' - '.$this->application_settings->application_name.' billing invoice ');
        print_r($response);die;
    }


    /********************Billing payments********/
    function receipt_export_pdf($id=0,$group_id=0){
        $post = $this->billing_m->get_group_billing_receipt($id,$group_id);
        if(empty($post)){
            return FALSE;
        }

        $this->data['package'] = $this->billing_m->get_package($post->billing_package_id);
        $this->data['post'] = $post;
        $this->data['payment_methods'] = $this->billing_settings->payment_method;
        $this->data['group'] = $this->groups_m->get_group_owner($post->group_id);
        $this->data['group_logo'] = site_url('uploads/logos/'.$this->application_settings->paper_header_logo);
        $response = $this->curl_post_data->curl_post_json_pdf((json_encode($this->data)),'https://pdfs.chamasoft.com/billing_receipts',$this->data['group']->name.' - '.$this->application_settings->application_name.' billing receipt ');
        print_r($response);die;
    }
    protected $billing_payment_rules = array(
                array(
                    'field' => 'receipt_date',
                    'label' => 'Bill Payment Date',
                    'rules' => 'date|required|trim',
                ),
                array(
                    'field' => 'group_id',
                    'label' => 'Group Name',
                    'rules' => 'numeric|required|trim',
                ),
                array(
                    'field' => 'billing_package_id',
                    'label' => 'Bill Package',
                    'rules' => 'numeric|required|trim',
                ),
                array(
                    'field' => 'payment_method',
                    'label' => 'Payment Method',
                    'rules' => 'numeric|required|trim',
                ),
                array(
                    'field' => 'ipn_transaction_code',
                    'label' => 'Transaction code',
                    'rules' => 'trim',
                ),
                array(
                    'field' => 'amount',
                    'label' => 'Amount Paid',
                    'rules' => 'currency|required|trim',
                ),
                array(
                    'field' => 'description',
                    'label' => 'Payment Description',
                    'rules' => 'trim',
                ),

        );

    function billing_payments(){
        if($this->input->get('filter')){
            $group_id = $this->input->get('group_id');
            $payment_method = $this->input->get('payment_method');
            $receipt_date = strtotime($this->input->get('receipt_date'));

            $total_rows = $this->billing_m->count_all_active_payments($group_id,$payment_method,$receipt_date);
            $pagination = create_pagination('admin/billing/billing_payments/pages', $total_rows,'',5,TRUE);
            $posts = $this->billing_m->limit($pagination['limit'])->get_all_active_payments('',$group_id,$payment_method,$receipt_date);
        }else{
            $total_rows = $this->billing_m->count_all_active_payments();
            $pagination = create_pagination('admin/billing/billing_payments/pages', $total_rows,'',5,TRUE);
            $posts = $this->billing_m->limit($pagination['limit'])->get_all_active_payments();
        }
    	$this->data['posts'] = $posts;
    	$this->data['pagination'] = $pagination;
    	$this->data['billing_cycle'] = $this->billing_settings->billing_cycle;
        $this->data['groups'] = $this->groups_m->get_options();
        $this->data['billing_packages'] = $this->billing_m->billing_packages_options();
        $this->data['payment_methods'] = $this->billing_settings->payment_method;
    	$this->template->title('List Billing Payments')->build('admin/billing_payments',$this->data);	
    }

    function billing_payments_information_listing(){
        $minimum_amount = 290;
        if($this->input->get('filter')){
            $group_id = $this->input->get('group_id');
            $payment_method = $this->input->get('payment_method');
            $receipt_date_from = strtotime($this->input->get('receipt_date_from'));
            $receipt_date_to = strtotime($this->input->get('receipt_date_to'));
            $total_rows = $this->billing_m->count_all_active_payments($group_id,$payment_method,"",$minimum_amount,$receipt_date_from,$receipt_date_to);
            $pagination = create_pagination('admin/billing/billing_invoices/pages', $total_rows);
            $posts = $this->billing_m->limit($pagination['limit'])->get_all_active_payments('',$group_id,$payment_method,'',$minimum_amount,$receipt_date_from,$receipt_date_to);
        }else{
            $total_rows = $this->billing_m->count_all_active_payments('','','','',$minimum_amount);
            $pagination = create_pagination('admin/billing/billing_invoices/pages', $total_rows);
            $posts = $this->billing_m->limit($pagination['limit'])->get_all_active_payments('','','','',$minimum_amount);
        }
        $this->data['posts'] = $posts;
        $this->data['pagination'] = $pagination;
        $this->data['billing_cycle'] = $this->billing_settings->billing_cycle;
        $this->data['groups'] = $this->groups_m->get_options();
        $this->data['group_bank_account_options'] = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options(FALSE);
        $this->data['group_account_number_options'] = $this->groups_m->get_group_account_number_options();
        $this->data['billing_packages'] = $this->billing_m->billing_packages_options();
        $this->data['payment_methods'] = $this->billing_settings->payment_method;
        $this->template->title('List Billing Payments Information')->build('admin/billing_payments_information_listing',$this->data);
    }

    function billing_payments_information_csv(){
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment; filename="paying_groups.csv"');
        $minimum_amount = 290;
        if($this->input->get('filter')){
            $group_id = $this->input->get('group_id');
            $payment_method = $this->input->get('payment_method');
            $receipt_date_from = strtotime($this->input->get('receipt_date_from'));
            $receipt_date_to = strtotime($this->input->get('receipt_date_to'));
            $total_rows = $this->billing_m->count_all_active_payments($group_id,$payment_method,"",$minimum_amount,$receipt_date_from,$receipt_date_to);
            $pagination = create_pagination('admin/billing/billing_invoices/pages', $total_rows);
            $posts = $this->billing_m->limit($pagination['limit'])->get_all_active_payments('',$group_id,$payment_method,'',$minimum_amount,$receipt_date_from,$receipt_date_to);
        }else{
            $total_rows = $this->billing_m->count_all_active_payments('','','','',$minimum_amount);
            $pagination = create_pagination('admin/billing/billing_invoices/pages', $total_rows);
            $posts = $this->billing_m->limit($pagination['limit'])->get_all_active_payments('','','','',$minimum_amount);
        }
        $this->data['posts'] = $posts;
        $this->data['pagination'] = $pagination;
        $this->data['groups'] = $this->groups_m->get_options();
        $payment_methods = $this->billing_settings->payment_method;
        $group_bank_account_options = $this->bank_accounts_m->get_group_bank_account_group_id_as_key_options(FALSE);
        $group_account_number_options = $this->groups_m->get_group_account_number_options();
        $fp = fopen('php://output','w');
        $group_data[] = "Activation Date";
        $group_data[] = "Group Name";
        $group_data[] = "Bank Account Numbers";
        $group_data[] = "Payment Details";
        $group_data[] = "Amount Paid";
        fputcsv($fp,$group_data);
        $group_data = array();
        foreach ($posts as $post) {
            # code...
            $group_data[] = timestamp_to_date($post->receipt_date,TRUE);
            $group_data[] = $group_account_number_options[$post->group_id];
            $bank_account_string = "";
            if(isset($group_bank_account_options[$post->group_id])){
                $bank_accounts = $group_bank_account_options[$post->group_id];
                $count = 1;
                foreach($bank_accounts as $bank_account):
                    if($count==1){
                        $bank_account_string= "'".$bank_account."'";
                    }else{
                        $bank_account_string.=",'".$bank_account."'";
                    }
                    $count++;
                endforeach;
            }
            $group_data[] = $bank_account_string;
            $payment_details = "";
            $payment_method = $post->payment_method?$post->payment_method:1;
            $payment_details .= $payment_methods[$payment_method].' payment';
            if($post->description){
                $payment_details .=' - '.$post->description;
            }
            $group_data[] = $payment_details;
            $group_data[] = number_to_currency($post->amount);
            fputcsv($fp,$group_data);
            $group_data = array();
        }
        fclose($fp);
    }

    function void_payment($id=0,$redirect=TRUE,$invoice_id=0){
    	if(!$id && !$invoice_id){
    		redirect('admin/billing/billing_payments');
    	}if($id){
    		$post = $this->billing_m->get_payment($id);
    	}else if($invoice_id){
    		$post = $this->billing_m->get_payment_by_billing_invoice_id($invoice_id);
    	}
    	if(!$post){
    		if($redirect){
    			$this->session->set_flashdata('error','Sorry, the payment is not available');
    			redirect('admin/billing/billing_payments');
    		}
    		return FALSE;
    	}else{
    		if($post->active){
    			$payment_data = array(
						'active' => NULL,
						'modified_on'=>time(),
						'modified_by'=>$this->user->id,
					);
    			$update = $this->billing_m->update_payment($post->id,$payment_data);
    			$this->billing_settings->update_invoices($post->group_id);
    			if($update){
    				$this->session->set_flashdata('success','Payment successfully voided');
	    			if($redirect){
		    			redirect('admin/billing/billing_payments');
		    		}
                    return TRUE;
    			}else{
    				$this->session->set_flashdata('error','Unable to void the payment');
	    			if($redirect){
		    			redirect('admin/billing/billing_payments');
		    		}
                    return FALSE;
    			}
    		}else{
    			$this->session->set_flashdata('error','Sorry, payment already voided');
    			if($redirect){
	    			redirect('admin/billing/billing_payments');
	    		}
                return FALSE;
    		}
    	}
    }

    function receive_billing_payments(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['payment_dates'])){
                    $count = count($posts['payment_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['payment_dates'][$i])&&isset($posts['groups'][$i])&&isset($posts['billing_packages'][$i])&&isset($posts['payment_methods'][$i])&&isset($posts['amounts'][$i])):    
                            //Deposit dates
                            if($posts['payment_dates'][$i]==''){
                                $successes['payment_dates'][$i] = 0;
                                $errors['payment_dates'][$i] = 1;
                                $error_messages['payment_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['payment_dates'][$i] = 1;
                                $errors['payment_dates'][$i] = 0;
                            }
                            //Members
                            if($posts['groups'][$i]==''){
                                $successes['groups'][$i] = 0;
                                $errors['groups'][$i] = 1;
                                $error_messages['groups'][$i] = 'Please select a group';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['groups'][$i])){
                                    $successes['groups'][$i] = 1;
                                    $errors['groups'][$i] = 0;
                                }else{
                                    $successes['groups'][$i] = 0;
                                    $errors['groups'][$i] = 1;
                                    $error_messages['groups'][$i] = 'Please enter a valid group value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Billing Packages
                            if($posts['billing_packages'][$i]==''){
                                $successes['billing_packages'][$i] = 0;
                                $errors['billing_packages'][$i] = 1;
                                $error_messages['billing_packages'][$i] = 'Please select a billing package';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['billing_packages'][$i])){
                                    $successes['billing_packages'][$i] = 1;
                                    $errors['billing_packages'][$i] = 0;
                                }else{
                                    $successes['billing_packages'][$i] = 0;
                                    $errors['billing_packages'][$i] = 1;
                                    $error_messages['billing_packages'][$i] = 'Please select a valid billing package value';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            
                            //Payment Method
                            if($posts['payment_methods'][$i]==''){
                                $successes['payment_methods'][$i] = 0;
                                $errors['payment_methods'][$i] = 1;
                                $error_messages['payment_methods'][$i] = 'Please select a payment method';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['payment_methods'][$i])){
                                    $successes['payment_methods'][$i] = 1;
                                    $errors['payment_methods'][$i] = 0;
                                }else{
                                    $successes['payment_methods'][$i] = 0;
                                    $errors['payment_methods'][$i] = 1;
                                    $error_messages['payment_methods'][$i] = 'Please enter a valid payment method';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //amounts
                            if($posts['amounts'][$i]==''){
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter payment amount';
                                $entries_are_valid = FALSE;
                            }else{
                                if(valid_currency($posts['amounts'][$i])){
                                    $successes['amounts'][$i] = 1;
                                    $errors['amounts'][$i] = 0;
                                }else{
                                    $successes['amounts'][$i] = 0;
                                    $errors['amounts'][$i] = 1;
                                    $error_messages['amounts'][$i] = 'Please enter a valid payment amount';
                                    $entries_are_valid = FALSE; 
                                }
                            }
                        endif;
                    endfor;
                }
            }
            if($entries_are_valid){
                $this->session->set_flashdata('error','');
                if(isset($posts['payment_dates'])){
                    $count = count($posts['payment_dates']);
                    $successful_billing_payment_entry_count = 0;
                    $unsuccessful_billing_payment_entry_count = 0;
                    $count = count($posts['payment_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['payment_dates'][$i])&&isset($posts['groups'][$i])&&isset($posts['billing_packages'][$i])&&isset($posts['amounts'][$i])&&isset($posts['payment_methods'][$i])):    
                            $amount = valid_currency($posts['amounts'][$i]);
                            $description = $posts['payment_descriptions'][$i]?$posts['payment_descriptions'][$i]:'';
                            $group_id = $posts['groups'][$i];
                            $package_id = $posts['billing_packages'][$i];
                            $receipt_date = strtotime($posts['payment_dates'][$i]);
                            $billing_invoice_id = $posts['billing_invoices_id'];
                            $payment_method = $posts['payment_methods'][$i];
                            $ipn_transaction_code = $posts['transaction_codes'][$i];
                            $created_by = $this->user->id;

                            if($this->billing_settings->record_billing_payments($amount,$group_id,$package_id,$billing_invoice_id,$receipt_date,$payment_method,$ipn_transaction_code,$description,$created_by,TRUE)){
                                ++$successful_billing_payment_entry_count;
                            }else{
                                ++$unsuccessful_billing_payment_entry_count;
                            }
                        endif;
                    endfor;
                }
                if($successful_billing_payment_entry_count){
                    if($successful_billing_payment_entry_count==1){
                        $this->session->set_flashdata('success',$successful_billing_payment_entry_count.' billing payment successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_billing_payment_entry_count.' billing payments successfully recorded. ');
                    }
                }
                if($unsuccessful_billing_payment_entry_count){
                    if($unsuccessful_billing_payment_entry_count==1){
                        $this->session->set_flashdata('error',$unsuccessful_billing_payment_entry_count.' billing payment was not successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('error',$unsuccessful_billing_payment_entry_count.' billing payments were not successfully recorded. ');
                    }
                }
                redirect('admin/billing/billing_payments');
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
        }
        $this->data['errors'] = $errors;
        $this->data['error_messages'] = $error_messages;
        $this->data['successes'] = $successes;
        $this->data['posts'] = $posts;
        $this->data['groups'] = $this->groups_m->get_group_options_for_billing();
        $this->data['billing_packages'] = $this->billing_m->billing_packages_options();
        $this->data['payment_methods'] = $this->billing_settings->payment_method;
        $this->template->title('Receive Billing Payments')->build('admin/receive_billing_payments',$this->data);
    }


    function edit_billing_payment($id=0){
        $id OR redirect('admin/billing/edit_billing_payment');
        $post = $this->billing_m->get_payment($id);
        if(!$post){
            $this->session->set_flashdata('error','Billing payment not available');
            redirect('admin/billing/edit_billing_payment');
            return FALSE;
        }

        $this->form_validation->set_rules($this->billing_payment_rules);
        if($this->form_validation->run()){
            if($this->void_payment($id,FALSE)){
                $amount = $this->input->post('amount');
                $group_id = $this->input->post('group_id');
                $package_id = $this->input->post('billing_package_id');
                $billing_invoice_id = $post->billing_invoice_id;
                $receipt_date = $this->input->post('receipt_date');
                $payment_method = $this->input->post('payment_method');
                $ipn_transaction_code = $this->input->post('ipn_transaction_code');
                $description = $this->input->post('description');
                $created_by = $this->user->id;
                if($this->billing_settings->record_billing_payments($amount,$group_id,$package_id,$billing_invoice_id,$receipt_date,$payment_method,$ipn_transaction_code,$description,$created_by)){
                    $this->session->set_flashdata('success','Billing payment success recorded');
                }else{

                }
            }else{
                $this->session->set_flashdata('error','Unable to void and re-record billing payment');
            }
            redirect('admin/billing/billing_payments','refresh');
        }else{
            foreach(array_keys($this->billing_payment_rules) as $field)
            {
                if (isset($_POST[$field]))
                {
                    $post->$field = $this->billing_payment_rules->$field;
                }
            }
        }

        $this->data['post'] = $post;
        $this->data['groups'] = $this->groups_m->get_group_options_for_billing();
        $this->data['billing_packages'] = $this->billing_m->billing_packages_options();
        $this->data['payment_methods'] = $this->billing_settings->payment_method;
        $this->template->title('Edit Billing Payment')->build('admin/edit_billing_payment',$this->data);
    }


    /******Menu Pairing***/

    protected $menu_pairing_rules = array(
                                        array(
                                            'field' => 'menu_id[]',
                                            'label' => 'Menu Name',
                                            'rules' => 'trim',
                                        ),
                                        array(
                                            'field' => 'quick_action_menu_id[]',
                                            'label' => 'Quick Action Menu Name',
                                            'rules' => 'trim',
                                        ),

                                    );


    function menu_pairing($id=0){
        $id OR redirect('admin/billing/billing_packages');
        $post = new StdClass;
        $package = $this->billing_m->get_package($id);
        if(!$post){
            $this->session->set_flashdata('error','Billing Package not found');
            redirect('admin/billing/billing_packages');
            return FALSE;
        }
        $menu_pairings = $this->billing_m->get_package_menu_pairing($id);
        $qucik_action_menu_pairings = $this->billing_m->get_package_quick_action_menu_pairing($id);
        $this->form_validation->set_rules($this->menu_pairing_rules);
        if($this->form_validation->run()){
            $menus = $this->input->post('menu_id');
            $quick_action_menus = $this->input->post('quick_action_menu_id');
            $successes = 0;
            $fail = 0;
            $this->billing_m->delete_package_menu_pairing($menu_pairings);
            $this->billing_m->delete_package_menu_pairing($qucik_action_menu_pairings);
            if($menus){
                foreach ($menus as $menu) 
                {
                    $update = $this->billing_m->insert_menu_package_pairing(array(
                            'package_id' => $package->id,
                            'menu_id' => $menu,
                            'active' => 1,
                            'type' => 1,
                            'created_by' => $this->user->id,
                            'created_on' => time(),
                        ));
                    if($update){
                        ++$successes;
                    }else{
                        ++$fail;
                    }
                } 
            }
           
           if($quick_action_menus){
                foreach($quick_action_menus as $quick_action_menu) 
                {
                    $update2 = $this->billing_m->insert_menu_package_pairing(array(
                            'package_id' => $package->id,
                            'menu_id' => $quick_action_menu,
                            'active' => 1,
                            'type' => 2,
                            'created_by' => $this->user->id,
                            'created_on' => time(),
                        ));
                    if($update2){
                        ++$successes;
                    }else{die;
                        ++$fail;
                    }
                }
           }
           if(!$quick_action_menus && !$menus)
           {
                $this->session->set_flashdata('success','Menus successfully updated');
           }else{
                if($successes){
                    $this->session->set_flashdata('success',$successes.' menus successfully added');
                }else{
                    $this->session->set_flashdata('error',$fail.' menus not added');
                }
            }
            
            redirect('admin/billing/menu_pairing/'.$id,'refresh');
        }
        $this->data['menu_options'] = $this->menus_m->get_menu_options();
        $this->data['quick_action_menu_options'] = $this->quick_action_menus_m->get_menu_options();
        $this->data['menu_pairings'] = $menu_pairings;
        $this->data['qucik_action_menu_pairings'] = $qucik_action_menu_pairings;
        $this->template->title($package->name.' Menu Pairing')->build('admin/menu_pairing',$this->data); 
    }

    /**************SMS Payments************/

    function billing_sms_payments(){
        if($this->input->get('filter')){
            $group_id = $this->input->get('group_id');
            $payment_method = $this->input->get('payment_method');
            $receipt_date = strtotime($this->input->get('receipt_date'));

            $total_rows = $this->billing_m->count_all_active_sms_payments($group_id,$payment_method,$receipt_date);
            $pagination = create_pagination('admin/billing/billing_invoices/pages', $total_rows);
            $posts = $this->billing_m->limit($pagination['limit'])->get_all_active_sms_payments('',$group_id,$payment_method,$receipt_date);
        }else{
            $total_rows = $this->billing_m->count_all_active_sms_payments();
            $pagination = create_pagination('admin/billing/billing_invoices/pages', $total_rows);
            $posts = $this->billing_m->limit($pagination['limit'])->get_all_active_sms_payments();
        }
        $this->data['posts'] = $posts;
        $this->data['pagination'] = $pagination;
        $this->data['billing_cycle'] = $this->billing_settings->billing_cycle;
        $this->data['groups'] = $this->groups_m->get_options();
        $this->data['payment_methods'] = $this->billing_settings->payment_method;
        $this->template->title('List Billing SMS Payments')->build('admin/billing_sms_payments',$this->data);   
    }


    /************IPNS****************/

    function list_ipns(){
        $total_rows = $this->ipn_m->count_all();
        $pagination = create_pagination('admin/billing/billing_invoices/pages', $total_rows);
        $posts = $this->ipn_m->limit($pagination['limit'])->get_all();

        $this->data['posts'] = $posts;
        $this->data['ipn_depositor'] = $this->billing_settings->ipn_depositor;
        $this->data['ipn_status'] = $this->billing_settings->ipn_status;
        $this->template->title('List IPNs')->build('admin/list_ipns',$this->data);
    }

    function delete_ipn($id=0){
        $id OR redirect('admin/billing/list_ipns');
        $result = $this->ipn_m->delete_ipn_entry($id);
        if($result){
            $this->session->set_flashdata('success','Ipn successfully deleted');
        }else{
            $this->session->set_flashdata('error','Ipn not deleted');
        }
        redirect('admin/billing/list_ipns');
    }

    /*********IPN Forwading*************/

    protected $forwarder_rules = array(
            array(
                    'field' => 'title',
                    'label' => 'Forwarder Title',
                    'rules' => 'required|trim'
            ),
            array(
                    'field' => 'equity_ipn_end_point',
                    'label' => 'Equity IPN EndPoint',
                    'rules' => 'required|trim'
            ),
            array(
                    'field' => 'account_validation_end_point',
                    'label' => 'Account Validation EndPoint',
                    'rules' => 'required|trim'
            ),
            array(
                    'field' => 'mpesa_validation_end_point',
                    'label' => 'Mpesa Validation Endpoint',
                    'rules' => 'required|trim'
            ),
            array(
                    'field' => 'mpesa_confirmation_end_point',
                    'label' => 'Mpesa Confirmation Endpoint',
                    'rules' => 'required|trim'
            ),
        );
    function create_billing_payment_forwader(){
        $post = new StdClass();
        $forwarder_types =array('all'=>'All Gateways')+$this->billing_settings->ipn_depositor;

        $this->form_validation->set_rules($this->forwarder_rules);
        if($this->form_validation->run()){
            $id = $this->billing_m->insert_billing_payments_forwarder(array(
                    'title' => $this->input->post('title'),
                    'account_validation_end_point' => $this->input->post('account_validation_end_point'),
                    'equity_ipn_end_point' => $this->input->post('equity_ipn_end_point'),
                    'mpesa_validation_end_point' => $this->input->post('mpesa_validation_end_point'),
                    'mpesa_confirmation_end_point' => $this->input->post('mpesa_confirmation_end_point'),
                    'created_by' => $this->user->id,
                    'created_on' => time(),
                    'active' => 1
                ));
            if($id){
                $this->session->set_flashdata('success','IPN Forwader for '.$this->input->post('title').' created successfully');
                if($this->input->post('new_item')){
                    redirect('admin/billing/create_billing_payment_forwader');
                    return FALSE;
                }
            }else{
                $this->session->set_flashdata('Error','Cannot add Forwarder');
            }
            redirect('admin/billing/billing_payments_forwarder_listing');

        }
        foreach ($this->forwarder_rules as $key => $field) {
             $post->$field['field'] = set_value($field['field']);
        }

        $this->data['forwarder_types']=$forwarder_types;
        $this->data['post']=$post;
        $this->template->title('Create IPN Forwarder')->build('admin/create_ipn_forwader',$this->data);
    }

    function billing_payments_forwarder_listing(){
        $posts = $this->billing_m->get_ipn_forwarders();


        $this->data['posts'] = $posts;
        $this->template->title('IPN Forwarder Listing')->build('admin/list_ipn_forwader',$this->data);
    }


    function edit_billing_payments_forwarder($id=0){
        $id OR redirect('admin/billing/billing_payments_forwarder_listing');
        $post = $this->billing_m->get_billing_payment_forwarder($id);
        if(!$post){
            $this->session->set_flashdata('error','Does not exists');
            redirect('admin/billing/billing_payments_forwarder_listing');
            return FALSE;
        }
        $this->form_validation->set_rules($this->forwarder_rules);
        if($this->form_validation->run()){
            $update_id = $this->billing_m->update_billing_payments_forwarder($id,array(
                    'title' => $this->input->post('title'),
                    'account_validation_end_point' => $this->input->post('account_validation_end_point'),
                    'equity_ipn_end_point' => $this->input->post('equity_ipn_end_point'),
                    'mpesa_validation_end_point' => $this->input->post('mpesa_validation_end_point'),
                    'mpesa_confirmation_end_point' => $this->input->post('mpesa_confirmation_end_point'),
                    'modified_by' => $this->user->id,
                    'modified_on' => time(),
                ));
            if($update_id){
                $this->session->set_flashdata('success','Payment Forwader for '.$this->input->post('title').' updated successfully');
                if($this->input->post('new_item')){
                    redirect('admin/billing/create_billing_payment_forwader');
                    return FALSE;
                }
            }else{
                $this->session->set_flashdata('Error','Cannot update Forwarder');
            }
            redirect('admin/billing/billing_payments_forwarder_listing');
        }else{
            foreach(array_keys($this->forwarder_rules) as $field)
            {
                if (isset($_POST[$field]))
                {
                    $post->$field = $this->forwarder_rules->$field;
                }
            }
        }

        $this->data['post'] = $post;
        $this->template->title('Create IPN Forwarder')->build('admin/create_ipn_forwader',$this->data);
    }

    function delete_billing_payments_forwarder($id=0){
        $id OR redirect('admin/billing/billing_payments_forwarder_listing');
        $post = $this->billing_m->get_billing_payment_forwarder($id);
        if(!$post){
            $this->session->set_flashdata('error','Does not exists');
            redirect('admin/billing/billing_payments_forwarder_listing');
            return FALSE;
        }
        else{
            $delete_id = $this->billing_m->delete_billing_payments_forwarder($id);
            if($delete_id){
                $this->session->set_flashdata('success','Deleted successfully'); 
            }else{
              $this->session->set_flashdata('error','Unable to delete');  
            } 
        }
        redirect('admin/billing/billing_payments_forwarder_listing');
    }



    /****configurations*****/

    function _is_unique_username(){
        $username = $this->input->post('username');
        $id = $this->input->post('id');
        $shortcode = $this->input->post('shortcode');
        if($this->ipn_m->is_unique_username($username,$id,$shortcode)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_is_unique_username','Kindly use a different username');
            return FALSE;
        }
    }

    function create_safaricom_configurations(){
        $post = new StdClass();
        $this->form_validation->set_rules($this->configuration_rules);
        if($this->form_validation->run()){
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $api_key = $this->input->post('api_key');
            $shortcode = $this->input->post('shortcode');
            $input = array(
                'username' => $username,
                'password' => $password,
                'shortcode' => $shortcode,
                'api_key' => "Basic ".base64_encode($username.':'.$password),
                'active' => 1,
                'created_by' => $this->user->id,
                'created_on' => time(),
            );
            if($this->ipn_m->insert_configuration($input)){
                $this->session->set_flashdata('success','Successfully created configuration');
            }else{
                $this->session->set_flashdata('error','Error occured creating configuration');
            }
            redirect('admin/billing/safaricom_configuration_listing');
        }else{
            foreach ($this->configuration_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value  = set_value($field['field']);
            }
        }
        $this->data['id'] = '';
        $this->data['post'] = $post;
        $this->template->title('Create Safaricom Configurations')->build('admin/safaricom_configuration_form',$this->data);
    }

    function edit_safaricom_configuration($id=0){
        $id OR redirect('admin/billing/safaricom_configuration_listing');
        $post = $this->ipn_m->get_configuation($id);
        if(!$post){
            $this->session->set_flashdata('error','Configuration not found');
            redirect('admin/billing/safaricom_configuration_listing');
        }
        $this->form_validation->set_rules($this->configuration_rules);
        if($this->form_validation->run()){
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $api_key = $this->input->post('api_key');
            $shortcode = $this->input->post('shortcode');
            $access_token = $this->input->post('access_token');
            $update = array(
                'username' => $username,
                'password' => $password,
                'shortcode' => $shortcode,
                'api_key' => "Basic ".base64_encode($username.':'.$password),
                'modified_by' => $this->user->id,
                'modified_on' => time(),
                'access_token' => $access_token,
                'access_token_expires_at' => strtotime("+30 minutes",time()),
                'access_token_type' => 'Bearer',
            );
            if($this->ipn_m->update_configuration($post->id,$update)){
                $this->session->set_flashdata('success','Successfully updated configuration');
            }else{
                $this->session->set_flashdata('error','Error occured updating configuration');
            }
            redirect('admin/safaricom/safaricom_configuration_listing');
        }else{
            foreach ($this->configuration_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value= $post->$field_value?$post->$field_value:set_value($field_value);
            }
        }
        $this->data['id'] = $id;
        $this->data['post'] = $post;
        $this->template->title('Edit Safaricom Configurations')->build('admin/safaricom_configuration_form',$this->data);
    }

    function safaricom_configuration_listing(){
        $posts = $this->ipn_m->get_configurations();
        $this->data['posts'] = $posts;
        $this->template->title('List Safaricom Configurations')->build('admin/safaricom_list_configuration',$this->data);
    }

    function delete_configuration($id=0){
        $id OR redirect('admin/billing/safaricom_configuration_listing');
        $post = $this->ipn_m->get_configuation($id);
        if(!$post){
            $this->session->set_flashdata('error','Configuration not found');
            redirect('admin/billing/safaricom_configuration_listing');
        }
        if($this->ipn_m->delete_configuration($id)){
            $this->session->set_flashdata('success','Configuration successfully removed');
        }else{
            $this->session->set_flashdata('error','Error occured removing configuation');
        }
        redirect('admin/safaricom/safaricom_configuration_listing');
    }

    function configuration_action(){
        $action = $this->input->post('btnAction');
        $ids = $this->input->post('action_to');
        if($action == 'setDefault'){
            $result = $this->set_default($ids);
        }
        redirect('admin/billing/safaricom_configuration_listing');
    }

    function set_default($ids = array()){
        if($ids){
            $post = $this->ipn_m->get_default_configuration();
            if($post){
                $update = array(
                    'is_default' => '',
                    'modified_by' => $this->user->id,
                    'modified_on' => time(),
                );
                $this->ipn_m->update_configuration($post->id,$update);
            }
            $update = array(
                'is_default' => 1,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            $this->ipn_m->update_configuration($ids[0],$update);
            return TRUE;
        }else{
            return FALSE;
        }
        
    }

     function payments_vs_invoices(){
        $filter = array(
            'billing_cycle' => $this->input->get_post('billing_cycle'),
            'group_ids' => $this->input->get_post('group_ids'),
        );
        $monthly_invoices = $this->billing_m->get_invoices_grouped_monthly($filter);
        $monthly_payments = $this->billing_m->get_payments_grouped_monthly($filter);
        $years = array();
        foreach ($monthly_invoices as $key => $monthly_invoice) {
            $years[] = $key;
        }

        $this->data['years'] = $years;
        $this->data['monthly_payments'] = $monthly_payments;
        $this->data['monthly_invoices'] = $monthly_invoices;
        $this->data['groups'] = $this->groups_m->get_options();
        $this->data['billing_cycle'] = $this->billing_settings->billing_cycle;
        $this->template->title('Monthly Payments and Invoices Comparison')->build('admin/payments_vs_invoices',$this->data);
    }
}