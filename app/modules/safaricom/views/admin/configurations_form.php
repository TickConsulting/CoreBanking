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
                <?php echo form_open(current_url(),'class="form_submit" role="form"'); ?>
                    <div class="form-body">
                        <div class="form-group">
                            <label>Shortcode<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-file-text-o"></i>
                                </span>
                                <?php echo form_input('shortcode',$post->shortcode?:"",'class="form-control" placeholder="shortcode"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Username<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-file-text-o"></i>
                                </span>
                                <?php echo form_input('username',$post->username?:"",'class="form-control" placeholder="Username"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <?php echo form_input('password',$post->password?:"",'class="form-control" placeholder="Password"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Access Token<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <?php echo form_input('access_token',$post->access_token?:"",'class="form-control" placeholder="access_token"');?>
                            </div>
                        </div>
                        <!--
                        <div class="form-group">
                            <label>API Key<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <?php echo form_input('api_key',$post->api_key?:"",'class="form-control" placeholder="API Key"');?>
                            </div>
                        </div>
                    -->
                    </div>
                    <?php echo form_hidden('id',$id); ?>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>