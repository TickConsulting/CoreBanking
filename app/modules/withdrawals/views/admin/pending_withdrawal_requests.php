<style type="text/css">
    .table td, .table th {
        font-size: 11px;
    }
</style>
<div class="row">
	<div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
               	<div class="caption">
                    <?php echo $this->admin_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form">

                <?php if(!empty($posts)){ ?>
                    <?php echo form_open('admin/withdrawals/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Withdrawal Requests </p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>

                        <div class=''>
                            <table class="table table-striped table-bordered table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th width='2%'>
                                            #
                                        </th>
                                        <th>
                                            Approved On
                                        </th>
                                        <th width="30%">
                                            Group/ Approvals
                                        </th>
                                        <th>
                                            Withdrawal For / Recipient
                                        </th>
                                        <th class="text-right">
                                            Amount
                                        </th>
                                        <th class="text-left">
                                            Status
                                        </th>
                                    </tr>

                                </thead>
                                <tbody>
                                    <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                        <tr>
                                            <td><?php echo $i+1;?></td>
                                            <td>
                                                <?php echo timestamp_to_datetime($post->approved_on); ?>
                                            </td>
                                            <td>
                                                <p><?php echo $group_options[$post->group_id]; ?></p>
                                                <p><?php
                                                    $approval_requests = $withdrawal_approval_requests[$post->id];
                                                    $key = 0; 
                                                    foreach ($approval_requests as $approval_request) {
                                                        $is_approved = $approval_request['is_approved'];
                                                        $is_declined = $approval_request['is_declined'];
                                                        $member_id = $approval_request['member_id'];
                                                        $member = $this->members_m->get_group_member($member_id,$post->group_id);
                                                        $status = '';
                                                        if($is_declined){
                                                            $status = '- Declined';
                                                        }elseif ($is_approved) {
                                                            $status = '- Approved';
                                                        }
                                                        echo '<p>'.($key+1).'. '.$member->first_name.' '.$member->last_name.'('.$member->phone.') '.$status.'</p>';
                                                        ++$key;
                                                    }
                                                ?></p>
                                            </td>
                                            <td>
                                                <p><?php echo $withdrawal_request_transaction_names[$post->withdrawal_for]; ?></p>

                                                    <?php if(preg_match('/member/', $post->recipient_id)){
                                                        $recipient = $this->members_m->get_group_member(str_replace('member-', '', $post->recipient_id),$post->group_id);
                                                        if($recipient){
                                                            $recipient_description = 'Mobile Money Account - '.$recipient->first_name.' '.$recipient->last_name.'('.valid_phone($recipient->phone).') <span class="m-badge m-badge--success m-badge--wide">Member</span>';
                                                        }else{
                                                            $recipient_description = '<span class="m-badge m-badge--metal m-badge--wide">Deleted Member</span>';
                                                        }
                                                    }else if(preg_match('/bank/', $post->recipient_id)){
                                                        $recipient = $this->recipients_m->get(str_replace('bank-', '', $post->recipient_id));
                                                        $recipient_description = 'Bank Account - '.$recipient->account_name.'('.$recipient->account_number.')';
                                                    }else if(preg_match('/paybill/', $post->recipient_id)){
                                                        $recipient = $this->recipients_m->get(str_replace('paybill-', '', $post->recipient_id));
                                                        $recipient_description = 'Paybill Account - '.$recipient->name.'('.$recipient->paybill_number.' Account:'.$recipient->account_number.')';
                                                    }else if(preg_match('/mobile/', $post->recipient_id)){
                                                        $recipient = $this->recipients_m->get(str_replace('mobile-', '', $post->recipient_id));
                                                        $recipient_description = 'Mobile Money Account - '.$recipient->name.'('.valid_phone($recipient->phone_number).')';
                                                    }else{
                                                        $recipient = $this->recipients_m->get($post->recipient_id);
                                                        if($recipient){
                                                            if($recipient->type == 1){ //mobile money
                                                                $recipient_description = 'Mobile Money Account - '.$recipient->name.'('.valid_phone($recipient->phone_number).')';
                                                            }else if($recipient->type == 2){ //paybill
                                                                $recipient_description = 'Paybill Account - '.$recipient->name.'('.$recipient->paybill_number.' Account:'.$recipient->account_number.')';
                                                            }else if($recipient->type == 3){ //bank account
                                                                $recipient_description = 'Bank Account - '.$recipient->account_name.'('.$recipient->account_number.')';
                                                            }
                                                        }else{
                                                            $recipient = $this->members_m->get_group_member($post->recipient_member_id,$post->group_id);
                                                            $recipient_description = $recipient?$recipient->first_name.' '.$recipient->last_name.'('.valid_phone($post->recipient_phone_number).')':'';
                                                        }
                                                    }
                                                    echo '<p>'.$recipient_description.'</p>';
                                                ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo number_to_currency($post->amount); ?>
                                            </td>

                                            <td>
                                                <?php if($post->status==1){ ?>
                                                    <span class="label label-info" style="margin-bottom:5px;"> Pending Disbursement </span>
                                                    <br/><br/>
                                                    <a href="<?php echo site_url('admin/withdrawals/cancel_disbursement/'.$post->id)?>" class="btn btn-xs btn-danger prompt_confirmation_message_link" data-title="Enter the reason to cancel this withdrawal request."> <i class="fa fa-eject"></i> 
                                                        Cancel Disbursement
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php $i++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="clearfix"></div>
                        <div class="row col-md-12">
                        <?php 
                            if( ! empty($pagination['links'])): 
                            echo $pagination['links']; 
                            endif; 
                        ?>  
                        </div>
                        <div class="clearfix"></div>

                        <!--
                            <?php if($posts):?>
                                <button class="btn btn-sm btn-success confirmation_bulk_action" name='btnAction' value='bulk_activate' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye'></i> Bulk Activate</button>
                                <button class="btn btn-sm btn-default confirmation_bulk_action" name='btnAction' value='bulk_hide' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye-slash'></i> Bulk Hide</button>
                            <?php endif;?>
                        -->
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No withdrawal requests to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>