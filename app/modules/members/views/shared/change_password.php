<link rel="stylesheet" href="<?php echo site_url('templates/admin_themes/admin/intl-tel-input/css/intlTelInput.min.css');?>">
<div id="">                     
    <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="change_password_line"'); ?>
        <div id="general_user_details"> 
            <div class="col-sm-12 m-form__group-sub m-input--air">
                <label><?php echo translate('Current Password');?><span class="required">*</span></label>
                <?php echo form_password('old_password',$this->input->post('old_password')?$this->input->post('old_password'):'','class="form-control old_password m-input--air" placeholder="Current Password"'); ?>                
            </div>

            <div class="col-sm-12 m-form__group-sub m-input--air mt-2">
                <label><?php echo translate('New Password');?><span class="required">*</span></label> 
                <?php echo form_password('new_password',$this->input->post('new_password')?$this->input->post('new_password'):'','class="form-control new_password m-input--air" id="cust_new_pass" placeholder="New Password"'); ?>                    
            </div>

            <div class="col-sm-12 m-form__group-sub m-input--air mt-2">
                <label><?php echo translate('Confirm Password');?><span class="required">*</span></label> 
                <?php echo form_password('conf_password',$this->input->post('conf_password')?$this->input->post('conf_password'):'','class="form-control cust_new_pass_conf m-input--air" id="cust_new_pass_conf" placeholder="Confirm Password"'); ?>                  
            </div>
        </div>
        <div class="m-form__actions m-form__actions">                            
            <div class="row">
                <div class="col-md-12">
                    <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="change_password_form" type="button">
                            Change Password                                
                        </button>
                        &nbsp;&nbsp;
                        <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_edit_members_form">
                            Cancel                              
                        </button>
                    </span>
                </div>
            </div>
        </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">    
    $(document).ready(function(){        
        SnippetChangePasswordLine.init();
    });

    var SnippetChangePasswordLine = function(){  
        $("#change_password_line");
        var t = function (redirect) {
            $(document).on('click',".btn#change_password_form",function (t) {
                t.preventDefault();
                var e = $(this),
                    a = $("#change_password_line");                    
                    RemoveDangerClass();
                a.validate({
                    rules: {
                        old_password:{
                            required: true,
                            minlength: 8  
                        },
                        new_password: {
                            required: true,
                            minlength: 8
                        },
                        conf_password: {
                            required: true,
                            equalTo: "#cust_new_pass"
                        }
                    },
                    messages: { 
                        old_password:{
                            minlength: "Your new password is too short"
                        },                      
                        password: {
                            minlength: "Your new password is too short"
                        },
                        confirm_password: {
                            equalTo: "Your passwords do not match"
                        }
                    }
                })
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                if(valid_form(a)==false){
                    mApp.unblock(a);
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger",error_message)
                    }, 2e2)
                }else{
                    a.find(".alert").html('').slideUp();
                    var calling_codes = [];
                    $('.selected-dial-code').each(function(key,value){
                        value  = $(this).html();
                        calling_codes.push(value);
                    });
                    (
                    e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),
                    $.post({
                        url: `<?php echo site_url('ajax/users/change_password') ?>`, 
                        data: encryptdata(passphrase,a.serializeArray()),
                        success: function (t, i, n, r) {
                            if(isJson(t)){
                                response = $.parseJSON(t);
                                if(response.status == '1'){
                                    setTimeout(function () {
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                            a.find(".alert").html('').slideUp();
                                            mApp.unblock(a);
                                            Toastr.show("Success",response.message,'success');
                                            if(redirect){                                        
                                                if(response.hasOwnProperty('refer')){
                                                    window.location = response.refer;
                                                }
                                            }else{
                                                $('#cancel_add_members_form').trigger('click');
                                                load_members();
                                            }                                        
                                    }, 2e3)
                                }else if(response.status == '202'){
                                    Toastr.show("Session Expired",response.message,'error');
                                    window.location.href = response.refer;
                                }else{
                                    var message = response.message;
                                    var validation_errors = '';
                                    var fine_validation_errors = '';
                                    if(response.hasOwnProperty('validation_errors')){
                                        validation_errors = response.validation_errors;
                                    }
                                    if(response.hasOwnProperty('fine_validation_errors')){
                                        fine_validation_errors = response.fine_validation_errors;
                                    }
                                    setTimeout(function () {
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", message);
                                        if(validation_errors){
                                            $.each(validation_errors, function( key, value ) {
                                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                                $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            });
                                        }
                                        if(fine_validation_errors){
                                            $.each(fine_validation_errors, function( key, value ) {
                                                if(value){
                                                    $.each(value,function(keyval, valueval){
                                                        var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                        $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                        $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    });
                                                }
                                            });
                                        }
                                        mApp.unblock(a);
                                        mUtil.scrollTop();
                                    }, 2e3)

                                }
                            }else{
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", "Could not complete processing the request at the moment.")
                                }, 2e3)
                            }
                        },
                        error: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        },
                        always: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        }
                    }));
                }
                
            })
        };
        return {
            init: function (redirect = true) {
                t(redirect)
            }
        }

    }();
    
</script>

