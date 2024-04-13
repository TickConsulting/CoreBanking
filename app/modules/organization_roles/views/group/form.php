
<div class="portlet-body form">
    <?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
        <div class="form-body">
            <div class="form-group">
                <label>
                        <?php
                            $default_message='Oranization Role Name';
                            $this->languages_m->translate('oranization_role_name',$default_message);
                        ?>
                    <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa  fa-map"></i>
                    </span>
                    <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Oranization Role Name"');?>
                </div>
            </div>
            <div class="form-group">
                <label>
                    <?php
                        $default_message='Oranization Role Description';
                        $this->languages_m->translate('oranization_role_description',$default_message);
                    ?>

                </label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa  fa-map"></i>
                    </span>
                    <?php echo form_textarea('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control" placeholder="Oranization Role Description"');?>
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
            <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Oranization Role"/>
			<button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                        <?php
                            $default_message='Processing';
                            $this->languages_m->translate('processing',$default_message);
                        ?>
                    </button> 
            <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
        </div>
    <?php echo form_close(); ?>
</div>