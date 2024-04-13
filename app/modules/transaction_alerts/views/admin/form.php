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
                            <label>Forwarder Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-bank"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Forwarder Name"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Forwarder URL<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-link"></i>
                                </span>
                                <?php echo form_input('url',$this->input->post('url')?$this->input->post('url'):$post->url,'class="form-control" placeholder="Forwarder URL"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Forwarder Type<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-institution"></i>
                                </span>
                                <?php echo form_dropdown('type',array(''=>'Select Forwarder Type')+$forwarder_type_options,$this->input->post('type')?$this->input->post('type'):(isset($type)?$type:$post->type),' id="type" class="form-control select2" placeholder=""');?>
                            </div>
                        </div>
                        <div id="bank_account_number_options">
                            <div class="form-group">
                                <label>Bank<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-institution"></i>
                                    </span>
                                    <?php echo form_dropdown('bank_id',array(''=>'Select Bank')+$bank_options,$this->input->post('bank_id')?$this->input->post('bank_id'):(isset($bank_id)?$bank_id:$post->bank_id),' class="form-control select2" placeholder=""');?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Account Name<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-institution"></i>
                                    </span>
                                    <?php echo form_input('account_name',$this->input->post('account_name')?$this->input->post('account_name'):$post->account_name,'class="form-control" placeholder="Bank Account Name"');?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Account Number<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-institution"></i>
                                    </span>
                                    <?php echo form_input('account_number',$this->input->post('account_number')?$this->input->post('account_number'):$post->account_number,'class="form-control" placeholder="Bank Account Number"');?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <button type="button" class="btn default">Cancel</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#type').change(function(){
            if($(this).val()==1){
                $('#bank_account_number_options').slideUp();
            }else if($(this).val()==2){
                $('#bank_account_number_options').slideDown();
            }else{
                $('#bank_account_number_options').slideUp();
            }
        });
        <?php if($this->input->post('type')==1||$post->type==1): ?>
            $('#bank_account_number_options').slideUp();
        <?php elseif($this->input->post('type')==2||$post->type==2): ?>
            $('#bank_account_number_options').slideDown();
        <?php else: ?>
            $('#bank_account_number_options').slideUp();
        <?php endif; ?>
    });
</script>