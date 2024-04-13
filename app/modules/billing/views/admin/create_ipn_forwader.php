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
                            <label>Forwarder Title<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-bank"></i>
                                </span>
                                <?php echo form_input('title',$this->input->post('title')?:$post->title,'class="form-control" placeholder="Forwarder Title"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Forwarder Equity IPN Endpoint<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-link"></i>
                                </span>
                                <?php echo form_input('equity_ipn_end_point',$this->input->post('equity_ipn_end_point')?:$post->equity_ipn_end_point,'class="form-control" placeholder="Equity IPN Endpoint"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Account Validation Endpoint<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-link"></i>
                                </span>
                                <?php echo form_input('account_validation_end_point',$this->input->post('account_validation_end_point')?:$post->account_validation_end_point,'class="form-control" placeholder="Account Validation Endpoint"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Mpesa Validation Endpoint<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-link"></i>
                                </span>
                                <?php echo form_input('mpesa_validation_end_point',$this->input->post('mpesa_validation_end_point')?:$post->mpesa_validation_end_point,'class="form-control" placeholder="Mpesa Validation Endpoint"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Mpesa Confirmation Endpoint<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-link"></i>
                                </span>
                                <?php echo form_input('mpesa_confirmation_end_point',$this->input->post('mpesa_confirmation_end_point')?:$post->mpesa_confirmation_end_point,'class="form-control" placeholder="Mpesa Confirmation Endpoint"');?>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Send and Create New Forwarder"/>
                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <button type="button" class="btn default">Cancel</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>