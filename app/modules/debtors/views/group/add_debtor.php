<?php echo form_open_multipart($this->uri->uri_string(), ' role="form" class="form_submit" '); ?>
    <div class="form-body">
        <div class="form-group name">
            <label>Debtor Name <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                <?php echo form_input('name',$this->input->post('name')?:$post->name,'  class="form-control name" placeholder="Debtor Name"'); ?>
            </div>
            <span class="help-block"> Eg. Chamasoft Team </span>
        </div>
        <div class="form-group phone">
            <label>Debtor Phone <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                <?php echo form_input('phone',$this->input->post('phone')?:$post->phone,'  class="form-control phone" placeholder="Debtor Phone"'); ?>
            </div>
            <span class="help-block"> Eg. +254 725*** </span>
        </div>
        <div class="form-group email">
            <label>Debtor Email</label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                </span>
                <?php echo form_input('email',$this->input->post('email')?:$post->email,'  class="form-control email" placeholder="Debtor Email"'); ?>
            </div>
        </div>
        <div class="form-group description">
            <label>
                
                <?php
                    $default_message='Description';
                    $this->languages_m->translate('description',$default_message);
                ?>
            </label>
            <div class="input-group">
                <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
                </span>
                <?php
                    $textarea = array(
                        'name'=>'description',
                        'id'=>'',
                        'value'=> $this->input->post('description')?:'',
                        'cols'=>30,
                        'rows'=>6,
                        'maxlength'=>200,
                        'class'=>'form-control maxlength',
                        'placeholder'=>'Debtor description'
                    ); 
                    echo form_textarea($textarea); 
                ?>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit"  class="btn blue submit_form_button"></i>
                <?php
                    $default_message='Save Changes';
                    $this->languages_m->translate('save_changes',$default_message);
                ?>
        </button>
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
        Cancel</button></a>
    </div>
<?php echo form_close();?>