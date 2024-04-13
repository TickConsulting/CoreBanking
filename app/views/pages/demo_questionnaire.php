<div class="hero-wrap inner_page short">
    <div class="row m-0">
    <div class="container">
        <div class="">
            <div class="overlay"></div>
            <div class="circle-bg"></div>
            <div class="circle-bg-2"></div>
            <div class="circle-bg-3"></div>
            <div class="container-fluid pt-2">
                <div class="row no-gutters d-flex slider-text align-items-center justify-content-center" data-scrollax-parent="true">
                    <div class="col-md-12 ftco-animate text-left" data-scrollax=" properties: { translateY: '70%' }">
                        <!-- <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }"><span class="mr-2"><a href="<?php echo site_url(''); ?>"><?php echo translate('Home'); ?></a></span> <span><?php echo translate('Features'); ?></span></p> -->
                        <h1 class="mb-3 bread magic_hdr" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }"><?php echo $this->application_settings->application_name; ?> <?php echo translate('Demo Registration'); ?></h1>
                        <p data-scrollax="properties: { translateY: '30%', opacity: 1.6 }" style="margin-top:-20px;"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<!-- <link rel="stylesheet" href="<?php echo site_url(); ?>templates/admin_themes/groups/vendors/base/vendors.bundle.css"> -->
<style>
    label {
        line-height: 1!important;
    }
</style>
<section class="ftco-section px-4 pt-5 pb-5" style="z-index:1;">
    <div class="container px-4 ftco-animate fadeInUp ftco-animated" style="margin-top:-100px;">
        <div class="row d-md-flex bg-white p-4" style="border-radius:4px;box-shadow:0px 0px 20px 0px rgba(0, 0, 0, 0.1);">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12 d-flex align-items-center pb-5">
                        <div class="feature-cont">
                        <div id="err_disp"></div> 
                        <p>
                            After you fill out this order request, we will contact you to go over details and availability and how further we can help you. If you would like faster service and direct information on our current offerings and pricing please contact us at Contact us at <a href="tel:+254202133865" style="font-size:inherit;">+254 202 133 865</a> or <a href="mailto:sales@websacco.com" style="font-size:inherit;">sales@websacco.com</a>
                        </p>
                        <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="demo_form"'); ?>
                            <div class="alert alert-success alert_success" style="display: none;">
                                <p class="data_success">
                                    
                                </p>
                                <span class="close"></span>
                            </div>
                            <div class="m-form__group form-group mt-5">
                            <label style="color: var(--primary_text); margin-left: -36px;margin-bottom:20px;"><strong>Kindly pick one which suits you most</strong> <sup style="font-size:12px;color:#ff0000;">*</sup></label>
                            <div class="m-radio-list">
                                <label class="m-radio m-radio--state-success">
                                    <?php echo form_radio('question_1',1,'',""); ?>
                                    <?php echo translate('I am in a Sacco');?>
                                    <span></span>
                                </label>

                                <label class="m-radio m-radio--state-success">
                                    <?php echo form_radio('question_1',2,'',""); ?>
                                    <?php echo translate('I am a Sacco official/owner');?>
                                    <span></span>
                                </label>

                                <label class="m-radio m-radio--state-success">
                                    <?php echo form_radio('question_1',3,'',""); ?>
                                    <?php echo translate('I am into Digital Lending');?>
                                    <span></span>
                                </label>

                                <label class="m-radio m-radio--state-success">
                                    <?php echo form_radio('question_1',4,'',""); ?>
                                    <?php echo translate('I am in a Microfinance');?>
                                    <span></span>
                                </label>

                                <label class="m-radio m-radio--state-success">
                                    <?php echo form_radio('question_1',5,'',""); ?>
                                    <?php echo translate(' I am a Microfinance owner');?>
                                    <span></span>
                                </label>

                                <label class="m-radio m-radio--state-success">
                                    <?php echo form_radio('question_1',6,'',""); ?>
                                    <?php echo translate('Other');?>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="m-form__group form-group mt-5">
                            <label style="color: var(--primary_text); margin-left: -36px;margin-bottom:20px;"><strong>What product are you interested in?</strong> <sup style="font-size:12px;color:#ff0000;">*</sup></label>
                            <div class="m-radio-list">
                                <label class="m-radio m-radio--state-success">
                                    <?php echo form_radio('question_2',1,'',""); ?>
                                    <?php echo translate('Websacco OnPremise Platform');?>
                                    <span></span>
                                </label>

                                <label class="m-radio m-radio--state-success">
                                    <?php echo form_radio('question_2',2,'',""); ?>
                                    <?php echo translate('Websacco Cloud Platform');?>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group m-form__group mt-5">
                            <label style="color: var(--primary_text); margin-left: -36px;">
                                Your name
                            </label>
                            <div class="m-input-icon m-input-icon--left">
                                <input type="text" name="full_name" class="form-control m-input" placeholder="Full name">
                                <span class="m-input-icon__icon m-input-icon__icon--left">
                                    <span>
                                        <i class="la la-user"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <div class="col-6">
                                <label style="color: var(--primary_text); margin-left: -36px;">
                                    Your email
                                </label>
                                <div class="m-input-icon m-input-icon--left">
                                    <input type="email" name="email" class="form-control m-input" placeholder="Email address" >
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-envelope-o"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label style="color: var(--primary_text); margin-left: -36px;">
                                    Your phone
                                </label>
                                <div class="m-input-icon m-input-icon--left">
                                    <input type="text" name="phone" class="form-control m-input" placeholder="Phone number" >
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-mobile-phone"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group m-form__group mt-5">
                            <label style="color: var(--primary_text); margin-left: -36px;">
                                Preferred contact method
                            </label>
                            <?php
                                $enable_activate_invoicing = TRUE;
                                $disable_activate_invoicing = TRUE;
                            ?>
                            <div class="m-checkbox-list">
                                <label class="m-checkbox m-checkbox--state-success">
                                    <?php echo form_checkbox('enable_phone_contact',1,$enable_activate_invoicing,""); ?>
                                    <?php echo translate('Phone');?>
                                    <span></span>
                                </label>

                                <label class="m-checkbox m-checkbox--state-success">
                                    <?php echo form_checkbox('enable_email_contact',1,$disable_activate_invoicing,""); ?>
                                    <?php echo translate('Email');?>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                            <div class="form-group m-form__group">
                                <button type="button" class="btn btn-success btn-sm px-4 py-3 mt-5 continue_to_demo" id="continue_to_demo">
                                    Continue to Demo
                                </button>
                            </div>

                        <?php echo form_close() ?>                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    
    var base_url = window.location.origin;

    $(document).on('click','#continue_to_demo',function(){
        $('#continue_to_demo').prop('disabled', true);
        $('#continue_to_demo').html('<i class="la la-spinner la-spin mr-1"></i> Processing . . .');
        var form = $('#demo_form');
        //console.log(form.serialize())
        $.ajax({
            url: base_url+"/ajax/create_demo_users",
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded',
            data: form.serialize(),
            success: function(response) {
                if(isJson(response)){
                    var data = $.parseJSON(response);
                    if(data.status == '1'){
                        $('#continue_to_demo').prop('disabled', false);
                        $('#continue_to_demo').html('Continue to Demo');
                        window.location = data.refer;
                    }else{
                        $('#continue_to_demo').prop('disabled', false);
                        $('#continue_to_demo').html(' Continue to Demo');
                        var val_errors = '';
                        var i = 1;
                        var obj = data.validation_errors;
                        for (let key in obj) {
                            val_errors = val_errors + i + '. ' + obj[key] + '<br>'; i++;
                        }
                        show_errors(data.message, val_errors);
                    }
                }else{
                    $('#continue_to_demo').prop('disabled', false);
                    $('#continue_to_demo').html('Continue to Demo');
                }
            },
            error: function (data, textStatus, jqXHR) {
                $('.data_error').html(data.message).show();
                $('.alert_danger').slideDown();
            }

        });
    });

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    function show_errors(message, errors){
        $('#err_disp').html(`
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                <strong>` + message + `</strong><br><small>` + errors + `</small>
            </div>
        `);
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    

    function RemoveDangerClass(form=''){
        var dangerclasses = $('.m-form.m-form--state input, .m-form.m-form--state select ,  .m-form.m-form--state textarea');
        $.each(dangerclasses,function(){
            if(($(this).parent()).hasClass('has-danger')){
                ($(this).parent()).removeClass('has-danger');
                ($(this).parent()).find('.form-control-feedback').remove();
                ($(this).parent()).find('.m-form__help').slideDown();
            }
        });
        $('.m-form.m-form--state').find(".alert").html('').slideUp();
        $('.m-form.m-form--state').find(".cancel_form").attr("disabled","disabled");
        if(form){
            form.find(".alert").html('').slideUp();
            form.find(".cancel_form").attr("disabled","disabled");
        }
    }


</script>
