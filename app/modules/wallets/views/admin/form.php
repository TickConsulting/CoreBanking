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
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Channel Name<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-shield"></i>
                                    </span>
                                    <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Name"');?>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Channel Number<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-shield"></i>
                                    </span>
                                    <?php echo form_input('channel',$this->input->post('channel')?$this->input->post('channel'):$post->channel,'class="form-control" placeholder="Channel Number"');?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Channel Logo<span class="required">*</span></label><br/>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="max-width: 100px;">
                                    <img src="<?php echo $post->logo?site_url($path.'/'.$post->logo):site_url('templates/admin_themes/admin/img/no_image.png'); ?>" alt="" /> </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 50px;"> </div>
                                <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select Icon </span>
                                        <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="logo"> 
                                        </span>
                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Operating Countries<span class="required">*</span></label>
                            <div class="input-group">
                                <?php echo form_dropdown('country_ids[]',$countries,$this->input->post('country_ids')?$this->input->post('country_ids'):$post->country_ids,'class="form-control select2" multiple="multiple" placeholder="Name"');?>
                            </div>
                        </div>
                        <?php echo form_hidden('id',$id); ?>

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
