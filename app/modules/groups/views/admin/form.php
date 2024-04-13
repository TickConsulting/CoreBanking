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

                <?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
                    <div class="form-body">
                        <div class="form-group">
                            <label>Group Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-file-text-o"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?:$post->name,'class="form-control" placeholder="Group Name"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Group Slug<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-file-text"></i>
                                </span>
                                <?php echo form_input('slug',$this->input->post('slug')?:$post->slug,'class="form-control" placeholder="Group Slug"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Group Size<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-users"></i>
                                </span>
                                <?php echo form_input('size',$this->input->post('size')?:$post->size,'class="form-control" placeholder="Group Size"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Country<span class="required">*</span></label>
                            <?php echo form_dropdown('country_id',array(''=>'Select Country')+$country_options,$this->input->post('country_id')?:$post->country_id?:$this->default_country->id,'class="form-control select2" id="country_id" placeholder="Select Country"');?>
                        </div>

                        <div class="form-group">
                            <label>Currency<span class="required">*</span></label>
                            <?php echo form_dropdown('currency_id',array(''=>'Select Currency')+$currency_options,$this->input->post('currency_id')?:$post->currency_id?:$this->default_country->id,'class="form-control select2" id="currency_id" placeholder="Select Currency"');?>
                        </div>

                        <div class="form-group">
                            <label>Remaining Trial Days</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-battery-3"></i>
                                </span>
                                <?php echo form_input('trial_days',$this->input->post('trial_days')?:$post->trial_days,'class="form-control" placeholder="Group Remaining Trial Days"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Billing Package</label>
                            <?php echo form_dropdown('billing_package_id',array(''=>'Select package')+$billing_packages,$this->input->post('billing_package_id')?:$post->billing_package_id,'class="form-control select2" placeholder="Select Package"');?>
                        </div>

                        <div class="form-group">
                            <label>Billing Account Number</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-mobile"></i>
                                </span>
                                <?php echo form_input('account_number',$this->input->post('account_number')?:$post->account_number,'class="form-control" placeholder="Group Billing Account Number"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Group SMS Balance</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-mobile"></i>
                                </span>
                                <?php echo form_input('sms_balance',$this->input->post('sms_balance')?:$post->sms_balance,'class="form-control" placeholder="Group SMS Balance"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Lock Access</label>
                            <?php echo form_dropdown('lock_access',array(''=>'Account Unlocked',1=>'Account Locked'),$this->input->post('lock_access')?:$post->lock_access,'class="form-control select2" placeholder="Select Lock Access"');?>
                        </div>

                        <div class="locked_code">

                            <div class="form-group">
                                <label>Group Activation Code</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    <?php echo form_input('activation_code',$this->input->post('activation_code')?:$post->activation_code,'class="form-control" placeholder="Group Activation Code"');?>
                                </div>
                            </div>
                        </div>

                        <div class="group_status">
                            <div class="form-group">
                                <label>Billing Date</label>
                                <div class="input-group date date-picker" data-date="<?php echo $this->input->post('billing_date')?timestamp_to_datepicker(strtotime($this->input->post('billing_date'))):($post->billing_date?timestamp_to_datepicker($post->billing_date):timestamp_to_datepicker(time()));?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <?php echo form_input('billing_date',$this->input->post('billing_date')?timestamp_to_datepicker(strtotime($this->input->post('billing_date'))):timestamp_to_datepicker($post->billing_date),'class="form-control" id="billing_date" placeholder="Group Billing Date" readonly="readonly"');?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Group Status</label>
                                <?php echo form_dropdown('status',$group_status,$this->input->post('status')?:$post->status,'class="form-control select2" id="status" placeholder="Group Status"');?>
                            </div>

                        </div>

                        <div class="form-group">
                            <label>Billing Cycle</label>
                            <?php echo form_dropdown('billing_cycle',array(''=>'Select Group Billing Cycle')+$billing_cycles,$this->input->post('billing_cycle')?:$post->billing_cycle,'class="form-control select2" id="billing_cycle" placeholder="Group Billing Cycle"');?>
                        </div>

                        <div class="form-group">
                            <label>Enable Online Banking</label>
                            <?php echo form_dropdown('online_banking_enabled',array(''=>'Select option','0'=>'Disable','1'=>'Enable'),$this->input->post('online_banking_enabled')?:$post->online_banking_enabled,'class="form-control select2" id="online_banking_enabled" placeholder="Enable online banking"');?>
                        </div>
                        <div class="form-group">
                            <label>Notify Members on Withdrawals</label>
                            <?php echo form_dropdown('notify_members_on_withdrawals',array(''=>'Select option','0'=>'Disable','1'=>'Enable'),$this->input->post('notify_members_on_withdrawals')?:$post->notify_members_on_withdrawals,'class="form-control select2" id="online_banking_enabled" placeholder="Notify Members on Withdrawals"');?>
                        </div>

                        <div class="form-group">
                            <label>Select if group is sacco</label>
                            <?php echo form_dropdown('is_sacco',array(''=>'Select option','0'=>'Disable','1'=>'Enable'),$this->input->post('is_sacco')?:$post->is_sacco,'class="form-control select2" id="is_sacco" placeholder="Select if sacco "');?>
                        </div>
                        <div class="form-group">
                            <label>Enable Edit Member Profile</label>
                            <?php echo form_dropdown('enable_edit_member_profile',array(''=>'Select option','0'=>'Disable','1'=>'Enable'),$this->input->post('enable_edit_member_profile')?:$post->enable_edit_member_profile,'class="form-control select2" id="enable_edit_member_profile" placeholder="Enable Edit Member Profile"');?>
                        </div>
                        <div class="form-group">
                            <label>Enable Compose SMS</label>
                            <?php echo form_dropdown('enable_compose_sms',array(''=>'Select option','0'=>'Disable','1'=>'Enable'),$this->input->post('enable_compose_sms')?:$post->enable_compose_sms,'class="form-control select2" id="enable_compose_sms" placeholder="Enable Compose SMS"');?>
                        </div>
                        <div class="form-group">
                            <label>Enable Compose Email</label>
                            <?php echo form_dropdown('enable_compose_email',array(''=>'Select option','0'=>'Disable','1'=>'Enable'),$this->input->post('enable_compose_email')?:$post->enable_compose_email,'class="form-control select2" id="enable_compose_email" placeholder="Enable Compose Email"');?>
                        </div>
                        <div class="form-group">
                            <label>Enable Add Members Manually</label>
                            <?php echo form_dropdown('enable_add_members_manually',array(''=>'Select option','0'=>'Disable','1'=>'Enable'),$this->input->post('enable_add_members_manually')?:$post->enable_add_members_manually,'class="form-control select2" id="enable_add_members_manually" placeholder="Enable Add Members Manually"');?>
                        </div>
                        <div class="form-group">
                            <label>Enable Import Members Manually</label>
                            <?php echo form_dropdown('enable_import_members_manually',array(''=>'Select option','0'=>'Disable','1'=>'Enable'),$this->input->post('enable_import_members_manually')?:$post->enable_import_members_manually,'class="form-control select2" id="enable_import_members_manually" placeholder="Enable Import Members Manually"');?>
                        </div>
                        <?php echo form_hidden('id',$id);?>

                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn blue submit_form_button">Save Changes</button>
                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                    </div>
                <?php echo form_close(); ?>


            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#country_id').change(function(){
        $('#currency_id').select2('val',$(this).val());
    });

    $('select[name="lock_access"]').on('change',function(){
        var lock_access = $(this).val();
        if(lock_access==1)
        {
            $('.locked_code').slideDown();
            $('.group_status').slideUp();
        }
        else
        {
            $('.locked_code').slideUp();
            $('.group_status').slideDown();
        }
    });

    var lock_access = $('select[name="lock_access"]').val();
    if(lock_access==1)
        {
            $('.locked_code').slideDown();
            $('.group_status').slideUp();
        }
        else
        {
            $('.locked_code').slideUp();
            $('.group_status').slideDown();
        }
});
</script>
