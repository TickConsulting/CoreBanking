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
                            <label>Menu Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-file-text-o"></i>
                                </span>
                                <?php echo form_input('name',$post->name?$post->name:'','class="form-control" placeholder="Menu Name"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Menu URL<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-link"></i>
                                </span>
                                <?php echo form_input('url',$post->url?$post->url:'','class="form-control" placeholder="Menu URL"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Menu Icon<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-empire"></i>
                                </span>
                                <?php echo form_input('icon',$post->icon?$post->icon:'','class="form-control" placeholder="Menu Icon"');?>
                            </div>
                        </div>

      					<div class="form-group">
                            <label>Parent Menu</label>
                            <?php echo form_dropdown('parent_id',array(''=>'--Select Parent Menu --')+$menus,$post->parent_id?$post->parent_id:'','class="form-control select2" placeholder="Select Menu Parent"');?>
                        </div>

                        <div class="form-group">
                            <label>Tile Size</label>
                            <?php echo form_dropdown('size',$tile_sizes,$post->size?$post->size:'','class="form-control select2" placeholder="Select Tile Size"');?>
                        </div>

                        <div class="form-group">
                            <label>Tile Color<span class="required">*</span></label>
                            <?php echo form_dropdown('color',$tile_colors,$post->color?$post->color:'','class="form-control select2" placeholder="Select Tile Color"');?>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>

                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Menu"/>

						<button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>