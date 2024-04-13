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
                            <label>Country Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-map"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Country Name"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Country Code<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-sort-alpha-asc"></i>
                                </span>
                                <?php echo form_input('code',$this->input->post('code')?$this->input->post('code'):$post->code,'class="form-control" placeholder="Country Code"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Currency<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </span>
                                <?php echo form_input('currency',$this->input->post('currency')?$this->input->post('currency'):$post->currency,'class="form-control" placeholder="Currency Name"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Currency Code<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-sort-alpha-asc"></i>
                                </span>
                                <?php echo form_input('currency_code',$this->input->post('currency_code')?$this->input->post('currency_code'):$post->currency_code,'class="form-control" placeholder="Currency Code"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Country Calling Code<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-phone"></i>
                                </span>
                                <?php echo form_input('calling_code',$this->input->post('calling_code')?$this->input->post('calling_code'):$post->calling_code,'class="form-control" placeholder="Calling Code"');?>
                            </div>
                            <span class="help-block">eg 254</span>
                        </div>
                        <?php echo form_hidden('id',$id); ?>
                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Country"/>
						<button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <button type="button" class="btn default">Cancel</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
