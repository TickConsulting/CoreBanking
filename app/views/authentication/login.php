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
				<li class="nav-item pl-3 active">
					<a class="nav-link" href="<?php echo site_url('login') ?>"><?php echo translate('Login');?> <span class="sr-only">(current)</span></a>
				</li>
				
				<!-- <li class="nav-item pl-3">
					<a class="nav-link" href="<?php echo site_url('signup') ?>"><?php echo translate('Signup');?></a>
				</li> -->
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
<div class="container mb-5 center">
	
	<div class="row justify-content-center pt-3 pb-5">
		<!-- <div class="col-md-7 pt-5 pb-5 d-none d-lg-block prlx_shell">
			<img class="prlx img_entry" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ echo 'entry_sw.png'; } else { echo 'entry_en.png'; } ?>" draggable="false" alt="chamasoft entry">
			<img class="prlx img_admin" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'admin_sw.png'; } else { echo 'admin_en.png'; } ?>" draggable="false" alt="group admins">
			<img class="prlx img_group" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'group_sw.png'; } else { echo 'group_en.png'; } ?>" draggable="false" alt="chama">
		</div> -->
		<div class="col-md-5">
			<!--begin::Head-->
			<!-- <div class="m-login__head">
				<span>Don't have an account?</span>
				<a href="#" class="m-link m--font-danger">Sign Up</a>
			</div> -->
			<!--end::Head-->
			<!--begin::Body-->
			<div class="m-login__body">
				<!--begin::Signin-->
				<div class="m-login__signin">
					<div class="m-login__title">
						<h4><?php echo translate('Log in to');?> <?php echo $this->application_settings->application_name; ?></h4>
						<p><?php echo translate('Use your credentials to login');?></p>
					</div>
					<!-- <div class="alert alert-danger alert-dismissible fade show m-alert m-alert--air mb-5" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						</button>
						<strong>Oops!</strong> Looks like your log in credentials are incorrect.
					</div> -->
					<!--begin::Form-->
					<?php echo form_open(site_url('ajax/login'),'class="m-login__form m-form m-form--state" autocomplete="off"');?>
						<div class="form-group m-form__group cust_login_inputs">
							<input class="form-control m-input--air cust_login_username" id="cust_login_username" type="text" placeholder="Phone number or email" name="identity" autocomplete="off" autofocus value="<?php echo $phone ? $phone : '' ?>" >
							<label for="cust_login_username"><?php echo translate('Email or Phone');?></label>
						</div>
						<div class="form-group m-form__group cust_login_inputs">
							<input class="form-control m-input--air cust_login_password" id="cust_login_password" type="password" placeholder="Password" name="password" autocomplete="off" value="<?php echo $password ? $password : '' ?>" >
							<label for="cust_login_password"><?php echo translate('Password');?></label>
							<i class="fa fa-eye toggle_password_visibility" data-skin="light" data-toggle="m-tooltip" data-placement="bottom" title="" data-original-title="Show/Hide password"></i>
						</div>
						<?php if($this->application_settings->enable_google_recaptcha): ?>
							<div class="form-group m-form__group cust_login_inputs">
							<div class="g-recaptcha" data-sitekey="<?php echo $this->config->item('google_key') ?>"></div>
							</div>
						<?php endif; ?>
						<?php echo form_hidden('refer','');?>
						<!-- <div class="form-group m-form__group rem_me">
							<label class="m-checkbox m-checkbox--bold">
								<input type="checkbox" autocomplete="off"> <?php echo translate('Keep me logged in');?>
								<span></span>
							</label>
						</div> -->
						<div class="m-login__action mt-2 mb-3">
							<button type="submit" id="m_login_signin_submit" class="btn cust_checkin_btn btn-success m-btn m-btn--outline-2x m-login__btn" disabled="disabled"><?php echo translate('Log In');?></button>
							&nbsp;&nbsp;
							<!-- <a href="<?php echo site_url('signup'); ?>" id="" class="btn cust_checkin_btn btn-outline-success m-btn m-btn--outline-2x second_button"><?php echo translate('Sign Up');?></a> -->
						</div>
					</form>
					<!--end::Form-->

					<!--begin::Action-->
					<div class="m-login__action">
						
						<a href="<?php echo site_url('forgot_password') ?>" class="m-link cust_link">
							<span><?php echo translate('Forgot Password');?> ?</span>
						</a>
						
						<div class="mt-5 d-none d-lg-block">
							<p class="text-left small">&copy; <?php echo date('Y');?> Risk Tick Credit Limited &middot; 
							<!-- <a href="<?php echo site_url('terms_of_use') ?>" class="m-link"><?php echo translate('Terms of use');?></a></p> -->
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
		<?php if(preg_match('/eazzychama/',$_SERVER['HTTP_HOST'])) { ?>
          <div class="text-left" style="margin-top:-20px;">
              <p>
                  <strong>Get the <?php echo $this->application_settings->application_name; ?> App</strong>
              </p>
              <a class="text-center" href="https://play.google.com/store/apps/details?id=com.eazzychama" target="_blank">
                  {group:theme:image width="20%" align="middle" style="margin:0 auto;"  class="img-responsive animated hiding text-center"  file="get_play_store.png" data-animation="bounceInUp" data-delay="1000" }
              </a>
          </div>
      <?php } ?>
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

	var isMobile = false;
	if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
		isMobile = true;
	}
	var SnippetLogin = function () {
		$("#m_login");
		var t = function () {
			$("#m_login_signin_submit").click(function (t) {
				t.preventDefault();
				var e = $(this),
					a = $(".m-login__form");
				var s = $('.second_button');
				a.find(".alert").html('').slideUp();
				a.validate({
					rules: {
                        identity: {
                            required: true
                        },
                        password: {
                            required: true
                        }
					},
					messages: {
						identity: {
							required: "<?php echo translate('Hey, ensure this field is not empty');?>"
						},
                        password: {
                            required: "<?php echo translate('Enter password to continue');?>",
                        }
					}
				}),a.valid() && (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),
				s.addClass('disabled'),
				$.post({
					url: "<?php echo site_url('ajax/login');?>",
					data : encryptdata(passphrase,a.serializeArray()),
					success: function (t, i, n, r) {
						if(isJson(t)){
							response = $.parseJSON(t);
							if(response.status == '1'){
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
										}(a, "danger",translate(message))
								}, 2e3)
							}
						}else{
							setTimeout(function () {
								e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
									function (t, e, a) {
										var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
										t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
									}(a, "danger", "<?php echo translate('Could not complete processing the request at the moment');?>.")
							}, 2e3)
						}
					},
					error: function(){
				        setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "<?php echo translate('Could not complete processing the request at the moment');?>.")
						}, 2e3)
				    },
				    always: function(){
				    	setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),s.removeClass('disabled'),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "<?php echo translate('Could not complete processing the request at the moment');?>.")
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
		if(isMobile){
			$('.toggle_password_visibility').hide();
		}
		$('.toggle_password_visibility').on('click', function() {
			$(this).toggleClass('fa-eye-slash').toggleClass('fa-eye');
			$('.cust_login_password').togglePassword().focus();
		});
		SnippetLogin.init();

		$(".prlx_shell").mousemove(function(e){
			$('.prlx.img_group').css({'margin-left': prx_getNewY(e, -1, -5) + 'px'});
			$('.prlx.img_admin').css({'margin-left': (240 + prx_getNewY(e, 0.5, +5)) + 'px'});
			$('.prlx.img_entry').css({'margin-left': (300 + prx_getNewY(e, -2, -5)) + 'px'});
		});

	});
</script>
