<!---Compose sms-->
<div id="compose_sms_panel">
    <div class="m-form__section m-form__section--first">
        <div id="compose_sms_setting">
            <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="add_compose_message"'); ?>            
                <div>
                    <div class="form-group m-form__group row pt-0 m--padding-10 ">
                        <div class="col-sm-12 m-form__group-sub m-input--air">
                            <label><?php echo translate('Send Message To ');?><span class="required">*</span></label>
                            <?php echo form_dropdown('send_to',$send_to_list,$this->input->post('send_to')?:'','class="form-control send_to m-select2" placeholder="Send To"'); ?>
                        </div>
                    </div>

                    <div class="form-group m-form__group row pt-0 m--padding-10 member_input">
                        <div class="col-sm-12 m-form__group-sub m-input--air">
                            <label><?php echo translate('Select Member');?><span class="required">*</span></label>
                            <?php echo form_dropdown('member_id[]',translate($members),$this->input->post('member_id')?:'','class=" form-control m-select2" multiple="multiple" data-placeholder="Select..."'); ?>
                        </div>
                    </div>

                    <div class="form-group m-form__group row pt-0 m--padding-10 ">
                        <div class="col-sm-12 m-form__group-sub m-input--air">
                            <label><?php echo translate('Message');?><span class="required">*</span></label>
                            <?php
                                $textarea = array(
                                    'name'=>'message',
                                    'id'=>'',
                                    'value'=> $this->input->post('message')?:'',
                                    'cols'=>40,
                                    'rows'=>8,
                                    'maxlength'=>160,
                                    'class'=>'form-control maxlength',
                                    'placeholder'=>'Compose SMS to send'
                                ); 
                                echo form_textarea($textarea); 
                            ?>
                        </div>
                    </div>


                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-12 col-md-12">
                            <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="compose_sms_button" type="button">
                                    <?php echo translate('Save Changes');?>
                                </button>
                                &nbsp;&nbsp;
                                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_compose_sms_button">
                                    <?php echo translate('Cancel');?>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>
<!------>

<script type="text/javascript">
    $(document).ready(function(){
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        var send_to = $('select[name="send_to"]').val();        
        if(send_to){
            $('select[name="send_to"]').trigger('change');
        }

        $(document).on('change','select[name="send_to"]',function(){
            var send_to = $(this).val();
            if(send_to==1){
                $('.member_input').slideUp();
            }else if(send_to==2){
                $('.member_input').slideDown();
            }else{
                $('.member_input').hide();
            }
            $('.m-select2').select2();
        });
        SnippetComposeSms.init();
    });

    

</script>

