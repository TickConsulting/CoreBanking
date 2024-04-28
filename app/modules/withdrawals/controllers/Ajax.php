<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->model('withdrawals_m');
        $this->load->model('expense_categories/expense_categories_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('recipients/recipients_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('assets/assets_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->library('transactions');
        $this->load->library('messaging');
        $this->load->library('loan');
    }

    function get_withdrawals_listing(){
       // if(array_key_exists($this->member->id, $this->member_role_holder_options)){
            $transaction_alert_id = $this->input->get('transaction_alert');
            $data = array();
            $from = strtotime($this->input->get('from'))?:"";
            $to = strtotime($this->input->get('to'))?:'';
            $filter_parameters = array(
                'transaction_alert_id' => $transaction_alert_id,
                'from' => $from,
                'to' => $to,
                'type' => $this->input->get('type'),
                'expense_categories' => $this->input->get('expense_categories'),
                'assets' => $this->input->get('assets'),
                'member_id' => $this->input->get('member_id'),
                'accounts' => $this->input->get('accounts'),
                'contributions' => $this->input->get('contributions'),
                'stocks' => $this->input->get('stocks'),
                'money_market_investments' => $this->input->get('money_market_investments'),
            );
            $total_rows = $this->withdrawals_m->count_group_withdrawals($filter_parameters);
            $pagination = create_pagination('group/withdrawals/listing/pages', $total_rows,50,5,TRUE);
            $asset_options = $this->assets_m->get_group_asset_options();
            $contribution_options = $this->contributions_m->get_group_contribution_options();
            $withdrawal_transaction_names = $this->transactions->withdrawal_transaction_names;
            $withdrawal_type_options = $this->transactions->withdrawal_type_options;
            $expense_category_options = $this->expense_categories_m->get_group_expense_category_options();
            $asset_options = $this->assets_m->get_group_asset_options();
            $account_options = $this->accounts_m->get_active_group_account_options(FALSE,FALSE,FALSE,$this->group->id);
            $posts = $this->withdrawals_m->limit($pagination['limit'])->get_group_withdrawals($filter_parameters);
            if($this->group->id == 33){
                foreach ($posts as $post) {
                    if($post->transaction_alert_id){
                        $input = array(
                            'reconciled'=>1,
                            'modified_on'=>time()
                        );
                        $this->transaction_alerts_m->update($post->transaction_alert_id,$input);
                    }
                }
            }
            if(!empty($posts)){ 
                echo form_open('group/withdrawals/action', ' id="form"  class="form-horizontal"'); 
                if (! empty($pagination['links'])):
                    echo '
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Withdrawals</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
                echo ' 
                    <table class="table m-table m-table--head-separator-primary">
                        <thead>
                            <tr>
                                <th width=\'2%\'>
                                    <label class="m-checkbox">
                                        <input type="checkbox" name="check" value="all" class="check_all">
                                        <span></span>
                                    </label>
                                </th>
                                <th>
                                    '.translate('Details').'
                                </th>
                                <th class=\'text-right\'>
                                    '.translate('Amount').' ('.$this->group_currency.')
                                </th>  
                                <th>
                                    '.translate('Actions').'
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = $this->uri->segment(5, 0); 
                            $i++; 
                            foreach($posts as $post):
                                echo '
                                <tr>
                                    <th scope="row">
                                        <label class="m-checkbox">
                                            <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                            <span></span>
                                        </label>
                                    </th>
                                    <td>';
                                        echo "#".($i++)." : <strong>".$withdrawal_transaction_names[$post->type].'</strong><br/>';
                                        echo '<strong>Withdrawal Date : </strong>'.timestamp_to_date($post->withdrawal_date).', <small><strong>Recorded On : </strong>'.timestamp_to_date_and_time($post->created_on).'</small></br>';
                                        if($post->type==1||$post->type==2||$post->type==3||$post->type==4){
                                            echo $withdrawal_transaction_names[$post->type].' for '.$expense_category_options[$post->expense_category_id];
                                        }else if($post->type==5||$post->type==6||$post->type==7||$post->type==8){
                                            echo $withdrawal_transaction_names[$post->type].' for '.$asset_options[$post->asset_id];
                                        }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
                                            echo $withdrawal_transaction_names[$post->type];
                                            if($post->member_id){ echo ' to '.$this->group_member_options[$post->member_id];}
                                        }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                                            echo $withdrawal_transaction_names[$post->type].' to '.$this->group_member_options[$post->member_id].' for '.$contribution_options[$post->contribution_id];
                                        }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                                            echo $withdrawal_transaction_names[$post->type];
                                        }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                                            echo $withdrawal_transaction_names[$post->type];
                                        }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                                            echo $withdrawal_transaction_names[$post->type];
                                        }else if($post->type==33||$post->type==34||$post->type==35||$post->type==36){
                                            echo $withdrawal_transaction_names[$post->type];
                                            if($post->debtor_id){ echo ' to '.$this->group_debtor_options[$post->debtor_id];}
                                        }else if($post->type==37||$post->type==38||$post->type==39||$post->type==40){
                                            echo $withdrawal_transaction_names[$post->type];
                                            if($post->member_id){ echo ' to '.$this->group_member_options[$post->member_id];}
                                        }
                                        if($post->description){
                                            echo ' : '.$post->description;
                                        }
                                        echo '<br/>
                                            <strong>Account : </strong>'.$account_options[$post->account_id];

                                        if($post->transaction_alert_id){
                                                
                                            echo '<span class="m-badge m-badge--info m-badge--wide float-right">Reconciled</span>';
                                        }
                                    echo '
                                    </td>
                                    <td  class=\'text-right\'>
                                        '.number_to_currency($post->amount).'
                                    </td>  
                                    <td>
                                        <a href="'.site_url('group/withdrawals/void/'.$post->id).'" class="btn btn-sm action_button confirmation_link btn-danger m-btn m-btn--icon">
                                            <span>
                                                <i class="la la-trash"></i>
                                                <span>
                                                    '.translate('Void').' &nbsp;&nbsp;
                                                </span>
                                            </span>
                                        </a>
                                    </td>
                                </tr>';
                            endforeach;
                            echo '
                        </tbody>
                    </table>
                <div class="row col-md-12">';
                    if( ! empty($pagination['links'])): 
                        echo $pagination['links']; 
                    endif; 
            echo '
                </div>';
            if($posts):
                echo '<button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> '.translate('Bulk Void').'</button>';
            endif;
            echo form_close();
            }else{
                echo '
                    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                        <strong>'.translate('Sorry').'!</strong> '.translate('There are no withdrawal records to display').'.
                    </div>
                ';
            } 
        // }else{
        //     echo '
        //     <div class="container-fluid">
        //         <div class="row">
        //             <div class="col-md-12">
        //                 <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
        //                     <strong>Information!</strong> You dont have rights to access this panel
        //                 </div>
        //             </div>
        //         </div>
        //     </div>';
        // }
    }
    
    function cancel_withdrawal_request($id = 0){
        if($id){
            $refer =  $this->agent->referrer();
            if(preg_match('/member/', $refer)){
                $segment = 'member';
            }else{
                $segment = 'group';
            }
            $withdrawal_request = $this->withdrawals_m->get_withdrawal_request($id);
            if($withdrawal_request){
                if($withdrawal_request->is_approved){
                    $response = array(
                        'status' => 0,
                        'message' => 'The request has already been approved by all signatories',
                    );
                }elseif($withdrawal_request->is_declined){
                    $response = array(
                        'status' => 0,
                        'message' => 'The request has already been declined by all signatories',
                    );
                }else{
                    $decline_reason = 'cancelled by the initiator';
                    if($this->transactions->decline_withdrawal_request($this->user->id,$this->group,$withdrawal_request->id,$this->member->id,$decline_reason,TRUE,$this->group_currency)){
                        $response = array(
                            'status' => 1,
                            'message' => 'Success',
                            'refer'=>site_url($segment.'/withdrawals/withdrawal_requests')
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not update withdrawal approval request.',
                        );
                    }

                    // $input = array(
                    //     'active' => 0,
                    //     'modified_by' => $this->user->id,
                    //     'modified_on' => time(),
                    // );
                    // if($this->withdrawals_m->update_withdrawal_request($id,$input)){
                    //     $response = array(
                    //         'status' => 1,
                    //         'message' => 'Withdrawal request cancelled',
                    //     );
                    // }else{
                    //     $response = array(
                    //         'status' => 0,
                    //         'message' => 'Could not update withdrawal request entry',
                    //     );
                    // }
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find withdrawal request',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find withdrawal request',
            );
        }
        echo json_encode($response);
    }

    function get_pending_withdrawal_requests(){
        $withdrawal_request_transaction_names = $this->transactions->withdrawal_request_transaction_names;
        // $total_rows = $this->withdrawals_m->count_group_withdrawal_requests('pending');
        // $pagination = create_pagination('group/withdrawals/withdrawal_requests/pages', $total_rows,50,5,TRUE);
        $posts = $this->withdrawals_m->get_group_withdrawal_requests('pending');
        if(empty($posts)){ 
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'. translate('Sorry') .'!</strong>'. translate('There are no pending withdrawal requests') .'.
                </div>
            ';
        }else{
            echo ' 
                <div class="table-responsive datatable">
                    <table class="table table-bordered table-hover ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>'.translate('Requested on').'</th>
                                <th>'.translate('Requested By').'</th>
                                <th class="text-right">'.translate('Amount').'('.$this->group_currency.')</th>
                                <th>'.translate('Type').'</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = $this->uri->segment(5, 0); 
                            $i++;
                            foreach($posts as $post){
                                $user = $this->ion_auth->get_user($post->user_id)?$this->ion_auth->get_user($post->user_id):$this->ion_auth->get_user($post->created_by);
                                echo '
                                    <tr data-toggle="modal" class="get_withdrawal_request" id="'.$post->id.'" data-target="#get_withdrawal_request_modal" style="cursor:pointer;">
                                        <th scope="row">'.($i++).'</th>
                                        <td>'.timestamp_to_date_and_time($post->request_date).'</td>
                                        <td>'.(isset($this->active_group_member_options_by_user_id[$post->user_id])?($user?$user->first_name.' '.$user->last_name:''):($user?($this->ion_auth->is_admin($user->id)?'System Admin':'Deleted member'):'Deleted member')).'</td>
                                        <td class="text-right">'.number_to_currency($post->amount).'</td>
                                        <td>
                                            '.$withdrawal_request_transaction_names[$post->withdrawal_for].'
                                            <span class="m-badge m-badge--info m-badge--wide float-right">'.translate('More').'...</span>
                                        </td>
                                    </tr>
                                ';
                            }
                        echo '
                        </tbody>
                    </table>
                </div>
                <div class="row col-md-12">';
                    if(!empty($pagination['links'])): 
                        echo $pagination['links']; 
                    endif; 
                    echo '
                </div>
            ';
        }
    }

    function get_disbursement_pending_withdrawal_requests(){
        $withdrawal_request_transaction_names = $this->transactions->withdrawal_request_transaction_names;
        $posts = $this->withdrawals_m->get_group_undisbursed_approved_withdrawal_requests();
        if(empty($posts)){ 
            echo '
                    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                        <strong>'.translate('Sorry').'!</strong>'. translate('There are no approved withdrawal request records to show') .'.
                    </div>
                ';
        }else{
            echo ' 
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>'.translate('Requested on').'</th>
                                <th>'.translate('Requested By').'</th>
                                <th class="text-right">'.translate('Amount').'("KES")</th>
                                <th>'.translate('Type').'</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = $this->uri->segment(5, 0); 
                            $i++;
                            foreach($posts as $post){
                                $user = $this->ion_auth->get_user($post->user_id);
                                echo '
                                    <tr data-toggle="modal" class="get_withdrawal_request" id="'.$post->id.'" data-target="#get_withdrawal_request_modal" style="cursor:pointer;">
                                        <th scope="row">'.($i++).'</th>
                                        <td>'.timestamp_to_date_and_time($post->request_date).'</td>
                                        <td>'.$this->members_m->get_group_member($post->member_id)->first_name.' '.$this->members_m->get_group_member($post->member_id)->last_name.'</td>
                                        <td class="text-right">'.number_to_currency($post->amount).'</td>
                                        <td>
                                            '.$withdrawal_request_transaction_names[$post->withdrawal_for].'
                                            <span class="m-badge m-badge--info m-badge--wide float-right">'.translate('More').'...</span>
                                        </td>
                                    </tr>
                                ';
                            }
                        echo '
                        </tbody>
                    </table>
                </div>
            ';
        }
    }

    function get_disbursed_withdrawal_requests(){
        $withdrawal_request_transaction_names = $this->transactions->withdrawal_request_transaction_names;
        $posts = $this->withdrawals_m->get_group_disbursed_withdrawal_requests();
        if(empty($posts)){ 
            echo '
                    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                        <strong>'.translate('Sorry').'!</strong>'. translate('There are no approved withdrawal request records to show') .'.
                    </div>
                ';
        }else{
            echo ' 
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>'.translate('Requested on').'</th>
                                <th>'.translate('Requested By').'</th>
                                <th class="text-right">'.translate('Amount').'('.$this->group_currency.')</th>
                                <th>'.translate('Type').'</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = $this->uri->segment(5, 0); 
                            $i++;
                            foreach($posts as $post){
                                $user = $this->ion_auth->get_user($post->user_id);
                                echo '
                                    <tr data-toggle="modal" class="get_withdrawal_request" id="'.$post->id.'" data-target="#get_withdrawal_request_modal" style="cursor:pointer;">
                                        <th scope="row">'.($i++).'</th>
                                        <td>'.timestamp_to_date_and_time($post->request_date).'</td>
                                        <td>'.(isset($this->active_group_member_options_by_user_id[$post->user_id])?($user?$user->first_name.' '.$user->last_name:''):($user?($this->ion_auth->is_admin($user->id)?'System Admin':'Deleted member'):'Deleted member')).'</td>
                                        <td class="text-right">'.number_to_currency($post->amount).'</td>
                                        <td>
                                            '.$withdrawal_request_transaction_names[$post->withdrawal_for].'
                                            <span class="m-badge m-badge--info m-badge--wide float-right">'.translate('More').'...</span>
                                        </td>
                                    </tr>
                                ';
                            }
                        echo '
                        </tbody>
                    </table>
                </div>
            ';
        }
    }

    function get_disbursement_failed_withdrawal_requests(){
        $withdrawal_request_transaction_names = $this->transactions->withdrawal_request_transaction_names;
        $posts = $this->withdrawals_m->get_group_disbursement_failed_withdrawal_requests();
        if(empty($posts)){ 
            echo '
                    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                        <strong>'.translate('Sorry').'!</strong>'. translate('There are no approved withdrawal request records to show') .'.
                    </div>
                ';
        }else{
            echo ' 
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>'.translate('Requested on').'</th>
                                <th>'.translate('Requested By').'</th>
                                <th class="text-right">'.translate('Amount').'('.$this->group_currency.')</th>
                                <th>'.translate('Type').'</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = $this->uri->segment(5, 0); 
                            $i++;
                            foreach($posts as $post){
                                $user = $this->ion_auth->get_user($post->user_id);
                                echo '
                                    <tr data-toggle="modal" class="get_withdrawal_request" id="'.$post->id.'" data-target="#get_withdrawal_request_modal" style="cursor:pointer;">
                                        <th scope="row">'.($i++).'</th>
                                        <td>'.timestamp_to_date_and_time($post->request_date).'</td>
                                        <td>'.(isset($this->active_group_member_options_by_user_id[$post->user_id])?($user?$user->first_name.' '.$user->last_name:''):($user?($this->ion_auth->is_admin($user->id)?'System Admin':'Deleted member'):'Deleted member')).'</td>
                                        <td class="text-right">'.number_to_currency($post->amount).'</td>
                                        <td>
                                            '.$withdrawal_request_transaction_names[$post->withdrawal_for].'
                                            <span class="m-badge m-badge--info m-badge--wide float-right">'.translate('More').'...</span>
                                        </td>
                                    </tr>
                                ';
                            }
                        echo '
                        </tbody>
                    </table>
                </div>
            ';
        }
    }

    function get_declined_withdrawal_requests(){
        $withdrawal_request_transaction_names = $this->transactions->withdrawal_request_transaction_names;
        $posts = $this->withdrawals_m->get_group_withdrawal_requests('declined');
        if(empty($posts)){ 
            echo '
                    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                        <strong>'.translate('Sorry').'!</strong>'. translate('There are no declined withdrawal request records to show') .'.
                    </div>
                ';
        }else{
            echo ' 
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>'.translate('Requested on').'</th>
                                <th>'.translate('Requested By').'</th>
                                <th class="text-right">'.translate('Amount').'('.$this->group_currency.')</th>
                                <th>'.translate('Type').'</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = $this->uri->segment(5, 0); 
                            $i++;
                            foreach($posts as $post){
                                $user = $this->ion_auth->get_user($post->user_id);
                                echo '
                                    <tr data-toggle="modal" class="get_withdrawal_request" id="'.$post->id.'" data-target="#get_withdrawal_request_modal" style="cursor:pointer;">
                                        <th scope="row">'.($i++).'</th>
                                        <td>'.timestamp_to_date_and_time($post->request_date).'</td>
                                        <td>'.(isset($this->active_group_member_options_by_user_id[$post->user_id])?($user?$user->first_name.' '.$user->last_name:''):($user?($this->ion_auth->is_admin($user->id)?'System Admin':'Deleted member'):'Deleted member')).'</td>
                                        <td class="text-right">'.number_to_currency($post->amount).'</td>
                                        <td>
                                            '.$withdrawal_request_transaction_names[$post->withdrawal_for].'
                                            <span class="m-badge m-badge--info m-badge--wide float-right">'.translate('More').'...</span>
                                        </td>
                                    </tr>
                                ';
                            }
                        echo '
                        </tbody>
                    </table>
                </div>
            ';
        }
    }

    function get_withdrawal_request($id = 0,$url_segment=""){
        if($id && $url_segment){
            $withdrawal_request = $this->withdrawals_m->get_withdrawal_request($id);
            if($withdrawal_request){
                $recipient = '';
                $recipient_description = '';
                $withdrawal_approval_requests = $this->withdrawals_m->get_group_withdrawal_request_approval_requests($withdrawal_request->id,$this->group->id);
                $table = '
                  
                ';
                if(preg_match('/member/', $withdrawal_request->recipient_id)){
                    $recipient = $this->members_m->get_group_member(str_replace('member-', '', $withdrawal_request->recipient_id));
                    if($recipient){
                        $recipient_description = 'Mobile Money Account - '.$recipient->first_name.' '.$recipient->last_name.'('.valid_phone($recipient->phone).') <span class="m-badge m-badge--success m-badge--wide">Applicant</span>';
                    }else{
                        $recipient_description = '<span class="m-badge m-badge--metal m-badge--wide">Deleted Applicant</span>';
                    }
                }else if(preg_match('/bank/', $withdrawal_request->recipient_id)){
                    $recipient = $this->recipients_m->get(str_replace('bank-', '', $withdrawal_request->recipient_id));
                    $recipient_description = 'Bank Account - '.$recipient->account_name.'('.$recipient->account_number.')';
                }else if(preg_match('/paybill/', $withdrawal_request->recipient_id)){
                    $recipient = $this->recipients_m->get(str_replace('paybill-', '', $withdrawal_request->recipient_id));
                    $recipient_description = 'Paybill Account - '.$recipient->name.'('.$recipient->paybill_number.' Account:'.$recipient->account_number.')';
                }else if(preg_match('/mobile/', $withdrawal_request->recipient_id)){
                    $recipient = $this->recipients_m->get(str_replace('mobile-', '', $withdrawal_request->recipient_id));
                    $recipient_description = 'Mobile Money Account - '.$recipient->name.'('.valid_phone($recipient->phone_number).')';
                }else{
                    $recipient = $this->recipients_m->get($withdrawal_request->recipient_id);
                    if($recipient){
                        if($recipient->type == 1){ //mobile money
                            $recipient_description = 'Mobile Money Account - '.$recipient->name.'('.valid_phone($recipient->phone_number).')';
                        }else if($recipient->type == 2){ //paybill
                            $recipient_description = 'Paybill Account - '.$recipient->name.'('.$recipient->paybill_number.' Account:'.$recipient->account_number.')';
                        }else if($recipient->type == 3){ //bank account
                            $recipient_description = 'Bank Account - '.$recipient->account_name.'('.$recipient->account_number.')';
                        }
                    }else{
                        $recipient = $this->members_m->get_group_member($withdrawal_request->recipient_member_id);
                        $recipient_description = $recipient?$recipient->first_name.' '.$recipient->last_name.'('.valid_phone($withdrawal_request->recipient_phone_number).')':'';
                    }
                }

                $description = '';
                if($withdrawal_request->withdrawal_for == 1){ //Loan Disbursement
                    $loan_type = $this->loan_types_m->get($withdrawal_request->loan_type_id);
                    if($loan_type){
                        $description .= $this->transactions->withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]." of ".$this->group_currency." ".number_to_currency($withdrawal_request->amount)." to ".$this->active_group_member_options[$withdrawal_request->member_id].' for the '.$loan_type->name." loan ";
                    }else{
                        $description .= 'Loan type details not found';
                    }
                   
                    if($withdrawal_request->description){
                        $description .= ': '.$withdrawal_request->description;
                    }
                }else if($withdrawal_request->withdrawal_for == 2){ //Expense Payment
                    $expense_category = $this->expense_categories_m->get($withdrawal_request->expense_category_id);
                    $description .= $this->transactions->withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for].($expense_category?" for ".$expense_category->name:'')." of <strong> ".$this->group_currency." ".number_to_currency($withdrawal_request->amount)."</strong>";
                    if($withdrawal_request->description){
                        $description .= ': '.$withdrawal_request->description;
                    }
                }else if($withdrawal_request->withdrawal_for == 3){ //Dividend Payout
                    $description .= $this->transactions->withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]."  of <strong> ".$this->group_currency." ".number_to_currency($withdrawal_request->amount)."</strong>";
                    if($withdrawal_request->description){
                        $description .= ': '.$withdrawal_request->description;
                    }
                }else if($withdrawal_request->withdrawal_for == 4){ //Welfare
                    $description .= $this->transactions->withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]." payment of <strong> ".$this->group_currency." ".number_to_currency($withdrawal_request->amount)."</strong>";
                    if($withdrawal_request->description){
                        $description .= ': '.$withdrawal_request->description;
                    }
                }else if($withdrawal_request->withdrawal_for == 5){ //Shares Refund
                    $contribution = $this->contributions_m->get($withdrawal_request->contribution_id);
                    $description .= $this->transactions->withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]." to ".$this->group_member_options[$withdrawal_request->member_id]." of <strong> ".$this->group_currency." ".number_to_currency($withdrawal_request->amount)."</strong> from ".$contribution->name;
                    if($withdrawal_request->description){
                        $description .= ': '.$withdrawal_request->description;
                    }
                }else if($withdrawal_request->withdrawal_for == 6){ //Account transfer
                    $active_accounts = $this->accounts_m->get_active_group_account_options('','','','',TRUE);
                    $description .= $this->transactions->withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]." to ".$active_accounts[$withdrawal_request->account_to_id]." of ".$this->group_currency." ".number_to_currency($withdrawal_request->amount);
                    if($withdrawal_request->description){
                        $description .= ': '.$withdrawal_request->description;
                    }
                }
                
                $user = $this->ion_auth->get_user($withdrawal_request->user_id);
                
                $requested_by = isset($user->first_name)?$user->first_name.' '.$user->last_name:'';
                $declined_by = '';
                if($withdrawal_request->is_declined){
                    $user = $this->ion_auth->get_user($withdrawal_request->declined_by);
                    $declined_by = isset($this->active_group_member_options_by_user_id[$withdrawal_request->declined_by])?($user?$user->first_name.' '.$user->last_name:''):($user?($this->ion_auth->is_admin($user->id)?'System':'Deleted member'):'Deleted member');

                }
                $response = array(
                    'withdrawal_approval_requests' => $table,
                    'withdrawal_request' => array(
                        'type' => $this->transactions->withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for],
                        'request_date' => timestamp_to_date_and_time($withdrawal_request->request_date),
                        'requested_by' => $requested_by,
                        'declined_by' => $declined_by,
                        'recipient' => $recipient_description?:$withdrawal_request->disbursement_result_description,
                        'status' => $withdrawal_request->is_approved?'is_approved':($withdrawal_request->is_declined?'is_declined':'is_pending'),
                        'disbursement_failed_error_message' => $withdrawal_request->disbursement_failed_error_message,
                        'is_disbursement_declined' => $withdrawal_request->is_disbursement_declined,
                        'is_disbursed' => $withdrawal_request->is_disbursed,
                        'decline_reason' => $withdrawal_request->decline_reason,
                        'created_by' => $withdrawal_request->created_by,
                        'reference_number' => $withdrawal_request->reference_number,
                        // 'group_id' => $withdrawal_request->group_id,
                        'description' => $description,
                        'withdrawal_for' => $withdrawal_request->withdrawal_for,
                        'account_to' => $withdrawal_request->account_to_id,
                    ),
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Error occured. We could not find the requested withdrawal request. kindly refesh the page and try again',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Error occured trying to get the request. Refresh the page and try again',
            );
        }
        echo json_encode($response);
    }

    function approve_withdrawal_request(){
        $response = array();
        $refer =  $this->agent->referrer();
        if(preg_match('/member/', $refer)){
            $segment = 'member';
        }else{
            $segment = 'group';
        }
        $withdrawal_approval_request_id = $_POST['withdrawal_approval_request_id'];
        if($withdrawal_approval_request_id){
            $withdrawal_approval_request = $this->withdrawals_m->get_group_withdrawal_approval_request($withdrawal_approval_request_id);
            if($withdrawal_approval_request){                
                if($withdrawal_approval_request->is_otp_verified){
                    if($withdrawal_approval_request->is_approved || $withdrawal_approval_request->is_declined){
                        $response = array(
                            'status' => 0,
                            'message' => 'You have already responded to this withdrawal request.',
                        );
                    }else{
                        if($this->transactions->approve_withdrawal_request($this->user->id,$this->group,$withdrawal_approval_request->withdrawal_request_id,$this->member->id,$this->group_currency)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Success',
                                'refer'=>site_url($segment.'/withdrawals/withdrawal_requests')
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not update withdrawal approval request. Error Message :'.$this->session->flashdata('error'),
                            );
                        }
                    }
                }else{$response = array(
                        'status' => 0,
                        'message' => 'Approval request OTP has not been verified.',
                    );                    
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find withdrawal approval request.',
                ); 
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find withdrawal approval request.',
            );
        }
        echo json_encode($response);
    }

    function decline_withdrawal_request(){
        $refer =  $this->agent->referrer();
        if(preg_match('/member/', $refer)){
            $segment = 'member';
        }else{
            $segment = 'group';
        }

        $response = array();
        $withdrawal_approval_request_id = $_POST['withdrawal_approval_request_id'];
        $decline_reason = $_POST['decline_reason'];
        $password = $_POST['password'];
        if($password){
            if($withdrawal_approval_request_id){
                if($decline_reason){
                    $identity = $this->user->phone?:(valid_email($this->user->email)?$this->user->email:'');
                    if($this->ion_auth->login(trim($identity),$password)){
                        $withdrawal_approval_request = $this->withdrawals_m->get_group_withdrawal_approval_request($withdrawal_approval_request_id);
                        if($withdrawal_approval_request){
                            if($withdrawal_approval_request->is_approved || $withdrawal_approval_request->is_declined){
                                $response = array(
                                    'status' => 0,
                                    'message' => 'You have already responded to this withdrawal request.',
                                );
                            }else{
                                if($this->transactions->decline_withdrawal_request($this->user->id,$this->group,$withdrawal_approval_request->withdrawal_request_id,$this->member->id,$decline_reason,FALSE,$this->group_currency)){
                                    $response = array(
                                        'status' => 1,
                                        'message' => 'Success',
                                        'refer'=>site_url($segment.'/withdrawals/withdrawal_requests')
                                    );
                                }else{
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'Could not update withdrawal approval request. Error Message :'.$this->session->flashdata('error'),
                                    );
                                }
                            }
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not find withdrawal approval request.',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Incorrect password provided.',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Please give a reason.',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find withdrawal approval request.',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Incorrect password provided.',
            );
        }
        echo json_encode($response);
    }

    function _conditional_validation_rules(){
        $withdrawal_for = $this->input->post('withdrawal_for');
        if($withdrawal_for == 1){
            $this->withdraw_money_rules[] = array(
                'field' =>  'loan_type_id',
                'label' =>  'Loan Type',
                'rules' =>  'xss_clean|trim|numeric|required',
            );
            $this->withdraw_money_rules[] = array(
                'field' =>  'member_id',
                'label' =>  'Member',
                'rules' =>  'xss_clean|trim|numeric|required',
            );
        }else if($withdrawal_for == 2){
            $this->withdraw_money_rules[] = array(
                'field' =>  'expense_category_id',
                'label' =>  'Expense category',
                'rules' =>  'xss_clean|trim|numeric|required',
            );
        }else if($withdrawal_for == 3){
            $this->withdraw_money_rules[] = array(
                'field' =>  'member_id',
                'label' =>  'Member',
                'rules' =>  'xss_clean|trim|numeric|required',
            );
            $this->withdraw_money_rules[] = array(
                'field' =>  'description',
                'label' =>  'Description',
                'rules' =>  'xss_clean|trim|required',
            );
        }else if($withdrawal_for == 4){
            $this->withdraw_money_rules[] = array(
                'field' =>  'member_id',
                'label' =>  'Member',
                'rules' =>  'xss_clean|trim|numeric|required',
            );
            $this->withdraw_money_rules[] = array(
                'field' =>  'description',
                'label' =>  'Description',
                'rules' =>  'xss_clean|trim|required',
            );
        }else if($withdrawal_for == 5){
            $this->withdraw_money_rules[] = array(
                'field' =>  'member_id',
                'label' =>  'Member',
                'rules' =>  'xss_clean|trim|numeric|required',
            );
            $this->withdraw_money_rules[] = array(
                'field' =>  'contribution_id',
                'label' =>  'Contribution',
                'rules' =>  'xss_clean|trim|numeric|required',
            );
        }else if($withdrawal_for == 6){
            $this->withdraw_money_rules[] = array(
                'field' =>  'account_to_id',
                'label' =>  'Account To',
                'rules' =>  'xss_clean|trim|required',
            );
            $this->withdraw_money_rules[] = array(
                'field' =>  'description',
                'label' =>  'Description',
                'rules' =>  'xss_clean|trim',
            );
        }
    }

    protected $withdraw_money_rules = array(
        array(
            'field' =>  'withdrawal_for',
            'label' =>  'Withdrawal For',
            'rules' =>  'xss_clean|trim|numeric|required',
        ),
        array(
            'field' =>  'amount',
            'label' =>  'Amount',
            'rules' =>  'xss_clean|trim|valid_currency|required',
        ),
        array(
            'field' =>  'bank_account_id',
            'label' =>  'Disbursing Bank Account',
            'rules' =>  'xss_clean|trim|numeric|required',
        ),
        array(
            'field' =>  'transfer_to',
            'label' =>  'Transfer to',
            'rules' =>  'xss_clean|trim|numeric|required',
        ),
        array(
            'field' =>  'recipient',
            'label' =>  'Recipient',
            'rules' =>  'xss_clean|trim|required',
        ),
    );

    function withdraw_money(){ //process a withdrawal request, does batch for scalability
        $response = array();
        $this->_conditional_validation_rules();
        $this->form_validation->set_rules($this->withdraw_money_rules);
        $funds_are_available = TRUE;
        if($this->form_validation->run()){
            // check if it is via websacco or eazzys
            if(preg_match('/(websacco)/i',$this->application_settings->application_name)){
                $withdrawal = new stdClass;
                $withdrawal->withdrawal_for = $this->input->post('withdrawal_for');
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
                $withdrawal->account_to_id = $this->input->post('account_to_id');
                // $withdrawal->bank_account_id = $bank_account_id;

                if($this->transactions->process_batch_withdrawal_requests_websacco($withdrawal,$this->group_currency,$this->member,$this->group,$this->user)){
                    $response = array(
                        'status' => 1,
                        'message' => 'Successfully processed withdrawal request(s)',
                        'refer' => site_url('group/withdrawals/withdrawal_requests'),
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => $this->session->flashdata('error'),
                        'errors' =>  array(
                            array(
                                'message' => '',
                                'field' => '',
                            )
                        ),
                    );
                }

            }else{
                $bank_account_id = $this->input->post('bank_account_id');
                $bank_account = $this->bank_accounts_m->get_group_verified_bank_account_by_id($bank_account_id,$this->group->id);
                if($bank_account_id && $bank_account){
                    $withdrawal = new stdClass;
                    $withdrawal->withdrawal_for = $this->input->post('withdrawal_for');
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
                    $withdrawal->account_to_id = $this->input->post('account_to_id');
                    $withdrawal->bank_account_id = $bank_account_id;
                    // if(floatval($bank_account->actual_balance) >= floatval($withdrawal->amount)){
                        if($this->transactions->process_batch_withdrawal_requests($withdrawal,$this->group_currency,$this->member,$this->group,$this->user)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Successfully processed withdrawal request(s)',
                                'refer' => site_url('group/withdrawals/withdrawal_requests'),
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => $this->session->flashdata('error'),
                                'errors' =>  array(
                                    array(
                                        'message' => '',
                                        'field' => '',
                                    )
                                ),
                            );
                        }
                    // }else{
                    //     $response = array(
                    //         'status' => 0,
                    //         'message' => translate('You can not disburse more than is in the selected account. Available balance is ').$this->group_currency.' '.number_to_currency($bank_account->actual_balance),
                    //         'errors' =>  array(
                    //             array(
                    //                 'message' => '',
                    //                 'field' => '',
                    //             )
                    //         ),
                    //     );
                    // }
                }else{
                    $errors[] = array(
                        'message' => translate('You must select a valid disbursing bank account that is connected'),
                        'field' => 'bank_account_id',
                    );
                    $response = array(
                        'status' => 0,
                        'message' => translate('You must select a valid disbursing bank account that is connected'),
                        'errors' => $errors,
                    );
                }
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => translate('The form has some errors. Kindly review'),
                'validation_errors' => validation_errors(),
            );
        }

        echo json_encode($response);die;




























        $withdrawal_fors = isset($_POST['withdrawal_fors'])?$_POST['withdrawal_fors']:array();
        $members = isset($_POST['members'])?$_POST['members']:array();
        $loan_types = isset($_POST['loan_types'])?$_POST['loan_types']:array();
        $amounts = isset($_POST['amounts'])?$_POST['amounts']:array();
        $expense_categories = isset($_POST['expense_categories'])?$_POST['expense_categories']:array();
        $descriptions = isset($_POST['descriptions'])?$_POST['descriptions']:array();
        $recipients = isset($_POST['recipients'])?$_POST['recipients']:array();
        $contribution_ids = isset($_POST['contribution_ids'])?$_POST['contribution_ids']:array();
        $fine_categories = isset($_POST['fine_categories'])?$_POST['fine_categories']:array();
        $contributions = isset($_POST['contributions'])?$_POST['contributions']:array();
        $descriptions = isset($_POST['descriptions'])?$_POST['descriptions']:array();
        $disbursement_channel = isset($_POST['disbursement_channel'])?$_POST['disbursement_channel']:'';
        $transfer_to = isset($_POST['transfer_to'])?$_POST['transfer_to']:'';
        $recipient = isset($_POST['recipient'])?$_POST['recipient']:'';
        $entries_are_valid = TRUE;
        $disbursement_details_are_valid = TRUE;
        $recipient_options = 
        $response = array();
        $errors = array();
        $bank_account_id = $this->input->post('bank_account_id');
        if($bank_account_id && $this->bank_accounts_m->get_group_verified_bank_account_by_id($bank_account_id,$this->group->id)){
            if($recipient){
                for($i = 0;$i < count($withdrawal_fors);$i++){
                    if(isset($amounts[$i])){
                        if($amounts[$i] && valid_currency($amounts[$i])){
                            if($withdrawal_fors[$i] == 1){ //loan disbursement
                                if(isset($loan_types[$i])){
                                    if($loan_types[$i]){
                                        if(isset($members[$i])){
                                            if($members[$i]){
                                                //continue
                                            }else{
                                                $entries_are_valid = FALSE;
                                                $errors[] = array(
                                                    'message' => 'Select member to loan',
                                                    'field' => 'members['.$i.']',
                                                );
                                            }
                                        }else{
                                            $entries_are_valid = FALSE;
                                            $errors[] = array(
                                                'message' => 'Select member to loan',
                                                'field' => 'members['.$i.']',
                                            );
                                        }
                                    }else{
                                        $entries_are_valid = FALSE;
                                        $errors[] = array(
                                            'message' => 'Select loan type to disburse',
                                            'field' => 'loan_types['.$i.']',
                                        );
                                    }
                                }else{
                                    $entries_are_valid = FALSE;
                                    $errors[] = array(
                                        'message' => 'Select loan type to disburse',
                                        'field' => 'loan_types['.$i.']',
                                    );
                                }
                            }else if($withdrawal_fors[$i] == 2){ //Expense Payment
                                if(isset($expense_categories[$i])){
                                    if($expense_categories[$i]){
                                        //continue
                                    }else{
                                        $entries_are_valid = FALSE;
                                        $errors[] = array(
                                            'message' => 'Select the expense category',
                                            'field' => 'expense_categories['.$i.']',
                                        );
                                    }
                                }else{
                                    $entries_are_valid = FALSE;
                                    $errors[] = array(
                                        'message' => 'Select the expense category',
                                        'field' => 'expense_categories['.$i.']',
                                    );
                                }
                            }else if($withdrawal_fors[$i] == 3){ //Dividend Payout
                                if(isset($members[$i])){
                                    if($members[$i]){
                                        //continue
                                    }else{
                                        $entries_are_valid = FALSE;
                                        $errors[] = array(
                                            'message' => 'Select member to payout',
                                            'field' => 'members['.$i.']',
                                        );
                                    }
                                }else{
                                    $entries_are_valid = FALSE;
                                    $errors[] = array(
                                        'message' => 'Select member to payout',
                                        'field' => 'members['.$i.']',
                                    );
                                }
                            }else if($withdrawal_fors[$i] == 4){ //Welfare
                                if(isset($members[$i])){
                                    if($members[$i]){
                                        //continue
                                    }else{
                                        $entries_are_valid = FALSE;
                                        $errors[] = array(
                                            'message' => 'Select welfare recipient member',
                                            'field' => 'members['.$i.']',
                                        );
                                    }
                                }else{
                                    $entries_are_valid = FALSE;
                                    $errors[] = array(
                                        'message' => 'Select welfare recipient members',
                                        'field' => 'members['.$i.']',
                                    );
                                }
                            }else if($withdrawal_fors[$i] == 5){ //Shares Refund
                                if(isset($contributions[$i])){
                                    if($contributions[$i]){
                                        if(isset($members[$i])){
                                            if($members[$i]){
                                                //continue
                                            }else{
                                                $entries_are_valid = FALSE;
                                                $errors[] = array(
                                                    'message' => 'Select member to refund',
                                                    'field' => 'members['.$i.']',
                                                );
                                            }
                                        }else{
                                            $entries_are_valid = FALSE;
                                            $errors[] = array(
                                                'message' => 'Select member to refund',
                                                'field' => 'members['.$i.']',
                                            );
                                        }
                                    }else{
                                        $entries_are_valid = FALSE;
                                        $errors[] = array(
                                            'message' => 'Select contribution to refund',
                                            'field' => 'contributions['.$i.']',
                                        );
                                    }
                                }else{
                                    $entries_are_valid = FALSE;
                                    $errors[] = array(
                                        'message' => 'Select contribution to refund',
                                        'field' => 'contributions['.$i.']',
                                    );
                                }
                            }
                        }else{
                            $entries_are_valid = FALSE;
                            $errors[] = array(
                                'message' => 'Enter a valid currency amount',
                                'field' => 'members['.$i.']',
                            );
                        }
                    }else{
                        $entries_are_valid = FALSE;
                        $errors[] = array(
                            'message' => 'Enter a valid currency amount',
                            'field' => 'members['.$i.']',
                        );
                    }
                }
            }else{
                $entries_are_valid = FALSE;
                $errors[] = array(
                    'message' => 'Select disbursement recipient',
                    'field' => 'recipient',
                );
            }
        }else{
            $entries_are_valid = FALSE;
            $errors[] = array(
                'message' => 'You must select a valid disbursing bank account that is connected',
                'field' => 'bank_account_id',
            );
        }
        if($entries_are_valid){
            $withdrawals = array();
            for($i = 0;$i < count($withdrawal_fors);$i++){
                if($withdrawal_fors[$i] == 1){ //loan disbursement
                    $withdrawals[] = array(
                        'withdrawal_for' => $withdrawal_fors[$i],
                        'member_id' => $members[$i],
                        'loan_type_id' => $loan_types[$i],
                        'amount' => $amounts[$i],
                        'disbursement_channel' => $disbursement_channel,
                        'transfer_to' => $transfer_to,
                        // 'description' => $descriptions[$i],
                        'recipient' => $recipient,
                        'request_date' => time(),
                        'bank_account_id' => $bank_account_id,
                        'group_id' => $this->group->id,
                        'user_id' => $this->user->id,
                        'created_on' => time(),
                        'created_by' => $this->user->id,
                    );
                }else if($withdrawal_fors[$i] == 2){ //Expense Payment
                    $withdrawals[] = array(
                        'withdrawal_for' => $withdrawal_fors[$i],
                        'expense_category_id' => $expense_categories[$i],
                        'amount' => $amounts[$i],
                        'disbursement_channel' => $disbursement_channel,
                        'transfer_to' => $transfer_to,
                        'recipient' => $recipient,
                        // 'description' => $descriptions[$i],
                        'request_date' => time(),
                        'bank_account_id' => $bank_account_id,
                        'group_id' => $this->group->id,
                        'user_id' => $this->user->id,
                        'created_on' => time(),
                        'created_by' => $this->user->id,
                    );
                }else if($withdrawal_fors[$i] == 3){ //Dividend Payout
                    $withdrawals[] = array(
                        'withdrawal_for' => $withdrawal_fors[$i],
                        'member_id' => $members[$i],
                        'amount' => $amounts[$i],
                        'disbursement_channel' => $disbursement_channel,
                        'transfer_to' => $transfer_to,
                        'recipient' => $recipient,
                        'description' => $descriptions[$i],
                        'bank_account_id' => $bank_account_id,
                        'request_date' => time(),
                        'group_id' => $this->group->id,
                        'user_id' => $this->user->id,
                        'created_on' => time(),
                        'created_by' => $this->user->id,
                    );
                }else if($withdrawal_fors[$i] == 4){ //Welfare
                    $withdrawals[] = array(
                        'withdrawal_for' => $withdrawal_fors[$i],
                        'amount' => $amounts[$i],
                        'member_id' => $members[$i],
                        'disbursement_channel' => $disbursement_channel,
                        'transfer_to' => $transfer_to,
                        'recipient' => $recipient,
                        'description' => $descriptions[$i],
                        'bank_account_id' => $bank_account_id,
                        'request_date' => time(),
                        'group_id' => $this->group->id,
                        'user_id' => $this->user->id,
                        'created_on' => time(),
                        'created_by' => $this->user->id,
                    );
                }else if($withdrawal_fors[$i] == 5){ //Shares Refund
                    $withdrawals[] = array(
                        'withdrawal_for' => $withdrawal_fors[$i],
                        'amount' => $amounts[$i],
                        'member_id' => $members[$i],
                        'contribution_id' => $contributions[$i],
                        'bank_account_id' => $bank_account_id,
                        'disbursement_channel' => $disbursement_channel,
                        'transfer_to' => $transfer_to,
                        'recipient' => $recipient,
                        // 'description' => $descriptions[$i],
                        'request_date' => time(),
                        'group_id' => $this->group->id,
                        'user_id' => $this->user->id,
                        'created_on' => time(),
                        'created_by' => $this->user->id,
                    );
                }
            }

            if(empty($withdrawals)){
                $response = array(
                    'status' => 0,
                    'errors' =>  array(
                        array(
                            'message' => 'There were no withdrawal requests to process',
                            'field' => '',
                        )
                    ),
                );
            }else{
                if($this->transactions->process_batch_withdrawal_requests($withdrawals)){
                    $response = array(
                        'status' => 1,
                        'message' => 'Successfully processed withdrawal request(s)',
                        'refer' => site_url('group/withdrawals/withdrawal_requests'),
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => $this->session->warning,
                        'errors' =>  array(
                            array(
                                'message' => '',
                                'field' => '',
                            )
                        ),
                    );
                }
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form, please review and submit again',
                'errors' => $errors,
            );
        }
        echo json_encode($response);
    }

    function record_expenses(){
        $data = array();
        $response = array();
        $post = new stdClass();
        $posts = $_POST;
        $entries_are_valid = TRUE;

        if(!empty($posts)){ 
            if(isset($posts['expense_dates'])){
                $count = count($posts['expense_dates']);
                for($i=0;$i<=$count;$i++):
                    if(isset($posts['expense_dates'][$i])&&isset($posts['expense_categories'][$i])&&isset($posts['withdrawal_methods'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])):    
                        //Deposit dates
                        if($posts['expense_dates'][$i]==''){
                            $successes['expense_dates'][$i] = 0;
                            $errors['expense_dates'][$i] = 1;
                            $error_messages['expense_dates'][$i] = 'Please enter a date';
                            $entries_are_valid = FALSE;
                        }else{
                            $successes['expense_dates'][$i] = 1;
                            $errors['expense_dates'][$i] = 0;
                        }

                        if(valid_date($posts['expense_dates'][$i])){
                            $successes['expense_dates'][$i] = 1;
                            $errors['expense_dates'][$i] = 0;
                        }else{
                            $successes['expense_dates'][$i] = 0;
                            $errors['expense_dates'][$i] = 1;
                            $error_messages['expense_dates'][$i] = 'Please enter a date';
                            $entries_are_valid = FALSE;                               
                        }
                        //Members
                        if($posts['expense_categories'][$i]==''){
                            $successes['expense_categories'][$i] = 0;
                            $errors['expense_categories'][$i] = 1;
                            $error_messages['expense_categories'][$i] = 'Please select an expense category';
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($posts['expense_categories'][$i])){
                                $successes['expense_categories'][$i] = 1;
                                $errors['expense_categories'][$i] = 0;
                            }else{
                                $successes['expense_categories'][$i] = 0;
                                $errors['expense_categories'][$i] = 1;
                                $error_messages['expense_categories'][$i] = 'Please enter a valid expense category value';
                                $entries_are_valid = FALSE;
                            }
                        }
                        //Contributions
                        if($posts['withdrawal_methods'][$i]==''){
                            $successes['withdrawal_methods'][$i] = 0;
                            $errors['withdrawal_methods'][$i] = 1;
                            $error_messages['withdrawal_methods'][$i] = 'Please select a withdrawal method';
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($posts['withdrawal_methods'][$i])){
                                $successes['withdrawal_methods'][$i] = 1;
                                $errors['withdrawal_methods'][$i] = 0;
                            }else{
                                $successes['withdrawal_methods'][$i] = 0;
                                $errors['withdrawal_methods'][$i] = 1;
                                $error_messages['withdrawal_methods'][$i] = 'Please select a valid withdrawal method value';
                                $entries_are_valid = FALSE;
                            }
                        }
                         //Accounts
                        if($posts['accounts'][$i]==''){
                            $successes['accounts'][$i] = 0;
                            $errors['accounts'][$i] = 1;
                            $error_messages['accounts'][$i] = 'Please select an account';
                            $entries_are_valid = FALSE;
                        }else{
                            $successes['accounts'][$i] = 1;
                            $errors['accounts'][$i] = 0;
                        }
                        //amounts
                        if($posts['amounts'][$i]==''){
                            $successes['amounts'][$i] = 0;
                            $errors['amounts'][$i] = 1;
                            $error_messages['amounts'][$i] = 'Please enter a expense amount';
                            $entries_are_valid = FALSE;
                        }else{
                            if(valid_currency($posts['amounts'][$i])){
                                $successes['amounts'][$i] = 1;
                                $errors['amounts'][$i] = 0;
                            }else{
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter a valid expense amount';
                                $entries_are_valid = FALSE; 
                            }
                        }
                    endif;
                endfor;
            }
        }

        if($entries_are_valid){
            $successful_expense_entry_count = 0;
            $unsuccessful_expense_entry_count = 0;
            if(isset($posts['expense_dates'])){
                $count = count($posts['expense_dates']);
                //print_r($count); die();
                for($i=0;$i<=$count;$i++):
                    if(isset($posts['expense_dates'][$i])&&isset($posts['expense_categories'][$i])&&isset($posts['withdrawal_methods'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])):    
                        $amount = valid_currency($posts['amounts'][$i]);
                        $expense_date = strtotime($posts['expense_dates'][$i]); 
                        $description = isset($posts['expense_descriptions'][$i])?$posts['expense_descriptions'][$i]:'';
                        if($this->transactions->record_expense_withdrawal($this->group->id,$expense_date,$posts['expense_categories'][$i],$posts['withdrawal_methods'][$i],$posts['accounts'][$i],$description,$amount)){
                            $successful_expense_entry_count++;
                        }else{
                            $unsuccessful_expense_entry_count++;
                        }
                    endif;
                endfor;
            }
            if($successful_expense_entry_count){
                $response = array(
                    'status' => 1,
                    'message' => 'success',$successful_expense_entry_count.' expense successfully recorded. ',
                    'refer' => site_url('group/withdrawals/listing')
                );
            }

            /*if($unsuccessful_expense_entry_count){
                $response = array(
                    'status' => 1,
                    'message' => 'success',$successful_expense_entry_count.' expense successfully recorded. ',
                    'refer' => site_url('group/withdrawals/listing')
                );
            }*/
        }else{
            $response = array(
                'status' => 0,
                'validation_errors'=>$error_messages,
                'message' => 'There are some errors on the form. Please review and try again.',
            );
        }
        echo json_encode($response);

    }

    function record_dividend_payments(){
        $data = array();
        $response = array();
        $post = new stdClass();
        $posts = $_POST;
        $entries_are_valid = TRUE;

        if(!empty($posts)){ 
            if(isset($posts['dividend_dates'])){
                $count = count($posts['dividend_dates']);
                for($i=0;$i<=$count;$i++):
                    if(isset($posts['dividend_dates'][$i])&&isset($posts['member_ids'][$i])&&isset($posts['withdrawal_methods'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])):    
                        //Deposit dates
                        if($posts['dividend_dates'][$i]==''){
                            $successes['dividend_dates'][$i] = 0;
                            $errors['dividend_dates'][$i] = 1;
                            $validation_errors['dividend_dates'][$i] = 'Please enter a date';
                            $entries_are_valid = FALSE;
                        }else{
                            $successes['dividend_dates'][$i] = 1;
                            $errors['dividend_dates'][$i] = 0;
                        }

                       /*  if(valid_date($posts['deposit_dates'][$i])){
                            $successes['dividend_dates'][$i] = 1;
                            $errors['dividend_dates'][$i] = 0;
                        }else{
                            $successes['dividend_dates'][$i] = 0;
                            $errors['dividend_dates'][$i] = 1;
                            $validation_errors['dividend_dates'][$i] = 'Please enter a date';
                            $entries_are_valid = FALSE;                               
                        } */
                        //Members
                        if($posts['member_ids'][$i]==''){
                            $successes['member_ids'][$i] = 0;
                            $errors['member_ids'][$i] = 1;
                            $validation_errors['member_ids'][$i] = 'Please select a member';
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($posts['member_ids'][$i])){
                                $successes['member_ids'][$i] = 1;
                                $errors['member_ids'][$i] = 0;
                            }else{
                                $successes['member_ids'][$i] = 0;
                                $errors['member_ids'][$i] = 1;
                                $validation_errors['member_ids'][$i] = 'Please select a valid member';
                                $entries_are_valid = FALSE;
                            }
                        }

                        if($posts['withdrawal_methods'][$i]==''){
                            $successes['withdrawal_methods'][$i] = 0;
                            $errors['withdrawal_methods'][$i] = 1;
                            $validation_errors['withdrawal_methods'][$i] = 'Please select a withdrawal method';
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($posts['withdrawal_methods'][$i])){
                                $successes['withdrawal_methods'][$i] = 1;
                                $errors['withdrawal_methods'][$i] = 0;
                            }else{
                                $successes['withdrawal_methods'][$i] = 0;
                                $errors['withdrawal_methods'][$i] = 1;
                                $validation_errors['withdrawal_methods'][$i] = 'Please select a valid withdrawal method value';
                                $entries_are_valid = FALSE;
                            }
                        }
                         //Accounts
                        if($posts['accounts'][$i]==''){
                            $successes['accounts'][$i] = 0;
                            $errors['accounts'][$i] = 1;
                            $validation_errors['accounts'][$i] = 'Please select an account';
                            $entries_are_valid = FALSE;
                        }else{
                            $successes['accounts'][$i] = 1;
                            $errors['accounts'][$i] = 0;
                        }
                        //amounts
                        if($posts['amounts'][$i]==''){
                            $successes['amounts'][$i] = 0;
                            $errors['amounts'][$i] = 1;
                            $validation_errors['amounts'][$i] = 'Please enter dividend amount';
                            $entries_are_valid = FALSE;
                        }else{
                            if(valid_currency($posts['amounts'][$i])){
                                $successes['amounts'][$i] = 1;
                                $errors['amounts'][$i] = 0;
                            }else{
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $validation_errors['amounts'][$i] = 'Please enter a valid dividend amount';
                                $entries_are_valid = FALSE; 
                            }
                        }
                    endif;
                endfor;
            }
        }

        if($entries_are_valid){
            $successful_dividend_entry_count = 0;
            $unsuccessful_dividend_entry_count = 0;
            if(isset($posts['dividend_dates'])){
                $count = count($posts['dividend_dates']);
                //print_r($count); die();
                for($i=0;$i<=$count;$i++):
                    if(isset($posts['dividend_dates'][$i])&&isset($posts['member_ids'][$i])&&isset($posts['withdrawal_methods'][$i])&&isset($posts['accounts'][$i])&&isset($posts['amounts'][$i])):    
                        $amount = valid_currency($posts['amounts'][$i]);
                        $withdrawal_date = strtotime($posts['dividend_dates'][$i]); 
                        $description = isset($posts['expense_descriptions'][$i])?$posts['expense_descriptions'][$i]:'';
                        if($this->transactions->record_dividend_withdrawal($this->group->id,$withdrawal_date,$posts['member_ids'][$i],$posts['withdrawal_methods'][$i],$posts['accounts'][$i],$description,$amount)){
                            $successful_dividend_entry_count++;
                        }else{
                            $unsuccessful_dividend_entry_count++;
                        }
                    endif;
                endfor;
            }
            if($successful_dividend_entry_count){
                $response = array(
                    'status' => 1,
                    'message' => 'success',$successful_dividend_entry_count.' dividend payments successfully recorded. ',
                    'refer' => site_url('group/withdrawals/listing')
                );
            }

            /*if($unsuccessful_expense_entry_count){
                $response = array(
                    'status' => 1,
                    'message' => 'success',$successful_expense_entry_count.' expense successfully recorded. ',
                    'refer' => site_url('group/withdrawals/listing')
                );
            }*/
        }else{
            $response = array(
                'status' => 0,
                'validation_errors'=>$validation_errors,
                'message' => 'There are some errors on the form. Please review and try again.',
            );
        }
        echo json_encode($response);
    }

    function send_approval_code(){
        $response = array();
        $id = $_POST['id'];
        if($id){
            $withdrawal_approval_request = $this->withdrawals_m->get_group_withdrawal_approval_request($id);
            if($withdrawal_approval_request){
                //Egt code
                $reference_number = random_int(1000, 9999);
                $approval_code = "";
                $generate_otp_success = TRUE;
                // check if the platform to send the otp.
                if(preg_match('/(eazzy)i/',$this->application_settings->application_name)){
                    // customer way to send otp for eazzy.
                    $approval_code = $this->messaging->generate_user_otp($this->user->phone,$reference_number);
                    $generate_otp_success = $approval_code ? 1 : FALSE;
                }else{
                    // for websacco.
                    // $approval_code = $reference_number;
                    if(preg_match('/demo\.websacco\.com/',$_SERVER['HTTP_HOST']) || preg_match('/uat\.websacco\.com/',$_SERVER['HTTP_HOST'])){
                        $approval_code = '1234';
                    }else{
                        $approval_code = $reference_number;
                        // get the member approving.
                        $approving_member = $this->members_m->get_group_member($this->member->id);
                        $generate_otp_success = $this->messaging->generate_user_otp_websacco(
                            $approving_member->phone,
                            $approval_code, 
                            $approving_member->id,
                            $approving_member->user_id, 
                            $approving_member->group_id
                        );
                    }
                }
               
                $input = array(
                    'reference_number' => $reference_number,
                    'approval_code' => $approval_code,
                    'is_approved'=>0,
                    'modified_by' => $this->user->id,
                    'modified_on' => time(),
                );
                if($generate_otp_success){
                    if($this->withdrawals_m->update_withdrawal_approval_request($id,$input)){
                        $response = array(
                            'status' => 1,
                            'message' => 'Success, approval code sent to your registered phone number',
                            //'refer' =>site_url(''),
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not generate approval code.',
                        );
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'Error, we could not generate an approval OTP',
                        //'refer' =>site_url(''),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Approval request not found.',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Approval request not found.',
            );
        }
        echo json_encode($response);
    }

    function verify_approval_code(){
        $response = array();
        $id = $_POST['id'];
        $approval_code = $_POST['approval_code'];
        if($id){
            if($approval_code){
                $withdrawal_approval_request = $this->withdrawals_m->get_group_withdrawal_approval_request($id);

                if($withdrawal_approval_request){
                    //verify
                    $verified = FALSE;

                    if(preg_match('/(eazzy)i/',$this->application_settings->application_name)){
                        $verified = $this->messaging->verify_user_otp($withdrawal_approval_request->reference_number,$approval_code);
                    }else{
                        // confirm the code received and the code saved on the withdrawal request.
                        $verified = $approval_code == $withdrawal_approval_request->approval_code ? TRUE : FALSE;
                    }
                   
                    if($verified){
                        $update = [
                            'is_otp_verified'=>1,
                            'modified_on'=>time(),
                            'modified_by'=>$this->user->id,
                        ];
                        if($this->withdrawals_m->update_withdrawal_approval_request($id,$update)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Success, approval code verified',
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Could not generate approval code.',
                            );
                        }
                    }else{
                        if(preg_match('/(eazzy)i/',$this->application_settings->application_name)){
                            $response = array(
                                'status' => 0,
                                'message' => $this->session->flashdata('error'),
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => "Invalid OTP Code",
                            );
                        }
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Approval code request not found.',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Approval code not submitted.',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Approval request not found.',
            );
        }
        echo json_encode($response);
    }
}