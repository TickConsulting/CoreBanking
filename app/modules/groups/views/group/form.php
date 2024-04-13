
<?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
    <div class="form-body">
        <div class="form-group">
            <label>Group Logo </label><br/>
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="max-width: 150px;">
                    <img src="<?php echo is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('templates/admin_themes/admin/img/no_image.png'); ?>" alt="" /> </div>
                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 50px;"> </div>
                <div>
                    <span class="btn default btn-file">
                        <span class="fileinput-new"> Select Logo </span>
                        <span class="fileinput-exists"> Change </span>
                        <input type="file" name="logo"> </span>
                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>
                    <?php
                        $default_message='Group Name';
                        $this->languages_m->translate('group_name',$default_message);
                    ?>

                <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa  fa-file-text-o"></i>
                </span>
                <?php echo form_input('name',$post->name?$post->name:$this->input->post('name'),'class="form-control" placeholder="Group Name"');?>
            </div>
        </div>
        <div class="form-group">
            <label>Group Size<span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-users"></i>
                </span>
                <?php echo form_input('size',$post->size?$post->size:$this->input->post('size'),'class="form-control" placeholder="Group Size"');?>
            </div>
        </div>

        <div class="form-group">
            <label>Country<span class="required">*</span></label>
            <?php echo form_dropdown('country_id',array(''=>'Select Country')+$country_options,$post->country_id?$post->country_id:$this->input->post('country_id'),'class="form-control select2" id="country_id" placeholder="Select Country"');?>
        </div>

        <div class="form-group">
            <label>Currency<span class="required">*</span></label>
            <?php echo form_dropdown('currency_id',array(''=>'Select Currency')+$currency_options,$post->currency_id?$post->currency_id:$this->input->post('currency_id'),'class="form-control select2" id="currency_id" placeholder="Select Currency"');?>
        </div>

        <div class="form-group">
            <label>Group Phone Number</label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                <?php echo form_input('phone',$post->phone?$post->phone:$this->input->post('phone'),'class="form-control" placeholder="Group Phone Number"');?>
            </div>
        </div>

        <div class="form-group">
            <label>Group Email Address</label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                </span>
                <?php echo form_input('email',$post->email?$post->email:$this->input->post('email'),'class="form-control" placeholder="Group Email Address"');?>
            </div>
        </div>


        <div class="form-group">
            <label>Group Address</label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                </span>
                <?php echo form_textarea('address',$post->address?$post->address:$this->input->post('address'),'class="form-control" placeholder=""');?>
            </div>
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
        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">
                    <?php
                        $default_message='Cancel';
                        $this->languages_m->translate('cancel',$default_message);
                    ?>

        </button></a>
    </div>
<?php echo form_close(); ?>
<script>
$(document).ready(function(){
    $('#country_id').change(function(){
        $('#currency_id').select2('val',$(this).val());
    });
});
</script>