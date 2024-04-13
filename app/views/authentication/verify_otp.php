<!--Navbar -->
<nav class="mb-1 navbar navbar-expand-lg navbar-light pt-1">
	<div class="container">
		<a class="navbar-brand font-bold" href="<?php echo site_url('') ?>"><img src="<?php echo $this->application_settings?site_url('uploads/logos/'.$this->application_settings->logo):base_url('/templates/admin_themes/groups/img/').'logo_contrast.png'; ?>" draggable="false" height="60px" alt="Logo"></a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent-4">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item pl-3">
					<div class="float-right mr-2 m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="hover" aria-expanded="true">
                        <div class="checkin_prof_snap m-dropdown__toggle">
                            <span class="m-topbar__username m--padding-right-5">
                                <span><?php echo $this->user->last_name; ?></span>
                            </span>    
                            <a href="#" class="btn btn-link m-btn--icon m-btn--icon-only m-btn--pill">
                                <span class="m-topbar__userpic">
                                    <?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo $this->user->first_name; ?>+<?php echo $this->user->last_name; ?>&background=da720d&color=fff&size=32&bold=true" class="m--img-rounded m--marginless m--img-centered" alt=""/>
                                    <?php } 
                                    if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo $this->user->first_name; ?>+<?php echo $this->user->last_name; ?>&background=00abf2&color=fff&size=32&" class="m--img-rounded m--marginless m--img-centered" alt=""/>
                                    <?php } ?>
                                </span>
                            </a>
                        </div>
                        <div class="m-dropdown__wrapper" style="z-index: 101;">
                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 21.5px;"></span>
                            <div class="m-dropdown__inner">
                                <div class="m-dropdown__body">
                                    <div class="m-dropdown__content">
                                        <ul class="m-nav">
                                            <li class="m-nav__item">
                                                <a href="https://help.chamasoft.com" class="m-nav__link">
                                                    <i class="m-nav__link-icon flaticon-info"></i>
                                                    <span class="m-nav__link-text"><?php echo translate('FAQ');?></span>
                                                </a>
                                            </li>
                                            <li class="m-nav__item">
                                                <a href="#" class="m-nav__link">
                                                    <i class="m-nav__link-icon flaticon-support"></i>
                                                    <span class="m-nav__link-text"><?php echo translate('Need help');?>?</span>
                                                </a>
                                            </li>
                                            <li class="m-nav__separator m-nav__separator--fit">
                                            </li>
                                            <li class="m-nav__item">
                                                <a href="<?php echo site_url('logout'); ?>" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm"><?php echo translate('Log out');?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				</li>
				</li>
				<!-- <li class="nav-item">
					<a class="nav-link" href="#"><i class="fa fa-info-circle"></i> Help</a>
				</li> -->
				<!-- <li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> Profile </a>
					<div class="dropdown-menu dropdown-menu-right dropdown-cyan" aria-labelledby="navbarDropdownMenuLink-4">
						<a class="dropdown-item" href="#">My account</a>
						<a class="dropdown-item" href="#">Log out</a>
					</div>
				</li> -->
			</ul>
		</div>
	</div>
</nav>
<!--/.Navbar -->
<div class="container mb-5">
	<div class="row pt-3 pb-5">
		<div class="col-md-7 pt-5 pb-5 d-none d-lg-block prlx_shell">
			<img class="prlx img_entry" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ echo 'entry_sw.png'; } else { echo 'entry_en.png'; } ?>" draggable="false" alt="chamasoft entry">
			<img class="prlx img_admin" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'admin_sw.png'; } else { echo 'admin_en.png'; } ?>" draggable="false" alt="group admins">
			<img class="prlx img_group" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'group_sw.png'; } else { echo 'group_en.png'; } ?>" draggable="false" alt="chama">
		</div>
		<div class="col-md-5">
			<!--begin::Body-->
			<div class="m-login__body">
				<!--begin::Signin-->
				<div class="m-login__signin">
					<div class="m-login__title">
						<h4><?php echo translate('Verify OTP');?></h4>
						<p><?php echo translate('Use the one time password code sent via SMS to your phone');?></p>
					</div>
					
					<!--begin::Form-->
					<?php echo form_open(site_url('ajax/verify_otp'),'class="m-verify_otp__form m-form m-form--state" autocomplete="off"');?>
						<!-- <div class="alert alert-danger alert-dismissible fade show m-alert m-alert--air mb-3" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							</button>
							<strong>Oops!</strong> Looks like you gave us incorrect information.
						</div> -->
						<div class="">
							<div class="form-group m-form__group cust_login_inputs">
								<input class="form-control m-input--air cust_login_username" id="cust_confirm_code" type="text" placeholder="Code" name="code" autocomplete="off" autofocus>
								<label for="cust_confirm_code"><?php echo translate('One Time Password Code');?></label>
							</div>
						</div>

						<div class="row mb-5 mt-3">
							<div class="col-12">
								<button type="submit" id="verify_otp_submit_btn" class="btn cust_checkin_btn btn-success m-btn m-btn--outline-2x mt-3 mr-2" ><?php echo translate('Continue');?> <i class="la la-angle-right"></i></button>
								<a id="resend_otp_btn" class="btn  btn-outline-success m-btn m-btn--outline-2x mt-3 second_button"><?php echo translate('Resend OTP');?></a>
							</div>
						</div>
					</form> 
					<!--end::Form-->

					<!--begin::Action-->
					<div class="m-login__action">
						<div class="mt-5 d-none d-lg-block">
							<p class="text-left small">&copy; <?php echo date('Y');?> Risk Tick Credit Limited &middot; <a href="<?php echo site_url('terms_of_use') ?>" class="m-link"><?php echo translate('Terms of use');?></a></p>
						</div>
					</div>
					<!--end::Action-->
				</div>
				<!--end::Signin-->
			</div>
			<!--end::Body-->
		</div>
		<div class="col-md-7 pt-5 pb-5 d-block d-lg-none">
			<img src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'all_sw.png'; } else { echo 'all_en.png'; } ?>" draggable="false" width="100%" alt="group contributions">
		</div>
	</div>
	<div class="row pt-5 pb-5 d-block d-lg-none">
		<div class="col-md-12">
			<p class="text-center small">&copy; <?php echo date('Y');?> Risk Tick Credit Limited &middot; <a href="<?php echo site_url('terms_of_use') ?>" class="m-link">Terms of use</a></p>
		</div>
	</div>
</div>

<script type="text/javascript">
	function isJson(str) {
	    try {
	        JSON.parse(str);
	    } catch (e) {
	        return false;
	    }
	    return true;
	}

	var SnippetVerifyOtp = function () {
		var t = function () {
			$("#verify_otp_submit_btn").click(function (t) {
				t.preventDefault();
				var e = $(this),
					a = $(".m-verify_otp__form");
				var s = $(".second_button");
				a.validate({
					rules: {
                        code: {
                            required: true
                        }
					},
					messages: {
                        code: {
                            required: "<?php echo translate('OTP code is required');?>"
                        }
					}
				}), a.valid() && (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),s.addClass('disabled'),
		        $.post({
					url: "<?php echo site_url('ajax/verify_otp_code');?>",
					data : encryptdata(passphrase,a.serializeArray()),
					success: function (t, i, n, r) {
						if(isJson(t)){
							response = $.parseJSON(t);
							if(response.status == '1'){
								Toastr.show("OTP confirmed successfull",response.message,'success');
								window.location.href = response.refer;
							}else if(response.status == '200'){
								Toastr.show("Session Active",response.message,'info');
								window.location.href = response.refer;
								
							}else if(response.status == '202'){
								Toastr.show("Session Expired",response.message,'error');
								window.location.href = response.refer;
							}else{
								var message = '';
								if(response.hasOwnProperty('refer')){
									Toastr.show("Login Error",response.message,'error');
									window.location.href = response.refer;
								}else if(response.hasOwnProperty('validation_errors')){
									validation_errors = response.validation_errors;
									$.each(validation_errors, function( key, value ) {
										message+= value+"<br/>";
									});
								}else{
									message = response.message;
								}
								setTimeout(function () {
									e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
										function (t, e, a) {
											var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
											t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
										}(a, "danger",(message))
								}, 2e3)
							}
						}else{
							setTimeout(function () {
								e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
									function (t, e, a) {
										var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
										t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
									}(a, "danger", "<?php echo translate('You provided incorrect information. Please try again.');?>")
							}, 2e3)
						}
					},
					error: function(){
				        setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "<?php echo translate("Could not complete processing the request at the moment.");?>")
						}, 2e3)
				    },
				    always: function(){
				    	setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "<?php echo translate("Could not complete processing the request at the moment.");?>")
						}, 2e3)
				    }
				}))
			})
		};
		return {
			init: function () {
				t()
			}
		}
    }();
	
        
    var SnippetResendOtp =  function(){ 
        var t = function () {
            $("#resend_otp_btn").click(function (t) {
                t.preventDefault();
                var e = $(this),
                    a = $(".m-verify_otp__form");
                var s = $(".second_button");
                a.valid() && (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),s.addClass('disabled'),
                $.post({
                    url: "<?php echo site_url('ajax/resend_otp_code');?>",
                    data : encryptdata(passphrase,a.serializeArray()),
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                Toastr.show("OTP code successfull",response.message,'success');
                               	setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
                                        function (t, e, a) {
                                            
                                        }(a, "danger",message)
                                }, 2e3)
                                //window.location.href = response.refer;
                            }else if(response.status == '200'){
                                Toastr.show("Session Active",response.message,'info');
                                window.location.href = response.refer;
                            }else if(response.status == '202'){
								Toastr.show("Session Expired",response.message,'error');
								window.location.href = response.refer;
							}else{
                                var message = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                    $.each(validation_errors, function( key, value ) {
                                        message+= value+"<br/>";
                                    });
                                }else{
                                    message = response.message;
                                }
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger",(message))
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
                                    function (t, e, a) {
                                    	var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "<?php echo translate('You provided incorrect information. Please try again.');?>")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
                                function (t, e, a) {
                                	var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "<?php echo translate("Could not complete processing the request at the moment.");?>")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
                                function (t, e, a) {
                                	var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "<?php echo translate("Could not complete processing the request at the moment.");?>")
                        }, 2e3)
                    }
                }))
            })
        };
        return {
            init: function () {
                t()
            }
        }
    }();
	
	function prx_getNewY(e, a, b){
		var movementStrength = 20;
		var width = movementStrength / $(window).width();
		var pageX = e.pageX - ($(window).width() / 2);
		var newvalueX = width * pageX * a - b;
		return newvalueX;
	}

	jQuery(document).ready(function () {
        
        SnippetVerifyOtp.init();
        SnippetResendOtp.init();
		
		$(".prlx_shell").mousemove(function(e){
			$('.prlx.img_group').css({'margin-left': prx_getNewY(e, -1, -5) + 'px'});
			$('.prlx.img_admin').css({'margin-left': (240 + prx_getNewY(e, 0.5, +5)) + 'px'});
			$('.prlx.img_entry').css({'margin-left': (300 + prx_getNewY(e, -2, -5)) + 'px'});
		});

	});
</script>