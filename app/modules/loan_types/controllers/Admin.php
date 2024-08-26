<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends Admin_Controller{
    protected $validation_rules = array(
		array(
			'field' => 'name',
			'label' => 'Loan Type Name',
			'rules' => 'required|trim'
		),
		array(
            'field' => 'minimum_loan_amount',
            'label' => 'Minimum Loan Amount',
            'rules' => 'trim|currency'
        ),array(
            'field' => 'maximum_loan_amount',
            'label' => 'Maximum Loan Amount',
            'rules' => 'trim|currency'
        ),
		array(
			'field' => 'enable_automatic_disbursements',
			'label' => 'Enable Automatic Disbursements',
			'rules' => 'xss_clean|trim|numeric|required'
		),
		array(
			'field' => 'interest_type',
			'label' => 'Loan Interest Type',
			'rules' => 'required|trim|currency'
		),
		array(
			'field' => 'interest_rate',
			'label' => 'Loan Interest Rate',
			'rules' => 'trim|currency'
		),array(
			'field' => 'loan_interest_rate_per',
			'label' => 'Loan Interest Rate Per',
			'rules' => 'trim|currency'
		),
		array(
			'field'=>'loan_repayment_period_type',
			'label'=>'Loan Repayment Period Type',
			'rules'=>'required|trim',
		),
		array(
			'field' => 'minimum_repayment_period',
			'label' => 'Minimum Repayment Period',
			'rules' => 'trim|numeric'
		),array(
			'field' => 'maximum_repayment_period',
			'label' => 'Maximum Repayment Period',
			'rules' => 'trim|numeric'
		),array(
			'field' => 'enable_loan_guarantors',
			'label' => 'Enable Loan Guarantors',
			'rules' => 'trim|numeric'
		),array(
            'field' => 'fixed_repayment_period',
            'label' => 'Fixed Period To make Loan Repayment',
            'rules' => 'trim|numeric'
        ),
		array(
			'field' => 'minimum_guarantors',
			'label' => 'Minimum Guarantors',
			'rules' => 'trim|numeric'
		),array(
			'field' => 'minimum_guarantors_exceed_amount',
			'label' => 'Minimum Guarantors',
			'rules' => 'trim|numeric'
		),
		array(
			'field' => 'enable_loan_processing_fee',
			'label' => 'Enable Loan Processing Fee',
			'rules' => 'trim|numeric'
		),
		array(
            'field' =>  'loan_processing_fee_type',
            'label' =>  'Loan Processing Fee Type',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_processing_fee_fixed_amount',
            'label' =>  'Loan Processing Fee Fixed Amount',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_processing_fee_percentage_rate',
            'label' =>  'Loan Processing Fee Fixed Percentage Rate',
            'rules' =>  'trim'
        ),
        array(
            'field' =>  'loan_processing_fee_percentage_charged_on',
            'label' =>  'Loan Processing Fee Fixed Percentage Charged On',
            'rules' =>  'trim'
        ),
        array(
            'field' => 'loan_interest_rate_per',
            'label' => 'Loan Interest Rate Per',
            'rules' => 'numeric|trim',
        ),
        array(
            'field' => 'enable_loan_fines',
            'label' => 'Enable Loan Fines',
            'rules' => 'numeric|trim',
        ),
        array(
            'field' => 'loan_fine_type',
            'label' => 'Loan Fine Type',
            'rules' => 'numeric|trim',
        ),
        array(
            'field' => 'fixed_fine_amount',
            'label' => 'Fixed Fine Amount',
            'rules' => 'trim|currency',
        ),
        array(
            'field' => 'fixed_amount_fine_frequency',
            'label' => 'Fixed Fine Amount Frequency',
            'rules' => 'numeric|trim',
        ),
        array(
            'field' => 'fixed_amount_fine_frequency_on',
            'label' => 'Fixed Fine Amount Frequency On',
            'rules' => 'numeric|trim',
        ),
        array(
            'field' => 'percentage_fine_rate',
            'label' => 'Percentage Fine Rate',
            'rules' => 'numeric|trim',
        ),
        array(
            'field' => 'percentage_fine_frequency',
            'label' => 'Percentage Fine Frequency',
            'rules' => 'numeric|trim',
        ),
        array(
            'field' => 'percentage_fine_on',
            'label' => 'Percentage Fine On',
            'rules' => 'numeric|trim',
        ),
        array(
            'field' => 'one_off_fine_type',
            'label' => 'One Off Fine Type',
            'rules' => 'numeric|trim',
        ),
        array(
            'field' => 'one_off_fixed_amount',
            'label' => 'One Off Fixed Amount',
            'rules' => 'trim|currency',
        ),
        array(
        	'field' => 'one_off_percentage_rate',
            'label' => 'One Off Percentage Rate',
            'rules' => 'trim|numeric',
        ),
        array(
        	'field' => 'one_off_percentage_rate_on',
            'label' => 'One Off Percentage Rate On',
            'rules' => 'trim|numeric',
        ),
        array(
        	'field' => 'enable_outstanding_loan_balance_fines',
            'label' => 'Enable Outstanding Loan Balance Fines',
            'rules' => 'trim|numeric',
        ),
        array(
        	'field' => 'outstanding_loan_balance_fine_type',
            'label' => 'Outstanding Loan Balance Fine Type',
            'rules' => 'trim|numeric',
        ),
        array(
        	'field' => 'outstanding_loan_balance_fine_fixed_amount',
            'label' => 'Outstanding Loan Balance Fine Fixed Amount',
            'rules' => 'trim|currency',
        ),
        array(
        	'field' => 'outstanding_loan_balance_fixed_fine_frequency',
            'label' => 'Outstanding Loan Balance Fine Fixed Fine Frequency',
            'rules' => 'trim|numeric',
        ),
        array(
        	'field' => 'outstanding_loan_balance_percentage_fine_rate',
            'label' => 'Outstanding Loan Balance Percentage Fine Rate',
            'rules' => 'trim|currency',
        ),
        array(
        	'field' => 'outstanding_loan_balance_percentage_fine_frequency',
            'label' => 'Outstanding Loan Balance Percentage Fine Frequency',
            'rules' => 'trim|numeric',
        ),
        array(
        	'field' => 'outstanding_loan_balance_percentage_fine_on',
            'label' => 'Outstanding Loan Balance Percentage Fine On',
            'rules' => 'trim|numeric',
        ),
        array(
        	'field' => 'outstanding_loan_balance_fine_one_off_amount',
            'label' => 'Outstanding Loan Balance Percentage Fine One Off Amount',
            'rules' => 'trim|currency',
        ),
        array(
    		'field'=>'loan_times_number',
    		'label'=> ' Loan times number',
    		'rules'=> 'trim|numeric'
    	),
    	array(
    		'field'=>'loan_amount_type',
    		'label'=> ' Loan Amount Type',
    		'rules'=> 'trim|numeric|required'
    	), 
    	array(
    		'field'=>'enable_loan_guarantors_exceeds_loan_amount',
    		'label'=> 'Enable loan guarantors when loan amount applied exceeds memeber savings',
    		'rules'=>'trim|numeric'
    	),
    	array(
    		'field'=>'enable_loan_guarantors_details',
    		'label'=>'Enable guarantor details',
    		'rules'=>'trim|numeric'
    	),
    	array(
    		'field'=>'loan_guarantors_type',
    		'label'=>'Loan guarantors',
    		'rules'=>'trim|numeric'
    	),
    	array(
    		'field'=>'enable_reducing_balance_installment_recalculation',
    		'label'=>'Enable loan recalculations',
    		'rules'=>'trim|numeric'
    	),
    	array(
            'field' =>  'grace_period',
            'label' =>  'Loan Grace Period',
            'rules' =>  'trim|required'
        ),
	); 	
	protected $data = array();

	function __construct(){
		parent::__construct();
		$this->load->model('loan_types_m');
		$this->load->library('loan');
		$this->data['loan_amount_type'] = $this->loan->loan_amount_type;
		$this->data['interest_types'] = $this->loan->interest_types;
		$this->data['loan_interest_rate_per'] = $this->loan->loan_interest_rate_per;
		$this->data['loan_grace_periods'] = $this->loan->loan_grace_periods;
		$this->data['loan_repayment_period_type'] = $this->loan->loan_repayment_period_type;
		$this->data['late_loan_payment_fine_types'] = $this->loan->late_loan_payment_fine_types;
		$this->data['late_payments_fine_frequency'] = $this->loan->late_payments_fine_frequency;
		$this->data['fixed_amount_fine_frequency_on'] = $this->loan->fixed_amount_fine_frequency_on;
		$this->data['percentage_fine_on'] = $this->loan->percentage_fine_on;
		$this->data['one_off_fine_types'] = $this->loan->one_off_fine_types;
		$this->data['one_off_percentage_rate_on'] = $this->loan->one_off_percentage_rate_on;
		$this->data['loan_processing_fee_types'] = $this->loan->loan_processing_fee_types;
		$this->data['loan_processing_fee_percentage_charged_on'] = $this->loan->loan_processing_fee_percentage_charged_on;
	}

	function index(){

	}
	function _is_greater_than_minimum_repayment_period(){
		if(currency($this->input->post('minimum_repayment_period'))>currency($this->input->post('maximum_repayment_period'))){
			$this->form_validation->set_message('_is_greater_than_minimum_repayment_period','Maximum Repayment Period must be greater than Minimum Repayment Period');
			return FALSE;
		}else{
			return TRUE;
		}
	}
	function _additional_validation_rules(){
    	if($this->input->post('enable_loan_guarantors')){
    		$this->validation_rules[] = array(
					'field' => 'minimum_guarantors',
					'label' => 'Minimum Guarantors',
					'rules' => 'trim|numeric'
				);
			$this->validation_rules[] = array(
					'field' => 'maximum_guarantors',
					'label' => 'Maximum Guarantors',
					'rules' => 'trim|numeric'
				);
    	}

    	if($this->input->post('enable_loan_guarantors_exceeds_loan_amount')){
    		$this->validation_rules[] = array(
					'field' => 'minimum_guarantors',
					'label' => 'Minimum Guarantors',
					'rules' => 'trim|numeric'
			);
			$this->validation_rules[] = array(
					'field' => 'maximum_guarantors',
					'label' => 'Maximum Guarantors',
					'rules' => 'trim|numeric'
			);

    	}
    	if($this->input->post('interest_type') ==1){
    		$this->validation_rules[] = array(
    			'field'=>'interest_rate',
    			'label'=>'Interest rate ',
    			'rules'=>'required|numeric|trim'
    		);
    		$this->validation_rules[] = array(
    			'field'=>'loan_interest_rate_per',
    			'label'=>'Interest rate per ',
    			'rules'=>'required|numeric|trim'
    		);
    	}else if($this->input->post('interest_type')==2){
    		$this->validation_rules[] = array(
    			'field'=>'interest_rate',
    			'label'=>'Interest rate ',
    			'rules'=>'required|numeric|trim'
    		);
    		$this->validation_rules[] = array(
    			'field'=>'loan_interest_rate_per',
    			'label'=>'Interest rate per ',
    			'rules'=>'required|numeric|trim'
    		);
    		$this->validation_rules[] = array(
    			'field'=>'enable_reducing_balance_installment_recalculation',
    			'label'=>'Enable reducing balance recalculations ',
    			'rules'=>'numeric|trim'
    		);
    	}
    	if($this->input->post('enable_loan_processing_fee')){
    		$this->validation_rules[] = array(
		            'field' =>  'loan_processing_fee_type',
		            'label' =>  'Loan Processing Fee Type',
		            'rules' =>  'trim|required'
		        );
    		if($this->input->post('loan_processing_fee_type')==1){
    			$this->validation_rules[] = array(
		            'field' =>  'loan_processing_fee_fixed_amount',
		            'label' =>  'Loan Processing Fee Fixed Amount',
		            'rules' =>  'trim|currency|required'
		        );
    		}else{
    			$this->validation_rules[] = array(
			            'field' =>  'loan_processing_fee_percentage_rate',
			            'label' =>  'Loan Processing Fee Fixed Percentage Rate',
			            'rules' =>  'trim|required|currency'
			        );
	        	$this->validation_rules[] = array(
			            'field' =>  'loan_processing_fee_percentage_charged_on',
			            'label' =>  'Loan Processing Fee Fixed Percentage Charged On',
			            'rules' =>  'trim|required|numeric'
			        );
    		}
    	}

    	if($this->input->post('enable_loan_fines')){
    		$this->validation_rules[] =  array(
			            'field' => 'loan_fine_type',
			            'label' => 'Loan Fine Type',
			            'rules' => 'numeric|trim|required',
			        );
    		if($this->input->post('loan_fine_type') == 1){
    			$this->validation_rules[] = array(
			            'field' => 'fixed_fine_amount',
			            'label' => 'Fixed Fine Amount',
			            'rules' => 'trim|currency|required',
			        );
		        $this->validation_rules[] = array(
			            'field' => 'fixed_amount_fine_frequency',
			            'label' => 'Fixed Fine Amount Frequency',
			            'rules' => 'numeric|trim|required',
			        );
		        $this->validation_rules[] = array(
			            'field' => 'fixed_amount_fine_frequency_on',
			            'label' => 'Fixed Fine Amount Frequency On',
			            'rules' => 'numeric|trim|required',
			        );
    		}else if($this->input->post('loan_fine_type') == 2){
    			$this->validation_rules[] = array(
		            'field' => 'percentage_fine_rate',
		            'label' => 'Percentage Fine Rate',
		            'rules' => 'numeric|trim|required',
		        );
		        $this->validation_rules[] = array(
		            'field' => 'percentage_fine_frequency',
		            'label' => 'Percentage Fine Frequency',
		            'rules' => 'numeric|trim|required',
		        );
		        $this->validation_rules[] = array(
		            'field' => 'percentage_fine_on',
		            'label' => 'Percentage Fine On',
		            'rules' => 'numeric|trim|required',
		        );
    		}else if($this->input->post('loan_fine_type') == 3){
    			$this->validation_rules[] = array(
		            'field' => 'one_off_fine_type',
		            'label' => 'One Off Fine Type',
		            'rules' => 'numeric|trim|required',
		        );
		        if($this->input->post('one_off_fine_type')==1){
		        	$this->validation_rules[] = array(
			            'field' => 'one_off_fixed_amount',
			            'label' => 'One Off Fixed Amount',
			            'rules' => 'trim|currency|required',
			        );
		        }else if($this->input->post('one_off_fine_type')==2){
		        	$this->validation_rules[] = array(
			        	'field' => 'one_off_percentage_rate',
			            'label' => 'One Off Percentage Rate',
			            'rules' => 'trim|numeric|required',
			        );
			        $this->validation_rules[] = array(
			        	'field' => 'one_off_percentage_rate_on',
			            'label' => 'One Off Percentage Rate On',
			            'rules' => 'trim|numeric|required',
			        );
		        }
    		}
    	}

    	if($this->input->post('enable_outstanding_loan_balance_fines')){
    		$this->validation_rules[] = array(
	        	'field' => 'outstanding_loan_balance_fine_type',
	            'label' => 'Outstanding Loan Balance Fine Type',
	            'rules' => 'trim|numeric|required',
	        );
	        if($this->input->post('outstanding_loan_balance_fine_type')==1){
	        	$this->validation_rules[] =  array(
		        	'field' => 'outstanding_loan_balance_fine_fixed_amount',
		            'label' => 'Outstanding Loan Balance Fine Fixed Amount',
		            'rules' => 'trim|currency|required',
		        );
		        $this->validation_rules[] = array(
		        	'field' => 'outstanding_loan_balance_fixed_fine_frequency',
		            'label' => 'Outstanding Loan Balance Fine Fixed Fine Frequency',
		            'rules' => 'trim|numeric|required',
		        );
	        }else if($this->input->post('outstanding_loan_balance_fine_type')==2){
	        	$this->validation_rules[] = array(
		        	'field' => 'outstanding_loan_balance_percentage_fine_rate',
		            'label' => 'Outstanding Loan Balance Percentage Fine Rate',
		            'rules' => 'trim|currency|required',
		        );
		        $this->validation_rules[] = array(
		        	'field' => 'outstanding_loan_balance_percentage_fine_frequency',
		            'label' => 'Outstanding Loan Balance Percentage Fine Frequency',
		            'rules' => 'trim|numeric|required',
		        );
		        $this->validation_rules[] = array(
		        	'field' => 'outstanding_loan_balance_percentage_fine_on',
		            'label' => 'Outstanding Loan Balance Percentage Fine On',
		            'rules' => 'trim|numeric|required',
		        );
	        }else if($this->input->post('outstanding_loan_balance_fine_type')==3){
	        	$this->validation_rules[] = array(
		        	'field' => 'outstanding_loan_balance_fine_one_off_amount',
		            'label' => 'Outstanding Loan Balance Percentage Fine One Off Amount',
		            'rules' => 'trim|currency|required',
		        );
	        }
    	}
    }
    function _is_greater_than_minimum_loan_amount(){
        if(currency($this->input->post('minimum_loan_amount'))>=currency($this->input->post('maximum_loan_amount'))){
            $this->form_validation->set_message('_is_greater_than_minimum_loan_amount','Maximum Loan Amount must be greater than Minimum Loan Amount');
            return FALSE;
        }else{
            return TRUE;
        }
    }
	function create(){
		$post = new StdClass();
		if($this->input->post('loan_repayment_period_type') == 1){
	        $this->validation_rules[] = array(
	            'field' => 'fixed_repayment_period',
	            'label' => 'Fixed Period To make Loan Repayment',
	            'rules' => 'required|trim|numeric',
	        );
        }
        if($this->input->post('loan_repayment_period_type') == 2){
        	$this->validation_rules[] = array(
				'field' => 'minimum_repayment_period',
				'label' => 'Minimum Repayment Period',
				'rules' => 'required|trim|numeric'
			);

	       $this->validation_rules[] = array(
				'field' => 'maximum_repayment_period',
				'label' => 'Maximum Repayment Period',
				'rules' => 'required|trim|numeric|callback__is_greater_than_minimum_repayment_period'
			);
        }
		$this->_additional_validation_rules();
		$this->form_validation->set_rules($this->validation_rules);
		if($this->form_validation->run()){
			$data = array(
				'active' => 1,
				'name'	=>	$this->input->post('name'),
                'loan_amount_type' => $this->input->post('loan_amount_type'),
				'minimum_loan_amount' => $this->input->post('minimum_loan_amount'),    				
				'maximum_loan_amount' => $this->input->post('maximum_loan_amount'),
				'loan_repayment_period_type'=>$this->input->post('loan_repayment_period_type'),
				'fixed_repayment_period'=>$this->input->post('fixed_repayment_period'),
				'minimum_repayment_period' => $this->input->post('minimum_repayment_period'),
				'maximum_repayment_period' => $this->input->post('maximum_repayment_period'),
				'enable_automatic_disbursements' => $this->input->post('enable_automatic_disbursements')?:0,
				'limit_to_one_loan_application' => $this->input->post('limit_to_one_loan_application')?:0,
				'interest_rate' => $this->input->post('interest_rate'),
				'interest_type' => $this->input->post('interest_type'),
				'loan_interest_rate_per' => $this->input->post('loan_interest_rate_per'),
                'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation'),
				'grace_period' => $this->input->post('grace_period')?:1,
				'enable_loan_fines' => $this->input->post('enable_loan_fines')?:0,
				'enable_outstanding_loan_balance_fines' => $this->input->post('enable_outstanding_loan_balance_fines')?:0,
				'enable_loan_processing_fee' => $this->input->post('enable_loan_processing_fee')?:0,
				'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment')?:0,
				'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors')?:0,
				'created_by' => $this->user->id,
				'is_admin' => 1,
				'created_on' => time(),
			);
    		if($this->input->post('enable_loan_fines')){
    			$data = $data+array('loan_fine_type'=>$this->input->post('loan_fine_type'));
    			if($this->input->post('loan_fine_type')==1){
    				$data = $data+array(
    						'fixed_fine_amount' => $this->input->post('fixed_fine_amount'),
    						'fixed_amount_fine_frequency' => $this->input->post('fixed_amount_fine_frequency'),
    						'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
    					);
    			}else if($this->input->post('loan_fine_type')==2){
    				$data = $data+array(
    						'percentage_fine_rate' => $this->input->post('percentage_fine_rate'),
    						'percentage_fine_frequency' => $this->input->post('percentage_fine_frequency'),
    						'percentage_fine_on' => $this->input->post('percentage_fine_on'),
    					);
    			}else if($this->input->post('loan_fine_type')==3){
    				$data = $data + array('one_off_fine_type'=>$this->input->post('one_off_fine_type'));
    				if($this->input->post('one_off_fine_type')==1){
    					$data = $data+array('one_off_fixed_amount'=>$this->input->post('one_off_fixed_amount'));
    				}else if($this->input->post('one_off_fine_type')==2){
    					$data = $data+array(
    							'one_off_percentage_rate' => $this->input->post('one_off_percentage_rate'),
								'one_off_percentage_rate_on' => $this->input->post('one_off_percentage_rate'),
    						);
    				}
    			}
    		}

    		if($this->input->post('enable_outstanding_loan_balance_fines')){
    			$data = $data + array('outstanding_loan_balance_fine_type'=>$this->input->post('outstanding_loan_balance_fine_type'));
    			if($this->input->post('outstanding_loan_balance_fine_type')==1){
    				$data = $data + array(
	    						'outstanding_loan_balance_fine_fixed_amount' => $this->input->post('outstanding_loan_balance_fine_fixed_amount'),
								'outstanding_loan_balance_fixed_fine_frequency' => $this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
							);
    			}else if($this->input->post('outstanding_loan_balance_fine_type')==2){
    				$data = $data + array(
	    						'outstanding_loan_balance_percentage_fine_rate' => $this->input->post('outstanding_loan_balance_percentage_fine_rate'),
								'outstanding_loan_balance_percentage_fine_frequency' => $this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
								'outstanding_loan_balance_percentage_fine_on' => $this->input->post('outstanding_loan_balance_percentage_fine_on'),
							);
    			}else if($this->input->post('outstanding_loan_balance_fine_type')==3){
    				$data = $data + array(
	    						'outstanding_loan_balance_fine_one_off_amount' => $this->input->post('outstanding_loan_balance_fine_one_off_amount'),
							);
    			}
    		}

    		if($this->input->post('enable_loan_processing_fee')){
    			$data = $data + array('loan_processing_fee_type'=>$this->input->post('loan_processing_fee_type'));
    			if($this->input->post('loan_processing_fee_type')==1){
    				$data = $data + array('loan_processing_fee_fixed_amount'=>$this->input->post('loan_processing_fee_fixed_amount'));
    			}elseif($this->input->post('loan_processing_fee_type')==2){
    				$data = $data+array(
    						'loan_processing_fee_percentage_rate' => $this->input->post('loan_processing_fee_percentage_rate'),
							'loan_processing_fee_percentage_charged_on' => $this->input->post('loan_processing_fee_percentage_charged_on'),
						);
    			}
    		}
            if($this->input->post('enable_loan_guarantors')){
                $data = $data + array(
                    "loan_guarantors_type" => $this->input->post('loan_guarantors_type'),
                );
                if($this->input->post('loan_guarantors_type') == 1){
                    $data = $data + array(
                        'minimum_guarantors' => $this->input->post('minimum_guarantors'),
                        'maximum_guarantors' => $this->input->post('minimum_guarantors')+3,
                    );
                }else if($this->input->post('loan_guarantors_type') == 2){
                    $data = $data + array(
                        'minimum_guarantors' => $this->input->post('minimum_guarantors_exceed_amount'),
                        'maximum_guarantors' => $this->input->post('minimum_guarantors_exceed_amount')+3,
                    );
                }               
            }
            if($this->input->post('loan_amount_type') == 1){
                $data = $data+array(
                    'minimum_loan_amount' => $this->input->post('minimum_loan_amount'),                 
                    'maximum_loan_amount' => $this->input->post('maximum_loan_amount'),
                );
            }else if($this->input->post('loan_amount_type') == 2){
                
                $data = $data+array(                
                    'loan_times_number' => $this->input->post('loan_times_number')
                );
            }

            if($this->input->post('loan_repayment_period_type') == 1){
                $data = $data+array(
                    'fixed_repayment_period'=>$this->input->post('fixed_repayment_period'),  
                );
            }else if($this->input->post('loan_repayment_period_type') == 2){
                $data = $data+array(
                    'minimum_repayment_period' => $this->input->post('minimum_repayment_period'),
                    'maximum_repayment_period' => $this->input->post('maximum_repayment_period'),
                );
            }
            if($id = $this->loan_types_m->insert($data)){
            	$this->session->set_flashdata('success','Loan type successfully created');
            }else{
            	$this->session->set_flashdata('error','Could not create default loan type');
            }
            redirect('admin/loan_types/listing');
		}else{
			foreach ($this->validation_rules as $key => $field){
	        	$field_value = $field['field'];
	            $post->$field_value= set_value($field['field']);
	        }
		}
		$this->data['post'] = $post;
		$this->data['id'] = '';
		$this->template->title('Default Loan Types')->build('admin/form',$this->data);
	}

    function edit($id=0){
        $id OR redirect('admin/loan_types/listing');
        $post = $this->loan_types_m->get($id,'a');
        if(!$post){
            $this->session->set_flashdata('error','Loan type not available');
            redirect('admin/loan_types/listing');
        }

        if($this->input->post('loan_repayment_period_type') == 1)
        {
            $this->validation_rules[] = array(
                'field' => 'fixed_repayment_period',
                'label' => 'Fixed Period To make Loan Repayment',
                'rules' => 'required|trim|numeric',
            );
        }
        if($this->input->post('loan_repayment_period_type') == 2){
            $this->validation_rules[] = array(
                'field' => 'minimum_repayment_period',
                'label' => 'Minimum Repayment Period',
                'rules' => 'required|trim|numeric'
            );

           $this->validation_rules[] = array(
                'field' => 'maximum_repayment_period',
                'label' => 'Maximum Repayment Period',
                'rules' => 'required|trim|numeric|callback__is_greater_than_minimum_repayment_period'
            );
        }  

        if($this->input->post('loan_repayment_period_type') == 1)
        {
            $this->validation_rules[] = array(
                'field' => 'fixed_repayment_period',
                'label' => 'Fixed Period To make Loan Repayment',
                'rules' => 'required|trim|numeric',
            );
        }
        if($this->input->post('loan_amount_type') == 1){
            $this->validation_rules[] = array(
                'field' => 'minimum_loan_amount',
                'label' => 'Minimum Loan Amount',
                'rules' => 'trim|currency'
            );
            $this->validation_rules[] = array(
                'field' => 'maximum_loan_amount',
                'label' => 'Maximum Loan Amount',
                'rules' => 'trim|currency|callback__is_greater_than_minimum_loan_amount'
            );
        }else if($this->input->post('loan_amount_type') == 2){
            $this->validation_rules[] = array(
                'field'=>'loan_times_number',
                'label'=> ' Loan times number',
                'rules'=> 'trim|numeric|callback__is_greater_than_zero'
            );
        }
        if($this->input->post('loan_repayment_period_type') == 2){
            $this->validation_rules[] = array(
                'field' => 'minimum_repayment_period',
                'label' => 'Minimum Repayment Period',
                'rules' => 'required|trim|numeric'
            );

           $this->validation_rules[] = array(
                'field' => 'maximum_repayment_period',
                'label' => 'Maximum Repayment Period',
                'rules' => 'required|trim|numeric|callback__is_greater_than_minimum_repayment_period'
            );
        }   
        if($this->input->post('enable_loan_guarantors')){
            if($this->input->post('loan_guarantors_type') == 1){    
                $this->validation_rules[] = array(
                    'field' => 'minimum_guarantors',
                    'label' => 'Maximum Guarantors',
                    'rules' => 'required|trim|numeric|greater_than[0]'
                );
            }elseif($this->input->post('loan_guarantors_type') == 2){
                $this->validation_rules[] = array(
                    'field' => 'minimum_guarantors_exceed_amount',
                    'label' => 'Maximum Guarantors',
                    'rules' => 'required|trim|numeric|greater_than[0]'
                );
            }else{
                $this->validation_rules[] = array(
                    'field' => 'loan_guarantors_type',
                    'label' => 'Loan guarantors type',
                    'rules' => 'trim|currency|required',
                );
            }
        }
        $this->_additional_validation_rules();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                    'name'  =>  $this->input->post('name'),
                    'loan_repayment_period_type'=>$this->input->post('loan_repayment_period_type'),
                    'interest_rate' => $this->input->post('interest_rate'),
                    'interest_type' => $this->input->post('interest_type'),
                    'loan_interest_rate_per' => $this->input->post('loan_interest_rate_per'),
                    'grace_period' => 1,
                    'enable_loan_fines' => $this->input->post('enable_loan_fines')?:0,
					'enable_automatic_disbursements' => $this->input->post('enable_automatic_disbursements')?:0,
					'limit_to_one_loan_application' => $this->input->post('limit_to_one_loan_application')?:0,
                    'enable_outstanding_loan_balance_fines' => $this->input->post('enable_outstanding_loan_balance_fines')?:0,
                    'enable_loan_processing_fee' => $this->input->post('enable_loan_processing_fee')?:0,
                    'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment')?:0,
                    'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors')?:0,
                    'modified_by' => $this->user->id,
                    'modified_on' => time(),
                    'loan_amount_type' =>$this->input->post('loan_amount_type'),    
            );
            if($this->input->post('enable_loan_fines')){
                $data = $data+array('loan_fine_type'=>$this->input->post('loan_fine_type'));
                if($this->input->post('loan_fine_type')==1){
                    $data = $data+array(
                            'fixed_fine_amount' => $this->input->post('fixed_fine_amount'),
                            'fixed_amount_fine_frequency' => $this->input->post('fixed_amount_fine_frequency'),
                            'fixed_amount_fine_frequency_on' => $this->input->post('fixed_amount_fine_frequency_on'),
                        );
                }else if($this->input->post('loan_fine_type')==2){
                    $data = $data+array(
                            'percentage_fine_rate' => $this->input->post('percentage_fine_rate'),
                            'percentage_fine_frequency' => $this->input->post('percentage_fine_frequency'),
                            'percentage_fine_on' => $this->input->post('percentage_fine_on'),
                        );
                }else if($this->input->post('loan_fine_type')==3){
                    $data = $data + array('one_off_fine_type'=>$this->input->post('one_off_fine_type'));
                    if($this->input->post('one_off_fine_type')==1){
                        $data = $data+array('one_off_fixed_amount'=>$this->input->post('one_off_fixed_amount'));
                    }else if($this->input->post('one_off_fine_type')==2){
                        $data = $data+array(
                                'one_off_percentage_rate' => $this->input->post('one_off_percentage_rate'),
                                'one_off_percentage_rate_on' => $this->input->post('one_off_percentage_rate_on'),
                            );
                    }
                }
            }
            if($this->input->post('enable_outstanding_loan_balance_fines')){
                $data = $data + array('outstanding_loan_balance_fine_type'=>$this->input->post('outstanding_loan_balance_fine_type'));
                if($this->input->post('outstanding_loan_balance_fine_type')==1){
                    $data = $data + array(
                                'outstanding_loan_balance_fine_fixed_amount' => $this->input->post('outstanding_loan_balance_fine_fixed_amount'),
                                'outstanding_loan_balance_fixed_fine_frequency' => $this->input->post('outstanding_loan_balance_fixed_fine_frequency'),
                            );
                }else if($this->input->post('outstanding_loan_balance_fine_type')==2){
                    $data = $data + array(
                                'outstanding_loan_balance_percentage_fine_rate' => $this->input->post('outstanding_loan_balance_percentage_fine_rate'),
                                'outstanding_loan_balance_percentage_fine_frequency' => $this->input->post('outstanding_loan_balance_percentage_fine_frequency'),
                                'outstanding_loan_balance_percentage_fine_on' => $this->input->post('outstanding_loan_balance_percentage_fine_on'),
                            );
                }else if($this->input->post('outstanding_loan_balance_fine_type')==3){
                    $data = $data + array(
                                'outstanding_loan_balance_fine_one_off_amount' => $this->input->post('outstanding_loan_balance_fine_one_off_amount'),
                            );
                }
            }
            if($this->input->post('enable_loan_processing_fee')){
                $data = $data + array('loan_processing_fee_type'=>$this->input->post('loan_processing_fee_type'));
                if($this->input->post('loan_processing_fee_type')==1){
                    $data = $data + array('loan_processing_fee_fixed_amount'=>$this->input->post('loan_processing_fee_fixed_amount'));
                }elseif($this->input->post('loan_processing_fee_type')==2){
                    $data = $data+array(
                            'loan_processing_fee_percentage_rate' => $this->input->post('loan_processing_fee_percentage_rate'),
                            'loan_processing_fee_percentage_charged_on' => $this->input->post('loan_processing_fee_percentage_charged_on'),
                        );
                }
            }
            if($this->input->post('enable_loan_guarantors')){
                $data = $data + array(
                    "loan_guarantors_type" => $this->input->post('loan_guarantors_type'),
                );
                if($this->input->post('loan_guarantors_type') == 1){
                    $data = $data + array(
                        'minimum_guarantors' => $this->input->post('minimum_guarantors'),
                        'maximum_guarantors' => $this->input->post('minimum_guarantors'),
                    );
                }else if($this->input->post('loan_guarantors_type') == 2){
                    $data = $data + array(
                        'minimum_guarantors' => $this->input->post('minimum_guarantors_exceed_amount'),
                        'maximum_guarantors' => $this->input->post('minimum_guarantors_exceed_amount'),
                    );
                }               
            }
            if($this->input->post('loan_amount_type') == 1){
                $data = $data+array(
                    'minimum_loan_amount' => $this->input->post('minimum_loan_amount'),                 
                    'maximum_loan_amount' => $this->input->post('maximum_loan_amount'),
                );
            }else if($this->input->post('loan_amount_type') == 2){
                
                $data = $data+array(                
                    'loan_times_number' => $this->input->post('loan_times_number')
                );
            }

            if($this->input->post('loan_repayment_period_type') == 1){
                $data = $data+array(
                    'fixed_repayment_period'=>$this->input->post('fixed_repayment_period'),  
                );
            }else if($this->input->post('loan_repayment_period_type') == 2){
                $data = $data+array(
                    'minimum_repayment_period' => $this->input->post('minimum_repayment_period'),
                    'maximum_repayment_period' => $this->input->post('maximum_repayment_period'),
                );
            }
            if($this->loan_types_m->update($id,$data)){
                $this->session->set_flashdata('success','Loan type successfully updated');
            }else{
                $this->session->set_flashdata('error','Error updating loan type');
            }
            redirect('admin/loan_types/listing');
        }else{
             foreach (array_keys($this->validation_rules) as $field){
                if (isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                 }
            }
        }
        $this->data['post'] = $post;        
        $this->template->title('Create Loan Type')->build('admin/form',$this->data);
    }

	function listing(){
		$this->data['posts'] = $this->loan_types_m->get_admin_loan_types();
        $this->data['matches_options'] = $this->loan_types_m->get_loan_types_match();
		$this->template->title('Default loan types')->build('admin/listing',$this->data);
	}

	function match_groups($id=0){
		$id OR redirect('admin/loan_types/listing');
        $group_ids = $this->input->post('group_ids');
        if($group_ids){
            foreach ($group_ids as $group_id) {
                $groups[] = $group_id;
                $loan_type_ids[] = $id;
                $actives[] = $id;
                $created_ons[] = time();
                $created_bys[] = $this->user->id;
            }
            if($groups){
                $input = array(
                    'group_id' => $groups,
                    'loan_type_id' => $loan_type_ids,
                    'active' => $actives,
                    'created_on' => $created_ons,
                    'created_by' => $created_bys,
                );
                if($this->loan_types_m->delete_loan_types_match($id)){
                    $this->loan_types_m->insert_batch_match($input);
                }
            }
            redirect('admin/loan_types/listing');
        }

        $this->data['matches_options'] = $this->loan_types_m->get_loan_types_match($id);
        $this->data['group_options'] = $this->groups_m->get_options();
		$this->template->title('Default loan types')->build('admin/match_groups',$this->data);
	}
    function delete($id = 0){
    	$id OR redirect('admin/loan_types/listing');
    	$post = $this->loan_types_m->get($id);
    	if($post){
    		if($this->loan_types_m->safe_delete($id,$this->group->id)){
    			$this->session->set_flashdata('success','successfully deleted loan type');
    			redirect('admin/loan_types/listing');
    		}else{
    			$this->session->set_flashdata('error','Could not delete loan type');
    			redirect('admin/loan_types/listing');
    		}
    	}else{
    		$this->session->set_flashdata('error','group loan type details do not exist');
    		redirect('admin/loan_types/listing');
    	}
    }
}
?>