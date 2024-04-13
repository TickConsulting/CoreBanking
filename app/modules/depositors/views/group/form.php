<div class="portlet-body form">
    <?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
        <div class="form-body">
            <div class="form-group">
                <label>Depositor Name<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa  fa-map"></i>
                    </span>
                    <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Depositor Name"');?>
                </div>
            </div>
            <div class="form-group">
                <label>Depositor Phone</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa  fa-phone"></i>
                    </span>
                    <?php echo form_input('phone',$this->input->post('phone')?$this->input->post('phone'):$post->phone,'class="form-control" placeholder="Depositor Phone"');?>
                </div>
            </div>
            <div class="form-group">
                <label>Depositor Email</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa  fa-envelope"></i>
                    </span>
                    <?php echo form_input('email',$this->input->post('email')?$this->input->post('email'):$post->email,'class="form-control" placeholder="Depositor Email"');?>
                </div>
            </div>
            <div class="form-group">
                <label>Depositor Description</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa  fa-map"></i>
                    </span>
                    <?php echo form_textarea('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control" placeholder="Group Expense Category Description"');?>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit"  class="btn blue submit_form_button">
                
                <?php
                    $default_message='Save Changes';
                    $this->languages_m->translate('save_changes',$default_message);
                ?>
            </button>
            <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New"/>
            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                    <?php
                        $default_message='Processing';
                        $this->languages_m->translate('processing',$default_message);
                    ?>
            </button> 
            <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">
                <?php
                    $default_message='Cancel';
                    $this->languages_m->translate('cancel',$default_message);
                ?>
            </button></a>
        </div>
    <?php echo form_close(); ?>
</div>