<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_invoice"'); ?> 
    <fieldset class="m-3 ">
        <div class="form-group m-form__group row pt-0 m--padding-10">
            <div class="col-sm-6 m-form__group-sub pt-0 m--padding-10">
                <label><?php echo translate('Invoice Type');?><span class="required">*</span></label>
                <?php echo form_dropdown('type',array(''=>translate('Select Invoice Type'))+translate($this->invoice_type_options),$this->input->post('type')?:$post->type?:'',' class="form-control m-input m-select2 type" id="type"');?>
            </div>
            <div class="col-sm-6 m-form__group-sub pt-0 m--padding-10" id='contribution'>
                <label><?php echo translate('Contribution');?><span class="required">*</span></label>
                <?php echo form_dropdown('contribution_id',array(''=>translate('Select Contribution'))+translate($group_contribution_options),$this->input->post('contribution_id')?:$post->contribution_id?:'',' class="form-control m-input m-select2 contribution_id" id="contribution_id"');?>
            </div>
            <div class="col-sm-6 m-form__group-sub pt-0 m--padding-10" id='fine_category'>
                <label><?php echo translate('Fine Category');?><span class="required">*</span></label>
                <?php echo form_dropdown('fine_category_id',array(''=>translate('Select Fine Category'))+translate($group_fine_category_options),$this->input->post('fine_category_id')?:$post->fine_category_id?:'',' class="form-control m-input m-select2 fine_category_id" id="fine_category_id"');?>
            </div>

            <div class="col-sm-12 m-form__group-sub pt-0 m--padding-10">
                <label><?php echo translate('Send Invoices To');?><span class="required">*</span></label>
                <?php echo form_dropdown('send_to',translate($send_to_options),$this->input->post('send_to')?:$post->send_to?:'',' class="form-control m-input m-select2 send_to " id = "send_to" ');?>
            </div>

            <div class="col-sm-12 m-form__group-sub pt-0 m--padding-10" id="member_ids">
                <label><?php echo translate('Select Members to Invoice');?><span class="required">*</span></label>
                <?php echo form_dropdown('member_id[]',translate($this->active_group_member_options),$this->input->post('member_id')?:$post->member_id?:'',' class="form-control m-input m-select2 member_id" multiple="multiple" ');?>
            </div>
            <div class="col-lg-6 col-sm-12 m-form__group-sub pt-0 m--padding-10">                            
                <label><?php echo translate('Amount Payable');?><span class="required">*</span></label>
                <?php echo form_input('amount_payable',$this->input->post('amount_payable'),'  class="form-control currency m-input--air" id="amount_payable" autocomplete="off"  placeholder="'.translate('Amount Payable').'"'); ?>
            </div> 
            <div class="col-md-6 m-form__group-sub pt-0 m--padding-10">
                <label><?php echo translate('Invoices Date');?><span class="required">*</span></label>
                <div class="input-group ">
                    <?php echo form_input('invoice_date',$this->input->post('invoice_date')?timestamp_to_datepicker(strtotime($this->input->post('invoice_date'))):timestamp_to_datepicker(time()),'class="form-control m-input datepicker" readonly');?>
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-calendar-check-o"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-12 m-form__group-sub pt-0 m--padding-10">
                <label><?php echo translate('Contribution Date/Due Date');?><span class="required">*</span></label>
                <div class="input-group  " data-date="<?php echo $this->input->post('due_date')?timestamp_to_datepicker(strtotime($this->input->post('due_date'))):timestamp_to_datepicker(time());?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                    <?php echo form_input('due_date',$this->input->post('due_date')?timestamp_to_datepicker(strtotime($this->input->post('due_date'))):timestamp_to_datepicker(time()),'class="form-control m-input datepicker" readonly');?>
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-calendar-check-o"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-12 m-form__group-sub pt-0 m--padding-10">
                <label><?php echo translate('Description');?></label>
                <?php echo form_textarea('description',$this->input->post('description')?$this->input->post('description'):$post->description,' maxlength="200" class="form-control maxlength" placeholder=""'); ?>
            </div>
            <div class="col-md-12 m-form__group-sub pt-0 m--padding-10">
                <label><?php echo translate('Invoice Notifications');?></label>
                <div class="m-radio-inline">
                    <label class="m-radio m-radio--solid m-radio--brand">
                        <?php echo form_checkbox('send_sms_notification',1,$this->input->post('send_sms_notification')?$this->input->post('send_sms_notification'):$post->send_sms_notification,' id="send_sms_notification" '); ?> 
                        <?php echo translate('Send SMS Notification');?>
                        <span></span>
                    </label>

                    <label class="m-radio m-radio--solid m-radio--brand">
                        <?php echo form_checkbox('send_email_notification',1,$this->input->post('send_email_notification')?$this->input->post('send_email_notification'):$post->send_email_notification,' id="send_email_notification" '); ?> 
                        <?php echo translate('Send Email Notification');?>
                        <span></span>
                    </label>
                </div>
            </div> 
            <div id='sms_template' class="form-group d-none" >
                <label>SMS Template<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                    </span>
                    <textarea name="sms_template" cols="40" rows="5" id="" maxlength="200" class="form-control maxlength" placeholder="SMS Template" >
                        Hi [FIRST_NAME], you have been invoiced [GROUP_CURRENCY] [INVOICED_AMOUNT], for your [CONTRIBUTION_NAME], new balance is [GROUP_CURRENCY] [CONTRIBUTION_BALANCE].</textarea>
                </div>
                <span class="help-block"> Required placeholders: 
                    [FIRST_NAME],[GROUP_CURRENCY],[INVOICED_AMOUNT],[CONTRIBUTION_NAME],[GROUP_CURRENCY],[CONTRIBUTION_BALANCE]            </span>
            </div>                               
            <div class="col-md-12 m-form__group-sub pt-0 m--padding-10">
                <span class="float-lg-right float-md-right float-sm-right float-xl-right">
                    <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_invoice_button" type="button">
                        <?php echo translate('Save Changes & Submit');?>
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_invoice_button">
                        <?php echo translate('Cancel');?>
                    </button> 
                </span>
            </div>
        </div>
    </fieldset>
<?php echo form_Close() ?>

<script>
$(document).ready(function(){
    $('#contribution').slideUp();
    $(".m-select2").select2({
        placeholder:{
            id: '-1',
            text: "--Select option--",
        }, 
    });
    SnippetCreateInvoice.init();
    $('#type').on('change',function(){        
        if($(this).val()==1||$(this).val()==2||$(this).val()==5||$(this).val()==6){

            $('#contribution').slideDown();
            $('#fine_category').slideUp();
        }else if($(this).val()==3){
            $('#contribution').slideUp();
            $('#fine_category').slideDown();
        }else{
            $('#contribution,#fine_category').slideUp();
        }
    });
    /*<?php if(($this->input->post('type')==1||$post->type==1)||($this->input->post('type')==2||$post->type==2)||($this->input->post('type')==5||$post->type==5)||($this->input->post('type')==6||$post->type==6)){ ?>
        $('#contribution').slideDown();
        $('#fine_category').slideUp();
    <?php }else if($this->input->post('type')==3||$post->type==3){ ?>
        $('#contribution').slideUp();
        $('#fine_category').slideDown();
    <?php }else{ ?>
        $('#contribution,#fine_category').slideUp();
    <?php } ?>*/

    $('#send_to').on('change',function(){
        if($(this).val()==2){
            $('#member_ids').slideDown();
        }else{
            $('#member_ids').slideUp();
        }
    });
    <?php if($this->input->post('send_to')==2||$post->send_to==2){ ?>
        $('#member_ids').slideDown();
    <?php }else{ ?>
        $('#member_ids').slideUp();
    <?php } ?>

    $('#send_sms_notification').on('change',function(){
        if($('#type').val()==1){
            if($(this).prop('checked')){
                $('#sms_template').slideDown();
            }else{
                $('#sms_template').slideUp();
            }
        }
    });

    <?php 
    if($this->input->post('type')==1||$post->type==1){ 
        if($this->input->post('send_sms_notification')==1||$post->send_sms_notification==1){ 
    ?>
        $('#sms_template').slideDown();
    <?php }else{ ?>
        $('#sms_template').slideUp();
    <?php }
}
     ?>
});
</script>