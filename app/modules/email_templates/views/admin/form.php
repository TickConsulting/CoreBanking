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
                <?php echo form_open_multipart(current_url(), ' class="form_submit" role="form"'); ?>
                    <div class="form-body">
                        <div class="form-group">
                            <label>Template Title<span class="required">*</span></label>
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
                                <?php echo form_input('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control" placeholder="description"'); ?>
                            </div>
                        </div> 

                        <div class="form-body">
                            <div class="form-group last">
                                <label class="">Template Content<span class="required">*</span></label>
                                <div class="">
                                <?php
                                    echo form_textarea(
                                        array(
                                            'name' => 'content',
                                            'value' => $post->content,
                                            'class' => 'summernote_1',
                                        )
                                        );
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php echo form_hidden('id',$id);?>
 <div id="summernote"><p>Hello Summernote</p></div>
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