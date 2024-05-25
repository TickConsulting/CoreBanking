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
						<h4><?php echo translate('Change Password');?></h4>
						<p><?php echo translate('Fill in the form to create a new password');?></p>
					</div>

					<?php if ($this->session->flashdata('error')) {
						?>
						<div class="m-login__title alert alert-danger">
							<h4><?php echo translate('Error'); ?></h4>
							<p><?php echo translate($this->session->flashdata('error'));?></p>
						</div>
						<?php
					} ?>
					<?php if (validation_errors()) {
						?>
						<div class="m-login__title alert alert-danger">
							<h4><?php echo translate('Error'); ?></h4>
							<p><?php echo translate(validation_errors());?></p>
						</div>
						<?php
					} ?>
					
					<!--begin::Form-->
					<?php echo form_open(current_url(),'class="m-login__form m-form m-form--state" autocomplete="off"');?>
						<div class="">
							<div class="form-group m-form__group cust_login_inputs">
								<input class="form-control m-input--air cust_old_pass" id="cust_current_pass" type="password" placeholder="Your Current password" name="current_password" autocomplete="off" autofocus>
								<label for="cust_current_pass"><?php echo translate('Current Password');?></label>
							</div>
							<div class="form-group m-form__group cust_login_inputs">
								<input class="form-control m-input--air cust_new_pass" id="cust_new_pass" type="password" placeholder="Your new password" name="password" autocomplete="off" autofocus>
								<label for="cust_new_pass"><?php echo translate('New Password');?></label>
							</div>
							<div class="form-group m-form__group cust_login_inputs">
								<input class="form-control m-input--air cust_new_pass_conf" id="cust_new_pass_conf" type="password" placeholder="Confirm your password" autocomplete="off" name="confirm_password">
								<label for="cust_new_pass_conf"><?php echo translate('Confirm your password');?></label>
							</div>
							
						</div>

						<div class="row mb-5 mt-3">
							<div class="col-12">
								<button type="submit" class="btn cust_checkin_btn btn-success m-btn m-btn--outline-2x mt-3 mr-2"><i class="la la-check"></i> <?php echo translate('Save');?></button>
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
				a.validate({
					rules: {
						password: {
                            minlength: 8
						},
                        confirm_password: {
                            equalTo: "#cust_new_pass"
                        }
					},
					messages: {
						password: {
							minlength: "Your new password is too short"
						},
                        confirm_password: {
                            equalTo: "Your passwords do not match"
                        }
					}
				}), a.valid() && (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),s.addClass('disabled'),
				$.post({
					data : encryptdata(passphrase,a.serializeArray()),
					url: "<?php echo site_url('ajax/reset_password?code='.$this->input->get_post('code'));?>",
					success: function (t, i, n, r) {
						if(isJson(t)){
							response = $.parseJSON(t);
							if(response.status == '1'){
								Toastr.show("Password reset successful",response.message,'success');
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
								e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
									function (t, e, a) {
										var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
										t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
									}(a, "danger", "You provided incorrect information. Please try again.")
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