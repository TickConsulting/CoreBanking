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
                            <label>Sacco<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-bank"></i>
                                </span>
                                <?php echo form_dropdown('sacco_id',array(''=>'Select Sacco')+$sacco_options,$this->input->post('sacco_id')?$this->input->post('sacco_id'):(isset($sacco_id)?$sacco_id:$post->sacco_id),' class="form-control select2" placeholder=""');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Sacco Branch Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-bank"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Branch Name"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Branch Code<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-sort-numeric-asc"></i>
                                </span>
                                <?php echo form_input('code',$this->input->post('code')?$this->input->post('code'):$post->code,'class="form-control" placeholder="Branch Code"');?>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                            <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Sacco Branch"/>
                            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                            <button type="button" class="btn default">Cancel</button>
                        </div>
                    </div>
                    
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
