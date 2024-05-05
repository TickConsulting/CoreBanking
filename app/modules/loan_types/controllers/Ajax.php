<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

	protected $data = array();
    
	public $response = array(
		'result_code' => 0,
		'result_description' => 'Default Response'
	);
	
	protected $validation_rules = array(
		array(
			'field' => 'name',
			'label' => 'Loan Type Name',
			'rules' => 'xss_clean|required|trim'
		),
		array(
            'field' => 'minimum_loan_amount',
            'label' => 'Minimum Loan Amount',
            'rules' => 'xss_clean|trim|currency'
        ),array(
            'field' => 'maximum_loan_amount',
            'label' => 'Maximum Loan Amount',
            'rules' => 'xss_clean|trim|currency'
        ),
		array(
			'field' => 'interest_type',
			'label' => 'Loan Interest Type',
			'rules' => 'xss_clean|required|trim|currency'
		),
		array(
			'field' => 'interest_rate',
			'label' => 'Loan Interest Rate',
			'rules' => 'xss_clean|trim|currency'
		),array(
			'field' => 'loan_interest_rate_per',
			'label' => 'Loan Interest Rate Per',
			'rules' => 'xss_clean|trim|currency'
		),
		array(
			'field'=>'loan_repayment_period_type',
			'label'=>'Loan Repayment Period Type',
			'rules'=>'xss_clean|required|trim',
		),
		array(
			'field' => 'minimum_repayment_period',
			'label' => 'Minimum Repayment Period',
			'rules' => 'xss_clean|trim|numeric'
		),array(
			'field' => 'maximum_repayment_period',
			'label' => 'Maximum Repayment Period',
			'rules' => 'xss_clean|trim|numeric'
		),array(
			'field' => 'enable_loan_guarantors',
			'label' => 'Enable Loan Guarantors',
			'rules' => 'xss_clean|trim|numeric'
		),array(
            'field' => 'fixed_repayment_period',
            'label' => 'Fixed Period To make Loan Repayment',
            'rules' => 'xss_clean|trim|numeric'
        ),
		array(
			'field' => 'minimum_guarantors',
			'label' => 'Minimum Guarantors',
			'rules' => 'xss_clean|trim|numeric'
		),array(
			'field' => 'minimum_guarantors_exceed_amount',
			'label' => 'Minimum Guarantors',
			'rules' => 'xss_clean|trim|numeric'
		),
		array(
			'field' => 'enable_loan_processing_fee',
			'label' => 'Enable Loan Processing Fee',
			'rules' => 'xss_clean|trim|numeric'
		),
		array(
            'field' =>  'loan_processing_fee_type',
            'label' =>  'Loan Processing Fee Type',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'loan_processing_fee_fixed_amount',
            'label' =>  'Loan Processing Fee Fixed Amount',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'loan_processing_fee_percentage_rate',
            'label' =>  'Loan Processing Fee Fixed Percentage Rate',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' =>  'loan_processing_fee_percentage_charged_on',
            'label' =>  'Loan Processing Fee Fixed Percentage Charged On',
            'rules' =>  'xss_clean|trim'
        ),
        array(
            'field' => 'loan_interest_rate_per',
            'label' => 'Loan Interest Rate Per',
            'rules' => 'xss_clean|numeric|trim',
        ),
        array(
            'field' => 'enable_loan_fines',
            'label' => 'Enable Loan Fines',
            'rules' => 'xss_clean|numeric|trim',
        ),
        array(
            'field' => 'loan_fine_type',
            'label' => 'Loan Fine Type',
            'rules' => 'xss_clean|numeric|trim',
        ),
        array(
            'field' => 'fixed_fine_amount',
            'label' => 'Fixed Fine Amount',
            'rules' => 'xss_clean|trim|currency',
        ),
        array(
            'field' => 'fixed_amount_fine_frequency',
            'label' => 'Fixed Fine Amount Frequency',
            'rules' => 'xss_clean|numeric|trim',
        ),
        array(
            'field' => 'fixed_amount_fine_frequency_on',
            'label' => 'Fixed Fine Amount Frequency On',
            'rules' => 'xss_clean|numeric|trim',
        ),
        array(
            'field' => 'percentage_fine_rate',
            'label' => 'Percentage Fine Rate',
            'rules' => 'xss_clean|numeric|trim',
        ),
        array(
            'field' => 'percentage_fine_frequency',
            'label' => 'Percentage Fine Frequency',
            'rules' => 'xss_clean|numeric|trim',
        ),
        array(
            'field' => 'percentage_fine_on',
            'label' => 'Percentage Fine On',
            'rules' => 'xss_clean|numeric|trim',
        ),
        array(
            'field' => 'one_off_fine_type',
            'label' => 'One Off Fine Type',
            'rules' => 'xss_clean|numeric|trim',
        ),
        array(
            'field' => 'one_off_fixed_amount',
            'label' => 'One Off Fixed Amount',
            'rules' => 'xss_clean|trim|currency',
        ),
        array(
        	'field' => 'one_off_percentage_rate',
            'label' => 'One Off Percentage Rate',
            'rules' => 'xss_clean|trim|numeric',
        ),
        array(
        	'field' => 'one_off_percentage_rate_on',
            'label' => 'One Off Percentage Rate On',
            'rules' => 'xss_clean|trim|numeric',
        ),
        array(
        	'field' => 'enable_outstanding_loan_balance_fines',
            'label' => 'Enable Outstanding Loan Balance Fines',
            'rules' => 'xss_clean|trim|numeric',
        ),
        array(
        	'field' => 'outstanding_loan_balance_fine_type',
            'label' => 'Outstanding Loan Balance Fine Type',
            'rules' => 'xss_clean|trim|numeric',
        ),
        array(
        	'field' => 'outstanding_loan_balance_fine_fixed_amount',
            'label' => 'Outstanding Loan Balance Fine Fixed Amount',
            'rules' => 'xss_clean|trim|currency',
        ),
        array(
        	'field' => 'outstanding_loan_balance_fixed_fine_frequency',
            'label' => 'Outstanding Loan Balance Fine Fixed Fine Frequency',
            'rules' => 'xss_clean|trim|numeric',
        ),
        array(
        	'field' => 'outstanding_loan_balance_percentage_fine_rate',
            'label' => 'Outstanding Loan Balance Percentage Fine Rate',
            'rules' => 'xss_clean|trim|currency',
        ),
        array(
        	'field' => 'outstanding_loan_balance_percentage_fine_frequency',
            'label' => 'Outstanding Loan Balance Percentage Fine Frequency',
            'rules' => 'xss_clean|trim|numeric',
        ),
        array(
        	'field' => 'outstanding_loan_balance_percentage_fine_on',
            'label' => 'Outstanding Loan Balance Percentage Fine On',
            'rules' => 'xss_clean|trim|numeric',
        ),
        array(
        	'field' => 'outstanding_loan_balance_fine_one_off_amount',
            'label' => 'Outstanding Loan Balance Percentage Fine One Off Amount',
            'rules' => 'xss_clean|trim|currency',
        ),
        array(
    		'field'=>'loan_times_number',
    		'label'=> ' Loan times number',
    		'rules'=> 'xss_clean|trim|numeric'
    	),
    	array(
    		'field'=>'loan_amount_type',
    		'label'=> ' Loan Amount Type',
    		'rules'=> 'xss_clean|trim|numeric|required'
    	), 
    	array(
    		'field'=>'enable_loan_guarantors_exceeds_loan_amount',
    		'label'=> 'Enable loan guarantors when loan amount applied exceeds memeber savings',
    		'rules'=>'xss_clean|trim|numeric'
    	),
    	array(
    		'field'=>'enable_loan_guarantors_details',
    		'label'=>'Enable guarantor details',
    		'rules'=>'xss_clean|trim|numeric'
    	),
    	array(
    		'field'=>'loan_guarantors_type',
    		'label'=>'Loan guarantors',
    		'rules'=>'xss_clean|trim|numeric'
    	),
    	array(
    		'field'=>'enable_reducing_balance_installment_recalculation',
    		'label'=>'Enable loan recalculations',
    		'rules'=>'xss_clean|trim|numeric'
    	),
    	array(
            'field' =>  'grace_period',
            'label' =>  'Loan Grace Period',
            'rules' =>  'trim|required'
        ),
	); 

    protected $loan_repayment_period_type = array(
    	1=>  'Fixed Repayment Period',
    	2=>  'Varying Repayment Period',
    );
	

	function _is_greater_than_minimum_loan_amount(){
		if(currency($this->input->post('minimum_loan_amount'))>=currency($this->input->post('maximum_loan_amount'))){
			$this->form_validation->set_message('_is_greater_than_minimum_loan_amount','Maximum Loan Amount must be greater than Minimum Loan Amount');
			return FALSE;
		}else{
			return TRUE;
		}
	}
	function _is_greater_than_minimum_repayment_period(){
		if(currency($this->input->post('minimum_repayment_period'))>currency($this->input->post('maximum_repayment_period'))){
			$this->form_validation->set_message('_is_greater_than_minimum_repayment_period','Maximum Repayment Period must be greater than Minimum Repayment Period');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function _is_greater_than_minimum_guarantors(){
		if(currency($this->input->post('minimum_guarantors'))>currency($this->input->post('maximum_guarantors'))){
			$this->form_validation->set_message('_is_greater_than_minimum_guarantors','Maximum Guarantors more than Minimum Guarantors');
			return FALSE;
		}else{
			return TRUE;
		}
	}
	function _is_greater_than_zero(){
		$loan_amount_type = $this->input->post('loan_amount_type');
			if($loan_amount_type == 2){
				if($this->input->post('loan_times_number') >= 0 ){
					return TRUE;
				}else{
					$this->form_validation->set_message('_is_greater_than_zero','The  value used to times loans Savings to get loan amount must be greater than zero');
					return FALSE;
			}
		}else if($loan_amount_type == 1){

		}		
	}

    function __construct(){
        parent::__construct();
        $this->load->model('loan_types_m');
        $this->load->model('reports/reports_m');
        $this->load->model('contributions/contributions_m');
        $this->load->library('loan');
        $this->_additional_validation_rules();
        $interest_types = $this->loan->interest_types;
        $loan_interest_rate_per= $this->loan->loan_interest_rate_per;
        $loan_processing_fee_types = $this->loan->loan_processing_fee_types;
        $late_loan_payment_fine_types = $this->loan->late_loan_payment_fine_types;
        $late_payments_fine_frequency = $this->loan->late_payments_fine_frequency;
        $fixed_amount_fine_frequency_on = $this->loan->fixed_amount_fine_frequency_on;
        $percentage_fine_on = $this->loan->percentage_fine_on;
        $one_off_fine_types = $this->loan->one_off_fine_types;
        $one_off_percentage_rate_on = $this->loan->one_off_percentage_rate_on;
        $loan_processing_fee_percentage_charged_on = $this->loan->loan_processing_fee_percentage_charged_on;
    }

    function get_listing(){
    	$total_rows = $this->loan_types_m->count_all();
        $pagination = create_pagination('bank/loan_types/listing/pages',$total_rows,50,5,TRUE);
    	$posts = $this->loan_types_m->limit($pagination['limit'])->get_all();
    	$loan_interest_rate_per = $this->loan->loan_interest_rate_per;
    	$interest_types = $this->loan->interest_types;
    	if(empty($posts)){
    		echo '
    		<div class="alert alert-info">
		        <h4 class="block">Information! No records to display</h4>
		        <p>
		            No Loan Types to display.
		        </p>
		    </div>';
    	}else{
    		echo form_open('group/loan_types/action', ' id="form"  class="form-horizontal"');
    		if(!empty($pagination['links'])): 
    			echo '
			    <div class="row col-md-12">
			        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Loan Types</p>';
			        echo '<div class ="top-bar-pagination">';
			        echo $pagination['links']; 
		        echo '</div></div>';
		    endif; 
		    echo '
		    <table class="table table-striped table-hover table-header-fixed table-condensed table-searchable">
		        <thead>
		            <tr>
		                <th width=\'2%\'>
		                     <input type="checkbox" name="check" value="all" class="check_all">
		                </th>
		                <th width=\'2%\'>
		                    #
		                </th>
		                <th>
		                    Loan Details
		                </th>
		                <th>
		                    Actions
		                </th>
		            </tr>
		        </thead>
		        <tbody>';
		            $i = $this->uri->segment(5, 0); 
		            foreach($posts as $post){
		            	echo '
		                <tr>
		                    <td><input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" /></td>
		                    <td>'.($i+1).'.</td>
		                    <td>
		                    	<strong>Name: </strong> 
		                        '.$post->name.'<br/>
		                        <strong>Amount: </strong>'.number_to_currency($post->minimum_loan_amount).' - '.number_to_currency($post->maximum_loan_amount).' 
		                        <br/>
		                        <strong>Repayment: </strong>'.$post->repayment_period.' Months
		                        <br/>
		                        <strong>Interest Rate: </strong>'.$post->interest_rate.'% per '.$loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$interest_types[$post->interest_type].'
		                    </td>';
		                    echo '
		                    </td>
		                    <td>
		                        <a href="'.site_url('group/loan_types/edit/'.$post->id).'" class="btn btn-xs default">
		                            <i class="fa fa-edit"></i> Edit &nbsp;&nbsp; 
		                        </a>';
		                        if($post->is_hidden){
		                        	echo '
		                            <a href="'.site_url('group/loan_types/hide/'.$post->id).'" class="confirmation_link btn btn-xs blue">
		                                <i class="fa fa-eye"></i> Display &nbsp;&nbsp; 
		                            </a>';
		                        }else{
		                        	echo '
		                            <a href="'.site_url('group/loan_types/hide/'.$post->id).'" class="confirmation_link btn btn-xs yellow">
		                                <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
		                            </a>';
		                        }
		                        echo '
		                    </td>
		                </tr>';
		                $i++;
		            }
		            echo '
		        </tbody>
		    </table>';
		    echo '
		    <div class="clearfix"></div>
		    <div class="row col-md-12">';
		        if( ! empty($pagination['links'])): 
		        echo $pagination['links']; 
		        endif; 
		    echo ' 
		    </div>
		    <div class="clearfix"></div>';
		    if($posts):
		    	echo '
		        <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_hide\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Hide</button>
		        <button class="btn btn-sm btn-primary confirmation_bulk_action" name=\'btnAction\' value=\'bulk_display\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Display</button>';
		    endif;
		    echo '
		    <div class="clearfix"></div>
		    ';
			echo form_close();	
		}
    }

    function get_loan_type_information1(){
    	$loan_type_id = $this->input->post('loan_type_id');
    	if($loan_type_id){
    		$loan_type = $this->loan_types_m->get($loan_type_id);
    		if($loan_type){
        		$loan_interest_rate_per = $this->loan->loan_interest_rate_per;
        		$interest_types = $this->loan->interest_types;
        		$loan_repayment_period_type = $this->loan->loan_repayment_period_type;
        		$loan_amount_type = $this->loan->loan_amount_type;
        		$loan_processing_fee_types = $this->loan->loan_processing_fee_types;
        		$late_loan_payment_fine_types = $this->loan->late_loan_payment_fine_types;
        		$late_payments_fine_frequency = $this->loan->late_payments_fine_frequency;
        		$fixed_amount_fine_frequency_on = $this->loan->fixed_amount_fine_frequency_on;
        		$percentage_fine_on = $this->loan->percentage_fine_on;
        		$one_off_fine_types = $this->loan->one_off_fine_types;
        		$one_off_percentage_rate_on = $this->loan->one_off_percentage_rate_on;
        		$loan_grace_periods = $this->loan->loan_grace_periods;
        		$loan_days = $this->loan->loan_days;
        		$loan_processing_fee_percentage_charged_on = $this->loan->loan_processing_fee_percentage_charged_on;
    			$table = '';
    			$table .= '
    				<div class="text-center">
		                <h5>'.$loan_type->name.'</h5>
		            </div>
    				<table class="table">
	                    <tbody>
	                        <tr>
	                            <th nowrap>
	                            	'.translate('Amount').'
                            	</th>
                            	<td nowrap>';
		                            if($loan_type->loan_amount_type == 2){
	                                    $table .= translate('Get upto ').' '.$loan_type->loan_times_number.translate(' times your savings'); 
	                                }else{
	                                    $table .= translate('Get between ').' '.$this->group_currency.' '.number_to_currency($loan_type->minimum_loan_amount).' - '.number_to_currency($loan_type->maximum_loan_amount);
	                                }
    								$table .= '
	                            </td>
	                        </tr>
	                        	<th nowrap>
	                            	'.translate('Grace Period').'
                            	</th>
	                            <td nowrap>';
	                            	if($loan_type->grace_period == 12){
                                        $table .= 'Start paying after 1 '.translate('year');
                                    }else if($loan_type->grace_period == 'date'){
                                    }elseif($loan_type->grace_period>=1 || $loan_type->grace_period <=12){
                                        $table .= $loan_type->grace_period.' '.translate('months');
                                    }
    								$table .= '
	                            </td>
	                        </tr>
	                        	<th nowrap>
	                            	'.translate('Repayment').'
                            	</th>
	                            <td nowrap>';
                                	if($loan_type->loan_repayment_period_type == 1){
                                     	$table .= $loan_type->fixed_repayment_period.' '.translate('Months Repayment Period');
                                    }else if ($loan_type->loan_repayment_period_type == 2) {
                                         $table .= $loan_type->minimum_repayment_period.' - '.$loan_type->maximum_repayment_period.translate('Months Repayment Period');
                                    }
    								$table .= '
	                            </td>
	                        </tr>
	                        	<th nowrap>
	                            	'.translate('Interest Rate').'
                            	</th>
	                            <td nowrap>';
		                            if($loan_type->interest_type ==3){
	                                    $table .= $interest_types[$loan_type->interest_type]; 
	                                }else{
	                                    $table .= $loan_type->interest_rate.'% '.$loan_interest_rate_per[$loan_type->loan_interest_rate_per].' on '.$interest_types[$loan_type->interest_type]; 
	                                }
	                                $table .= '
	                            </td>
	                        </tr>
	                        <tr>
	                            <th nowrap>
	                            	'.translate('Late Payment Fine').'
	                            </th>
	                            <td nowrap>';
	                            	if($loan_type->enable_loan_fines){
                                        $table .= $late_loan_payment_fine_types[$loan_type->loan_fine_type].' of ';
                                        if($loan_type->loan_fine_type==1){
                                            $table .= $this->group_currency.' '.number_to_currency($loan_type->fixed_fine_amount).' fine '.$late_payments_fine_frequency[$loan_type->fixed_amount_fine_frequency].' on ';
                                            $table .= isset($fixed_amount_fine_frequency_on[$loan_type->fixed_amount_fine_frequency_on])?$fixed_amount_fine_frequency_on[$loan_type->fixed_amount_fine_frequency_on]:'';
                                        }else if($loan_type->loan_fine_type==2){
                                            $table .= $loan_type->percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan_type->percentage_fine_frequency].' on '.$percentage_fine_on[$loan_type->percentage_fine_on];
                                        }else if($loan_type->loan_fine_type==3){
                                            if($loan_type->one_off_fine_type==1){
                                                $table .= $this->group_currency.' '.number_to_currency($loan_type->one_off_fixed_amount).' per Installment';
                                            }else if($loan_type->one_off_fine_type==2){
                                                $table .= $loan_type->one_off_percentage_rate.'% on '.$percentage_fine_on[$loan_type->one_off_percentage_rate_on];
                                            }
                                        }
                                    }else{
                                        $table .= ' <span class="m-badge m-badge--success m-badge--wide">'.translate('None').'</span><br/>';
                                    }
                                    $table .= '
	                            </td>
	                        </tr>
	                        <tr>
	                            <th nowrap>
	                            	'.translate('Outstanding loan balance fine').'
	                            </th>
	                            <td nowrap>';
		                            if($loan_type->enable_outstanding_loan_balance_fines):
	                                    if($loan_type->outstanding_loan_balance_fine_type==1){
	                                        $table .= $this->group_currency.' '.number_to_currency($loan_type->outstanding_loan_balance_fine_fixed_amount).' '.$late_payments_fine_frequency[$loan_type->outstanding_loan_balance_fixed_fine_frequency];
	                                    }else if($loan_type->outstanding_loan_balance_fine_type==2){
	                                        $table .= $loan_type->outstanding_loan_balance_percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan_type->outstanding_loan_balance_percentage_fine_frequency].' on '.$percentage_fine_on[$loan_type->outstanding_loan_balance_percentage_fine_on];
	                                    }else{
	                                        $table .= translate('One Off Amount').$this->group_currency.' '.number_to_currency($loan_type->outstanding_loan_balance_fine_one_off_amount);
	                                    }
	                               else:
	                                    $table .= ' <span class="m-badge m-badge--success m-badge--wide">'.translate('None').'</span><br/>';
	                                endif;
	                                $table .= '
	                            </td>
	                        </tr>
	                        <tr>
	                            <th nowrap>
	                            	'.translate('Processing Fee Charges').'
	                            </th>
	                            <td nowrap>';
		                            if($loan_type->enable_loan_processing_fee):
	                                   if($loan_type->loan_processing_fee_type==1){
	                                        $table .= translate('Fixed Amount of ').$this->group_currency.' '.number_to_currency($loan_type->loan_processing_fee_fixed_amount).'</br>';
	                                    }else{
	                                        $table .= $loan_type->loan_processing_fee_percentage_rate.'% of '.$loan_processing_fee_percentage_charged_on[$loan_type->loan_processing_fee_percentage_charged_on].'<br/>';
	                                    }
	                                else:
		                                    $table .= ' <span class="m-badge m-badge--success m-badge--wide">'.translate('None').'</span><br/>';
	                                endif;
	                                $table .= '
	                            </td>
	                        </tr>
	                        <tr>
	                            <th nowrap>
	                            	'.translate('Guarantors').'
	                            </th>
	                            <td nowrap>';
		                            if($loan_type->enable_loan_guarantors == 1){
	                                    if($loan_type->loan_guarantors_type == 1){
	                                        $table .= translate('A minimum of ').$loan_type->minimum_guarantors.translate(' guarantors required');
	                                    }else if($loan_type->loan_guarantors_type == 2){
	                                        $table .= translate('A minimum of ').$loan_type->minimum_guarantors.translate(' guarantors required if your application exceeds the maximum loan amount');
	                                    }
	                                }else{
		                                $table .= ' <span class="m-badge m-badge--success m-badge--wide">'.translate('None Required').'</span><br/>';
	                                }
	                                $table .= '
	                            </td>
	                        </tr>
	                    </tbody>
	                </table>
	            ';
	            $response = array(
		    		'status' => 1,
		    		'table' => $table,
		    	);
    		}else{
    			$response = array(
		    		'status' => 0,
		    		'message' => 'error',
		    	);
    		}
	    }else{
	    	$response = array(
	    		'status' => 0,
	    		'message' => 'error',
	    	);
	    }
	    echo json_encode($response);
    }
    
    function get_loan_type_information(){
    	if($loan_type_id = $this->input->post('loan_type_id')){
	    	$loan_interest_rate_per = $this->loan->loan_interest_rate_per;
	    	$interest_types = $this->loan->interest_types;
	    	$validation_rules = array(
	    		array(
	                'field' => 'loan_type_id',
	                'label' => 'Loan Type',
	                'rules' => 'trim|required|numeric',
	            ),
	    	);

	        $this->form_validation->set_rules($validation_rules);
	        $response = array();
	        if($this->form_validation->run()){
	        	$loan_type_id = $this->input->post('loan_type_id');
	        	$loan_type = $this->loan_types_m->get($loan_type_id);
	        	if($loan_type){
	        		$member_id = $this->member->id;
	        		$data['contribution_options'] = $this->contributions_m->get_group_savings_contribution_options();
       				$savings = $this->reports_m->get_group_member_total_contributions($this->member->id,$data['contribution_options']); 
       				$total_savings = $savings>0?$savings:0;
	        		$html ='';
	        		$maximum_amount_to_borrow = 0;
	        		$html.=' 
	        		<div class="m-section__content">
						<div class="m-demo" data-code-preview="true" data-code-html="true" data-code-js="false">
							<div class="m-demo__preview">        			
			        			<strong>'.translate('Loan Type').' : </strong>  '.ucfirst($loan_type->name).' 
			        			<br>';    			        		
				        		if($loan_type->loan_amount_type == 2){
				        		    $html.='<strong> '.translate('You total savings').' : </strong> '.$this->group_currency. ' ' .number_to_currency($total_savings).' <br>';	
				        		    $maximum_amount_to_borrow = ($loan_type->loan_times_number?:1) * $total_savings;	 
				        			$html.='<strong>'.translate('Maximum amount you can borrow').': </strong>  ' .$this->group_currency. ' ' .number_to_currency($maximum_amount_to_borrow). ' <br> ';
				        		}else if($loan_type->loan_amount_type == 1){ 
				        		    $html.='<strong> '.translate('Amount you can borrow').' : </strong> '.translate('Between').' '.$this->group_currency.' '.number_to_currency($loan_type->minimum_loan_amount).' - '.$this->group_currency.' '.number_to_currency($loan_type->maximum_loan_amount).'<br>';
				        		}

				        		if($loan_type->grace_period == 12){
				        			$html.='<strong>'.translate('Grace Period').': </strong>  1year <br> ';
                                }else if($loan_type->grace_period == 'date'){
				        			$html.='<strong>'.translate('Grace Period').': </strong>  Custom Dates <br> ';
                                }elseif($loan_type->grace_period>=1 || $loan_type->grace_period <=12){
				        			$html.= '<strong>'.translate('Grace Period').': </strong>'.$loan_type->grace_period.' '.translate('month').''.($loan_type->grace_period>1?'s':'').' <br> ';
                                }

				        		if($loan_type->loan_repayment_period_type == 1){
				        		  $html.='<strong>  '.translate('Repayment Period').' : </strong>'. $loan_type->fixed_repayment_period.' '.translate('months').' <br>';
				        		}else if($loan_type->loan_repayment_period_type == 2){
				        		  $html.='<strong> '.translate('Repayment Period').' : </strong>'. $loan_type->minimum_repayment_period.' - '.$loan_type->maximum_repayment_period.' '.translate('months').' <br>';
				        		}


				        		if($loan_type->interest_type ==3){
                                    $html.= '<strong> '.translate('Interest Rate').': </strong> ' .$interest_types[$loan_type->interest_type]; 
                                }else{
                                    $html.= '<strong> '.translate('Interest Rate').': </strong> ' .$loan_type->interest_rate.'% '.$loan_interest_rate_per[$loan_type->loan_interest_rate_per].' '.translate('on').' '.$interest_types[$loan_type->interest_type];
                                }
				        		// $html.='  <strong> Interest Rate: </strong> ' .$loan_type->interest_rate.'%  '.$loan_interest_rate_per[$loan_type->loan_interest_rate_per].' on '.$interest_types[$loan_type->interest_type].' .
                            ' </div>
						</div>
					</div>
	        		';


	        		// if($loan_type->loan_amount_type == 2){
	        		//     $html.='<strong> You total savings : </strong> '.$this->group_currency. ' ' .number_to_currency($total_savings).' <br>';	
	        		//     $maximum_amount_to_borrow = $loan_type->loan_times_number * $total_savings;	 
	        		// 	$html.='<strong>Maximum amount you can borrow: </strong>  ' .$this->group_currency. ' ' .number_to_currency($maximum_amount_to_borrow). ' <br> ';
	        		// }else if($loan_type->loan_amount_type == 1){ 
	        		//     $html.='<strong> Amount you can borrow : </strong> Between '.$this->group_currency.' '.number_to_currency($loan_type->minimum_loan_amount).' - '.$this->group_currency.' '.number_to_currency($loan_type->maximum_loan_amount).'<br>';
	        		// }
	        		$response = array(
	        			'status' => 200,
		        		'message' => 'ok',
		        		'html' => $html,
		        		'loan_guarantor_type' =>$loan_type->loan_guarantors_type,
		        		'member_savings' => currency($maximum_amount_to_borrow),
		        		'loan_amount_type' => $loan_type->loan_amount_type,
		        		'minimum_guarantors' => $loan_type->minimum_guarantors,
		        		'minimum_loan_amount' => $loan_type->minimum_loan_amount,
		        		'maximum_loan_amount' => $loan_type->maximum_loan_amount,
		        		'repayment_period_type' => $loan_type->loan_repayment_period_type,
	        		);	        		
	        	}else{
	        		$response = array(
		        		'status' => 0,
		        		'message' => 'Could not find loan type.',
		        		'html' => '',
		        	);
	        	}
	        }else{
	        	$response = array(
	        		'status' => 0,
	        		'message' => 'Validation',
	        		'html' => validation_errors(),
	        	);
	        }
	        echo json_encode($response);
	    }
    }   

    function get_loan_repayment_type(){
        $loan_type_id =  $this->input->post('loan_type_id');
        $get_loan_type_details = $this->loan_types_m->get($loan_type_id);
        $loan_repayment_period_type = $get_loan_type_details->loan_repayment_period_type;
        $loan_amount_type = $get_loan_type_details->loan_amount_type;
        if($loan_repayment_period_type == 1){
        	echo json_encode(1);
        }elseif ($loan_repayment_period_type == 2) {
        	echo json_encode(2);
        }
    }

    function get_no_of_guarantors(){
    	$loan_type_id =  $this->input->post('loan_type_id');
        $get_loan_type_details = $this->loan_types_m->get($loan_type_id);
        $loan_repayment_period_type = $get_loan_type_details->loan_repayment_period_type;
        $loan_amount_type = $get_loan_type_details->loan_amount_type;
        $maximum_guarantors =$get_loan_type_details->maximum_guarantors; 
        if($get_loan_type_details->maximum_guarantors > 0){
        	echo json_encode($maximum_guarantors-1);
        }else {
        	 echo json_encode(0);
        }       
    }

    function get_loan_types_guarantors_option(){
    	$loan_type_id = $this->input->post('loan_type_id');
    	$loan_type_details = $this->loan_types_m->get($loan_type_id);
    	$response = array();
    	if($loan_type_details->enable_loan_guarantors){
    		if($loan_type_details->loan_guarantors_type == 1){
    			$response = array(
	    			'status' => 200,
	    			'message' => 'OK',
	    			'enabled' => 1,
	    		);
    		}else{
    			if($loan_type_details->loan_guarantors_type ==2){
    				$response = array(
		    			'status' => 200,
		    			'message' => 'OK',
		    			'enabled' => 2,
		    		);
    			}
    		}
    	}else{
    		$response = array(
    			'status' => 200,
    			'message' => 'OK',
    			'enabled' => 0,
    		);
    	}
    	echo json_encode($response);
    }

    function check_if_exceed_amount(){
    	$loan_type_id = $this->input->post('loan_type_id');
    	$get_loan_type_details = $this->loan_types_m->get($loan_type_id);
    	$loan_application_amount = currency($this->input->post('loan_amount'));
    	$member_id = $this->member->id;       			
	    $total_savings = $this->reports_m->get_group_member_total_contributions($member_id);
	    $get_number_to_multiply = $get_loan_type_details->loan_times_number;
	    $maximum_amount = $total_savings * $get_number_to_multiply;
	    echo  json_encode($maximum_amount);
    }

    function _additional_validation_rules(){
        if($this->input->post('loan_amount_type') == 1){
            $this->validation_rules[] = array(
                'field' => 'minimum_loan_amount',
                'label' => 'Minimum Loan Amount',
                'rules' => 'required|trim|currency'
            );
            $this->validation_rules[] = array(
                'field' => 'maximum_loan_amount',
                'label' => 'Maximum Loan Amount',
                'rules' => 'required|trim|currency|callback__is_greater_than_minimum_loan_amount'
            );
        }elseif ($this->input->post('loan_amount_type') == 2) {
        	$this->validation_rules[] = array(
                'field' => 'loan_times_number',
                'label' => 'Times number of savings',
                'rules' => 'required|trim|required'
            );            
        }
    	if($this->input->post('enable_loan_guarantors')){
    		$this->validation_rules[] = array(
					'field' => 'loan_guarantors_type',
					'label' => 'Loan Guarantor Type',
					'rules' => 'trim|numeric|required'
				);
    	}

        if($this->input->post('loan_guarantors_type') == 1){
            $this->validation_rules[] = array(
                    'field' => 'minimum_guarantors',
                    'label' => 'Minimum Guarantors',
                    'rules' => 'trim|numeric|required'
            );
        }elseif($this->input->post('loan_guarantors_type') == 2){
            $this->validation_rules[] = array(
                    'field' => 'minimum_guarantors_exceed_amount',
                    'label' => 'Minimum Guarantors',
                    'rules' => 'trim|numeric|required'
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

    function create(){ 
		 
    	$post = new StdClass();
        $response = array();
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

    	
    	$this->_additional_validation_rules();
    	$this->form_validation->set_rules($this->validation_rules);
    	if($this->form_validation->run()){
    		$name = $this->input->post('name');
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
				'interest_rate' => $this->input->post('interest_rate'),
				'interest_type' => $this->input->post('interest_type'),
				'loan_interest_rate_per' => $this->input->post('loan_interest_rate_per'),
                'enable_reducing_balance_installment_recalculation' => $this->input->post('enable_reducing_balance_installment_recalculation'),
				'grace_period' => $this->input->post('grace_period')?:1,
				'enable_loan_fines' => $this->input->post('enable_loan_fines')?:0,
				'enable_outstanding_loan_balance_fines' => $this->input->post('enable_outstanding_loan_balance_fines')?:0,
				'enable_loan_processing_fee' => $this->input->post('enable_loan_processing_fee')?:0,
				'enable_automatic_disbursements' => $this->input->post('enable_automatic_disbursements')?:0,
				'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment')?:0,
				'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors')?:0,
				'is_admin'=>1,
				'created_by' => $this->user->id,
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
				$data = $data + array('loan_processing_fee_type'=>$this->input->post('loan_processing_fee_type'),
				'loan_processing_recovery_on' => $this->input->post('loan_processing_recovery_on'));
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
    		if($loan_type_id = $this->loan_types_m->insert($data)){
                // $this->groups_m->update($this->group->id,array(
                //     'group_offer_loans' => 1,
                //     'modified_on' => time(),
                //     'modified_by' => $this->user->id,
                // ));
                $response = array(
                    'status' => 1,
                    'id' => $loan_type_id,
                    'name' => $name,
                    'massage' => 'Loan type successfully added',
                    'refer'=>site_url('bank/loan_types/listing'),
                );
    		}else{
                $response = array(
                    'status' => 0,
                    'massage' => 'Could not add loan type. Refresh page and try again'
                );
    		}
    	}else{
    		// $post = array();
      //       $form_errors = $this->form_validation->error_array();
      //       foreach ($form_errors as $key => $value) {
      //           $post[$key] = $value;
      //       }
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $this->form_validation->error_array(),
            );
    	}
        echo json_encode($response);
    }

    function edit($id = 0){
      	$id = $this->input->post('loan_type_id')?:$this->input->post('id');
      	if($id){
	      	$post = $this->loan_types_m->get($id);
	      	if(!$post){
	    		$response = array(
		            'status' => 0,
		            'massage' => 'Loan type details is missing',
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
	    				'minimum_loan_amount' => $this->input->post('minimum_loan_amount'),    				
	    				'maximum_loan_amount' => $this->input->post('maximum_loan_amount'),
	    				'loan_repayment_period_type'=>$this->input->post('loan_repayment_period_type'),
	    				'fixed_repayment_period'=>$this->input->post('fixed_repayment_period'),
	    				'minimum_repayment_period' => $this->input->post('minimum_repayment_period'),
	    				'maximum_repayment_period' => $this->input->post('maximum_repayment_period'),
	    				'interest_rate' => $this->input->post('interest_rate'),
	    				'interest_type' => $this->input->post('interest_type'),
	    				'loan_interest_rate_per' => $this->input->post('loan_interest_rate_per'),
	    				'grace_period' => $this->input->post('grace_period')?:1,
	    				'enable_loan_fines' => $this->input->post('enable_loan_fines')?:0,
						'enable_automatic_disbursements' => $this->input->post('enable_automatic_disbursements')?:0,
						'limit_to_one_loan_application' => $this->input->post('limit_to_one_loan_application')?:0,
	    				'enable_outstanding_loan_balance_fines' => $this->input->post('enable_outstanding_loan_balance_fines')?:0,
	    				'enable_loan_processing_fee' => $this->input->post('enable_loan_processing_fee')?:0,
	    				'enable_loan_fine_deferment' => $this->input->post('enable_loan_fine_deferment')?:0,
	    				'enable_loan_guarantors' => $this->input->post('enable_loan_guarantors')?:0,
	    				'created_by' => $this->user->id,
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
					$data = $data + array('loan_processing_fee_type'=>$this->input->post('loan_processing_fee_type'),
					'loan_processing_recovery_on' => $this->input->post('loan_processing_recovery_on'));
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
	    		if($this->loan_types_m->update($id,$data)){
	    			$response = array(
	                    'status' => 1,
	                    'massage' => 'Loan type successfully updated',
	                    'refer'=>site_url('bank/loan_types/listing'),
	                );
	    		}else{
	    			$response = array(
	                    'status' => 0,
	                    'massage' => 'Error updating loan type'
	                );
	    		}
	    	}else{
	    		$post = array();
	            $form_errors = $this->form_validation->error_array();
	            foreach ($form_errors as $key => $value) {
	                $post[$key] = $value;
	            }
	            $response = array(
	                'status' => 0,
	                'message' => 'There are some errors on the form. Please review and try again.',
	                'validation_errors' => $post,
	            );
	    	}
      	}else{
      		$response = array(
            	'status' => 0,
            	'massage' => 'Loan type id is missing',
        	);
      	}
      	echo json_encode($response);
    }

    function delete(){
        $response = array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->loan_types_m->get($id);  
            if($post){
            	$password = $this->input->post('password');
	            $identity = valid_phone($this->user->phone)?:$this->user->email;
	            if($this->ion_auth->login($identity,$password)){
	            	if($this->loans_m->count_loan_applications_by_loan_type($id)){
	            		$response = array(
	                        'status' => 0,
	                        'message'=>'Not allowed to delete this loan type',
	                    );
	            	}else{
	            		if($this->loan_types_m->safe_delete($post->id,$post->group_id)){
		            		$response = array(
		                        'status'=>1,
		                        'message'=>'Loan Type deleted successfully',
		                        'refer'=>$this->agent->referrer(),
		                    );
		                }else{
		                	$response = array(
		                        'status'=>0,
		                        'message'=>'Loan Type could not be deleted'
		                    );
		                }
	            	}
	            }else{
	                $response = array(
	                    'status' => 0,
	                    'message'=>'You entered the wrong password'
	                );
	            } 
            }else{
            	$response = array(
                    'status'=>0,
                    'message'=>'Could not find loan type to delete'
                );
            }         
        }else{
            $response = array(
                'status'=>0,
                'message'=>'COntribution id is required'
            );

        }
        echo json_encode($response);
    }    

    function listing(){
    	$posts = $this->loan_types_m->get_all();
    	$html = '';
    	if(!empty($posts)){
    		$i = 1;
    		$disabled = $this->input->get_post('disabled')?:'';
            $html.='
    		 <table class="table table-condensed table-striped margin-top-10 table-bordered">
                <thead>
                    <tr>
                        <th width="2%">
                            #
                        </th>
                        <th width="15%">
                            '.translate('Name').'
                        </th>
                        <th>
                            '.translate('Loan Details').'
                        </th>
                        <th '.($disabled?'class="hidden"':'').'>
                            '.translate('Actions').'
                        </th>
                    </tr>
                </thead>
                <tbody>';
                foreach($posts as $post):
                	$html.= ' <tr data-id="'.$post->id.'">';
                    $html.= ' <td>'.$i++.'</td>';
                    $html.= '<td class="name" data-name="'.$post->name.'">'.$post->name.'</td>';
                    $html.= '<td>';
	                $html.= '<strong> '.translate('Amount').' :</strong> '. number_to_currency($post->minimum_loan_amount).' - '.number_to_currency($post->maximum_loan_amount).'
	                        <br/>
	                        <br/>';
	                $html.= '<strong>'.translate('Repayment Type').' :</strong>'.$this->loan_repayment_period_type[$post->loan_repayment_period_type] .'
	                        <br/><br/>';
	                if($post->loan_repayment_period_type == 1){
                    $html.= '<strong>'.translate('Repayment').' :</strong> '. $post->fixed_repayment_period.' '.translate('Months').''.'
	                        <br/><br/>';
                    }else if($post->loan_repayment_period_type == 2){
                      $html.= '<strong>'.translate('Repayment').' :</strong> '. $post->minimum_repayment_period.' - '.$post->maximum_repayment_period.' '.translate('Months').' '.'
	                        <br/><br/>' ;
                    	} 	                        
                        
                    $html.= '<strong>'.translate('Interest Rate').' :</strong>'. $post->interest_rate.'% per '.$this->loan->loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$this->loan->interest_types[$post->interest_type].'
                        <br/><br/>';
                   $html.= '</td>';
                    $html.= '<td '.($disabled?'class="hidden"':'').'>';
                    $html.= '<a href="#" class="btn btn-xs default edit_loan_types full_width_inline" data-content="#create_loan_type_form_holder"  data-id="edit_loan_types" id="">
                            <i class="icon-pencil"></i> '.translate('Edit').' &nbsp;&nbsp;
                        </a><br>';
                    $html.= '<a data-title="'.translate('Enter your password to delete the Group Loan Types').'"  data-content="This will delete the group loan types permanently. Are you sure you want to proceed?" href='.site_url('ajax/loan_types/delete/'.$post->id).' class="tooltips btn btn-xs red prompt_confirmation_message_link">
                            <i class="fa fa-trash"></i> '.translate('Delete').' &nbsp;&nbsp; 
                        </a>';
                    $html.= '</td>';

                endforeach;

               $html.= '</tbody>
            </table>';

    	}else{ 
    	$html.='<div class="alert alert-info">
                <h4 class="block">'.translate('Information! No records to display').'</h4>
                <p>
                    '.translate('No contributions to display').'.
                </p>
            </div>';
    	}
    	    
        if($posts){
            $status = 1;
        }else{
            $status = 2;
        }
        echo json_encode(array(
            "status" => $status,
            "html" => $html,
        ));
    }

    function get(){
      $id = $this->input->post('id');
      if($id){
      	$loan_type_details = $this->loan_types_m->get($id);      
      	  echo json_encode($loan_type_details);
      }else{
      	echo "loan type is missing";
      }
    }

    function ajax_get_loan_details(){
        $loan_type_id = $this->input->post('loan_type_id');
        if($loan_type_id){
        	$loan_type_details = $this->loan_types_m->get($loan_type_id);
        	if($loan_type_details){
        		echo json_encode($loan_type_details);
        	}else{
        		echo json_encode("loan types details is missing");
        	}
        }else{
            echo json_encode("loan id is missing");
        }
    }

    function get_maximum_savings(){
    	$loan_type_id = $this->input->post('loan_type_id');
        if($loan_type_id){
        	$loan_type_details = $this->loan_types_m->get($loan_type_id);
        	if($loan_type_details){
        		$member_id = $this->input->post('member_id');	  
        		$data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();        
   				$savings = $this->reports_m->get_group_member_total_contributions($this->member->id,$data['contribution_options']); 
   				$total_savings = 0;
   				if($savings > 0){
        			$total_savings = $savings;
        		}else if($savings < 0){
        			$total_savings = 0;
        		}
        		$maximum_amount_to_borrow = 0;
        		if($loan_type_details->loan_amount_type == 2){	
        		    $maximum_amount_to_borrow = $loan_type_details->loan_times_number * $total_savings;	
        		}
        		echo json_encode($maximum_amount_to_borrow);
        	}else{
        		echo json_encode("loan types details is missing");
        	}
        }else{
            echo json_encode("loan id is missing");
        }
    }

    function setup_listing(){
    	$per_page = ($this->input->post('length'))>1?$this->input->post('length'):0;
        $start_number = $this->input->post('start');
        $order = $this->input->post('order');
        $order = $this->input->post('order');
        if($order){
            $dir = strtoupper($order[0]['dir']);
        }else{
            $dir = 'ASC';
        }
        $search = $this->input->post('search');
        $name ='';
        if($search){
            $name = $search['value'];
        }
        $filter_parameters = array(
            'name' => $name,
        );
    	$total_rows = $this->loan_types_m->count_group_loan_types('',$filter_parameters);
    	$pagination = create_custom_pagination('bank/loan_types/listing/pages', $total_rows,$per_page,$start_number,TRUE);
        $posts = $this->loan_types_m->limit($pagination['limit'])->get_all('',$filter_parameters);
        foreach ($posts as $key => $post) {
            $loan_details ='';
            $loan_type ='';
            $other_details ='';
            $loan_type = '<strong>'.$this->loan->loan_amount_type[$post->loan_amount_type].'</strong>';
            if($post->loan_amount_type == 1){
                $loan_details.='<strong>Amount Range</strong>: '.$this->group_currency.'. '.number_to_currency($post->minimum_loan_amount).' - '.$this->group_currency.'. '.number_to_currency($post->maximum_loan_amount).'<br/><br/>';
            }else if($post->loan_amount_type == 2){
                $loan_details.='<strong>Multiplier</strong>: '.$post->loan_times_number.' times member savings<br/><br/>';
            }

            if($post->interest_type == 1 || $post->interest_type==2){
                $calc = '';
                if($post->enable_reducing_balance_installment_recalculation){
                    $calc = ' - Recalculations on early payments enabled';
                }
                $loan_details.= '<strong>Interest Rate:</strong> '.$post->interest_rate.'% per '.$this->loan->loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$this->loan->interest_types[$post->interest_type].$calc.'<br/><br/>';
            }else{
                $loan_details.= '<strong>Interest Rate:</strong> Custom <br/><br/>';
            }
            $other_details = '<strong>Fines:</strong> ';
            if($post->enable_loan_fines){
                if($post->loan_fine_type==1){
                    $other_details.=$this->group_currency.' '.number_to_currency($post->fixed_fine_amount).' fine '.$this->loan->late_payments_fine_frequency[$post->fixed_amount_fine_frequency].' on ';
                    $other_details.= isset($this->loan->fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on])?$this->loan->fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on]:'';
                    $other_details.= '<br/><br/>';
                }else if($post->loan_fine_type==2){
                    $other_details.= $post->percentage_fine_rate.'% fine '.$this->loan->late_payments_fine_frequency[$post->percentage_fine_frequency].' on '.$this->loan->percentage_fine_on[$post->percentage_fine_on].'<br/><br/>';
                }else if($post->loan_fine_type==3){
                    if($post->one_off_fine_type==1){
                        $other_details.= $this->group_currency.' '.number_to_currency($post->one_off_fixed_amount).' per Installment<br/><br/>';
                    }else if($post->one_off_fine_type==2){
                        $other_details.= $post->one_off_percentage_rate.'% on '.$this->loan->percentage_fine_on[$post->one_off_percentage_rate_on];
                        $other_details.='<br/><br/>';
                    }
                }
            }else{
                $other_details.=" Disabled <br/><br/>";
            }
            $other_details.= '<strong>Outstanding loan fine:</strong> ';
            if($post->enable_outstanding_loan_balance_fines){
                if($post->outstanding_loan_balance_fine_type==1){
                    $other_details.=$this->group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_fixed_amount).' '.$this->loan->late_payments_fine_frequency[$post->outstanding_loan_balance_fixed_fine_frequency].'<br/><br/>';
                }else if($post->outstanding_loan_balance_fine_type==2){
                    $other_details.=$post->outstanding_loan_balance_percentage_fine_rate.'% fine '.$this->loan->late_payments_fine_frequency[$post->outstanding_loan_balance_percentage_fine_frequency].' on '.$this->loan->percentage_fine_on[$post->outstanding_loan_balance_percentage_fine_on].'<br/><br/>';
                }else{
                    $other_details.='One Off Amount '.$this->group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_one_off_amount).'<br/><br/>';
                }
            }else{
                $other_details.=" Disabled <br/><br/>";
            }
            $this->data[] = array(
                ($key+1).'.',
                $post->name,
                $loan_details,
                $other_details,
                $post->id,
            );
        }
        echo json_encode(array(
            "data" => $this->data,
            "iTotalDisplayRecords" => $total_rows,
            "iTotalRecords" => $this->loan_types_m->count_group_loan_types(),
        ));
    }

    function get_loan_types(){
    	$loan_types = $this->loan_types_m->get_options($this->group->id);
    	if($loan_types){
    		$this->response = array(
    			'result_code'=>200,
    			'result_description'=>"success",
    			'data'=>$loan_types
    		);
    	}else{
    		$this->response = array(
    			'result_code'=>0,
    			'result_description'=>"Loan type details does not exist",
    			'data'=>''
    		);
    	}
    	echo json_encode($this->response);

    }

    function get_loan_type_view($id=0){
    	$response = array();
    	if($id){
    		$post = $this->loan_types_m->get($id);
    		if($post){
    			$html='
				<div class="row invoice-body">
                    <div class="col-xs-12 table-responsive ">
                        <table class="table table-sm m-table m-table--head-separator-primary table table--hover table-borderless table-condensed loan-types-table">
                            <thead>
                                <tr>
                                    <th width="30%">'.$post->name.' '.translate('loan details').'</th>
                                    <th class="m--align-right">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="<?php echo $post->id ?>_active_row">
                                    <td class="m--align-right" nowrap>
                                        <strong>'.translate('Loan Amount').'</strong>
                                    </td>
                                    <td>: ';
	                                    $html.=$this->loan->loan_amount_type[($post->loan_amount_type?:1)];
										if($post->loan_amount_type == 1  ){
	                                        $html.='('.number_to_currency($post->minimum_loan_amount).' - '.number_to_currency($post->maximum_loan_amount).')';
	                                    }else if($post->loan_amount_type == 2){
	                                        $html.='('.$post->loan_times_number.'  times your savings)'; 
	                                    }else if($post->loan_amount_type == ''){
	                                        $html.='('.number_to_currency($post->minimum_loan_amount).' - '.number_to_currency($post->maximum_loan_amount).')';
	                                    }
	                                $html.='
                                    </td>
                                </tr>
                                <tr>
                                    <td class="m--align-right" nowrap>
                                        <strong>'.translate('Grace Period').'</strong>
                                    </td>
                                    <td>: ';
                                        if($post->grace_period == 12){
                                            $html.= '1 '.translate('year');
                                        }else if($post->grace_period == 'date'){
                                            $html.= translate('Custom Date');
                                        }elseif($post->grace_period>=1 || $post->grace_period <=12){
                                            $html.= $post->grace_period.' '.translate('months');
                                        }
                                    $html.='
                                    </td>
                                </tr>
                                <tr>
                                    <td class="m--align-right" nowrap>
                                        <strong>'.translate('Repayment').'</strong>
                                    </td>
                                    <td>: ';
                                        $html.=translate($this->loan->loan_repayment_period_type[$post->loan_repayment_period_type]).' '.translate('of').' ';

                                        if($post->loan_repayment_period_type == 1){
                                         	$html.= $post->fixed_repayment_period.' Months';
                                        }else if ($post->loan_repayment_period_type == 2) {
                                            $html.=$post->minimum_repayment_period.' - '.$post->maximum_repayment_period.' Months';
                                        }
                                    $html.='
                                    </td>
                                </tr>
                                <tr>
                                    <td class="m--align-right" nowrap>
                                        <strong>'.translate('Interest').'</strong>
                                    </td>
                                    <td>: ';
                                    if($post->interest_type ==3){
                                        $html.= $this->loan->interest_types[$post->interest_type]; 
                                    }else{
                                        $html.= $post->interest_rate.'% '.$this->loan->loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$this->loan->interest_types[$post->interest_type]; 
                                    }
                                    $html.='
                                    </td>
                                </tr>
                                <tr>
                                    <td class="m--align-right" nowrap>
                                        <strong>'.translate('Late Payment Fine').'</strong>
                                    </td>
                                    <td>: ';
                                        if($post->enable_loan_fines){
                                            $html.= $this->loan->late_loan_payment_fine_types[$post->loan_fine_type].' of ';
                                            if($post->loan_fine_type==1){
                                                $html.= $this->group_currency.' '.number_to_currency($post->fixed_fine_amount).' fine '.$this->loan->late_payments_fine_frequency[$post->fixed_amount_fine_frequency].' on ';
                                                $html.= isset($this->loan->fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on])?$this->loan->fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on]:'';
                                                $html.= '<br/>';
                                            }else if($post->loan_fine_type==2){
                                                $html.= $post->percentage_fine_rate.'% fine '.$this->loan->late_payments_fine_frequency[$post->percentage_fine_frequency].' on '.$this->loan->percentage_fine_on[$post->percentage_fine_on].'<br/>';
                                            }else if($post->loan_fine_type==3){
                                                if($post->one_off_fine_type==1){
                                                    $html.= $this->group_currency.' '.number_to_currency($post->one_off_fixed_amount).' per Installment<br/>';
                                                }else if($post->one_off_fine_type==2){
                                                    $html.= $post->one_off_percentage_rate.'% on '.$this->loan->percentage_fine_on[$post->one_off_percentage_rate_on];
                                                }
                                            }
                                        }else{
                                        	$html.='
                                            <span class="m-badge m-badge--success m-badge--wide">'.translate('Disabled').'</span><br/>';
                                        }
                                        $html.='
                                    </td>
                                </tr>
                                <tr>
                                    <td class="m--align-right" nowrap>
                                        <strong>'.translate('Outstanding loan balance fine').'</strong>
                                    </td>
                                    <td>: ';
                                        if($post->enable_outstanding_loan_balance_fines):
                                            if($post->outstanding_loan_balance_fine_type==1){
                                                $html.= $this->group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_fixed_amount).' '.$this->loan->late_payments_fine_frequency[$post->outstanding_loan_balance_fixed_fine_frequency].'<br/>';
                                            }else if($post->outstanding_loan_balance_fine_type==2){
                                                $html.= $post->outstanding_loan_balance_percentage_fine_rate.'% fine '.$this->loan->late_payments_fine_frequency[$post->outstanding_loan_balance_percentage_fine_frequency].' on '.$this->loan->percentage_fine_on[$post->outstanding_loan_balance_percentage_fine_on].'<br/>';
                                            }else{
                                                $html.= 'One Off Amount '.$this->group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_one_off_amount).'<br/>';
                                            }
                                        else:
                                        	$html.='
                                            <span class="m-badge m-badge--success m-badge--wide">'.translate('Disabled').'</span><br/>';
                                        endif;
                                        $html.='
                                    </td>
                                </tr>
                                <tr>
                                    <td class="m--align-right" nowrap>
                                        <strong>'.translate('Processing Fee Charges').'</strong>
                                    </td>
                                    <td>: ';
                                        if($post->enable_loan_processing_fee):
                                            if($post->loan_processing_fee_type==1){
                                                $html.= 'Fixed Amount of '.$this->group_currency.' '.number_to_currency($post->loan_processing_fee_fixed_amount).'</br>';
                                            }else{
                                                $html.= $post->loan_processing_fee_percentage_rate.'% of '.$this->loan->loan_processing_fee_percentage_charged_on[$post->loan_processing_fee_percentage_charged_on].'<br/>';
                                            }
                                        else:
                                        	$html.='
                                            <span class="m-badge m-badge--success m-badge--wide">'.translate('Disabled').'</span><br/>';
                                        endif;
                                        $html.='
                                    </td>
                                </tr>
                                <tr>
                                    <td class="m--align-right" nowrap>
                                        <strong>'.translate('Guarantors').'</strong>
                                    </td>
                                    <td>: ';
                                        if($post->enable_loan_guarantors == 1){
                                            if($post->loan_guarantors_type == 1){
                                                $html.= 'A minimum of '.$post->minimum_guarantors.' guarantors required';
                                            }else if($post->loan_guarantors_type == 2){
                                                $html.= 'A Minimum of '.$post->minimum_guarantors.' guarantors required';
                                            }
                                            if($post->loan_guarantors_type == 1){
                                                $html.= ' '.translate('every time a member is applying a loan');   
                                            }else if($post->loan_guarantors_type == 2){
                                                $html.= ' '.translate('When an applicant loan request exceeds loan limit ');   
                                            }
                                        }else{
                                            $html.= '<span class="m-badge m-badge--success m-badge--wide">'.translate('Disabled').'</span>';
                                        }
                                    $html.='
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
    			';
                $response = array(
                    'status' => 1,
                    'message' => $html,
                );
    		}else{
    			$response = array(
	    			'status' => 0,
	    			'message'=> 'Could not find the selected loan type. Try again',
	    		);
    		}
    	}else{
    		$response = array(
    			'status' => 0,
    			'message'=> 'Could not find the selected loan type. Try again',
    		);
    	}
    	echo json_encode($response);
    }
}