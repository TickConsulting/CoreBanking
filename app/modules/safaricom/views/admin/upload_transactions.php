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
                <div class="form-body">
                    <?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
                        <div class="form-group">
                            <label for="postal_code_list" class="">M-Pesa Transaction List File</label>
                            <div class="input-group">
                                <input type="file" name="postal_code_list">
                                <p class="help-block"> Choose your file list file here </p>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name='import' value='1' class="btn blue submit_form_button">Import Transaction File</button>
                            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                            <button type="button" class="btn default">Cancel</button>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>     