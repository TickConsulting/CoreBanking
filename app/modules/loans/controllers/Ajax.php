<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
    protected $data = array();
	protected $loan_type;

    protected $validation_rules = array(
        array(
            'field' => 'loan_type_id',
            'label' => 'Loan Type',
            'rules' => 'xss_clean|trim|required|numeric',
        ),
        array(
            'field' =>  'member_id',
            'label' =>  'Member',
            'rules' =>  'xss_clean|trim|required|numeric'
        ),
        array(
            'field' =>  'disbursement_date',
            'label' =>  'Loan Disbursement Date',
            'rules' =>  'xss_clean|trim|required'
        ),
        array(
            'field' =>  'loan_amount',
            'label' =>  'Loan Amount',
            'rules' =>  'xss_clean|trim|required|currency|greater_than[0]|callback__valid_application_amount'
        ),
        array(
            'field' =>  'repayment_period',
            'label' =>  'Loan Repayment Period',
            'rules' =>  'xss_clean|trim|required|numeric'
        ),
        array(
            'field' =>  'account_id',
            'label' =>  'Loan Disbursing Account',
            'rules' =>  'xss_clean|trim|required'
        ),
        array(
            'field' => 'repayment_period', 
            'label' => 'Loan Repayment Period', 
            'rules' => 'xss_clean|trim|callback__valid_repayment_period'
        )
    );

    protected $application_rules = array(
        array(
            'field' => 'loan_type_id', 
            'label' => 'Loan Type', 
            'rules' => 'xss_clean|trim|required|numeric'
        ),array(
            'field' => 'loan_application_amount', 
            'label' => 'Loan Application Amount', 
            'rules' => 'xss_clean|trim|required|currency|callback__valid_application_amount'
        ), array(
            'field' => 'loan_rules_check_box', 
            'label' => 'Agree to loan rules', 
            'rules' => 'xss_clean|trim|numeric'
        ), array(
            'field' => 'guaranteed_amount[]', 
            'label' => 'Guaranteed Amount', 
            'rules' => ''
        ),
        // array(
        //     'field' => 'guarantor_id[]', 
        //     'label' => 'Gurantor', 
        //     'rules' => 'callback__valid_guarantor_details'
        // ),
        array(
            'field' => 'repayment_period', 
            'label' => 'Loan Repayment Period', 
            'rules' => 'xss_clean|trim|callback__valid_repayment_period'
        )
    );
    
    function __construct(){
        parent::__construct();
        $this->load->model('loans_m');
        $this->load->model('wallets/wallets_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('reports/reports_m');
        $this->load->model('loan_types/loan_types_m');
        $this->load->model('loan_invoices/loan_invoices_m');
        $this->load->model('loan_repayments/loan_repayments_m');
        $this->load->model('members/members_m');
        $this->load->library('loan');
    }

    function _valid_application_amount(){
        $this->loan_type = $this->loan_types_m->get($this->input->post('loan_type_id'));
        $member=$this->members_m->get_group_member($this->input->post('member_id'));
        $loan_application_amount = currency($this->input->post('loan_amount'));
        if($member->loan_limit < currency($this->input->post('loan_amount'))){
            $this->form_validation->set_message('_valid_application_amount','Your loan limit is '.number_to_currency($member->loan_limit));
            return FALSE;
        }
        if($this->loan_type->loan_amount_type == 1){//range
            if($loan_application_amount < $this->loan_type->minimum_loan_amount){
                $this->form_validation->set_message('_valid_application_amount','Amount applied is below the allowed minimum amount');
                return FALSE;
            }elseif($loan_application_amount > $this->loan_type->maximum_loan_amount){
                if($this->loan_type->enable_loan_guarantors){
                    return TRUE; //handled in guarantor callback
                }else{
                    $this->form_validation->set_message('_valid_application_amount','Amount applied is above the allowed maximum amount');
                    return FALSE;
                }
            }else{
                if($this->loan_type->enable_loan_guarantors){
                    return TRUE; //handled in guarantor callback
                }else{
                    if($loan_application_amount > $this->loan_type->maximum_loan_amount){
                        $this->form_validation->set_message('_valid_application_amount','Amount applied is above the allowed maximum amount');
                        return FALSE;
                    }else{
                        return TRUE;
                    }
                }
            }
        }
        
        else if($this->loan_type->loan_amount_type == 2){//member savings
            $data['contribution_options'] = $this->contributions_m->get_group_savings_contribution_options();
            $member_savings = $this->reports_m->get_group_member_total_contributions($this->member->id,$data['contribution_options']); 
            $maximum_allowed_loan = $member_savings * ($this->loan_type->loan_times_number?$this->loan_type->loan_times_number:1);
            if($this->loan_type->enable_loan_guarantors){
                return TRUE; //handled in guarantor callback
            }else{
                if($loan_application_amount > $maximum_allowed_loan){
                    $this->form_validation->set_message('_valid_application_amount','Loan applied is above '.$this->loan_type->loan_times_number.' times your savings');
                    return FALSE;
                }else{
                    return TRUE;
                }
            }
        }
        
        else{
            $this->form_validation->set_message('_valid_application_amount','Invalid loan type selected');
            return FALSE;
        }

    
    }

    function _valid_repayment_period(){
        //check repayment period here
        if($this->loan_type->loan_repayment_period_type == 1){//fixed
            return TRUE;
        }elseif($this->loan_type->loan_repayment_period_type == 2){
            if($this->input->post('repayment_period') < $this->loan_type->minimum_repayment_period){
                $this->form_validation->set_message('_valid_repayment_period','Loan repayment period is less than the allowed repayment period');
                return FALSE;
            }else if($this->input->post('repayment_period') > $this->loan_type->maximum_repayment_period){
                $this->form_validation->set_message('_valid_repayment_period','Loan repayment period is above than the allowed repayment period');
                return FALSE;
            }else{
                return TRUE;
            }
        }
    }
    

    function _verify_guarantor_name(){
        $guarantors = $this->input->post('guarantor_id');
        $member_id = $this->input->post('member_id');
        $guaranteed_amounts = $this->input->post('guaranteed_amount');
        //print_r($guarantors); die();
        if(!empty($guarantors)){
            if(count($guarantors)>= 1)
            {
                for($i=0;$i<count($guarantors);$i++)
                {
                    if(empty($guarantors[$i]))
                    {
                      $this->form_validation->set_message('_verify_guarantor_name','The Guarantor Name field is required');
                        return FALSE;   
                    }
                    if($guarantors[$i]==$member_id)
                    {
                        $this->form_validation->set_message('_verify_guarantor_name','Guarantor number '.++$i.' should not be the same as the member taking the Loan');
                        return FALSE; 
                    }
                    if(!currency($guaranteed_amounts[$i]) && !empty($guaranteed_amounts[$i]))
                    {
                        $this->form_validation->set_message('_verify_guarantor_name','The Guaranteed amount row '.++$i.' must be a valid currency');
                        return FALSE;  
                    }
                    else{
                        return TRUE;
                    }
                }
            }else
            {
                $this->form_validation->set_message('_verify_guarantor_name','Add atleast one guarantor');
                return FALSE;
            }
        }
    }

    function calculate_loan_balance($loan_id=0){
        $amount_payable=($this->loan_invoices_m->get_total_installment_loan_payable($loan_id));
        $amount_paid=($this->loan_repayments_m->get_loan_total_payments($loan_id));
        $balance= ($amount_payable-$amount_paid);
        $balance=isset($balance) && $balance>=0?$balance:0;
        return number_to_currency($balance);
    }

    function get_loans_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'member_id' => $this->input->get('member_id')?:'',
            'accounts' => $this->input->get('accounts')?:'',
            'is_fully_paid' => $this->input->get('is_fully_paid')?:'',
            'from' => $from,
            'to' => $to,
        );
        $account_options = $this->accounts_m->get_group_account_options(FALSE);
        $total_rows = $this->loans_m->count_all_group_loans($filter_parameters);
        $pagination = create_pagination('bank/loans/listing/pages', $total_rows,50,5,TRUE);
        $loan_type_options = $this->loan_types_m->get_options();
        $posts = $this->loans_m->limit($pagination['limit'])->get_group_loans($filter_parameters);
        if(!empty($posts)){
        echo form_open('bank/loans/action', ' id="form"  class="form-horizontal"');
        if(!empty($pagination['links'])):
            echo '
            <div class="row col-md-12">
                <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Member Loans</p>';
                echo '<div class ="top-bar-pagination">';
                echo $pagination['links']; 
                echo '</div></div>';
                endif; 
            echo '  
                <div class="table-responsive">
                    <table class="table m-table m-table--head-separator-primary">
                        <thead>
                            <tr>
                                <th width=\'2%\' nowrap>
                                    <label class="m-checkbox">
                                        <input type="checkbox" name="check" value="all" class="check_all">
                                        <span></span>
                                    </label>
                                </th>
                                <th nowrap>
                                   #
                                </th>
                                <th nowrap>'.
                                    translate('Member')
                                .'</th>
                                <th nowrap>'.
                                    translate('Loan Type')
                                .'</th>
                                <th nowrap class=\'text-right\'>'.
                                    translate('Amount').' (KES)
                                </th>  
                                <th nowrap class=\'text-right\'>'.
                                translate('Balance').' (KES)
                            </th> 
                                <th nowrap class="text-right">'.
                                    translate('Disbursement On')
                                .'</th>
                                <th nowrap>
                                
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = $this->uri->segment(5, 0); 
                            foreach($posts as $post):
                                echo '
                                    <tr>
                                        <td scope="row" class="align-middle">
                                            <label class="m-checkbox">
                                                <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td scope="row" class="align-middle">'.
                                            ++$i.'
                                        </td>
                                        <td scope="row" class="align-middle">'.
                                            $this->group_member_options[$post->member_id].'
                                        </td>
                                        <td scope="row" class="align-middle">';
                                            echo isset($loan_type_options[$post->loan_type_id])?$loan_type_options[$post->loan_type_id]:'Normal Loan';

                                            if($post->is_fully_paid){
                                                echo '  <span class="m-badge m-badge--info m-badge--wide"><small>'.translate('Fully paid').'</small></span>';
                                            }else{
                                                
                                            }
                                            if($post->is_a_bad_loan){
                                                echo '  <span class="m-badge m-badge--danger m-badge--wide"><small>'.translate('Bad Loan').'</small></span>';
                                            }
                                        echo     
                                        '</td>
                                        <td scope="row" class="text-right align-middle">'.
                                            number_to_currency($post->loan_amount).'
                                        </td>
                                        <td scope="row" class="text-right align-middle">'.
                                        calculate_loan_balance($post->id).'
                                    </td>
                                        <td scope="row" class="text-right align-middle">'.
                                            timestamp_to_date($post->disbursement_date).'
                                        </td>
                                        <td nowrap class="text-left align-middle">
                                            <a href="'.site_url('bank/loans/edit/'.$post->id).'" class="btn btn-sm btn-primary m-btn m-btn--icon action_button" id="'.$post->id.'">
                                                <span>
                                                    <i class="la la-pencil"></i>
                                                    <span>
                                                        '.translate('Edit').' &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>&nbsp;&nbsp;
                                            <a href="'.site_url('bank/loans/view_installments/'.$post->id).'" class="btn btn-sm btn-primary m-btn m-btn--icon  action_button" id="'.$post->id.'" data-target="#deposit_receipt">
                                                <span>
                                                    <i class="fa fa-list-alt"></i>
                                                    <span>
                                                        '.translate('Installments').' &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>&nbsp;&nbsp;
                                            <a href="javascript:;" class="btn btn-sm btn-primary m-btn m-btn--icon view_loan_statement action_button" id="'.$post->id.'" data-toggle="modal" data-target="#deposit_receipt" data-keyboard="false" data-backdrop="static">
                                                <span>
                                                    <i class="la la-eye"></i>
                                                    <span>
                                                        '.translate('More').' &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>
                                            &nbsp;&nbsp;&nbsp;
                                            <br/>';
                                            if($post->is_fully_paid){
                                            }else{
                                                echo
                                                '<a href="'.site_url('bank/deposits/record_loan_repayments/?loan_id='.$post->id.'&member_id='.$post->member_id).'" class="btn btn-sm btn-primary m-btn m-btn--icon action_button mt-1" id="'.$post->id.'" data-target="#deposit_receipt">
                                                    <span>
                                                        <i class="mdi mdi-cash-multiple"></i>
                                                        <span>
                                                            '.translate('Record Repayment').' &nbsp;&nbsp; 
                                                        </span>
                                                    </span>
                                                </a>&nbsp;&nbsp;';
                                            }
                                        echo '
                                            <a href="'.site_url('bank/loans/void/'.$post->id).'" class="btn btn-sm btn-primary m-btn m-btn--icon action_button confirmation_link mt-1" id="'.$post->id.'">
                                                <span>
                                                    <i class="la la-trash-o"></i>
                                                    <span>
                                                        '.translate('Void').' &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>&nbsp;&nbsp;
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                        echo
                        '</tbody>
                    </table>
                </div>
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
                <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" type="button" data-placement="top"> <i class=\'fa fa-trash-o\'></i> '.translate('Bulk Void').'</button>';
            endif;
            echo '
            <div class="clearfix"></div>';
        echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Sorry').'!</strong> '.translate('There are no loans to display').'.
                </div>';
        }
    }
    function get_banks_loans_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'member_id' => $this->input->get('member_id')?:'',
            'accounts' => $this->input->get('accounts')?:'',
            'is_fully_paid' => $this->input->get('is_fully_paid')?:'',
            'from' => $from,
            'to' => $to,
        );
        $account_options = $this->accounts_m->get_group_account_options(FALSE);
        $total_rows = $this->loans_m->count_all_group_loans($filter_parameters);
        $pagination = create_pagination('bank/loans/listing/pages', $total_rows,10,5,TRUE);
        $loan_type_options = $this->loan_types_m->get_options();
        $posts = $this->loans_m->limit($pagination['limit'])->get_group_loans($filter_parameters);
        if(!empty($posts)){
        echo form_open('bank/loans/action', ' id="form"  class="form-horizontal"');
        if(!empty($pagination['links'])):
            echo '
            <div class="row col-md-12">
                <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Member Loans</p>';
                echo '<div class ="top-bar-pagination">';
                echo $pagination['links']; 
                echo '</div></div>';
                endif; 
            echo '  
                <div class="table-responsive">
                    <table class="table m-table m-table--head-separator-primary">
                        <thead>
                            <tr>
                                <th width=\'2%\' nowrap>
                                    <label class="m-checkbox">
                                        <input type="checkbox" name="check" value="all" class="check_all">
                                        <span></span>
                                    </label>
                                </th>
                                <th nowrap>
                                   #
                                </th>
                                <th nowrap>'.
                                    translate('Applicant')
                                .'</th>
                                <th nowrap>'.
                                    translate('Loan Product')
                                .'</th>
                                <th nowrap class=\'text-right\'>'.
                                    translate('Amount').' (KES)
                                </th>  
                                <th nowrap class=\'text-right\'>'.
                                    translate('Balance').' (KES)
                                </th>  
                                <th nowrap class="text-right">'.
                                    translate('Disbursement On')
                                .'</th>
                                <th nowrap>
                                
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = $this->uri->segment(5, 0); 
                            foreach($posts as $post):
                                echo '
                                    <tr>
                                        <td scope="row" class="align-middle">
                                            <label class="m-checkbox">
                                                <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td scope="row" class="align-middle">'.
                                            ++$i.'
                                        </td>
                                        <td scope="row" class="align-middle">'.
                                            $this->members_m->get_group_member($post->member_id)->first_name.' '.$this->members_m->get_group_member($post->member_id)->last_name.'
                                        </td>
                                        <td scope="row" class="align-middle">';
                                            echo isset($loan_type_options[$post->loan_type_id])?$loan_type_options[$post->loan_type_id]:'Normal Loan';

                                            if($post->is_fully_paid){
                                                echo '  <span class="m-badge m-badge--info m-badge--wide"><small>'.translate('Fully paid').'</small></span>';
                                            }else{
                                                
                                            }
                                            if($post->is_a_bad_loan){
                                                echo '  <span class="m-badge m-badge--danger m-badge--wide"><small>'.translate('Bad Loan').'</small></span>';
                                            }
                                        echo     
                                        '</td>
                                        <td scope="row" class="text-right align-middle">'.
                                            number_to_currency($post->loan_amount).'
                                        </td>
                                        <td scope="row" class="text-right align-middle">'.
                                            $this->calculate_loan_balance($post->id).'
                                        </td>
                                        <td scope="row" class="text-right align-middle">'.
                                            timestamp_to_date($post->disbursement_date).'
                                        </td>
                                        <td nowrap class="text-left align-middle">
                                            <a href="'.site_url('bank/loans/edit/'.$post->id).'" class="btn btn-sm btn-primary m-btn m-btn--icon action_button" id="'.$post->id.'">
                                                <span>
                                                    <i class="la la-pencil"></i>
                                                    <span>
                                                        '.translate('Edit').' &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>&nbsp;&nbsp;
                                            <a href="'.site_url('bank/loans/view_installments/'.$post->id).'" class="btn btn-sm btn-primary m-btn m-btn--icon  action_button" id="'.$post->id.'" data-target="#deposit_receipt">
                                                <span>
                                                    <i class="fa fa-list-alt"></i>
                                                    <span>
                                                        '.translate('Installments').' &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>&nbsp;&nbsp;
                                            <a href="javascript:;" class="btn btn-sm btn-primary m-btn m-btn--icon view_loan_statement action_button" id="'.$post->id.'" data-toggle="modal" data-target="#deposit_receipt" data-keyboard="false" data-backdrop="static">
                                                <span>
                                                    <i class="la la-eye"></i>
                                                    <span>
                                                        '.translate('More').' &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>
                                            &nbsp;&nbsp;&nbsp;
                                            <br/>';
                                            if($post->is_fully_paid){
                                            }else{
                                                echo
                                                '<a href="'.site_url('bank/deposits/record_loan_repayments/?loan_id='.$post->id.'&member_id='.$post->member_id).'" class="btn btn-sm btn-primary m-btn m-btn--icon action_button mt-1" id="'.$post->id.'" data-target="#deposit_receipt">
                                                    <span>
                                                        <i class="mdi mdi-cash-multiple"></i>
                                                        <span>
                                                            '.translate('Record Repayment').' &nbsp;&nbsp; 
                                                        </span>
                                                    </span>
                                                </a>&nbsp;&nbsp;';
                                            }
                                        echo '
                                            
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                        echo
                        '</tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
                <div class="row col-md-12">';
            if( ! empty($pagination['links'])): 
                echo $pagination['links']; 
            endif; 
            echo '
            </div>
            <div class="clearfix"></div>';
            
            echo '
            <div class="clearfix"></div>';
        echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Sorry').'!</strong> '.translate('There are no loans to display').'.
                </div>';
        }
    }

    function ajax_loan_guarantor_listing(){
       $group_id = $this->group->id;
        $member_id = $this->member->id;
        $user_id = $this->user->id;
        $get_loan_guarantor_request = $this->loans_m->get_loan_application_guarantorship_requests_by_member_id($member_id,$group_id);        
        if(!empty($get_loan_guarantor_request)){  ?>
            <table class="table table-condensed table-striped table-hover table-header-fixed ">
            <thead>
                <tr>                    
                    <th width="2%">
                        #
                    </th>
                    <th>
                        Guarantee Details
                    </th>
                    <th class="text-right">
                        Loan Amount (<?php echo $this->group_currency; ?>)
                    </th>
                </tr>
            </thead>
            <tbody> <?php
            $count =0;
            foreach ($get_loan_guarantor_request as $key => $get_loan_guarantor_request_details) {
                $count++;
                $guarantor_id = $get_loan_guarantor_request_details->id;
                $loan_type_id = $get_loan_guarantor_request_details->loan_type_id;
                $guarantor_member_id =$get_loan_guarantor_request_details->guarantor_member_id;                
                $loan_application_id = $get_loan_guarantor_request_details->loan_application_id;
                $loan_request_applicant_user_id = $get_loan_guarantor_request_details->loan_request_applicant_user_id;
                $loan_request_progress_status = $get_loan_guarantor_request_details->loan_request_progress_status;
                $decline_comment = $get_loan_guarantor_request_details->decline_comment;
                $guarantor_loan_amount = $get_loan_guarantor_request_details->amount;
                $get_loan_type = $this->loan_types_m->get($loan_type_id);
                $get_loan_application_details = $this->loan_applications_m->get($loan_application_id);

                $loan_amount = isset($get_loan_application_details->loan_amount)?$get_loan_application_details->loan_amount:'';

                $get_loan_applicant_details = $this->users_m->get($loan_request_applicant_user_id);
                $get_loan_guarantor_member_details = $this->members_m->get($guarantor_member_id);              
                $guarantor_user_id = $get_loan_guarantor_member_details->user_id;
                $get_loan_guarantor_user_details = $this->users_m->get($guarantor_user_id);                
                $loan_progress_status = $get_loan_guarantor_request_details->loan_request_progress_status;
               ?>
                <tr> 
                   
                    <td><?php echo $count;?></td>

                    <td>
                        <strong> Loan Name : </strong> <?php echo isset($get_loan_application_details->name)?$get_loan_application_details->name:'' ?> <br>
                        <strong> Loan Applicant Name : </strong> <?php echo $get_loan_applicant_details->first_name.' '.$get_loan_applicant_details->last_name ?> <br>
                        <?php if($loan_progress_status == 3){?>
                            <strong> Amount you have guaranteed : </strong> <?php echo number_to_currency($guarantor_loan_amount) ?> <br>
                        <?php }elseif ($loan_request_progress_status == 2) {?>
                            <strong>  Amount you could have guaranteed : </strong> <?php echo number_to_currency($guarantor_loan_amount) ?> <br>
                        <?php } elseif ($loan_request_progress_status == 1) {?>
                          <strong> Amount you will guarantee : </strong> <?php echo number_to_currency($guarantor_loan_amount) ?> <br>
                        <?php } ?>  
                        <strong> Your response to the request: </strong> 
                        <?php if($loan_progress_status == 1){ ?>
                            <span class="label label-success">In Progress</span><?php
                        }else if($loan_progress_status==2){?>
                             <span class="label label-danger">  Declined</span>
                        <?php }else if($loan_progress_status==3){?>
                             <span class="label label-success">  Approved</span>
                          <?php }
                        else{ ?>
                            <span class="label label-danger"> loan Declined</span>
                        <?php } ?>
                        
                    </td>
                    <td class="text-right"><?php echo number_to_currency($loan_amount) ?></td>

                  </tr><?php

            }?>
        </tbody>
        </table><?php
        }else{?>
            <div class="alert alert-danger">
               <button class="close" data-dismiss="alert"></button>
               <strong>Error!</strong> Could not find loans  application details
            </div>
         <?php
        }
    }

    function ajax_loan_requests_listing(){
      $loan_type_options = $this->loan_types_m->get_all();
      $user_id =$this->user->id;
      $member_id = $this->member->id;
      $group_id = $this->group->id;
      $count = 0;
      $get_loan_application_option = $this->loan_applications_m->get_member_loan_applications($group_id,$member_id);      
      if(!empty($get_loan_application_option)){    
      echo '<table class="table table-condensed table-striped table-hover table-header-fixed ">
        <thead>
            <tr>
                <th width="2%">
                     <span><input name="check" value="all" class="check_all" type="checkbox"></span>
                </th>
                <th width="2%">
                    #
                </th>
                <th>
                    Loan Details
                </th>
                <th class="text-right">
                    Amount (KES)
                </th>
            </tr>
        </thead>
        <tbody>';    
           foreach ($get_loan_application_option as $key => $get_loan_application_details) {        
               $loan_type_id = $get_loan_application_details->loan_type_id;
               $loan_type_options = $this->loan_types_m->get($loan_type_id); 
                $count++;             
                $loan_type_name = isset($loan_type_options->name)?$loan_type_options->name:'';
                $loan_application_id = $get_loan_application_details->id; 
                $member_id = $get_loan_application_details->member_id;
                $active = $get_loan_application_details->active;  
                $status = $get_loan_application_details->status;              
                $loan_amount = isset($get_loan_application_details->loan_amount)?$get_loan_application_details->loan_amount:'';
                $get_loan_applicant_user_details = $this->users_m->get($user_id); 
                $get_loan_type_application_requests = $this->loans_m->get_member_request_loans_application($loan_type_id,$loan_application_id,$group_id,$member_id);
                $get_loan_type_signatory_request = $this->loans_m->get_signatory_member_request_loans_application($loan_type_id,$loan_application_id,$group_id,$member_id);
                
                echo'<tr> 
                        <td><span>
                             <input type="checkbox" name="check" value="all" class="check_all"></span></td>
                        <td>'  .$count; 
                        echo '</td>
                        <td>
                            <strong> Loan Name : </strong>'  .$loan_type_name; 
                            echo '<br>';
                        echo ' <strong> Member Name : </strong> '  .$get_loan_applicant_user_details->first_name.' '.$get_loan_applicant_user_details->last_name;
                        echo '<br>';
                            if(isset($loan_type_options->loan_repayment_period_type)?$loan_type_options->loan_repayment_period_type:'' == 1){
                                ;
                            echo' <strong> Loan Duration : </strong> <span>'
                                    .$loan_type_options->fixed_repayment_period .' months</span> &nbsp;<strong> <br>';
                            }else if(isset($loan_type_options->loan_repayment_period_type)?$loan_type_options->loan_repayment_period_type:'' == 2){ 
                                echo '<strong> Loan Duration : </strong> <span>' .$loan_type_options->minimum_repayment_period .'  - '. $loan_type_options->maximum_repayment_period .' months</span> &nbsp; <br><br>';
                            }
                            if($active == 1){
                                echo '<strong> Loan Request  Status: </strong> <span class="label label-success">In Progress</span> <br>';
                            }else{ 
                                 echo '<strong> Loan Request  Status: </strong> <span class="label label-danger"> loan Declined</span> <br>';
                            }                       
                        echo '</td>';
                        echo '<td class="text-right">' .number_to_currency($loan_amount). '</td>   
                    </tr>'; 
                } 
        echo '</tbody>
     </table>';
          }else{ 
            echo '<div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No Loan Guarantor Requests to display.
                </p>
            </div>';  }
 
    }

    function ajax_loan_information(){
        $loan_application_id = $this->input->post('loan_application_id');
        if($loan_application_id){
            $validation_rules = array(
                array(
                    'field' => 'loan_application_id',
                    'label' => 'Loan application id parameter',
                    'rules' => 'trim|required|numeric',
                ),
            );
            $this->form_validation->set_rules($validation_rules);
            $response = array();
            if($this->form_validation->run()){
                $guarantors_available = $this->loans_m->check_if_loan_guarantors_exist($loan_application_id);
                $supervisor_recommendations_available = $this->loans_m->if_supervisor_recommendation_exists($loan_application_id);
                $hr_appraisal_available = $this->loans_m->if_payroll_accountant_exist($loan_application_id);
                $sacco_appraisals = $this->loans_m->if_sacco_appraisal_exist($loan_application_id);
                $committee_decision = $this->loans_m->if_committee_decision_exist($loan_application_id);
                $response = array(
                    'status' => 200,
                    'message' => 'ok',
                    'guarantors_available' =>$guarantors_available,
                    'supervisor_recommendations_available' =>$supervisor_recommendations_available,
                    'payroll_accountant' => $hr_appraisal_available,
                    'sacco_appraisal' => $sacco_appraisals,
                    'committee_decision' => $committee_decision
                );
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

    function ajax_loan_calculator(){
       $response = array();
       $loan_values = array();
       $total_amount_payable = 0;
       $total_principle_amount = 0;
       $total_interest = 0;
       $monthly_payment = 0;
       $loan_type_id = $this->input->post('loan_type_id');
       $loan_type = $this->loan_types_m->get($loan_type_id);
       $loan_amount =  currency($this->input->post('loan_amount'));
       $repayment_period = $this->input->post('repayment_period');
       $today = time(); 
       $total_payable =0; 
                            $total_principle=0;
                            $balance = $total_amount_payable;
                            $i=0;
                            $total_interest=0;
       $html ='';
       if($loan_type->interest_type == 1 || $loan_type->interest_type == 2){
            if($loan_type->loan_repayment_period_type == 1){ 
               $loan_values = $this->loan->calculate_loan_balance_invoice(
                        $loan_amount,
                        $loan_type->interest_type,
                        $loan_type->interest_rate,
                        $loan_type->fixed_repayment_period,
                        '',time(),
                        $loan_type->loan_interest_rate_per);
                foreach ($loan_values as $key => $value) {
                    $value = (object)$value;
                    $total_amount_payable +=$value->amount_payable;
                    $total_principle_amount+=$value->principle_amount_payable;
                    $total_interest+=$value->interest_amount_payable;
                    $monthly_payment=$value->amount_payable;
                } 
            }else if($loan_type->loan_repayment_period_type == 2){
                if($repayment_period){
                    $minimum_repayment_period = $loan_type->minimum_repayment_period;
                    $maximum_repayment_period =  $loan_type->maximum_repayment_period;
                    if($repayment_period >= $minimum_repayment_period && $repayment_period <= $maximum_repayment_period){
                        $loan_values = $this->loan->calculate_loan_balance_invoice(
                        $loan_amount,
                        $loan_type->interest_type,
                        $loan_type->interest_rate,
                        $repayment_period,
                        '',time(),
                        $loan_type->loan_interest_rate_per);
                        foreach ($loan_values as $key => $value) {
                            $value = (object)$value;
                            $total_amount_payable +=$value->amount_payable;
                            $total_principle_amount+=$value->principle_amount_payable;
                            $total_interest+=$value->interest_amount_payable;
                            $monthly_payment=$value->amount_payable;
                        }
                    }else{ 
                        $html.='<div class="m-alert m-alert--outline m-alert--outline-2x alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>'.translate('Sorry').'!</strong> Repayment period must be between <?php echo  $minimum_repayment_period ?> - <?php echo $maximum_repayment_period ?> months
                        </div>';
                    } 
                }else{
                    $html.='<div class="m-alert m-alert--outline m-alert--outline-2x alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>'.translate('Sorry').'!</strong> '.translate('Loan repayment period is required').'.
                    </div>';
                } 
            }
            $html.='<div class="amortized_schedule">
            <div class="clearfix table_details"></div>';
                if($loan_values):
                $html.='<div class="row">
                    <div class="col-md-6">
                        <strong>Total Amount Payable:</strong> '. $this->group_currency.' '.number_to_currency($total_amount_payable) .'<br/>
                        <strong>Total Interest :</strong> '.  $this->group_currency.' '.number_to_currency($total_interest) .'<br/>
                        <strong>Repayment Period:</strong> ';
                        if($loan_type->loan_repayment_period_type == 1){
                            $html.= $loan_type->fixed_repayment_period;
                        }else if($loan_type->loan_repayment_period_type == 2){
                           $html.= isset($repayment_period)?$repayment_period:''; 
                        }
                        $html.=' Months<br/>       
                    </div>
                    <div class="col-md-6 loan_details_calc">                  
                        <strong>Monthly Payments :</strong> '. $this->group_currency.' '.number_to_currency($monthly_payment).'<br/> 
                        <strong>Interest Rate :</strong> '. $loan_type->interest_rate .' % .'. $this->loan->loan_interest_rate_per[$loan_type->loan_interest_rate_per] ;
                                if($loan_type->loan_interest_rate_per!=3){
                                    if($loan_type->loan_interest_rate_per==1){
                                        $html.= 'at '.number_format($loan_type->interest_rate*30,1).'% Monthly rate';
                                    }
                                    else if($loan_type->loan_interest_rate_per==2){
                                        $html.= 'at '.number_format($loan_type->interest_rate*4,1).'% Monthly rate';
                                    }
                                    else if($loan_type->loan_interest_rate_per==4){
                                        $html.= 'at '.number_format($loan_type->interest_rate/12,1).'% Monthly rate';
                                    }else if($loan_type->loan_interest_rate_per==5){
                                        $interest_rate = $loan_type->interest_rate;
                                        $repayment_period = $repayment_period?:$loan_type->fixed_repayment_period;
                                        $html.= 'at '.number_format($interest_rate/$repayment_period,1).'% Monthly rate';
                                    }
                                }
                                $html.='
                            
                            <br/>
                        <strong>Interest Type :</strong> '. $this->loan->interest_types[$loan_type->interest_type] .'<br/>
                    </div>
                </div><br><hr>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-hover table-striped table-condensed table-statement">
                        <!-- <thead>
                            <tr>
                                <th nowrap class="invoice-title  text-right">** Amounts in '. $this->group_currency .'</th>
                            </tr>
                        </thead> -->
                        <thead>
                            <tr>
                                <th nowrap class="invoice-title" width="2%">#</th>
                                <th nowrap class="invoice-title">'.  translate('Payment Date') .'</th>
                                <th nowrap class="invoice-title text-right">'.  translate('Amount Payable') .'</th>
                                <th nowrap class="invoice-title text-right">'.  translate('Principal Payable') .'</th>
                                <th nowrap class="invoice-title text-right">'.  translate('Interest Payable') .'</th>
                                <th nowrap class="invoice-title text-right">'.  translate('Total Interest') .'</th>
                                <th nowrap class="invoice-title  text-right">'.  translate('Balance') .'</th>
                            </tr>
                        </thead>
                        <tbody>
                            ';                             
                            foreach($loan_values as $key=>$value):  
                                $value = (object)$value;
                                $total_payable+=$value->amount_payable;
                                $principle=$value->principle_amount_payable;
                                $total_principle+=$principle;
                                $html.='
                                    <tr>
                                        <td>'. ++$i .'</td>
                                        <td>'. timestamp_to_date($value->due_date) .'</td>
                                        <td class="text-right">'. number_to_currency($value->amount_payable)   .'</td>
                                        <td class="text-right">'. number_to_currency($principle=$value->principle_amount_payable)  .'</td>
                                        <td class="text-right">'. number_to_currency($value->interest_amount_payable)  .'</td>
                                        <td class="text-right">'. number_to_currency($total_interest+=$value->interest_amount_payable) .'</td>
                                        <td class="text-right">'. number_to_currency($total_amount_payable-$total_payable)  .'</td>
                                    </tr>
                            ';                             
                            endforeach; 
                            $html.='
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">'. translate('Totals') .'</th>
                                <th class="text-right">'. number_to_currency($total_payable) .'</th>
                                <th class="text-right">'. number_to_currency($total_principle) .'</th>
                                <th class="text-right">'. number_to_currency($total_interest) .'</th>
                                <th class="text-right"></th>
                                <th class="text-right"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>';
            endif;
        $html.='</div>';
            $response = array(
                'status'=>1,
                'html'=>$html
            );

       }
       echo json_encode($response); 
    }

    function loan_calculator(){
       $loan_values = array();
       $total_amount_payable = 0;
       $total_principle_amount = 0;
       $total_interest = 0;
       $monthly_payment = 0;
       $loan_type_id = $this->input->post('loan_type_id');
       $loan_type = $this->loan_types_m->get($loan_type_id);
       $loan_amount =  currency($this->input->post('loan_amount'));
       $repayment_period = $this->input->post('repayment_period');
       $gurantors = $this->input->post('gurantors');

       $today = time(); 
       if($loan_type->interest_type == 1 || $loan_type->interest_type == 2){        
            if($loan_type->loan_repayment_period_type == 1){ 
               
                $loan_values = $this->loan->calculate_loan_balance_invoice(
                        $loan_amount,
                        $loan_type->interest_type,
                        $loan_type->interest_rate,
                        $loan_type->fixed_repayment_period,
                        '',time(),
                        $loan_type->loan_interest_rate_per,0,$loan_type_id);

                foreach ($loan_values as $key => $value) {
                    $value = (object)$value;
                    $total_amount_payable +=$value->amount_payable;
                    $total_principle_amount+=$value->principle_amount_payable;
                    $total_interest+=$value->interest_amount_payable;
                    $monthly_payment=$value->amount_payable;
                } 
            }else if($loan_type->loan_repayment_period_type == 2){
                if($repayment_period){
                    $minimum_repayment_period = $loan_type->minimum_repayment_period;
                    $maximum_repayment_period =  $loan_type->maximum_repayment_period;
                    if($repayment_period >= $minimum_repayment_period && $repayment_period <= $maximum_repayment_period){
                        $loan_values = $this->loan->calculate_loan_balance_invoice(
                        $loan_amount,
                        $loan_type->interest_type,
                        $loan_type->interest_rate,
                        $repayment_period,
                        '',time(),
                        $loan_type->loan_interest_rate_per,0,$loan_type_id);
                        foreach ($loan_values as $key => $value) {
                            $value = (object)$value;
                            $total_amount_payable +=$value->amount_payable;
                            $total_principle_amount+=$value->principle_amount_payable;
                            $total_interest+=$value->interest_amount_payable;
                            $monthly_payment=$value->amount_payable;
                        }
                    }else{ ?>
                        <div class="m-alert m-alert--outline m-alert--outline-2x alert alert-warning alert-dismissible fade show" role="alert">
                            <strong><?php echo translate('Sorry'); ?>!</strong> <?php echo translate('Repayment period must be between') ?> <?php echo  $minimum_repayment_period ?> - <?php echo $maximum_repayment_period ?> <?php echo translate('months') ?>
                        </div>
                    <?php } 
                }else{?>
                    <div class="m-alert m-alert--outline m-alert--outline-2x alert alert-warning alert-dismissible fade show" role="alert">
                        <strong><?php echo translate('Sorry'); ?>!</strong> <?php echo translate('Loan repayment period is required') ?>.
                    </div>
                <?php } 
            }
        } 
        $processing_fee_amount=$this->loan->calculate_loan_processing_fee($loan_type_id,$is_a_loan = FALSE,$is_a_debtor=FALSE,$loan_amount,$total_amount_payable);
        $loan_processing_recovery_on=$loan_type->loan_processing_recovery_on;
       
      
        ?>
        <div class="amortized_schedule">
            <div class="clearfix table_details"></div>
                <?php if($loan_values):?>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Total Amount Payable:</strong> <?php echo $this->group_currency.' '.number_to_currency($total_amount_payable);?><br/>
                        <strong>Total Interest :</strong> <?php echo $this->group_currency.' '.number_to_currency($total_interest);?><br/>
                        <strong>Repayment Period:</strong> <?php if($loan_type->loan_repayment_period_type == 1){
                            echo  $loan_type->fixed_repayment_period;
                        }else if($loan_type->loan_repayment_period_type == 2){
                           echo isset($repayment_period)?$repayment_period:''; 
                        } ?> Months<br/>       
                    </div>

                    <div class="col-md-6 loan_details_calc">                  
                        <strong>Monthly Payments :</strong> <?php echo $this->group_currency.' '.number_to_currency($monthly_payment);?><br/> 
                        <strong>Interest Rate :</strong> <?php echo $loan_type->interest_rate?>% <?php echo $this->loan->loan_interest_rate_per[$loan_type->loan_interest_rate_per]?>
                            <!-- <?php 
                                if($loan_type->loan_interest_rate_per!=3){
                                    if($loan_type->loan_interest_rate_per==1){
                                        echo 'at '.number_format($loan_type->interest_rate*30,1).'% Monthly rate';
                                    }
                                    else if($loan_type->loan_interest_rate_per==2){
                                        echo 'at '.number_format($loan_type->interest_rate*4,1).'% Monthly rate';
                                    }
                                    else if($loan_type->loan_interest_rate_per==4){
                                        echo 'at '.number_format($loan_type->interest_rate/12,1).'% Monthly rate';
                                    }else if($loan_type->loan_interest_rate_per==5){
                                        $interest_rate = $loan_type->interest_rate;
                                        $repayment_period = $repayment_period?:$loan_type->fixed_repayment_period;
                                        echo 'at '.number_format($interest_rate/$repayment_period,1).'% Monthly rate';
                                    }
                                }
                            ?> -->

                            <br/>
                        <strong>Interest Type :</strong> <?php echo $this->loan->interest_types[$loan_type->interest_type];?><br/>
                        <strong>Processing Fee  :</strong> <?php echo number_to_currency($processing_fee_amount);?><br/>
                    </div>
                </div><br><hr>
                <div class="col-xs-12 table-responsive">
                    <table class="table table-hover table-striped table-condensed table-statement">
                        <!-- <thead>
                            <tr>
                                <th nowrap class="invoice-title  text-right">** Amounts in <?php echo $this->group_currency; ?></th>
                            </tr>
                        </thead> -->
                        <thead>
                            <tr>
                                <th nowrap class="invoice-title" width="2%">#</th>
                                <th nowrap class="invoice-title"><?php echo translate('Payment Date');?></th>
                                <th nowrap class="invoice-title text-right"><?php echo translate('Amount Payable');?></th>
                                <th nowrap class="invoice-title text-right"><?php echo translate('Principal Payable');?></th>
                                <th nowrap class="invoice-title text-right"><?php echo translate('Interest Payable');?></th>
                                <th nowrap class="invoice-title text-right"><?php echo translate('Total Interest');?></th>
                                <th nowrap class="invoice-title text-right"><?php echo translate('Processing Fee');?></th>
                                <th nowrap class="invoice-title  text-right"><?php echo translate('Balance');?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total_payable =0; $total_principle=0;$balance=$total_amount_payable;$i=0;$total_interest=0;  $processing_fee_displayed = false; // Added variable
                            foreach($loan_values as $key=>$value):  $value = (object)$value;
                                    $total_payable+=($value->amount_payable)+($value->processing_fee);
                                    
                                     // Display processing fee only on the first installment if loan_processing_recovery_on is 2
                        // if ($loan_processing_recovery_on == 2 && !$processing_fee_displayed) {
                        //     $processing_fees = $processing_fee_amount/$repayment_period;
                        //     $processing_fee_displayed = false;
                        // } else {
                        //     $processing_fees = 0;
                        // }
                        // if ($loan_processing_recovery_on == 1 && !$processing_fee_displayed) {
                        //     $processing_fees = $processing_fee_amount;
                        //     $processing_fee_displayed = true;
                        // } if ($loan_processing_recovery_on == 1 && !$processing_fee_displayed) {
                        //     $processing_fees = 0;
                           
                        // }
                                ?>
                                    <tr>
                                        <td><?php echo ++$i?></td>
                                        <td><?php echo timestamp_to_date($value->due_date);?></td>
                                        <td class="text-right"><?php echo number_to_currency(($value->amount_payable)+ $value->processing_fee);?></td>
                                        <td class="text-right"><?php echo number_to_currency($principle=$value->principle_amount_payable);?></td>
                                        <td class="text-right"><?php echo number_to_currency($value->interest_amount_payable);?></td>
                                        <td class="text-right"><?php echo number_to_currency($total_interest+=$value->interest_amount_payable);?></td>
                                        <td class="text-right"><?php echo number_to_currency($value->processing_fee);?></td>
                                        <td class="text-right"><?php echo number_to_currency($balance-$total_payable);?></td>
                                    </tr>
                            <?php $total_principle+=$principle; endforeach;?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2"><?php echo translate('Totals');?></th>
                                <th class="text-right"><?php echo number_to_currency($total_payable);?></th>
                                <th class="text-right"><?php echo number_to_currency($total_principle);?></th>
                                <th class="text-right"><?php echo number_to_currency($total_interest);?></th>
                                <th class="text-right"></th>
                                <th class="text-right"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif;?>
        </div>
        <?php  
    }

    function ajax_loan_details(){
        $loan_application_id = $this->input->post('loan_application_id');
        if($loan_application_id){
            $validation_rules = array(
                array(
                    'field' => 'loan_application_id',
                    'label' => 'Loan application id parameter',
                    'rules' => 'trim|required|numeric',
                ),
            );
            $this->form_validation->set_rules($validation_rules);
            $response = array();
            if($this->form_validation->run()){
                $loan_application = $this->loan_applications_m->get($loan_application_id);
                if($loan_application){
                    $loan_type = $this->loan_types_m->get($loan_application->loan_type_id);
                    if($loan_type){
                        $html ='';
                        $html.=' 
                            <div class="well well-lg" >
                                <h5 class="block" style="margin-top: -7px !important; margin-bottom: -3px !important;">
                                    <strong>Loan Type : </strong>  '.ucfirst($loan_type->name).' 
                                </h5>';
                        $html.='<strong> Loan Applicant : </strong>  '. $this->active_group_member_options[$loan_application->member_id].'<br>'; 
                        $html.='<strong> Loan Amount : </strong>  ' .$this->group_currency.' '. number_to_currency($loan_application->loan_amount).'<br>';
                        $html.='<strong>  Loan Duration : </strong>' ;
                        if($loan_type->loan_repayment_period_type == 1){
                           $html.= $loan_type->fixed_repayment_period.' Months';
                        }else if ($loan_type->loan_repayment_period_type == 2) {
                            $html.= $loan_type->minimum_repayment_period.' - '.$loan_type->maximum_repayment_period.' Months';
                        } 
                        $response = array(
                            'status' => 200,
                            'message' => 'ok',
                            'html' => $html
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Loan type id details missing',
                            'html' =>'',
                        );  
                    }
                }else{
                  $response = array(
                        'status' => 0,
                        'message' => 'Loan application details missing',
                        'html' =>'',
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

    function ajax_member_loan_appraisal(){
        $loan_application_id = $this->input->post('loan_application_id');
        if($loan_application_id){
            $validation_rules = array(
                array(
                    'field' => 'loan_application_id',
                    'label' => 'Loan application id parameter',
                    'rules' => 'trim|required|numeric',
                ),
            );
            $this->form_validation->set_rules($validation_rules);
            $response = array();
            if($this->form_validation->run()){
                $loan_application = $this->loan_applications_m->get($loan_application_id);
                if($loan_application){
                    $total_payable = 0;
                    $loan_type = $this->loan_types_m->get($loan_application->loan_type_id);
                    $loan_values = $this->loan->calculate_loan_balance_invoice(
                        $loan_application->loan_amount,
                        $loan_type->interest_type,
                        $loan_type->interest_rate,
                        $loan_type->fixed_repayment_period,
                        '',time(),
                        $loan_type->loan_interest_rate_per);
                    foreach($loan_values as $key=>$value): 
                        $value = (object)$value;
                        $total_payable+=$value->amount_payable;
                    endforeach;
                    if($loan_type){
                        $html ='';
                        $html.=' 
                            <div class="well well-lg" >
                                <h5 class="block" style="margin-top: -7px !important; margin-bottom: -3px !important;">
                                    <strong>Loan Type : </strong>  '.ucfirst($loan_type->name).' 
                                </h5>';
                        $html.='<strong> Loan Applicant : </strong>  '. $this->active_group_member_options[$loan_application->member_id].'<br>'; 
                        $html.='<strong> Loan Amount : </strong>  ' .$this->group_currency.' '. number_to_currency($loan_application->loan_amount).'<br>';
                        $html.='<strong> Net Amount : </strong>  ' .$this->group_currency.' '. number_to_currency($total_payable).'<br>';
                        $html.="<strong> Late Monthly repayment penalty  : </strong>  ".$this->group_currency." ".number_to_currency(1000) ." <br>";
                        $html.="<strong> Guarantor's response : </strong> Approved <br>";
                        $html.='<strong>  Loan Duration : </strong>' ;
                        if($loan_type->loan_repayment_period_type == 1){
                           $html.= $loan_type->fixed_repayment_period.' Months';
                        }else if ($loan_type->loan_repayment_period_type == 2) {
                            $html.= $loan_type->minimum_repayment_period.' - '.$loan_type->maximum_repayment_period.' Months';
                        } 
                        $response = array(
                            'status' => 200,
                            'message' => 'ok',
                            'html' => $html
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Loan type id details missing',
                            'html' =>'',
                        );  
                    }
                }else{
                  $response = array(
                        'status' => 0,
                        'message' => 'Loan application details missing',
                        'html' =>'',
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

    function ajax_signatory_details(){
        $signatory_id = $this->input->post('signatory_id');
        if($signatory_id){
            $validation_rules = array(
                array(
                    'field' => 'signatory_id',
                    'label' => 'Loan signatory id parameter',
                    'rules' => 'trim|required|numeric',
                ),
            );
            $this->form_validation->set_rules($validation_rules);
            $response = array();
            if($this->form_validation->run()){
                $signatory = $this->loans_m->get_loan_signatories($signatory_id,$this->group->id);
                if($signatory){
                    $loan_application = $this->loan_applications_m->get($signatory->loan_application_id);
                    if($loan_application){
                        $total_payable = 0;
                        $loan_type = $this->loan_types_m->get($loan_application->loan_type_id);
                        $loan_values = $this->loan->calculate_loan_balance_invoice(
                            $loan_application->loan_amount,
                            $loan_type->interest_type,
                            $loan_type->interest_rate,
                            $loan_type->fixed_repayment_period,
                            '',time(),
                            $loan_type->loan_interest_rate_per);
                        $amount_payable = 0;
                        foreach($loan_values as $key=>$value): 
                            $value = (object)$value;
                            $total_payable+=$value->amount_payable;
                            $amount_payable = $value->amount_payable;
                        endforeach;
                        if($loan_type){
                            $html ='';
                            $html.=' 
                                <div class="well well-lg" >
                                    <h5 class="block" style="margin-top: -7px !important; margin-bottom: -3px !important;">
                                        <strong>Loan Type : </strong>  '.ucfirst($loan_type->name).' 
                                    </h5>';
                            $html.='<strong> Loan Applicant : </strong>  '. $this->active_group_member_options[$loan_application->member_id].'<br>'; 
                            $html.='<strong> Approved Amount : </strong>  ' .$this->group_currency.' '. number_to_currency($loan_application->loan_amount).'<br>';
                            /*$html.='<strong> Net Amount : </strong>  ' .$this->group_currency.' '. number_to_currency($total_payable).'<br>';*/
                            $html.='<strong>  Recoverable in : </strong>' ;
                            if($loan_type->loan_repayment_period_type == 1){
                               $html.= $loan_type->fixed_repayment_period.' Months <br>';
                            }else if ($loan_type->loan_repayment_period_type == 2) {
                                $html.= $loan_type->minimum_repayment_period.' - '.$loan_type->maximum_repayment_period.' Months <br>';
                            } 
                            $html.="<strong> Monthly installments : </strong> ".$this->group_currency." ".number_to_currency($amount_payable)." <br>";
                            $loan_value = (object)$loan_values[0];
                            $html.="<strong> Commencing on : </strong> ". timestamp_to_receipt($loan_value->due_date)." <br>";
                            $response = array(
                                'status' => 200,
                                'message' => 'ok',
                                'html' => $html
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Loan type id details missing',
                                'html' =>'',
                            );  
                        }
                    }else{
                      $response = array(
                            'status' => 0,
                            'message' => 'Loan application details missing',
                            'html' =>'',
                        );  
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Loan signatory details missing',
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

    function ajax_loan_requests_for_sacco_manager(){
        $posts = $this->loan_applications_m->get_pending_group_loan_applications();
        $html ='';
        $loan_type = $this->loan_types_m->get_options();
        $group_members = $this->members_m->get_group_members_array();
        $response = array();
        if($posts){
            $html.='
            <table class="table table-condensed table-striped table-hover table-header-fixed ">
                <thead>
                    <tr>
                        <th >
                            #
                        </th>
                        <th>
                            Loan Details
                        </th>
                        <th >
                            Amount (KES)
                        </th>
                    </tr>
                </thead>';
                $count =0 ;
            foreach ($posts as $key => $post):
                //print_r($post);
                if($post->sacco_manager_member_id){
                    ++$count;
                    $html.='<tbody>
                    <tr>
                        <td>'
                            .$count.
                        '</td>
                        <td>
                          <strong> Loan name : </strong>'.$loan_type[$post->loan_type_id].'<br>  
                          <strong> Loan Applicant  Name : </strong>'.$group_members[$post->member_id]->first_name.' '.$group_members[$post->member_id]->last_name.'<br>
                          <strong> Loan Duration : </strong>'.$post->repayment_period.' Months<br>
                          <strong>You response Status: </strong>';
                            if ($post->sacco_manager_status == 1) {
                                $html.='<span class="label label-success">Approved</span>';
                            } else {
                                $html.='<span class="label label-danger"> loan Declined</span>';
                            } 
                        $html.='</td>
                        <td>
                          '.number_to_currency($post->loan_amount).'
                        </td>
                    </tr>
                    </tbody>';
                    $response = array(
                        'status' => 200,
                        'message' => 'ok',
                        'html' => $html
                    );
                }
            endforeach;

        }else{
           $response = array(
                'status' => 0,
                'message' => 'Loan application details is missing',
                'html' => '',
            ); 
        }
        echo json_encode($response);
    } 
    
    function ajax_get_signatory_requests($id = 0){
        
    }

    function _additional_validation_rules(){
        if($this->input->post('enable_loan_guarantors') == 1){
            $this->validation_rules[] = array(
                'field' => 'guarantor_id[]',
                'label' => 'Guarantor name required',
                'rules' => 'callback__verify_guarantor_name',
            );
            $this->validation_rules[] = array(
                'field' => 'guaranteed_amount[]',
                'label' => 'Guarantor Amount ',
                'rules' => 'callback__valid_application_amount',
            );
        }

    } 

    function create(){
        $post = new StdClass();
        $response = array();
        $this->_additional_validation_rules();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $group_id  =  $this->group->id;  
            $loan_details = new StdClass();
            $custom_loan_values = array();
            $loan_type_id = $this->input->post('loan_type_id');
            $member_id = $this->input->post('member_id');
            $loan_amount = currency($this->input->post('loan_amount'));
            $account_id = $this->input->post('account_id');
            $disbursement_date = strtotime($this->input->post('disbursement_date'));
            $disbursement_option_id = $this->input->post('disbursement_option_id');
            $mobile_money_wallet_id = $this->input->post('mobile_money_wallet_id');
            $equity_bank_account_id = $this->input->post('equity_bank_account_id');
            $repayment_period = $this->input->post('repayment_period');
            $guarantor_id = $this->input->post('guarantor_ids');
            $guaranteed_amount = $this->input->post('guaranteed_amounts');
            $guarantor_comment = $this->input->post('guarantor_comments');
            $guarantors= array();
            foreach ($guarantor_id as $key => $value) {
                if($value){
                    $guarantors['guarantor_id'][] = $value;
                    $guarantors['guaranteed_amount'][] = $guaranteed_amount[$key];
                    $guarantors['guarantor_comment'][] = $guarantor_comment[$key];
                }
            }
            $loan_type = $this->loan_types_m->get_group_loan_type($loan_type_id);
            if($loan_type){
                $loan_details->loan_type_id = $loan_type_id;
                $loan_details->disbursement_date = $disbursement_date;
                $loan_details->account_id = $account_id;
                $loan_details->loan_amount = $loan_amount;
                $loan_details->created_by = $this->user->id;
                $loan_details->created_on = time();
                if($loan_type->loan_repayment_period_type == 1){
                    $loan_details->repayment_period = $loan_type->fixed_repayment_period;
                }else{
                    $loan_details->repayment_period = $repayment_period;
                }
                $fields = $this->loans_m->get_table_fields();
                foreach ($loan_type as $key => $value) {
                    if(!isset($loan_details->$key)){
                        if(in_array($key, $fields) && $key!='id'){
                            $loan_details->$key = $value;
                        }
                    }
                }
                $verified_bank_accounts = $this->bank_accounts_m->get_group_verified_partner_bank_account_options_ids($this->group->id);
                // if(preg_match('/bank-/', $account_id) && array_key_exists(trim(preg_replace('/[^0-9]/', '', $account_id)), $verified_bank_accounts)){
                //     //withdrawal request
                //     $bank_account_id = trim(preg_replace('/[^0-9]/','', $account_id));
                //     $withdrawal = new StdClass();
                //     $withdrawal->withdrawal_for = 1;
                //     $withdrawal->amount = $loan_amount;
                //     $withdrawal->bank_account_id = $bank_account_id;
                //     $withdrawal->transfer_to = $disbursement_option_id==1?1:3;
                //     $withdrawal->recipient = $disbursement_option_id==1?$mobile_money_wallet_id:$equity_bank_account_id;
                //     $withdrawal->loan_type_id = $loan_type_id;
                //     $withdrawal->member_id = $member_id;
                //     $withdrawal->disbursement_channel = $disbursement_option_id;
                //     $bank_account = $this->bank_accounts_m->get_group_verified_bank_account_by_id($bank_account_id,$this->group->id);
                //     if($bank_account){
                //         if(floatval($bank_account->current_balance) >= floatval($withdrawal->amount)){
                //             if($this->transactions->process_batch_withdrawal_requests($withdrawal,$this->group_currency,$this->member,$this->group,$this->user)){
                //                 $response = array(
                //                     'status' => 1,
                //                     'message' => 'Successfully processed withdrawal request(s)',
                //                     'refer' => site_url('bank/withdrawals/withdrawal_requests'),
                //                 );
                //             }else{
                //                 $response = array(
                //                     'status' => 0,
                //                     'message' => $this->session->flashdata('error'),
                //                 );
                //             }
                //         }else{
                //             $response = array(
                //                 'status' => 0,
                //                 'message' => translate('You can not disburse more than is in the selected account. Available balance is ').$this->group_currency.' '.number_to_currency($bank_account->current_balance),
                //             );
                //         }
                //     }else{
                //          $response = array(
                //             'status' => 0,
                //             'message' => translate('You must select a valid disbursing bank account that is connected'),
                //         );
                //     }
                // }else{
                    if($id = $this->loan->create_automated_group_loan(1,$member_id,$group_id,(array)$loan_details,'','',$guarantors)){
                    $response = array(
                        'status' => 1,
                        'message' => 'Loan Successfully created',
                        'refer' => site_url('bank/loans/listing'),
                    );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => $this->session->flashdata('error')
                        );
                    }
                // }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'We could not get the loan type you trying to create',
                );
            }

            /*
                * if account id  -> cash 
                * $this->loan->create_automated_group_loan(1,$member_id,$group_id,$loan_details=array(),$custom_loan_values=array(),$custom_rate_procedure='',$guarantors=array())
    
                ** If bank account

                $withdrawal = new stdClass;
                $withdrawal->withdrawal_for = 1;
                $withdrawal->amount = currency($this->input->post('amount'));
                $withdrawal->bank_account_id = $this->input->post('bank_account_id');
                $withdrawal->transfer_to = $this->input->post('transfer_to');
                $withdrawal->recipient = $this->input->post('recipient');
                $withdrawal->loan_type_id = $this->input->post('loan_type_id');
                $withdrawal->member_id = $this->input->post('member_id');
                $withdrawal->expense_category_id = $this->input->post('expense_category_id');
                $withdrawal->description = $this->input->post('description');
                $withdrawal->contribution_id = $this->input->post('contribution_id');
                $withdrawal->disbursement_channel = $this->input->post('disbursement_channel');
                $withdrawal->bank_account_id = $bank_account_id;

                *  $this->transactions->process_batch_withdrawal_requests($withdrawal,$this->group_currency,$this->member,$this->group,$this->user)
            */
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
        echo json_encode($response);
    }
    function _member_has_ongoing_loans($member_id=0){
        
        //check ongoing member loans
        $base_where = array('member_id'=>$member_id,'is_fully_paid'=>0);
        $ongoing_member_loans = $this->loans_m->get_many_by($base_where);
        $successful_checks=0;
        if($ongoing_member_loans){
            foreach($ongoing_member_loans as $ongoing_member_loan){
                 $loan_type=$this->loan_types_m->get($ongoing_member_loan->loan_type_id);
               if($loan_type && $loan_type->limit_to_one_loan_application){
                $successful_checks++;
               }
            }   
            if($successful_checks){
                 
                return TRUE;
            }
            else{
                
                return FALSE;
            }
        }else{
            return FALSE;   
        }
    }
    function create_withdrawal_request(){
        $post = new StdClass();
       
        $response = array();
        $this->_additional_validation_rules();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $group_id  =  '';
            $loan_details = new StdClass();
            $custom_loan_values = array();
            $loan_type_id = $this->input->post('loan_type_id');
            $member_id = $this->input->post('member_id');
            $loan_amount = currency($this->input->post('loan_amount'));
            $account_id = $this->input->post('account_id');
            $disbursement_date = strtotime($this->input->post('disbursement_date'));
            $disbursement_option_id = $this->input->post('disbursement_option_id');
            $mobile_money_wallet_id = $this->input->post('mobile_money_wallet_id');
            $equity_bank_account_id = $this->input->post('equity_bank_account_id');
            $repayment_period = $this->input->post('repayment_period');
            $guarantor_id = $this->input->post('guarantor_ids');
            $guaranteed_amount = $this->input->post('guaranteed_amounts');
            $guarantor_comment = $this->input->post('guarantor_comments');
            $guarantors= array();
            if($this->_member_has_ongoing_loans($member_id)){
                $member=$this->members_m->get_group_member($member_id);
                $response = array(
                    'status' => 0,
                    'message' => $member->first_name.' '.$member->last_name.' has an ongoing mobi loan',
                );
                echo json_encode($response); 
                die;
            }

            foreach ($guarantor_id as $key => $value) {
                if($value){
                    $guarantors['guarantor_id'][] = $value;
                    $guarantors['guaranteed_amount'][] = $guaranteed_amount[$key];
                    $guarantors['guarantor_comment'][] = $guarantor_comment[$key];
                }
            }
            $loan_type = $this->loan_types_m->get_group_loan_type($loan_type_id);
           
            if($loan_type){
                $loan_details->loan_type_id = $loan_type_id;
                $loan_details->disbursement_date = $disbursement_date;
                $loan_details->account_id = $account_id;
                $loan_details->loan_amount = $loan_amount;
                $loan_details->enable_automatic_disbursements = $loan_type->enable_automatic_disbursements;
                $loan_details->created_by = $this->user->id;
                $loan_details->created_on = time();
                if($loan_type->loan_repayment_period_type == 1){
                    $loan_details->repayment_period = $loan_type->fixed_repayment_period;
                }else{
                    $loan_details->repayment_period = $repayment_period;
                }
                $fields = $this->loans_m->get_table_fields();
                foreach ($loan_type as $key => $value) {
                    if(!isset($loan_details->$key)){
                        if(in_array($key, $fields) && $key!='id'){
                            $loan_details->$key = $value;
                        }
                    }
                }
                $verified_bank_accounts = $this->bank_accounts_m->get_group_verified_partner_bank_account_options_ids('');
                if(preg_match('/bank-/', $account_id) && array_key_exists(trim(preg_replace('/[^0-9]/', '', $account_id)), $verified_bank_accounts)){
                    //withdrawal request
                    $bank_account_id = trim(preg_replace('/[^0-9]/','', $account_id));
                    $withdrawal = new StdClass();
                    $withdrawal->withdrawal_for = 1;
                    $withdrawal->amount = $loan_amount;
                    $withdrawal->bank_account_id = $bank_account_id;
                    $withdrawal->transfer_to = $disbursement_option_id==1?1:3;
                    $withdrawal->recipient = $disbursement_option_id==1?$mobile_money_wallet_id:$equity_bank_account_id;
                    $withdrawal->loan_type_id = $loan_type_id;
                    $withdrawal->member_id = $member_id;
                    $withdrawal->disbursement_channel = $disbursement_option_id;
                    $bank_account = $this->bank_accounts_m->get_group_verified_bank_account_by_id($bank_account_id,$this->group->id);
                    if($bank_account){
                        if(floatval($bank_account->current_balance) >= floatval($withdrawal->amount)){
                            if($this->transactions->process_batch_withdrawal_requests($withdrawal,$this->group_currency,$this->member,$this->group,$this->user)){
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Successfully processed withdrawal request(s)',
                                    'refer' => site_url('bank/withdrawals/withdrawal_requests'),
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => $this->session->flashdata('error'),
                                );
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => translate('You can not disburse more than is in the selected account. Available balance is ').$this->group_currency.' '.number_to_currency($bank_account->current_balance),
                            );
                        }
                    }else{
                         $response = array(
                            'status' => 0,
                            'message' => translate('You must select a valid disbursing bank account that is connected'),
                        );
                    }
                }else{
                    if($id = $this->loan->create_automated_group_loan(1,$member_id,$group_id,(array)$loan_details,'','',$guarantors)){
                        $withdrawal = new StdClass();
                        $withdrawal->withdrawal_for = 1;
                        $withdrawal->amount = $loan_amount;
                        $withdrawal->bank_account_id = $bank_account_id;
                        $withdrawal->transfer_to = $disbursement_option_id==1?1:3;
                        $withdrawal->recipient = $disbursement_option_id==1?$mobile_money_wallet_id:$equity_bank_account_id;
                        $withdrawal->loan_type_id = $loan_type_id;
                        $withdrawal->reference_number = time();
                        $withdrawal->member_id = $member_id;
                        $withdrawal->disbursement_channel = $disbursement_option_id;
                        $this->member=$this->members_m->get($member_id);
                        if($loan_type->enable_automatic_disbursements==1){    
                        if($this->transactions->process_batch_withdrawal_requests($withdrawal,"KES",$this->member,$this->group,$this->user)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Successfully processed withdrawal request(s)',
                                'refer' => site_url('bank/withdrawals/withdrawal_requests'),
                            );
                        }
                    }
                    $response = array(
                        'status' => 1,
                        'message' => 'Loan Successfully created',
                        'refer' => site_url('bank/loans/listing'),
                    );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => $this->session->flashdata('error')
                        );
                    }
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'We could not get the loan type you trying to create',
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
        echo json_encode($response);
    }

    function edit($id=0){
        $post = new StdClass();
        $response = array();
        $this->_additional_validation_rules();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $group_id  =  $this->group->id;  
            $loan_details = new StdClass();
            $custom_loan_values = array();
            $loan_type_id = $this->input->post('loan_type_id');
            $member_id = $this->input->post('member_id');
            $loan_amount = currency($this->input->post('loan_amount'));
            $account_id = $this->input->post('account_id');
            $disbursement_date = strtotime($this->input->post('disbursement_date'));
            $disbursement_option_id = $this->input->post('disbursement_option_id');
            $mobile_money_wallet_id = $this->input->post('mobile_money_wallet_id');
            $equity_bank_account_id = $this->input->post('equity_bank_account_id');
            $repayment_period = $this->input->post('repayment_period');
            $guarantor_id = $this->input->post('guarantor_ids');
            $guaranteed_amount = $this->input->post('guaranteed_amounts');
            $guarantor_comment = $this->input->post('guarantor_comments');
            $guarantors= array();
            foreach ($guarantor_id as $key => $value) {
                if($value){
                    $guarantors['guarantor_id'][] = $value;
                    $guarantors['guaranteed_amount'][] = $guaranteed_amount[$key];
                    $guarantors['guarantor_comment'][] = $guarantor_comment[$key];
                }
            }
            $loan_type = $this->loan_types_m->get_group_loan_type($loan_type_id);
            if($loan_type){
                $loan_details->loan_type_id = $loan_type_id;
                $loan_details->disbursement_date = $disbursement_date;
                $loan_details->account_id = $account_id;
                $loan_details->loan_amount = $loan_amount;
                $loan_details->created_by = $this->user->id;
                $loan_details->created_on = time();
                if($loan_type->loan_repayment_period_type == 1){
                    $loan_details->repayment_period = $loan_type->fixed_repayment_period;
                }else{
                    $loan_details->repayment_period = $repayment_period;
                }
                $fields = $this->loans_m->get_table_fields();
                foreach ($loan_type as $key => $value) {
                    if(!isset($loan_details->$key)){
                        if(in_array($key, $fields) && $key!='id'){
                            $loan_details->$key = $value;
                        }
                    }
                }
                $verified_bank_accounts = $this->bank_accounts_m->get_group_verified_partner_bank_account_options_ids($this->group->id);
                // if(preg_match('/bank-/', $account_id) && array_key_exists(trim(preg_replace('/[^0-9]/', '', $account_id)), $verified_bank_accounts)){
                //     //withdrawal request
                //     $bank_account_id = trim(preg_replace('/[^0-9]/','', $account_id));
                //     $withdrawal = new StdClass();
                //     $withdrawal->withdrawal_for = 1;
                //     $withdrawal->amount = $loan_amount;
                //     $withdrawal->bank_account_id = $bank_account_id;
                //     $withdrawal->transfer_to = $disbursement_option_id==1?1:3;
                //     $withdrawal->recipient = $disbursement_option_id==1?$mobile_money_wallet_id:$equity_bank_account_id;
                //     $withdrawal->loan_type_id = $loan_type_id;
                //     $withdrawal->member_id = $member_id;
                //     $withdrawal->disbursement_channel = $disbursement_option_id;
                //     $bank_account = $this->bank_accounts_m->get_group_verified_bank_account_by_id($bank_account_id,$this->group->id);
                //     if($bank_account){
                //         if(floatval($bank_account->current_balance) >= floatval($withdrawal->amount)){
                //             if($this->transactions->process_batch_withdrawal_requests($withdrawal,$this->group_currency,$this->member,$this->group,$this->user)){
                //                 $response = array(
                //                     'status' => 1,
                //                     'message' => 'Successfully processed withdrawal request(s)',
                //                     'refer' => site_url('bank/withdrawals/withdrawal_requests'),
                //                 );
                //             }else{
                //                 $response = array(
                //                     'status' => 0,
                //                     'message' => $this->session->flashdata('error'),
                //                 );
                //             }
                //         }else{
                //             $response = array(
                //                 'status' => 0,
                //                 'message' => translate('You can not disburse more than is in the selected account. Available balance is ').$this->group_currency.' '.number_to_currency($bank_account->current_balance),
                //             );
                //         }
                //     }else{
                //          $response = array(
                //             'status' => 0,
                //             'message' => translate('You must select a valid disbursing bank account that is connected'),
                //         );
                //     }
                // }else{
                    $id = $this->input->post('id');
                    $update = $this->loan->modify_automated_group_loan(
                        $id,
                        1,
                        $member_id,
                        $group_id,
                        (array)$loan_details,
                        '',
                        '',
                        $guarantors,
                        FALSE
                    );
                    if($update){
                    $response = array(
                        'status' => 1,
                        'message' => 'Loan Successfully created',
                        'refer' => site_url('bank/loans/listing'),
                    );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => $this->session->flashdata('error')
                        );
                    }
                // }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'We could not get the loan type you trying to create',
                );
            }

            /*
                * if account id  -> cash 
                * $this->loan->create_automated_group_loan(1,$member_id,$group_id,$loan_details=array(),$custom_loan_values=array(),$custom_rate_procedure='',$guarantors=array())
    
                ** If bank account

                $withdrawal = new stdClass;
                $withdrawal->withdrawal_for = 1;
                $withdrawal->amount = currency($this->input->post('amount'));
                $withdrawal->bank_account_id = $this->input->post('bank_account_id');
                $withdrawal->transfer_to = $this->input->post('transfer_to');
                $withdrawal->recipient = $this->input->post('recipient');
                $withdrawal->loan_type_id = $this->input->post('loan_type_id');
                $withdrawal->member_id = $this->input->post('member_id');
                $withdrawal->expense_category_id = $this->input->post('expense_category_id');
                $withdrawal->description = $this->input->post('description');
                $withdrawal->contribution_id = $this->input->post('contribution_id');
                $withdrawal->disbursement_channel = $this->input->post('disbursement_channel');
                $withdrawal->bank_account_id = $bank_account_id;

                *  $this->transactions->process_batch_withdrawal_requests($withdrawal,$this->group_currency,$this->member,$this->group,$this->user)
            */
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
        echo json_encode($response);

    }

    function ajax_get_loan_details(){
        $loan_application_id = $this->input->post('loan_id');
        $response = array();
        $validation_rules = array(
            array(
                'field' => 'loan_id',
                'label' => 'Loan application id parameter',
                'rules' => 'trim|required|numeric',
            ),
        );
        $this->form_validation->set_rules($validation_rules);           
        if($this->form_validation->run()){
            $loan_application = $this->loans_m->get($loan_application_id);
            if($loan_application){
                echo json_encode($loan_application);
            }else{
               echo json_encode('No loan application details ');  
            }
        }else{
            echo json_encode(validation_errors());
        }
    }

    function get_loan_statement($id = 0){
        if($id){
            $loan = $this->loans_m->get_loan_and_member($id);
            if(!$loan){
                $this->session->set_flashdata('info','Sorry the loan does not exist');
                redirect('bank/loans/listing');
            }

            $total_installment_payable = $this->loan_invoices_m->get_total_installment_loan_payable($id);
            $total_fines = $this->loan_invoices_m->get_total_loan_fines_payable($id);
            $total_transfers_out = $this->loan_invoices_m->get_total_loan_transfers_out($id);
            $total_paid = $this->loan_repayments_m->get_loan_total_payments($id);
            $loan_balance =$this->loans_m->get_loan_balance($id);
            $posts = $this->loans_m->get_loan_statement($id);
            $this->data['loan'] = $loan;
            $this->data['posts'] = $posts;
            $this->data['total_installment_payable'] = $total_installment_payable;
            $this->data['total_fines'] = $total_fines;
            $this->data['total_transfers_out'] = $total_transfers_out;
            $this->data['total_paid'] = $total_paid;
            $lump_sum_remaining = $this->loan_invoices_m->get_loan_lump_sum_as_date($id);
            $accounts = $this->accounts_m->get_group_account_options(FALSE);
            $deposit_options =$this->transactions->deposit_method_options;
            $this->data['group'] = $this->group;
            $this->data['group_currency'] = $this->group_currency;
            $this->data['application_settings'] = $this->application_settings;
            $transfer_options = $this->loan->transfer_options;
            $loan_type_options = $this->loan_types_m->get_options();
            $response = array(
                'status' => 1,
                'message' => 'Loan Details',
                'data' => array(
                    'loan' => $loan,
                    'total_installment_payable'=>$total_installment_payable,
                    'total_fines'=>$total_fines,
                    'total_transfers_out'=>$total_transfers_out,
                    'total_paid'=>$total_paid,
                    'loan_balance'=>$loan_balance,
                    'posts'=>$posts,
                    'lump_sum_remaining'=>$lump_sum_remaining,
                    'loan_type_options' => $loan_type_options,
                    'transfer_options' => $transfer_options,
                    'deposit_options' => $deposit_options,
                    'accounts' => $accounts,
                    'interest_types' => $this->loan->interest_types,
                    'loan_interest_rate_per' => $this->loan->loan_interest_rate_per,
                    'late_payments_fine_frequency' => $this->loan->late_payments_fine_frequency,
                    'percentage_fine_on' => $this->loan->percentage_fine_on,
                    'loan_processing_fee_percentage_rate' => $this->loan->loan_processing_fee_percentage_charged_on,
                ),
            );

        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not get the selected loan details',
            );
        }
        echo json_encode($response);
    }

    function _valid_guarantor_details(){
        $errors = array();
        if($this->loan_type->enable_loan_guarantors){
            $guarantor_ids  = $this->input->post('guarantor_id');
            $guarantor_amounts  = $this->input->post('guaranteed_amount');
            $loan_application_amount = currency($this->input->post('loan_application_amount'));
            $total_guaranteed_amount = array_sum($guarantor_amounts);
            if($guarantor_ids){
                if(count(array_unique($guarantor_ids)) == count($guarantor_ids)){ //checks for duplicate guarantors
                    foreach ($guarantor_ids as $key => $guarantor_id) {
                        if($guarantor_id){
                            if($guarantor_id == $this->member->id){
                                $this->form_validation->set_message('_valid_guarantor_details','You cannot select yourself as a guarantor');
                                return FALSE;
                            }else{//check if based on member savings loan_amount_type
                                $data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();
                                $total_savings = $this->reports_m->get_group_member_total_contributions($guarantor_id,$data['contribution_options']);
                                $guarantor_savings = $total_savings > 0?$total_savings:0;      
                                $maximum_amount_to_grant = $guarantor_savings*($this->loan_type->loan_times_number?:1);
                                $guarantor_amount = isset($guarantor_amounts[$key])?$guarantor_amounts[$key]:0;
                                if($guarantor_amount){
                                    if($guarantor_amount > $maximum_amount_to_grant){
                                        $this->form_validation->set_message('_valid_guarantor_details',$this->active_group_member_options[$guarantor_id].' can not guarantee that much ');
                                        return FALSE;
                                    }
                                }else{
                                    $this->form_validation->set_message('_valid_guarantor_details','Please select a valid amount to guarantee');
                                    return FALSE;
                                }
                            }
                        }else{
                            $this->form_validation->set_message('_valid_guarantor_details','Please select a guarantor');
                            return FALSE;
                        }
                    }
                }else{
                    $this->form_validation->set_message('_valid_guarantor_details','You cannot select the same guarantor more than once');
                    return FALSE;
                }
            
                //valid amount
                if($this->loan_type->loan_amount_type == 1){//range
                    if($loan_application_amount > $this->loan_type->maximum_loan_amount){
                        if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                            $this->form_validation->set_message('_valid_guarantor_details','Selected less guarantors than the required');
                            return FALSE;
                        }else{
                            if($this->loan_type->loan_guarantors_type == 1){//every time 
                                $this->form_validation->set_message('_valid_guarantor_details','Amount applied is above the required maximum amount');
                                return FALSE;
                            }elseif ($this->loan_type->loan_guarantors_type == 2) {//when exceeds savings
                                $amount_above_maximum = currency($loan_application_amount - $this->loan_type->maximum_loan_amount);
                                if($total_guaranteed_amount < $amount_above_maximum){
                                    $this->form_validation->set_message('_valid_guarantor_details','Guaranteed amount is less than allowed amount to be guaranteed');
                                    return FALSE;
                                }
                            }
                        }
                    }else{
                        if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                            $this->form_validation->set_message('_valid_guarantor_details','Guarantors selected are less than the required guarantors');
                            return FALSE;
                        }else{
                            if($this->loan_type->loan_guarantors_type == 1){//every time
                               //carry on
                            }elseif ($this->loan_type->loan_guarantors_type == 2) {//when exceeds savings
                                $amount_above_maximum = currency($loan_application_amount - $this->loan_type->maximum_loan_amount);
                                if($total_guaranteed_amount < $amount_above_maximum){
                                    $this->form_validation->set_message('_valid_guarantor_details','Guaranteed amount is less than allowed amount to be guaranteed');
                                    return FALSE;
                                }
                            }
                        }
                    }
                }else if($this->loan_type->loan_amount_type == 2){//member savings
                    $member_savings = $this->transactions->get_group_member_savings($this->group->id,$this->member->id);
                    $maximum_allowed_loan = currency($member_savings * $this->loan_type->loan_times_number);
                    $amount_above_savings = currency($loan_application_amount - $maximum_allowed_loan);
                    if($this->loan_type->enable_loan_guarantors){
                        if($this->loan_type->loan_guarantors_type == 1){//every time
                            if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                                $this->form_validation->set_message('_valid_guarantor_details','Guarantors selected is less than the minimum guarantors required');
                                return FALSE;
                            }else{

                            }
                        }elseif ($this->loan_type->loan_guarantors_type == 2) {//when exceeds savings
                            if($loan_application_amount > $maximum_allowed_loan){
                                if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                                    $this->form_validation->set_message('_valid_guarantor_details','Guarantors selected are less than the required');
                                    return FALSE;
                                }else{
                                    if($total_guaranteed_amount < $amount_above_savings){
                                        $this->form_validation->set_message('_valid_guarantor_details','Guaranteed amount is less than allowed amount to be guaranteed');
                                        return FALSE;
                                    }
                                }
                            }                      
                        }
                    }
                }else{
                    $this->form_validation->set_message('_valid_guarantor_details','Invalid loan type selected');
                    return FALSE;
                }
            }else{
                $this->form_validation->set_message('_valid_guarantor_details','Loan guaraantors are not selected');
                return FALSE;
            }
        }else{
            return TRUE;
        }
        
    }

    function apply(){
        $post = new StdClass();
        $response = array();
        $guarantor_ids = $this->input->post('guarantor_ids');
        $guaranteed_amounts = $this->input->post('guaranteed_amounts');
        $guarantor_comments = $this->input->post('guarantor_comments');
        $loan_type_id = $this->input->post('loan_type_id');
        $this->form_validation->set_rules($this->application_rules);
        $this->loan_type = $this->loan_types_m->get($loan_type_id);
        if($this->form_validation->run()){
            if($this->input->post('loan_rules_check_box')){
                if($this->loan_type){
                    $validation_errors = array();
                    $guarantor_details_are_valid = TRUE;
                    if($this->loan_type->enable_loan_guarantors){
                        $loan_application_amount = currency($this->input->post('loan_application_amount'));
                        $total_guaranteed_amount = array_sum($guaranteed_amounts);
                        if(count(array_unique($guarantor_ids)) == count($guarantor_ids)){ //checks for duplicate guarantors
                            foreach ($guarantor_ids as $key => $guarantor_id) {
                                if($guarantor_id){
                                    if($guarantor_id == $this->member->id){
                                        $guarantor_details_are_valid = FALSE;
                                        $validation_errors['guarantor_ids['.$key.']'] = 'You cannot select yourself as a guarantor';
                                    }else{//check if based on member savings loan_amount_type
                                        $data['contribution_options'] = $this->contributions_m->get_group_refundable_contribution_options();
                                        $guarantor_savings = $this->reports_m->get_group_member_total_contributions($guarantor_id,$data['contribution_options']);
                                        $guarantor_savings = $guarantor_savings > 0?$guarantor_savings:0;      
                                        $maximum_amount_to_grant = $guarantor_savings*($this->loan_type->loan_times_number?currency($this->loan_type->loan_times_number):1);
                                        $guarantor_amount = isset($guaranteed_amounts[$key])?$guaranteed_amounts[$key]:0;
                                        if($guarantor_amount){
                                            if($guarantor_amount > $maximum_amount_to_grant){
                                                $guarantor_details_are_valid = FALSE;
                                                $validation_errors['guaranteed_amounts['.$key.']'] = $this->active_group_member_options[$guarantor_id].' can not guarantee that much ';
                                            }
                                        }else{
                                            $guarantor_details_are_valid = FALSE;
                                            $validation_errors['guaranteed_amounts['.$key.']'] = 'Please select a valid amount to guarantee';
                                        }
                                    }
                                }else{
                                    $guarantor_details_are_valid = FALSE;
                                    $validation_errors['guarantor_id['.$key.']'] = 'Please select a valid amount to guarantee';
                                }
                            }

                            if($guarantor_details_are_valid){
                                //valid amount
                                if($this->loan_type->loan_amount_type == 1){//range
                                    if($loan_application_amount > $this->loan_type->maximum_loan_amount){
                                        if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                                            $guarantor_details_are_valid = FALSE;
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Selected less guarantors than the required',
                                            );
                                        }else{
                                            if($this->loan_type->loan_guarantors_type == 1){//every time 
                                                $guarantor_details_are_valid = FALSE;
                                                $response = array(
                                                    'status' => 0,
                                                    'message' => 'Amount applied is above the required maximum amount',
                                                );
                                            }elseif ($this->loan_type->loan_guarantors_type == 2) {//when exceeds savings
                                                $amount_above_maximum = currency($loan_application_amount - $this->loan_type->maximum_loan_amount);
                                                if($total_guaranteed_amount < $amount_above_maximum){
                                                    $guarantor_details_are_valid = FALSE;
                                                    $response = array(
                                                        'status' => 0,
                                                        'message' => 'Total guaranteed amount is less than allowed amount to be guaranteed',
                                                    );
                                                }
                                            }
                                        }
                                    }else{
                                        if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                                            $guarantor_details_are_valid = FALSE;
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Guarantors selected are less than the required guarantors',
                                            );
                                        }else{
                                            if($this->loan_type->loan_guarantors_type == 1){//every time
                                               //carry on
                                            }elseif ($this->loan_type->loan_guarantors_type == 2) {//when exceeds savings
                                                $amount_above_maximum = currency($loan_application_amount - $this->loan_type->maximum_loan_amount);
                                                if($total_guaranteed_amount < $amount_above_maximum){
                                                    $guarantor_details_are_valid = FALSE;
                                                    $response = array(
                                                        'status' => 0,
                                                        'message' => 'Guaranteed amount is less than allowed amount to be guaranteed',
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }else if($this->loan_type->loan_amount_type == 2){//member savings
                                    $member_savings = $this->transactions->get_group_member_savings($this->group->id,$this->member->id);
                                    $maximum_allowed_loan = currency($member_savings * $this->loan_type->loan_times_number);
                                    $amount_above_savings = currency($loan_application_amount - $maximum_allowed_loan);
                                    if($this->loan_type->enable_loan_guarantors){
                                        if($this->loan_type->loan_guarantors_type == 1){//every time
                                            if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                                                $guarantor_details_are_valid = FALSE;
                                                $response = array(
                                                    'status' => 0,
                                                    'message' => 'Guarantors selected is less than the minimum guarantors required',
                                                );
                                            }
                                        }elseif ($this->loan_type->loan_guarantors_type == 2) {//when exceeds savings
                                            if($loan_application_amount > $maximum_allowed_loan){
                                                if(count($guarantor_ids) < $this->loan_type->minimum_guarantors){
                                                    $guarantor_details_are_valid = FALSE;
                                                    $response = array(
                                                        'status' => 0,
                                                        'message' => 'Guarantors selected are less than the required',
                                                    );
                                                }else{
                                                    if($total_guaranteed_amount < $amount_above_savings){
                                                        $guarantor_details_are_valid = FALSE;
                                                        $response = array(
                                                            'status' => 0,
                                                            'message' => 'Guaranteed amount is less than allowed amount to be guaranteed',
                                                        );
                                                    }
                                                }
                                            }                      
                                        }
                                    }
                                }else{
                                    $guarantor_details_are_valid = FALSE;
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Invalid loan type selected',
                                    );
                                }

                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => 'Please review the highlighted fields and try again',
                                    'validation_errors' => empty($validation_errors)?'':$validation_errors,
                                );
                            }
                        }else{
                            $guarantor_details_are_valid = FALSE;
                            $repeated_guarantor_ids = array_diff_key($guarantor_ids,array_unique($guarantor_ids));
                            foreach ($repeated_guarantor_ids as $key => $value) {
                                $validation_errors['guarantor_ids['.$key.']'] = 'You cannot select the same guarantor more than once';
                            }
                            $response = array(
                                'status' => 0,
                                'message' => 'Please review the highlighted fields and try again',
                                'validation_errors' => empty($validation_errors)?'':$validation_errors,
                            );
                        }
                    }

                    if($guarantor_details_are_valid){
                        $loan_application = array(
                            'member_id'=>$this->member->id,
                            'group_id'=>$this->group->id,
                            'loan_type_id'=>$loan_type_id,
                            'loan_type_is_admin'=>$this->loan_type->is_admin,
                            'status'=>0,
                            'is_approved'=>0,
                            'is_declined'=>0,
                            'loan_amount'=>currency($this->input->post('loan_application_amount')),
                            'active'=>1,
                            'agree_to_rules'=>$this->input->post('loan_rules_check_box'),
                            'created_on'=>time(),
                            'created_by'=>$this->user->id
                        );
                        if($this->loan_type->loan_repayment_period_type == 1){
                            $loan_application += array(
                                'repayment_period'=>$this->loan_type->fixed_repayment_period,
                            );
                        }else if($this->loan_type->loan_repayment_period_type == 2){
                            $loan_application += array(
                                'repayment_period'=>$this->input->post('repayment_period'),
                            );  
                        }

                        $wallet_account = $this->loan_type->is_admin?$this->bank_accounts_m->get_admin_wallet_account():$this->wallets_m->get_wallet_account();
                        if($wallet_account){
                            $loan_application += array(
                                'account_id' => 'bank-'.$wallet_account->id,
                            );
                        }

                        $loan_application_id = $this->loan_applications_m->insert($loan_application);
                        $loan_application += array('id' => $loan_application_id);
                        if($this->loan_type->enable_loan_guarantors == 1){
                            $guarantor_details = array();
                            foreach ($guarantor_ids as $key => $guarantor_id) {
                                $guarantor_details[$guarantor_id] = array(
                                    'amount' => $guaranteed_amounts[$key],
                                    'comment' => $guarantor_comments[$key],
                                );
                            }

                            if($this->loan->create_guarantors_loan_application_approval_requests($loan_application,$this->loan_type,$guarantor_details)){
                                $this->loan->toggle_loan_application_status($loan_application,$this->loan_type);
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Loan application submitted. An approval request has been sent to all your guarantors, kindly await their responses to this loan application',
                                    'refer'=>site_url('member/loan_applications')
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => $this->session->warning,
                                );
                            }
                        }else{
                            if($this->loan->create_group_signatories_loan_application_approval_requests($loan_application,$this->loan_type)){
                                $this->loan->toggle_loan_application_status($loan_application,$this->loan_type);
                                $response = array(
                                    'status' => 1,
                                    'message' => 'Loan application submitted. An approval request has been sent to all your group signatories, kindly await their responses to this loan application',
                                    'refer'=>site_url('member/loan_applications')
                                );
                            }else{
                                $response = array(
                                    'status' => 0,
                                    'message' => $this->session->warning,
                                );
                            }
                        }
                    }else{
                        //do nothing for now
                        //response already set above
                    }
                    
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Loan type details missing',
                        'validation_errors' => empty($validation_errors)?'':$validation_errors,
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Please agree to group loan rules',
                    'validation_errors' => '',
                );
            }
            
        }else{
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $this->form_validation->error_array(),
            );
        }
        echo json_encode($response);
    }

    function get_pending_member_guarantorship_requests(){
        $pending_loan_guarantorship_requests = $this->loans_m->get_pending_member_loan_guarantorship_requests($this->member->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($pending_loan_guarantorship_requests){
            $table = '';
            $i = 0; 
            $table.='
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Requested By</th>
                            <th>Amount</th>
                            <th>Loan Type</th>
                            <th>Requested on</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>';
                        foreach ($pending_loan_guarantorship_requests as $pending_loan_guarantorship_request):
                            $i++;
                            $table .= '
                                <tr>
                                    <th scope="row">'.$i.'</th>
                                    <td>'.$this->active_group_member_options[$pending_loan_guarantorship_request->loan_applicant_member_id].'</td>
                                    <td>'.$this->group_currency.' '.currency($pending_loan_guarantorship_request->amount).'</td>
                                    <td>';
                                        $table .= isset($loan_type_options[$pending_loan_guarantorship_request->loan_type_id])?$loan_type_options[$pending_loan_guarantorship_request->loan_type_id]:'Custom Group Loan';
                                $table .= ' 
                                    </td>
                                    <td>'.timestamp_to_date_and_time($pending_loan_guarantorship_request->created_on).'</td>
                                    <td>
                                        <a href="#" class="btn btn-sm confirmatiosn_link btn-primary m-btn m-btn--icon action_button approve" id="'.$pending_loan_guarantorship_request->id.'">
                                            <span>
                                                <i class="la la-trash"></i>
                                                <span>
                                                    Approve &nbsp;&nbsp;
                                                </span>
                                            </span>
                                        </a>
                                        <a href="#" class="btn btn-sm confirmadtion_link btn-danger m-btn m-btn--icon action_button" id="decline" data-id="'.$pending_loan_guarantorship_request->id.'"  data-toggle="modal" data-target="#decline_guarantorship_request_modal">
                                            <span>
                                                <i class="la la-trash"></i>
                                                <span>
                                                    Decline &nbsp;&nbsp;
                                                </span>
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                            ';
                        endforeach;
                $table .='
                    </tbody>
                </table>';
                    echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>Oooops!</strong> Seems you have no pending loan guarantorship requests.
                </div>
            ';  
        }

    }

    function get_approved_member_guarantorship_requests(){
        $approved_loan_guarantorship_requests = $this->loans_m->get_approved_member_loan_guarantorship_requests($this->member->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($approved_loan_guarantorship_requests){
            $table = '';
            $i = 0; 
            $table.='
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Requested By</th>
                            <th>Amount</th>
                            <th>Loan Type</th>
                            <th>Requested on</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>';
                        foreach ($approved_loan_guarantorship_requests as $approved_loan_guarantorship_request):
                            $i++;
                            $table .= '
                                <tr>
                                    <th scope="row">'.$i.'</th>
                                    <td>'.$this->active_group_member_options[$approved_loan_guarantorship_request->loan_applicant_member_id].'</td>
                                    <td>'.$this->group_currency.' '.currency($approved_loan_guarantorship_request->amount).'</td>
                                    <td>';
                                        $table .= isset($loan_type_options[$approved_loan_guarantorship_request->loan_type_id])?$loan_type_options[$approved_loan_guarantorship_request->loan_type_id]:'Custom Group Loan';
                                $table .= ' 
                                    </td>
                                    <td>'.timestamp_to_date_and_time($approved_loan_guarantorship_request->created_on).'</td>
                                </tr>
                            ';
                        endforeach;
                $table .='
                    </tbody>
                </table>';
                    echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>Oooops!</strong> Seems you have no approved loan guarantorship requests yet.
                </div>
            ';  
        }

    }

    function get_declined_member_guarantorship_requests(){
        $declined_loan_guarantorship_requests = $this->loans_m->get_declined_member_loan_guarantorship_requests($this->member->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($declined_loan_guarantorship_requests){
            $table = '';
            $i = 0; 
            $table.='
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Requested By</th>
                            <th>Amount</th>
                            <th>Loan Type</th>
                            <th>Requested on</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>';
                        foreach ($declined_loan_guarantorship_requests as $declined_loan_guarantorship_request):
                            $i++;
                            $table .= '
                                <tr>
                                    <th scope="row">'.$i.'</th>
                                    <td>'.$this->active_group_member_options[$declined_loan_guarantorship_request->loan_applicant_member_id].'</td>
                                    <td>'.$this->group_currency.' '.currency($declined_loan_guarantorship_request->amount).'</td>
                                    <td>';
                                        $table .= isset($loan_type_options[$declined_loan_guarantorship_request->loan_type_id])?$loan_type_options[$declined_loan_guarantorship_request->loan_type_id]:'Custom Group Loan';
                                $table .= ' 
                                    </td>
                                    <td>'.timestamp_to_date_and_time($declined_loan_guarantorship_request->created_on).'</td>
                                </tr>
                            ';
                        endforeach;
                $table .='
                    </tbody>
                </table>';
                    echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>Oooops!</strong> Seems you have no declined loan guarantorship requests yet.
                </div>
            ';  
        }

    }

    function get_guarantor_details($id = 0){
        $response = array();
        if($id){
            $loan_application = $this->loan_applications_m->get($id);
            $loan_type = $this->loan_types_m->get($loan_application->loan_type_id);
            $guarantors = $this->loans_m->get_loan_application_guarantorship_requests_by_loan_application_id_array($loan_application->id);
            $signatories = $this->loans_m->get_group_signatories_array($loan_application->id);
            $table = '';
            $response = array(
                'loan_approval_requests' => $table,
                'loan_name' => $loan_type->name,
                'applied_on' => timestamp_to_date($loan_application->created_on),
                'applied_by' =>$this->active_group_member_options[$loan_application->member_id] ,
                'amount_applied'=>currency($loan_application->loan_amount),
                'is_approved'=>$loan_application->is_approved,
                'is_declined'=>$loan_application->is_declined,
            );
            echo json_encode($response);
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Sorry').'!</strong> Loan application id is required
                </div>
            ';
        }
    }

    function get_signatory_details($id = 0){        
        $response = array();
        if($id){
            $signatory_details = $this->loans_m->get_loan_signatories($id);
            if($signatory_details){
                $loan_application = $this->loan_applications_m->get($signatory_details->loan_application_id);
                $loan_type = $this->loan_types_m->get($loan_application->loan_type_id);
                $table = '';
                $response = array(
                    'account_id'=>$loan_application->account_id,
                    'loan_approval_requests' => $table,
                    'loan_name' => $loan_type->name,
                    'applied_on' => timestamp_to_date($loan_application->created_on),
                    'applied_by' =>$this->active_group_member_options[$loan_application->member_id] ,
                    'amount_applied'=>currency($loan_application->loan_amount),
                    'is_approved'=>$loan_application->is_approved,
                    'is_declined'=>$loan_application->is_declined,
                );
                echo json_encode($response);
            }else{
                echo '
                    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                        <strong>'.translate('Sorry').'!</strong> Signatory details is missing
                    </div>
                ';  
            }
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Sorry').'!</strong> Signatory details is missing
                </div>
            ';
        }
    }

    function approve_loan_guarantorship_request(){
        $response = array();
        $password = $_POST['password'];
        $loan_guarantorship_request_id = $_POST['id'];
        if($this->ion_auth->login($this->user->phone,$password)){
            $loan_guarantorship_request = $this->loans_m->get_loan_guarantorship_request($loan_guarantorship_request_id);
            if($loan_guarantorship_request){
                $input = array(
                    'is_approved' => 1,
                    'is_declined' => 0,
                    'modified_on' => time(),
                    'modified_by' => $this->user->id,
                );
                if($this->loans_m->update_loan_guarantorship_request($loan_guarantorship_request_id,$input)){
                    $loan_application = $this->loan_applications_m->get($loan_guarantorship_request->loan_application_id);
                    $loan_type = $this->loan_types_m->get($loan_guarantorship_request->loan_type_id);
                    $this->loan->toggle_loan_application_status($loan_application,$loan_type);
                    $response = array(
                        'status' => 1,
                        'message' => 'Loan guarantorship request approved.',
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not update loan guarantorship request status.',
                    );  
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Loan guarantorship request records not found.',
                );  
            }
            
        }else{
            $response = array(
                'status' => 0,
                'message' => 'The password provided is invalid.',
            );  
        }
        echo json_encode($response);
    }

    function decline_loan_guarantorship_request(){
        $response = array();
        $password = $_POST['password'];
        $decline_reason = $_POST['decline_reason'];
        $loan_guarantorship_request_id = $_POST['id'];
        if($this->ion_auth->login($this->user->phone,$password)){
            if($decline_reason){
                $loan_guarantorship_request = $this->loans_m->get_loan_guarantorship_request($loan_guarantorship_request_id);
                if($loan_guarantorship_request){
                    $input = array(
                        'is_approved' => 0,
                        'is_declined' => 1,
                        'decline_reason' => $decline_reason,
                        'modified_on' => time(),
                        'modified_by' => $this->user->id,
                    );
                    if($this->loans_m->update_loan_guarantorship_request($loan_guarantorship_request_id,$input)){
                        $loan_application = $this->loan_applications_m->get($loan_guarantorship_request->loan_application_id);
                        $loan_type = $this->loan_types_m->get($loan_guarantorship_request->loan_type_id);
                        $this->loan->toggle_loan_application_status($loan_application,$loan_type);
                        $response = array(
                            'status' => 1,
                            'message' => 'Loan guarantorship request declined.',
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not update loan guarantorship request status.',
                        );  
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Loan guarantorship request records not found.',
                    );  
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Please provide a reason for declining the request.',
                ); 
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'The password provided is invalid.',
            );  
        }
        echo json_encode($response);
    }


    function group_signatory_approve_loan_application(){
        $response = array();
        $password = $_POST['password'];
        $loan_signatory_request_id = $_POST['id'];
        if($this->ion_auth->login($this->user->phone,$password)){
            $loan_signatory_request = $this->loans_m->get_loan_signatory_request($loan_signatory_request_id);
            if($loan_signatory_request){
                $input = array(
                    'is_approved' => 1,
                    'is_declined' => 0,
                    'modified_on' => time(),
                    'modified_by' => $this->user->id,
                );
                if($this->loans_m->update_loan_signatory_request($loan_signatory_request_id,$input)){
                    $loan_application = $this->loan_applications_m->get($loan_signatory_request->loan_application_id);
                    $loan_type = $this->loan_types_m->get($loan_application->loan_type_id);
                    $this->loan->toggle_loan_application_status($loan_application,$loan_type);
                    $response = array(
                        'status' => 1,
                        'message' => 'Loan application approved.',
                        'refer'=>site_url('bank/loan_applications'),
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not update loan application status.',
                    );  
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Loan application records not found.',
                );  
            }
            
        }else{
            $response = array(
                'status' => 0,
                'message' => 'The password provided is invalid.',
            );  
        }
        echo json_encode($response);
    }

    function group_signatory_decline_loan_application(){
        $response = array();
        $password = $_POST['password'];
        $decline_reason = $_POST['decline_reason'];
        $loan_signatory_request_id = $_POST['id'];
        if($this->ion_auth->login($this->user->phone,$password)){
            if($decline_reason){
                $loan_signatory_request = $this->loans_m->get_loan_signatory_request($loan_signatory_request_id);
                if($loan_signatory_request){
                    $input = array(
                        'is_approved' => 0,
                        'is_declined' => 1,
                        'decline_reason' => $decline_reason,
                        'modified_on' => time(),
                        'modified_by' => $this->user->id,
                    );
                    if($this->loans_m->update_loan_signatory_request($loan_signatory_request_id,$input)){
                        $loan_application = $this->loan_applications_m->get($loan_signatory_request->loan_application_id);
                        $loan_type = $this->loan_types_m->get($loan_application->loan_type_id);
                        $this->loan->toggle_loan_application_status($loan_application,$loan_type);
                        $response = array(
                            'status' => 1,
                            'message' => 'Loan application declined.',
                            'refer'=>site_url('bank/loan_applications'),
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not update loan application status.',
                        );  
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Loan application records not found.',
                    );  
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Please provide a reason for declining the application.',
                ); 
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'The password provided is invalid.',
            );  
        }
        echo json_encode($response);
    }
}