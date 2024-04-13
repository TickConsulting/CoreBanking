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
                            <label>Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-desktop"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Setup Task Name"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Parent Task</label>
                            <?php echo form_dropdown('parent_id',array(''=>'--Select Parent Task--')+$setup_task_options,$this->input->post('parent_id')?$this->input->post('parent_id'):$post->parent_id,'class="form-control select2" placeholder="Select Parent Setup Task"');?>
                        </div>

                        <div class="form-group">
                            <label>Slug<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-diamond"></i>
                                </span>
                                <?php echo form_input('slug',$this->input->post('slug')?$this->input->post('slug'):$post->slug,'class="form-control" placeholder="Setup Task Slug" readonly="readonly"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Icon<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-file-picture-o"></i>
                                </span>
                                <?php echo form_input('icon',$this->input->post('icon')?$this->input->post('icon'):$post->icon,'class="form-control" placeholder="Setup Task Icon" ');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <div class=" col-md-12 input-group">
                                <?php 
                                    echo form_textarea(
                                        array(
                                            'name'  =>  'description',
                                            'value' =>  $this->input->post('description')?:$post->description,
                                            'class' =>  'form-control autosizeme',
                                            'rows'  =>  '3',
                                            'maxlength' =>  '200',
                                            'minlength' =>  '50',
                                            'id'    =>  "maxlength_alloptions"
                                        )
                                    );
                                ?>
                                <span class="help-block"> Maximum 200 Characters </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Call To Action Name:<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-cube"></i>
                                </span>
                                <?php echo form_input('call_to_action_name',$this->input->post('call_to_action_name')?$this->input->post('call_to_action_name'):$post->call_to_action_name,'class="form-control" placeholder="Name for Call to Action"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Call To Action Link:<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-link"></i>
                                </span>
                                <?php echo form_input('call_to_action_link',$this->input->post('call_to_action_link')?$this->input->post('call_to_action_link'):$post->call_to_action_link,'class="form-control" placeholder="Name for Call to Link"');?>
                            </div>
                        </div>
                        <?php echo form_hidden('id',$id); ?>

                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New"/>
						<button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <button type="button" class="btn default">Cancel</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
