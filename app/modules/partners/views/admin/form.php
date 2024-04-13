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
                <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit" role="form"'); ?>
                    <div class="form-body">
                        <div class="form-group">
                            <label>Partner's Official Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                <i class="fa fa-pencil"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Partner\'s Official Name"'); ?>
                            </div>
                        </div>
                        <div class="form-group hidden">
                            <label>Partner Slug<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                <i class="fa fa-bars"></i>
                                </span>
                                <?php echo form_input('slug',$this->input->post('slug')?$this->input->post('slug'):$post->slug,'class="form-control" placeholder="Template Slug" readonly="readonly"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Partner's Users<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                <i class="fa fa-users"></i>
                                </span>
                                <?php echo form_dropdown('user_ids[]',$selected_user_options,$this->input->post('user_ids')?$this->input->post('user_ids'):$selected_user_ids,'class="form-control user_search" multiple="multiple"'); ?>
                            </div>
                        </div>
                        <?php echo form_hidden('id',$id);?>
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
<script>
    $(document).ready(function(){

        
    });
</script>