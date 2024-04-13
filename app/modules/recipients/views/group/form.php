<div class="portlet-body form">
    <?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
        <div class="form-body">
            <div class="form-group">
                <label>Recipient Name<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa  fa-map"></i>
                    </span>
                    <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Recipient Name"');?>
                </div>
            </div>
            <div class="form-group">
                <label>Recipient Phone Number<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa  fa-phone"></i>
                    </span>
                    <?php echo form_input('phone_number',$this->input->post('phone_number')?$this->input->post('phone_number'):$post->phone_number,'class="form-control" placeholder="Recipient Phone Number"');?>
                </div>
            </div>
            <div class="form-group">
                <label>Recipient Description</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa  fa-map"></i>
                    </span>
                    <?php echo form_textarea('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control" placeholder="Recipient Description"');?>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
            <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create Another Recipient"/>
            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
            <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
        </div>
    <?php echo form_close(); ?>
</div>