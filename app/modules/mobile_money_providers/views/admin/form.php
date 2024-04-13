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
                            <label>Mobile Money Provider Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-mobile-phone"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Mobile Money Provider Name"');?>
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
                        <div class="form-group">
                            <label>Select Country 
                                <span class="required">*</span></label>
                            <div class="input-group col-md-12">

                                <?php echo form_dropdown('country_id',$this->country_options,$this->input->post('country_id')?:$post->country_id,' class="form-control m-input select2 member" ');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Primary Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('primary_color',$this->input->post('primary_color')?$this->input->post('primary_color'):$post->primary_color?$post->primary_color:'#000000','class="form-control colorpicker" id="hue-demo" ');?>
                            </div>
                        </div>

                         <div class="form-group">
                            <label>Secondary Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('secondary_color',$this->input->post('secondary_color')?$this->input->post('secondary_color'):$post->secondary_color?$post->secondary_color:'#000000','class="form-control colorpicker" placeholder="Secondary Color"');?>
                            </div>
                        </div> 

                        <div class="form-group">
                            <label>Tertiary Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('tertiary_color',$this->input->post('tertiary_color')?$this->input->post('tertiary_color'):$post->tertiary_color?$post->tertiary_color:'#000000','class="form-control colorpicker" placeholder="Tertiary Color"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Text Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('text_color',$this->input->post('text_color')?$this->input->post('text_color'):$post->text_color?$post->text_color:'#000000','class="form-control colorpicker" placeholder="Text Color"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="logo" class="">Mobile Money Provider Logo</label>
                            <div class="input-group">
                                <input type="file" name="logo">
                                <p class="help-block"> Upload Mobile Money Provider Logo Here </p>
                            </div>
                            <?php if(is_file(FCPATH.'uploads/files/'.$post->logo)){ ?>
                                <img src='<?php echo site_url('uploads/files/'.$post->logo); ?>' height="100px" />
                            <?php } ?>
                        </div>

                        <div class="form-group">
                            <label>Chamasoft Partner</label>
                            <div class="input-group">
                                <label class="checkbox-inline"><?php echo form_checkbox('partner',1,$this->input->post('partner')?$this->input->post('partner'):$post->partner,'class="form-control" placeholder="Chamasoft Partner"');?> Mobile_money_provider is a Chamasoft Partner</label>
                            </div>
                        </div>
                        <?php echo form_hidden('id',$id); ?>

                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Mobile_money_provider"/>
						<button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <button type="button" class="btn default">Cancel</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
