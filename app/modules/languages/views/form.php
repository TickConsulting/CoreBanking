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
<?php echo form_open(current_url(),'class="form_submit" role="form"');?>
    <div class="form-body">

        <div class="form-group">
            <label>Language<span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa  fa-file-text-o"></i>
                </span>
                <?php echo form_input('name',$post->name?$post->name:'','class="form-control" placeholder="Language"');?>
            </div>
        </div>
                <?php echo form_hidden('id',$id,'class="form-control" placeholder="Language"');?>


        <div class="form-group">
            <label>Country<span class="required">*</span></label>
             <div class="input-group">
            <span class="input-group-addon">
                    <i class="fa fa-globe"></i>
                </span>
            <?php echo form_dropdown('country_id',array(''=>'Select Country')+$country_options,$post->country_id?$post->country_id:$this->input->post('country_id'),'class="form-control select2" id="country_id" placeholder="Select Country"');?>
        </div>
    </div>


        <div class="form-group">
            <label>Short Code<span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-language"></i>
                </span>
                <?php echo form_input('short_code',$post->short_code?$post->short_code:'','class="form-control" placeholder="Short Code"');?>
            </div>
        </div>


    </div>
    <div class="form-actions">
        <button type="submit" class="btn blue submit_form_button">Save</button>
        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
        <a href="<?php echo $this->agent->referrer()?>">
            <button type="button" class="btn default">Cancel</button></a>
    </div>
<?php echo form_close(); ?>

            </div>
        </div>
    </div>
</div>

