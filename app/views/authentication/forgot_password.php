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
					<a class="nav-link" href="<?php echo $this->application_settings->protocol.$this->application_settings->url; ?>">&larr; <?php echo translate('Home');?></a>
				</li>
				<li class="nav-item pl-3">
					<a class="nav-link" href="<?php echo site_url('login') ?>"><?php echo translate('Login');?> <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item pl-3">
					<a class="nav-link" href="<?php echo site_url('signup') ?>"><?php echo translate('Signup');?></a>
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
	<div class="row justify-content-center pt-3 pb-5">
		<!-- <div class="col-md-7 pt-5 pb-5 d-none d-lg-block prlx_shell">
			<img class="prlx img_entry" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ echo 'entry_sw.png'; } else { echo 'entry_en.png'; } ?>" draggable="false" alt="chamasoft entry">
			<img class="prlx img_admin" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'admin_sw.png'; } else { echo 'admin_en.png'; } ?>" draggable="false" alt="group admins">
			<img class="prlx img_group" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'group_sw.png'; } else { echo 'group_en.png'; } ?>" draggable="false" alt="chama">
		</div> -->
		<div class="col-md-5">
			<!--begin::Body-->
			<div class="m-login__body mt-3">
				<!--begin::Signin-->
				<div class="m-login__signin">
					<div class="m-login__title">
						<h4><?php echo translate('Forgot Password');?></h4>
						<p><?php echo translate('Your phone number or email address is required.');?></p>
					</div>
					<!-- <div class="alert alert-danger alert-dismissible fade show m-alert m-alert--air mb-5" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						</button>
						<strong>Oops!</strong> Looks like your log in credentials are incorrect.
					</div> -->
					<!--begin::Form-->
					<?php echo form_open(site_url('ajax/forgot_password'),'class="m-login__form m-form m-form--state" autocomplete="off"');?>
						<!-- <div class="alert alert-danger alert-dismissible fade show m-alert m-alert--air mb-3" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							</button>
							<strong>Oops!</strong> Looks like you gave us incorrect information.
						</div> -->
						<div class="">
							<div class="form-group m-form__group cust_login_inputs">
								<input class="form-control m-input--air cust_login_username" id="cust_login_username" type="text" placeholder="Phone number or email" name="identity" autocomplete="off" autofocus>
								<label for="cust_login_username"><?php echo translate('Email or Phone');?></label>
							</div>
							<?php if($this->application_settings->enable_google_recaptcha): ?>
								<div class="form-group m-form__group cust_login_inputs">
									<div class="g-recaptcha" data-sitekey="<?php echo $this->config->item('google_key') ?>"></div>
								</div>
							<?php endif; ?>
							<div class="m-login__action text-left">
								<a href="<?php echo site_url('confirm_code') ?>" class="m-link cust_link second_button">
									<span><?php echo translate('Have a confirmation code'); ?> ?</span>
								</a>
							</div>
						</div>

						<div class="row mb-5 mt-3">
							<div class="col-12">
								<button type="submit" id="recover_password_submit_btn" class="btn cust_checkin_btn btn-success m-btn m-btn--outline-2x mt-3 mr-2" disabled="disabled"><?php echo translate('Continue');?> <i class="la la-angle-right"></i></button>
								<a href="<?php echo site_url('login'); ?>" class="btn cust_checkin_btn btn-outline-success m-btn m-btn--outline-2x mt-3 second_button"><?php echo translate('Back to login');?></a>
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
		<!-- <div class="col-md-7 pt-5 pb-5 d-block d-lg-none">
			<img src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'all_sw.png'; } else { echo 'all_en.png'; } ?>" draggable="false" width="100%" alt="group contributions">
		</div> -->
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

	var SnippetRecoverPassword = function () {
		var t = function () {
			$("#recover_password_submit_btn").click(function (t) {
				t.preventDefault();
				var e = $(this),
					a = $(".m-login__form");
				var s = $(".second_button");
				var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible mb-3" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
				a.find(".alert").html('').slideUp();
				a.validate({
					rules: {
						identity: {
							required: !0
						}
					},
					messages: {
						identity: {
							required: "Your email or phone number is required"
						}
					}
				}), a.valid() && (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),s.addClass("disabled"),
				$.post({
					url: "<?php echo site_url('ajax/forgot_password');?>",
					data : encryptdata(passphrase,a.serializeArray()),
					success: function (t, i, n, r) {
						if(isJson(t)){
							response = $.parseJSON(t);
							console.log(response);
							if(response.status == '1'){
								Toastr.show("Password reset code successfull",response.message,'success');
								window.location.href = response.refer;
							}else if(response.status == '200'){
								Toastr.show("Session Active",response.message,'info');
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
										}(a, "danger",message)
								}, 2e3)
							}
						}else{
							setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
						}
					},
					error: function(){
				        setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment.")
						}, 2e3)
				    },
				    always: function(){
				    	setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment.")
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
		SnippetRecoverPassword.init();
		
		$(".prlx_shell").mousemove(function(e){
			$('.prlx.img_group').css({'margin-left': prx_getNewY(e, -1, -5) + 'px'});
			$('.prlx.img_admin').css({'margin-left': (240 + prx_getNewY(e, 0.5, +5)) + 'px'});
			$('.prlx.img_entry').css({'margin-left': (300 + prx_getNewY(e, -2, -5)) + 'px'});
		});

	});
</script>
