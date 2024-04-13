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
                            <label>Theme Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-bank"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Theme Name"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Slug<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-link"></i>
                                </span>
                                <?php echo form_input('slug',$this->input->post('slug')?$this->input->post('slug'):$post->slug,' readonly="readonly" class="form-control" placeholder="Slug"');?>
                            </div>
                        </div>
                        <fieldset>
                            <legend>Background Colors</legend>
                            <div class="form-group">
                                <label>Primary Color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-paint-brush"></i>
                                    </span>
                                    <?php echo form_input('primary_background_color',$this->input->post('primary_background_color')?$this->input->post('primary_background_color'):$post->primary_background_color?$post->primary_background_color:'#000000','class="form-control colorpicker" id="hue-demo" ');?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Secondary Color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-paint-brush"></i>
                                    </span>
                                    <?php echo form_input('secondary_background_color',$this->input->post('secondary_background_color')?$this->input->post('secondary_background_color'):$post->secondary_background_color?$post->secondary_background_color:'#000000','class="form-control colorpicker" placeholder="Secondary Background  Color"');?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label>Tertiary Color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-paint-brush"></i>
                                    </span>
                                    <?php echo form_input('tertiary_background_color',$this->input->post('tertiary_background_color')?$this->input->post('tertiary_background_color'):$post->tertiary_background_color?$post->tertiary_background_color:'#000000','class="form-control colorpicker" placeholder="Tertiary Background Color"');?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Quaternary Color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-paint-brush"></i>
                                    </span>
                                    <?php echo form_input('quaternary_background_color',$this->input->post('quaternary_background_color')?$this->input->post('quaternary_background_color'):$post->quaternary_background_color?$post->quaternary_background_color:'#000000','class="form-control colorpicker" placeholder="Quaternary Background Color"');?>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Text Colors</legend>
                            <div class="form-group">
                                <label>Primary Color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-paint-brush"></i>
                                    </span>
                                    <?php echo form_input('primary_text_color',$this->input->post('primary_text_color')?$this->input->post('primary_text_color'):$post->primary_text_color?$post->primary_text_color:'#000000','class="form-control colorpicker" id="hue-demo" ');?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Secondary Color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-paint-brush"></i>
                                    </span>
                                    <?php echo form_input('secondary_text_color',$this->input->post('secondary_text_color')?$this->input->post('secondary_text_color'):$post->secondary_text_color?$post->secondary_text_color:'#000000','class="form-control colorpicker" placeholder="Secondary Text Color"');?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label>Tertiary Color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-paint-brush"></i>
                                    </span>
                                    <?php echo form_input('tertiary_text_color',$this->input->post('tertiary_text_color')?$this->input->post('tertiary_text_color'):$post->tertiary_text_color?$post->tertiary_text_color:'#000000','class="form-control colorpicker" placeholder="Tertiary Text Color"');?>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Border Colors</legend>
                            <div class="form-group">
                                <label>Primary Color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-paint-brush"></i>
                                    </span>
                                    <?php echo form_input('primary_border_color',$this->input->post('primary_border_color')?$this->input->post('primary_border_color'):$post->primary_border_color?$post->primary_border_color:'#000000','class="form-control colorpicker" id="hue-demo" ');?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Secondary Color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-paint-brush"></i>
                                    </span>
                                    <?php echo form_input('secondary_border_color',$this->input->post('secondary_border_color')?$this->input->post('secondary_border_color'):$post->secondary_border_color?$post->secondary_border_color:'#000000','class="form-control colorpicker" placeholder="Secondary Text Color"');?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label>Tertiary Color</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-paint-brush"></i>
                                    </span>
                                    <?php echo form_input('tertiary_border_color',$this->input->post('tertiary_border_color')?$this->input->post('tertiary_border_color'):$post->tertiary_border_color?$post->tertiary_border_color:'#000000','class="form-control colorpicker" placeholder="Tertiary Text Color"');?>
                                </div>
                            </div>
                        </fieldset>

                        <div class="form-group">
                            <label for="logo" class="">Theme Logo</label>
                            <div class="input-group">
                                <input type="file" name="logo">
                                <p class="help-block"> Upload Theme Logo Here </p>
                            </div>
                            <?php if(is_file(FCPATH.'uploads/files/'.$post->logo)){ ?>
                                <img src='<?php echo base_url('uploads/files/'.$post->logo); ?>' height="100px" />
                            <?php } ?>
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