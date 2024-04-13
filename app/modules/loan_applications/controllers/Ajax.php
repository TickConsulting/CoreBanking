<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    
    function __construct(){
        parent::__construct();

        $this->load->model('loan_applications_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('loan_types/loan_types_m');
    }

    function cancel_loan_application($id = 0){
        if($id){
            $loan_application = $this->loan_applications_m->get($id);
            if($loan_application){
                if($loan_application->is_approved){
                    $response = array(
                        'status' => 0,
                        'message' => 'The request has already been approved by all signatories',
                    );
                }elseif($loan_application->is_declined){
                    $response = array(
                        'status' => 0,
                        'message' => 'The request has already been declined by all signatories',
                    );
                }else{
                    $input = array(
                        'active' => 0,
                        'modified_by' => $this->user->id,
                        'modified_on' => time(),
                    );
                    if($this->loan_applications_m->update($id,$input)){
                        $response = array(
                            'status' => 1,
                            'message' => 'Loan application cancelled',
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not update loan application entry',
                        );
                    }
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find loan application',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find loan application',
            );
        }
        echo json_encode($response);
    }

    function get_loan_application($id = 0){
        $response = array();
        if($id){
            $loan_application = $this->loan_applications_m->get($id);
            $loan_application_guarantorship_requests = $this->loans_m->get_loan_application_guarantorship_requests($loan_application->id);
            $loan_application_signatory_requests = $this->loans_m->get_loan_application_signatory_requests($loan_application->id);
            $loan_type = $this->loan_types_m->get($loan_application->loan_type_id);
            $guarantors = $this->loans_m->get_loan_application_guarantorship_requests_by_loan_application_id_array($loan_application->id);
            $signatories = $this->loans_m->get_group_member_signatories_array($loan_application->id);
            $decline_reason = $loan_application->decline_reason;
            $table = '';
            if($loan_application_guarantorship_requests){
                $table .= '
                    <div class="text-center">
                        <h5>Guarantor Approvals</h5>
                    </div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Member</th>
                                <th>Application Date</th>
                                <th>Requested Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = 0;                        
                            foreach ($loan_application_guarantorship_requests as $loan_application_guarantorship_request) {
                                $i++;
                                $table .= '
                                    <tr>
                                        <th scope="row">'.$i.'</th>
                                        <td>'.$this->active_group_member_options[$loan_application_guarantorship_request->guarantor_member_id].'</td>
                                        <td>'.timestamp_to_date_and_time($loan_application_guarantorship_request->created_on).'</td>
                                        <td>'.$this->group_currency.' '.number_to_currency($loan_application_guarantorship_request->amount).'</td>
                                        <td>';
                                            if($loan_application_guarantorship_request->is_approved){
                                                $table .= '<span class="m-badge m-badge--success m-badge--wide">Approved</span>';
                                            }elseif($loan_application_guarantorship_request->is_declined){
                                                $decline_reason = $loan_application->decline_reason?:$loan_application_guarantorship_request->decline_reason;
                                                $table .= '<span class="m-badge m-badge--danger m-badge--wide">Declined</span>';
                                            }else{
                                                if($loan_application->is_declined){
                                                    $table .= '<span class="m-badge m-badge--warning m-badge--wide">Did not respond</span>';
                                                }else{
                                                    $table .= '<span class="m-badge m-badge--warning m-badge--wide">Pending</span>';
                                                }
                                            }
                                $table .='
                                        </td>
                                    </tr>
                                ';
                            }
                            $table .='
                        </tbody>
                    </table>
                ';
            }
            if($loan_application_signatory_requests){
                $table .= '
                    <div class="text-center">
                        <h5>Group Signatories Approvals</h5>
                    </div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                               <th>#</th>
                                <th>Signatory</th>
                                <th>Application Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = 0;
                            foreach ($loan_application_signatory_requests as $loan_application_signatory_request) {
                                $i++;
                                $table .= '
                                    <tr>
                                        <th scope="row">'.$i.'</th>
                                        <td>'.$loan_application_signatory_request->signatory_first_name.' '.$loan_application_signatory_request->signatory_last_name.'</td>
                                        <td>'.timestamp_to_date_and_time($loan_application_signatory_request->created_on).'</td>
                                        <td>';
                                            if($loan_application_signatory_request->is_approved){
                                                $table .= '<span class="m-badge m-badge--success m-badge--wide">Approved</span>';
                                            }elseif($loan_application_signatory_request->is_declined){
                                                $decline_reason = $loan_application->decline_reason?$loan_application->decline_reason:$loan_application_signatory_request->decline_reason;
                                                $table .= '<span class="m-badge m-badge--danger m-badge--wide">Declined</span>';
                                            }else{
                                                if($loan_application->is_declined){
                                                    $table .= '<span class="m-badge m-badge--warning m-badge--wide">Did not respond</span>';
                                                }else{
                                                    $table .= '<span class="m-badge m-badge--warning m-badge--wide">Pending</span>';
                                                }
                                            }
                                        if($loan_application_signatory_request->signatory_member_id == $this->member->id && !$loan_application_signatory_request->is_approved && !$loan_application_signatory_request->is_declined && !$loan_application->is_declined && !$loan_application->is_approved){
                                            $table .= '
                                                <span class="float-right">
                                                    <a href="'.base_url("group/loan_applications/respond/").$loan_application->id.'" class="btn btn-sm btn-primary m-btn m-btn--icon action_button" id="'.$loan_application_signatory_request->id.'" data-message="Are you sure you want to approve loan application?">
                                                        <span>
                                                            <i class="mdi mdi-briefcase-check"></i>
                                                            <span>
                                                                Respond &nbsp;&nbsp; 
                                                            </span>
                                                        </span>
                                                    </a>
                                                </span>
                                            ';
                                        }
                                $table .='
                                    </tr>
                                ';
                            }
                            $table .='
                        </tbody>
                    </table>
                ';
            }
            $disbursing_account = $loan_application->account_id?$this->accounts_m->get_group_account($loan_application->account_id):'';
            $account_set_by = $disbursing_account?$this->ion_auth->get_user($loan_application->account_set_by):'';
            $declined_by = $loan_application->declined_by?$this->ion_auth->get_user($loan_application->declined_by):'';
            $response = array(
                'loan_approval_requests' => $table,
                'loan_name' => $loan_type->name,
                'applied_on' => timestamp_to_date_and_time($loan_application->created_on),
                'applied_by' =>$this->active_group_member_options[$loan_application->member_id] ,
                'amount_applied'=>$this->group_currency.' '.number_to_currency($loan_application->loan_amount),
                'declined_by'=>$declined_by?$declined_by->first_name.' '.$declined_by->last_name:'Automatically declined by the system',
                'decline_reason'=>$decline_reason?:'',
                'is_approved'=>$loan_application->is_approved,
                'status'=>$loan_application->status,
                'disbursement_fail_reason'=>$loan_application->disbursement_fail_reason,
                'is_declined'=>$loan_application->is_declined,
                'disbursing_account' => $disbursing_account,
                'account_set_by' => $account_set_by?$account_set_by->first_name.' '.$account_set_by->last_name:'',
            );
            echo json_encode($response);
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>Sorry!</strong> Loan application data not found
                </div>
            ';
        }
    }

    function get_pending_group_loan_applications(){
        $pending_loan_applications = $this->loan_applications_m->get_group_pending_loan_applications($this->group->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($pending_loan_applications){
            $table = '';
            $i = 0; 
            $table.='
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th nowrap>#</th>
                                <th nowrap>Loan Type</th>
                                <th nowrap>Application By</th>
                                <th nowrap>Application Date</th>
                                <th nowrap>Amount</th>
                                <th nowrap>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($pending_loan_applications as $pending_loan_application):
                                $i++;
                                $table .= '
                                    <tr data-toggle="modal" class="get_loan_application" id="'.$pending_loan_application->id.'" data-target="#get_loan_application_modal" style="cursor:pointer;">
                                        <th scope="row">'.$i.'</th>
                                        <td>';
                                            $table .= isset($loan_type_options[$pending_loan_application->loan_type_id])?$loan_type_options[$pending_loan_application->loan_type_id]:'Custom Group Loan';
                            $table .= ' </td>
                                        <td>'.$this->active_group_member_options[$pending_loan_application->member_id].'</td>
                                        <td>'.timestamp_to_date_and_time($pending_loan_application->created_on).'</td>
                                        <td nowrap>'.$this->group_currency.' '.number_to_currency($pending_loan_application->loan_amount).'</td>
                                        <td>
                                            <span class="m-badge m-badge--info m-badge--wide float-right">More...</span>
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                    $table .='
                        </tbody>
                    </table>
                </div>
            ';
            echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Oooops').'</strong> '.translate('Seems you have no pending loan applications').'.
                </div>
            ';  
        }
    }

    function get_approved_group_loan_applications_pending_disbursement(){
        $approved_loan_applications = $this->loan_applications_m->get_approved_group_loan_applications_pending_disbursement($this->group->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($approved_loan_applications){
            $table = '';
            $i = 0; 
            $table.='
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th nowrap>#</th>
                                <th nowrap>Loan Type</th>
                                <th nowrap>Application by</th>
                                <th nowrap>Application Date</th>
                                <th nowrap>Amount</th>
                                <th nowrap>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($approved_loan_applications as $approved_loan_application):
                                $i++;
                                $table .= '
                                    <tr data-toggle="modal" class="get_loan_application" id="'.$approved_loan_application->id.'" data-target="#get_loan_application_modal" style="cursor:pointer;">
                                        <th scope="row">'.$i.'</th>
                                        <td>';
                                            $table .= isset($loan_type_options[$approved_loan_application->loan_type_id])?$loan_type_options[$approved_loan_application->loan_type_id]:'Custom Group Loan';
                            $table .= ' </td>
                                        <td>'.$this->active_group_member_options[$approved_loan_application->member_id].'</td>
                                        <td>'.timestamp_to_date_and_time($approved_loan_application->created_on).'</td>
                                        <td nowrap>'.$this->group_currency.' '.number_to_currency($approved_loan_application->loan_amount).'</td>
                                        <td>
                                            <span class="m-badge m-badge--info m-badge--wide float-right">More...</span>
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                    $table .='
                        </tbody>
                    </table>
                </div>
            ';
            echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Oooops').'!</strong> '.translate('Seems you have no approved loan applications yet').'.
                </div>
            ';  
        }
    }

    function get_disbursed_group_loan_applications(){
        $approved_loan_applications = $this->loan_applications_m->get_group_disbursed_loan_applications($this->group->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($approved_loan_applications){
            $table = '';
            $i = 0; 
            $table.='
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th nowrap>#</th>
                                <th nowrap>Loan Type</th>
                                <th nowrap>Application By</th>
                                <th nowrap>Application Date</th>
                                <th nowrap>Amount</th>
                                <th nowrap>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($approved_loan_applications as $approved_loan_application):
                                $i++;
                                $table .= '
                                    <tr data-toggle="modal" class="get_loan_application" id="'.$approved_loan_application->id.'" data-target="#get_loan_application_modal" style="cursor:pointer;">
                                        <th scope="row">'.$i.'</th>
                                        <td>';
                                            $table .= isset($loan_type_options[$approved_loan_application->loan_type_id])?$loan_type_options[$approved_loan_application->loan_type_id]:'Custom Group Loan';
                            $table .= ' </td>
                                        <td>'.$this->active_group_member_options[$approved_loan_application->member_id].'</td>
                                        <td>'.timestamp_to_date_and_time($approved_loan_application->created_on).'</td>
                                        <td nowrap>'.$this->group_currency.' '.number_to_currency($approved_loan_application->loan_amount).'</td>
                                        <td>
                                            <span class="m-badge m-badge--info m-badge--wide float-right">More...</span>
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                    $table .='
                        </tbody>
                    </table>
                </div>
            ';
            echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Oooops').'!</strong> '.translate('Seems you have no approved loan applications yet').'.
                </div>
            ';  
        }
    }

    function get_disbursement_failed_group_loan_applications(){
        $approved_loan_applications = $this->loan_applications_m->get_group_disbursement_failed_loan_applications($this->group->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($approved_loan_applications){
            $table = '';
            $i = 0; 
            $table.='
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th nowrap>#</th>
                                <th nowrap>Loan Type</th>
                                <th nowrap>Application By</th>
                                <th nowrap>Application Date</th>
                                <th nowrap>Amount</th>
                                <th nowrap>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($approved_loan_applications as $approved_loan_application):
                                $i++;
                                $table .= '
                                    <tr data-toggle="modal" class="get_loan_application" id="'.$approved_loan_application->id.'" data-target="#get_loan_application_modal" style="cursor:pointer;">
                                        <th scope="row">'.$i.'</th>
                                        <td>';
                                            $table .= isset($loan_type_options[$approved_loan_application->loan_type_id])?$loan_type_options[$approved_loan_application->loan_type_id]:'Custom Group Loan';
                            $table .= ' </td>
                                        <td>'.$this->active_group_member_options[$approved_loan_application->member_id].'</td>
                                        <td>'.timestamp_to_date_and_time($approved_loan_application->created_on).'</td>
                                        <td nowrap>'.$this->group_currency.' '.number_to_currency($approved_loan_application->loan_amount).'</td>
                                        <td>
                                            <span class="m-badge m-badge--info m-badge--wide float-right">More...</span>
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                    $table .='
                        </tbody>
                    </table>
                </div>
            ';
            echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Oooops').'!</strong> '.translate('Seems you have no approved loan applications yet').'.
                </div>
            ';  
        }
    }

    function get_declined_group_loan_applications(){
        $declined_loan_applications = $this->loan_applications_m->get_group_declined_loan_applications($this->group->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($declined_loan_applications){
            $table = '';
            $i = 0; 
            $table.='
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th nowrap>#</th>
                                <th nowrap>Loan Type</th>
                                <th nowrap>Application By</th>
                                <th nowrap>Application Date</th>
                                <th nowrap>Amount</th>
                                <th nowrap>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($declined_loan_applications as $declined_loan_application):
                                $i++;
                                $table .= '
                                    <tr data-toggle="modal" class="get_loan_application" id="'.$declined_loan_application->id.'" data-target="#get_loan_application_modal" style="cursor:pointer;">
                                        <th scope="row">'.$i.'</th>
                                        <td>';
                                            $table .= isset($loan_type_options[$declined_loan_application->loan_type_id])?$loan_type_options[$declined_loan_application->loan_type_id]:'Custom Group Loan';
                            $table .= ' </td>
                                        <td>'.$this->active_group_member_options[$declined_loan_application->member_id].'</td>
                                        <td>'.timestamp_to_date_and_time($declined_loan_application->created_on).'</td>
                                        <td nowrap>'.$this->group_currency.' '.number_to_currency($declined_loan_application->loan_amount).'</td>
                                        <td>
                                            <span class="m-badge m-badge--info m-badge--wide float-right">More...</span>
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                    $table .='
                        </tbody>
                    </table>
                </div>
            ';
            echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Oooops').'!</strong> '.translate('Seems you have no approved loan applications yet').'.
                </div>
            ';  
        }
    }

    function get_pending_member_loan_applications(){
        $pending_loan_applications = $this->loan_applications_m->get_member_pending_loan_applications($this->member->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($pending_loan_applications){
            $table = '';
            $i = 0; 
            $table.='
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th nowrap>#</th>
                                <th nowrap>'. translate('Loan Type').'</th>
                                <th nowrap>'. translate('Application Date').'</th>
                                <th nowrap>'. translate('Amount').'</th>
                                <th nowrap>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($pending_loan_applications as $pending_loan_application):
                                $i++;
                                $table .= '
                                    <tr data-toggle="modal" class="get_loan_application" id="'.$pending_loan_application->id.'" data-target="#get_loan_application_modal" style="cursor:pointer;">
                                        <th scope="row">'.$i.'</th>
                                        <td>';
                                            $table .= isset($loan_type_options[$pending_loan_application->loan_type_id])?$loan_type_options[$pending_loan_application->loan_type_id]:'Custom Group Loan';
                            $table .= ' </td>
                                        <td>'.timestamp_to_date_and_time($pending_loan_application->created_on).'</td>
                                        <td nowrap>'.$this->group_currency.' '.number_to_currency($pending_loan_application->loan_amount).'</td>
                                        <td>
                                            <span class="m-badge m-badge--info m-badge--wide float-right">More...</span>
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                    $table .='
                        </tbody>
                    </table>
                </div>
            ';
            echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'. translate('Oooops').'!</strong> '. translate('Seems you have no pending loan applications').'.
                </div>
            ';  
        }
    }

    function get_member_approved_loan_applications_pending_disbursement(){
        $approved_loan_applications = $this->loan_applications_m->get_member_approved_loan_applications_pending_disbursement($this->member->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($approved_loan_applications){
            $table = '';
            $i = 0; 
            $table.='
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th nowrap>#</th>
                                <th nowrap>'. translate('Loan Type').'</th>
                                <th nowrap>'. translate('Application Date').'</th>
                                <th nowrap>'. translate('Amount').'</th>
                                <th nowrap>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($approved_loan_applications as $approved_loan_application):
                                $i++;
                                $table .= '
                                    <tr data-toggle="modal" class="get_loan_application" id="'.$approved_loan_application->id.'" data-target="#get_loan_application_modal" style="cursor:pointer;">
                                        <th scope="row">'.$i.'</th>
                                        <td>';
                                            $table .= isset($loan_type_options[$approved_loan_application->loan_type_id])?$loan_type_options[$approved_loan_application->loan_type_id]:'Custom Group Loan';
                            $table .= ' </td>
                                        <td>'.timestamp_to_date_and_time($approved_loan_application->created_on).'</td>
                                        <td nowrap>'.$this->group_currency.' '.number_to_currency($approved_loan_application->loan_amount).'</td>
                                        <td>
                                            <span class="m-badge m-badge--info m-badge--wide float-right">More...</span>
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                    $table .='
                        </tbody>
                    </table>
                </div>
            ';
            echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'. translate('Oooops').'!</strong>'. translate('Seems you have no approved loan applications yet').'.
                </div>
            ';  
        }
    }

    function get_disbursed_member_loan_applications(){
        $approved_loan_applications = $this->loan_applications_m->get_member_disbursed_loan_applications($this->member->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($approved_loan_applications){
            $table = '';
            $i = 0; 
            $table.='
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th nowrap>#</th>
                                <th nowrap>'. translate('Loan Type').'</th>
                                <th nowrap>'. translate('Application Date').'</th>
                                <th nowrap>'. translate('Amount').'</th>
                                <th nowrap>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($approved_loan_applications as $approved_loan_application):
                                $i++;
                                $table .= '
                                    <tr data-toggle="modal" class="get_loan_application" id="'.$approved_loan_application->id.'" data-target="#get_loan_application_modal" style="cursor:pointer;">
                                        <th scope="row">'.$i.'</th>
                                        <td>';
                                            $table .= isset($loan_type_options[$approved_loan_application->loan_type_id])?$loan_type_options[$approved_loan_application->loan_type_id]:'Custom Group Loan';
                            $table .= '</td>
                                        <td>'.timestamp_to_date_and_time($approved_loan_application->created_on).'</td>
                                        <td nowrap>'.$this->group_currency.' '.number_to_currency($approved_loan_application->loan_amount).'</td>
                                        <td>
                                            <span class="m-badge m-badge--info m-badge--wide float-right">'. translate('More...').'</span>
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                    $table .='
                        </tbody>
                    </table>
                </div>
            ';
            echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'. translate('Oooops').'!</strong>'. translate('Seems you have no approved loan applications yet').'.
                </div>
            ';  
        }
    }

    function get_disbursement_failed_member_loan_applications(){
        $approved_loan_applications = $this->loan_applications_m->get_member_disbursement_failed_loan_applications($this->member->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($approved_loan_applications){
            $table = '';
            $i = 0; 
            $table.='
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th nowrap>#</th>
                                <th nowrap>'. translate('Loan Type').'</th>
                                <th nowrap>'. translate('Application Date').'</th>
                                <th nowrap>'. translate('Amount').'</th>
                                <th nowrap>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($approved_loan_applications as $approved_loan_application):
                                $i++;
                                $table .= '
                                    <tr data-toggle="modal" class="get_loan_application" id="'.$approved_loan_application->id.'" data-target="#get_loan_application_modal" style="cursor:pointer;">
                                        <th scope="row">'.$i.'</th>
                                        <td>';
                                            $table .= isset($loan_type_options[$approved_loan_application->loan_type_id])?$loan_type_options[$approved_loan_application->loan_type_id]:'Custom Group Loan';
                            $table .= ' </td>
                                        <td>'.timestamp_to_date_and_time($approved_loan_application->created_on).'</td>
                                        <td nowrap>'.$this->group_currency.' '.number_to_currency($approved_loan_application->loan_amount).'</td>
                                        <td>
                                            <span class="m-badge m-badge--info m-badge--wide float-right">'. translate('More...').'</span>
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                    $table .='
                        </tbody>
                    </table>
                </div>
            ';
            echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'. translate('Oooops').'!</strong> '. translate('Seems you have no approved loan applications yet').'.
                </div>
            ';  
        }
    }

    function get_declined_member_loan_applications(){
        $declined_loan_applications = $this->loan_applications_m->get_member_declined_loan_applications($this->member->id);
        $loan_type_options = $this->loan_types_m->get_options();
        if($declined_loan_applications){
            $table = '';
            $i = 0; 
            $table.='
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th nowrap>#</th>
                                <th nowrap>'. translate('Loan Type').'</th>
                                <th nowrap>'. translate('Application Date').'</th>
                                <th nowrap>'. translate('Amount').'</th>
                                <th nowrap>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($declined_loan_applications as $declined_loan_application):
                                $i++;
                                $table .= '
                                    <tr data-toggle="modal" class="get_loan_application" id="'.$declined_loan_application->id.'" data-target="#get_loan_application_modal" style="cursor:pointer;">
                                        <th scope="row">'.$i.'</th>
                                        <td>';
                                            $table .= isset($loan_type_options[$declined_loan_application->loan_type_id])?$loan_type_options[$declined_loan_application->loan_type_id]:'Custom Group Loan';
                            $table .= ' </td>
                                        <td>'.timestamp_to_date_and_time($declined_loan_application->created_on).'</td>
                                        <td nowrap>'.$this->group_currency.' '.number_to_currency($declined_loan_application->loan_amount).'</td>
                                        <td>
                                            <span class="m-badge m-badge--info m-badge--wide float-right">'. translate('More...').'</span>
                                        </td>
                                    </tr>
                                ';
                            endforeach;
                    $table .='
                        </tbody>
                    </table>
                </div>
            ';
            echo $table;
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'. translate('Oooops').'!</strong> '. translate('Seems you have no declined loan applications yet').'.
                </div>
            ';  
        }
    }

    function set_loan_application_disbursing_account(){
        if($_POST){
            $id = $_POST['id'];
            $account_id = $_POST['account_id'];
            $loan_application = $this->loan_applications_m->get($id);
            if($loan_application){
                $input = array(
                    'account_id' => $account_id,
                    'modified_by' => $this->user->id,
                    'account_set_by' => $this->user->id,
                    'modified_on' => time(),
                );
                if($this->loan_applications_m->update($id,$input)){
                    $response = array(
                        'status' => 1,
                        'message' => 'Disbursing account successfully set',
                        'account' => $this->accounts_m->get_group_account($account_id),
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not update loan application',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Loan Application Records Not Found',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Loan Application Records Not Found',
            );
        }
        echo json_encode($response);
    }

    function get_loan_application_disbursing_account(){
        if($_POST){
            $id = $_POST['id'];
            $loan_application = $this->loan_applications_m->get($id);
            if($loan_application){
                $account = $loan_application->loan_type_is_admin?$this->bank_accounts_m->get_admin_wallet_account():$this->accounts_m->get_group_account($loan_application->account_id);
                $account_set_by = $loan_application->account_set_by?$this->ion_auth->get_user($loan_application->account_set_by):'';
                $response = array(
                    'status' => 1,
                    'message' => '',
                    'account' => $account,
                    'account_set_by' => $account_set_by?$account_set_by->first_name.' '.$account_set_by->last_name:'',
                );
                
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Loan Application Records Not Found',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Loan Application Records Not Found',
            );
        }
        echo json_encode($response);
    }
}