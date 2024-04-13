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
                            <label>SMS Template Title<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                <i class="fa fa-bars"></i>
                                </span>
                                <?php echo form_input('title',$this->input->post('title')?$this->input->post('title'):$post->title,'class="form-control" placeholder="Template Title"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Slug<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                <i class="fa fa-bars"></i>
                                </span>
                                <?php echo form_input('slug',$this->input->post('slug')?$this->input->post('slug'):$post->slug,'class="form-control" placeholder="Template Slug" readonly="readonly"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                <i class="fa fa-list-ul"></i>
                                </span>
                                <?php echo form_input('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control" placeholder="Description"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Language<span class="required">*</span></label>
                            <div class="input-group col-md-12">
                                <span class="input-group-addon">
                                <i class="fa fa-list-ul"></i>
                                </span>
                                <?php echo form_dropdown('language_id',array(''=>'--Select Language--') + $languages,$this->input->post('language_id')?:$post->language_id,'class="form-control select2" placeholder="Language"'); ?>
                            </div>
                        </div> 
                        
        
                        <div class="form-group">
                            <label>SMS Message Template</label>
                            <div class=" col-md-12 input-group">
                                <?php echo form_textarea('sms_template',( $this->input->post('sms_template') ? $this->input->post('sms_template') : ($post->sms_template ? $post->sms_template : $sms_template)),' id="maxlength_alloptions" class="form-control autosizeme " rows="3" maxlength="300" '); ?>
                                <span class="help-block"> Maximum 300 Characters </span>
                            </div>
                        </div>


                        <?php echo form_hidden('id',$id);?>

                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>

                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Template"/>


                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
