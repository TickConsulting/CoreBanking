<!--Navbar -->
<link rel="stylesheet" href="<?php echo site_url('templates/admin_themes/admin/intl-tel-input/css/intlTelInput.min.css');?>">
<nav class="mb-1 navbar navbar-expand-lg navbar-light pt-1">
	<div class="container">
		<a class="navbar-brand font-bold" href="<?php echo site_url('') ?>">
		
		<img 
		src="<?php echo $this->application_settings?site_url('uploads/logos/'.$this->application_settings->logo)
		:base_url('/templates/admin_themes/groups/img/').'logo_contrast.png'?>" 
		draggable="false" 
		height="60px" 
		alt="Logo">
		</a>
		
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
			</ul>
		</div>
	</div>
</nav>
<!--/.Navbar -->
<div class="container mb-5">
	<div class="row pt-3 pb-5">
		<div class="col-md-7 pt-5 pb-5 d-none d-lg-none prlx_shell">
			<img class="prlx img_entry" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ echo 'entry_sw.png'; } else { echo 'entry_en.png'; } ?>" draggable="false" alt="chamasoft entry">
			<img class="prlx img_admin" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'admin_sw.png'; } else { echo 'admin_en.png'; } ?>" draggable="false" alt="group admins">
			<img class="prlx img_group" src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'group_sw.png'; } else { echo 'group_en.png'; } ?>" draggable="false" alt="chama">
		</div>
		<div class="col-md-12 mx-auto">
			<!--begin::Body-->
			<div class="m-login__body">
                <?php if($group)  { ?>
				<!--begin::Join Group-->
				<div class="m-login__signin">
					<div class="m-login__title">
						<div class="d-flex justify-content-between">
						<h4><?php echo translate('Request to join ');?> <strong><?php echo $group->name; ?></strong></h4>
						<?php if($group->avatar): ?>
							<img 
							src="<?php echo site_url('uploads/groups/'.$group->avatar)?>" 
							draggable="false" 
							height="60px" 
							alt="Logo">
						<?php else: ?>
							<img 
							src="<?php echo site_url('uploads/groups/default_group_placeholder_logo.png')?>" 
							draggable="false" 
							height="60px" 
							alt="Logo">
						<?php endif; ?>
						</div>
					</div>
					<!--begin::Form-->
					<?php echo form_open_multipart($this->uri->uri_string(),'role="form" class="m-login__form m-form m-form--state" autocomplete="off"');?>
					<div class="row">
						<div class="col-md-12">
							<?php 
								if($success_message){
									echo '<div class="m-alert--air mb-5 m-alert alert alert-success alert-dismissible animated fadeIn" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
									';

									echo $success_message;

									echo '</div>';
								}
							?>
						</div>
						<!-- End success message -->
						<!-- Error message -->
						<div class="col-md-12">
							<?php 
								if($error_message){
									echo '<div class="m-alert--air mb-5 m-alert alert alert-danger alert-dismissible animated fadeIn" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
									';

									echo $error_message;

									echo '</div>';
								}
							?>
						</div>
						<!-- End error message -->
						<!-- Validation error message -->
						<div class="col-md-12">
						<?php
							if($validation_errors){
								echo '<div class="m-alert--air mb-5 m-alert alert alert-danger alert-dismissible animated fadeIn" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
								';
								
								foreach($validation_errors as $validation_error){
									echo $validation_error.'<br>';
								}								

								echo '</div>';
							   							
							}
						?>
						<!-- End validation error message -->
						</div>						
						<div class="col-md-12 mb-2">
							<p>Primary details</p>
						</div>
						<input type="hidden" name="group_id" value="<?php echo $group->id; ?>">
						<input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
						<div class="col-md-4 mb-3">
							<div class="form-group m-form__group cust_login_inputs">
								<?php echo form_input("full_name",$this->input->post('full_name')?$this->input->post('full_name'):$post->full_name,'class="form-control m-input--air" name="full_name" id="full_name" placeholder="Your Full Name" autocomplete="off"');?>
								<label for="full_name"><?php echo translate('Full Name');?><span class="required text-danger">*</span></label>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<div class="form-group m-form__group cust_login_inputs">
								<?php echo form_input("phone",$this->input->post('phone')?$this->input->post('phone'):$post->phone,'class="form-control m-input--air name="phone" cust_login_phone" id="phone" autocomplete="off"');?>
								<label style="display:block" for="phone"><?php echo translate('Phone Number');?><span class="required text-danger">*</span></label>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<div class="form-group m-form__group cust_login_inputs">
								<?php echo form_input("email",$this->input->post('email')?$this->input->post('email'):$post->email,'class="form-control m-input--air" id="email" placeholder="Your Email Address" autocomplete="off"');?>
								<label for="email"><?php echo translate('Email Address');?></label>
							</div>
						</div>
						<div class="col-md-12 mb-2">
							<p>Secondary details</p>
						</div>						
						<div class="col-md-4 mb-3">
							<div class="form-group m-form__group cust_login_inputs">
								<?php echo form_input("id_number",$this->input->post('id_number')?$this->input->post('id_number'):$post->id_number,'class="form-control m-input--air" id="id_number" placeholder="Your ID Number" autocomplete="off"');?>
								<label for="id_number"><?php echo translate('ID Number');?></label>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<div class="form-group m-form__group cust_login_inputs">
								<?php echo form_input("location",$this->input->post('location')?$this->input->post('location'):$post->location,'class="form-control m-input--air" id="location" placeholder="Your Location" autocomplete="off"');?>
								<label for="location"><?php echo translate('Location');?></label>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<div class="form-group m-form__group cust_login_inputs">
								<?php echo form_input("next_of_kin_full_name",$this->input->post('next_of_kin_full_name')?$this->input->post('next_of_kin_full_name'):$post->next_of_kin_full_name,'class="form-control m-input--air" id="next_of_kin_full_name" placeholder="Next of Kin Full Name" autocomplete="off"');?>
								<label for="next_of_kin_full_name"><?php echo translate('Next of Kin Full Name');?></label>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<div class="form-group m-form__group cust_login_inputs">
								<?php echo form_input("next_of_kin_id_number",$this->input->post('next_of_kin_id_number')?$this->input->post('next_of_kin_id_number'):$post->next_of_kin_id_number,'class="form-control m-input--air" id="next_of_kin_id_number" placeholder="Next of Kin ID Number" autocomplete="off"');?>
								<label for="next_of_kin_id_number"><?php echo translate('Next of Kin ID Number');?></label>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<div class="form-group m-form__group cust_login_inputs">
								<?php echo form_input("next_of_kin_phone",$this->input->post('next_of_kin_phone')?$this->input->post('next_of_kin_phone'):$post->next_of_kin_phone,'class="form-control m-input--air cust_login_phone" id="next_of_kin_phone" autocomplete="off"');?>
								<label style="display:block" for="next_of_kin_phone"><?php echo translate('Next Of Kin Phone');?></label>
							</div>
						</div>
						<div class="col-md-4 mb-3">
							<div class="form-group m-form__group cust_login_inputs">
								<?php echo form_input("next_of_kin_relationship",$this->input->post('next_of_kin_relationship')?$this->input->post('next_of_kin_relationship'):$post->next_of_kin_relationship,'class="form-control m-input--air" id="next_of_kin_relationship" placeholder="Next of Kin Relationship" autocomplete="off"');?>
								<label for="next_of_kin_relationship"><?php echo translate('Next of Kin Relationship');?></label>
							</div>
						</div>
						<div class="col-md-12 mb-3">			
						<div class="fileinput fileinput-new" data-provides="fileinput">
								<label for="avatar">Your Profile Picture</label><br/>
                                <div class="fileinput-new thumbnail" style="max-width: 150px;">
                                    <img src="<?php echo site_url('templates/admin_themes/admin/img/no_image.png'); ?>" alt="" /> </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 50px;"> </div>
                                <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select Profile Picture </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" id="avatar" name="avatar"/></span>
                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                            </div>
						</div>
						<div class="col-md-12 mb-3">
							<div class="m-login__action">
								<button type="submit" id="request_membership_submit" class="btn cust_checkin_btn btn-success m-btn m-btn--outline-2x m-login__btn"><?php echo translate('Request Membership');?></button>
							</div>
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
				<!--end::Join Group-->

                <?php } else { ?>
                <div class="m-login__signin">
					<div class="m-login__title">
						<h4><strong><?php echo translate('No Such Group Exists ');?></strong></h4>
						<p><?php echo translate('Oops! You must have entered an incorrect link. Kindly ask the sender to send the correct one.');?></p>
					</div>
                </div>
                <?php } ?>
			</div>
			<!--end::Body-->
		</div>
		<div class="col-md-7 pt-5 pb-5 d-none d-lg-none">
			<img src="<?php echo base_url('/templates/admin_themes/groups/img/auth/'); ?><?php if(preg_match('/swahili/i', $this->selected_language_name)){ echo 'all_sw.png'; } else { echo 'all_en.png'; } ?>" draggable="false" width="100%" alt="group contributions">
		</div>
		<?php if(preg_match('/eazzychama/',$_SERVER['HTTP_HOST'])) { ?>
          <div class="text-left d-none d-lg-none" style="margin-top:-20px;">
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

<script src="<?php echo site_url('templates/admin_themes/admin/intl-tel-input/js/intlTelInput.min.js');?>"></script>
<script type="text/javascript">
	var input = document.querySelector("#phone");
	var input_two = document.querySelector("#next_of_kin_phone");
	
    var default_country_code = <?php if( isset($this->default_contry_code) && strlen($this->default_country_code) > 0) echo "'".strtolower($this->default_country_code)."'"; else echo "'ke'"; ?>;
    window.intlTelInput(input, {
        allowDropdown: true,
        // autoHideDialCode: false,
        // autoPlaceholder: "off",
        // dropdownContainer: document.body,
        // excludeCountries: ["us"],
        formatOnDisplay: true,
        // hiddenInput: "full_number",
        initialCountry: default_country_code,
        // localizedCountries: { 'de': 'Deutschland' },
        // nationalMode: false,
        // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
        // placeholderNumberType: "MOBILE",
        preferredCountries: ['ke', 'tz', 'ug'],
        separateDialCode: true,
        utilsScript: "<?php echo site_url('templates/admin_themes/admin/intl-tel-input/js/utils.js');?>",
    });
    
	window.intlTelInput(input_two, {
        allowDropdown: true,
        // autoHideDialCode: false,
        // autoPlaceholder: "off",
        // dropdownContainer: document.body,
        // excludeCountries: ["us"],
        formatOnDisplay: true,
        // hiddenInput: "full_number",
        initialCountry: default_country_code,
        // localizedCountries: { 'de': 'Deutschland' },
        // nationalMode: false,
        // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
        // placeholderNumberType: "MOBILE",
        preferredCountries: ['ke', 'tz', 'ug'],
        separateDialCode: true,
        utilsScript: "<?php echo site_url('templates/admin_themes/admin/intl-tel-input/js/utils.js');?>",
    });

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
	var SnippetRequestMembership = function () {
		var t = function () {			
			var e = $(this),
				a = $(".m-login__form");
			var is_successful = `<?php echo $success_message ?>`;
			console.log(`is_successful: ${is_successful}`);
			if(is_successful){
				a[0].reset();
				$("#full_name").val('');
				$("#phone").val('');
				$("#email").val('');
				$("#id_number").val('');
				$("#location").val('');
				$("#next_of_kin_full_name").val('');
				$("#next_of_kin_phone").val('');
				$("#next_of_kin_id_number").val('');
				$("#next_of_kin_relationship").val('');
			}
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
		SnippetRequestMembership.init();

		$(".prlx_shell").mousemove(function(e){
			$('.prlx.img_group').css({'margin-left': prx_getNewY(e, -1, -5) + 'px'});
			$('.prlx.img_admin').css({'margin-left': (240 + prx_getNewY(e, 0.5, +5)) + 'px'});
			$('.prlx.img_entry').css({'margin-left': (300 + prx_getNewY(e, -2, -5)) + 'px'});
		});
	});
</script>