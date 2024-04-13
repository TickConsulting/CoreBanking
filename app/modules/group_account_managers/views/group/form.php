<?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state" role="form" id="edit_group_account_managers_form"'); ?>
    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-sm-4 m-form__group-sub">
            <label><?php echo translate('First Name');?><span class="required">*</span></label>
            <?php echo form_input('first_name',$this->input->post('first_name')?$this->input->post('first_name'):$post->first_name,'class="form-control first_name m-input--air" placeholder="First Name"'); ?>
        </div>

        <div class="col-sm-4 m-form__group-sub m-input--air">
            <label><?php echo translate('Middle Name');?> <span class="required">*</span></label>
            <?php echo form_input('middle_name',$this->input->post('middle_name')?$this->input->post('middle_name'):$post->middle_name,'class="form-control middle_name m-input--air" placeholder="Middle Name"'); ?>
        </div>

        <div class="col-sm-4 m-form__group-sub m-input--air">
            <label><?php echo translate('Last Name');?> <span class="required">*</span></label>
            <?php echo form_input('last_name',$this->input->post('last_name')?$this->input->post('last_name'):$post->last_name,'class="form-control last_name m-input--air" placeholder="Last Name"'); ?>
        </div>

    </div>
    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-sm-4 m-form__group-sub">
            <label><?php echo translate('Phone Number');?><span class="required">*</span></label>
            <?php echo form_input('phone',$this->input->post('phone')?$this->input->post('phone'):$post->phone,'class="form-control phone m-input--air" placeholder="Phone Number"'); ?>
        </div>

        <div class="col-sm-4 m-form__group-sub m-input--air">
            <label><?php echo translate('Email Address');?></label>
            <?php echo form_input('email',$this->input->post('email')?$this->input->post('email'):$post->middle_name,'class="form-control email m-input--air" placeholder="Email Address"'); ?>
        </div>

        <div class="col-sm-4 m-form__group-sub m-input--air">
            <label><?php echo translate('Last Name');?></label>
            <?php echo form_input('id_number',$this->input->post('id_number')?$this->input->post('id_number'):$post->id_number,'class="form-control email m-input--air"  m-input--air" placeholder="ID Number"');?>
        </div>
    </div>
     <?php echo form_hidden('user_id',$post->user_id); ?>
     <?php echo form_hidden('id',$post->id); ?>

    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-lg-12 col-md-12">
            <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="edit_group_manager_button" type="button">
                    <?php echo translate('Save Changes');?>
                </button>
                &nbsp;&nbsp;
                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_edit_group_manager_button">
                    <?php echo translate('Cancel');?>
                </button> 
            </span>
        </div>
    </div>

<?php echo form_close() ?>

<script type="text/javascript">
    $(document).ready(function(){
        SnippetEditGroupAccountManagers.init();
    });
</script>



