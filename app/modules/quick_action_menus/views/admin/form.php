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
                            <label>User Menu Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-file-text-o"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Menu Name"');?>
                            </div>
                        </div>

						<div class="form-group">
							<label>User Menu URL<span class="required">*</span></label>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-link"></i>
								</span>
								<?php echo form_input('url',$this->input->post('url')?$this->input->post('url'):$post->url,'class="form-control" placeholder="Menu URL"'); ?>
							</div>
						</div>	
						<div class="form-group">
							<label>User Menu Icon<span class="required">*</span></label>
							<div class="input-group">
								<span class="input-group-addon">
								<i class="fa fa-empire"></i>
								</span>
								<?php echo form_input('icon',$this->input->post('icon')?$this->input->post('icon'):$post->icon,'class="form-control" placeholder="Menu Icon"'); ?>
							</div>
						</div>
						

						<div class="form-group">
                            <label>Parent Menu</label>
                            <?php echo form_dropdown('parent_id',array(''=>'--Select Parent Menu --')+$side_bar_menu_options,$this->input->post('parent_id')?$this->input->post('parent_id'):$post->parent_id,'class="form-control select2" placeholder="Select Parent Menu" id="parent_id"');?>
                        </div>

					</div>
					<div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>

                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Menu"/>

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
	$('#position').change(function(){
        $.post('<?php echo site_url('admin/quick_action_menus/ajax_get_options/'); ?>', {'position': $(this).val(),parent_id: $('#parent_id').val()},function(data){
        	$('#parent_id_content').html(data);
        	$('#parent_id').select2({
                placeholder: "Select an option",
               	allowClear: true
           	});
        });
	});
});
</script>