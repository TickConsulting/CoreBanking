
<?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
    <div class="form-body">

        <div class="form-group">
            <label>Group Billing Cycle</label>
            <?php echo form_dropdown('billing_cycle',array(''=>'Select Group Billing Cycle')+$billing_cycles,$this->input->post('billing_cycle')?:$post->billing_cycle,'class="form-control select2" id="billing_cycle" placeholder="Group Billing Cycle"');?>
        </div>
        <div class="form-group">
            <label>Group Owner</label>
            <?php echo form_dropdown('owner',$group_user_options,$this->input->post('owner')?:$post->owner,'class="form-control select2" id="owner" placeholder="Group Owner"');?>
        </div>
        <div class="form-group">
            <label>Order Group Member List By?</label>
            <?php echo form_dropdown('member_listing_order_by',array(''=> '-- Order Group Members By? --')+$member_listing_order_by_options,$this->input->post('member_listing_order_by')?:$post->member_listing_order_by,'class="form-control select2" id="owner" placeholder="Group Member List Ordering"');?>
        </div>
        <div class="form-group">
            <label>Order From?</label>
            <?php echo form_dropdown('order_members_by',array(''=> '-- Order Group Members from? --')+$order_by_options,$this->input->post('order_members_by')?:$post->order_members_by,'class="form-control select2" id="order_members_by" placeholder="Order Members By"');?>
        </div>
        <div class="form-group">
            <label>Enforce Member Information Privacy</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('enable_member_information_privacy',1,$this->input->post('enable_member_information_privacy')?:$post->enable_member_information_privacy); ?>
                    Enable Member Information Privacy
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Disable Contribution Arrears</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('disable_arrears',1,$this->input->post('disable_arrears')?:$post->disable_arrears); ?>
                    Disable Contribution Arrears
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Disable Ignore Contribution Transfers</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('disable_ignore_contribution_transfers',1,$this->input->post('disable_ignore_contribution_transfers')?:$post->disable_ignore_contribution_transfers); ?>
                    Disable Ignore Contribution Transfers
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Enable Send Email Statements Monthly</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('enable_send_monthly_email_statements',1,$this->input->post('enable_send_monthly_email_statements')?:$post->enable_send_monthly_email_statements,'class="enable_send_monthly_email_statements"'); ?>
                    Enable Send Email Statements Monthly
                </label>
            </div>
        
        </div>
        <div class="form-group statement-send-date">
            <label>Select day of the month to send statements</label>
            <?php echo form_dropdown('statement_send_date',array(''=> '-- Send statement every? --')+$statement_sending_date_options,$this->input->post('statement_send_date')?:$post->statement_send_date,'class="form-control select2" placeholder="Group Member List Ordering"');?>
        </div>

        <div class="form-group">
            <label>Disable Member Directory</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('disable_member_directory',1,$this->input->post('disable_member_directory')?:$post->disable_member_directory); ?>
                    Disable Member Directory
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Enable Bulk Transaction Alerts Reconciliation</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('enable_bulk_transaction_alerts_reconciliation',1,$this->input->post('enable_bulk_transaction_alerts_reconciliation')?:$post->enable_bulk_transaction_alerts_reconciliation); ?>
                    Enable Bulk Transaction Alerts Reconciliation
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Disable Member Edit Profile</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('disable_member_edit_profile',1,$this->input->post('disable_member_edit_profile')?:$post->disable_member_edit_profile); ?>
                    Disable Member Edit Profile
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Enable Absolute Reducing Balance Loan Recalculation</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('enable_absolute_loan_recalculation',1,$this->input->post('enable_absolute_loan_recalculation')?:$post->enable_absolute_loan_recalculation); ?>
                    Enable absolute reducing balance loan recalculation
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Enable Loans (Member loans and External loans)</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('group_offer_loans',1,$this->input->post('group_offer_loans')?:$post->group_offer_loans); ?>
                    Enable option for group to offer loans
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Enable Merging of Transaction Alerts</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('enable_merge_transaction_alerts',1,$this->input->post('enable_merge_transaction_alerts')?:$post->enable_merge_transaction_alerts); ?>
                    Enable Merging of Transaction Alerts
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn blue submit_form_button">
                        <?php
                            $default_message='Save Changes';
                            $this->languages_m->translate('save_changes',$default_message);
                        ?>
            </button>
    		<button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                        <?php
                            $default_message='Processing';
                            $this->languages_m->translate('processing',$default_message);
                        ?>
            </button> 
            <a href="<?php echo $this->agent->referrer()?>">
                <button type="button" class="btn default">
                    <?php
                        $default_message='Cancel';
                        $this->languages_m->translate('cancel',$default_message);
                    ?>
                </button>
            </a>
        </div>
    </div>
<?php echo form_close(); ?>
<script>
$(document).ready(function(){
    $('#country_id').change(function(){
        $('#currency_id').select2('val',$(this).val());
    });
    $('.enable_send_monthly_email_statements').on('change',function(){
        var enable_send_monthly_email_statements =  $('.enable_send_monthly_email_statements:checkbox:checked').val();
        if(enable_send_monthly_email_statements){
            $('.statement-send-date').slideDown();
        }else{
            $('.statement-send-date').slideUp();
        }
    });
    <?php
        if($post->enable_send_monthly_email_statements){
    ?>
        $('.statement-send-date').slideDown();
    <?php
    }else{
    ?>
        $('.statement-send-date').slideUp();
    <?php
    }
    ?>
    });
    
    
</script>