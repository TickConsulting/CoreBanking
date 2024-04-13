
<div class="container mb-5">

	<div class="row pt-3 pb-5">
		<div class="col-md-6">
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
						<h4><?php echo translate("Request membership to join");?> <?php echo $group_name; ?></h4>
						<p><?php echo translate('Fill in the form to request membership');?></p>
					</div>
					<?php echo form_open(current_url(),'class="m-login__form m-form m-form--state" autocomplete="off"');?>
						<div class="form-group m-form__group cust_login_inputs">
							<?php echo form_input("full_name",'','class="form-control m-input--air cust_login_full_name capitalize" id="cust_login_full_name" autocomplete="off" placeholder="Your full name" autofocus');?>
							<label for="cust_login_full_name"><?php echo translate('Full Name');?> *</label>
                        </div>
                        <div class="form-group m-form__group cust_login_inputs">
							<?php echo form_input("phone",'','class="form-control m-input--air cust_login_phone" id="cust_login_phone" autocomplete="off"');?>
							<label style="display:block" for="cust_login_phone"><?php echo translate('Phone Number');?> *</label>
						</div>
                        <div class="form-group m-form__group cust_login_inputs">
							<?php echo form_input("email",'','class="form-control m-input--air cust_login_email lowercase" id="cust_login_email" placeholder="Your Email" autocomplete="off"');?>
							<label for="cust_login_email"><?php echo translate('Email');?></label>
						</div>
						<div class="m-login__action mt-2 mb-3">
							<button type="submit" id="m_login_signup_submit" class="btn cust_checkin_btn btn-success m-btn m-btn--outline-2x m-login__btn" disabled="disabled"><?php echo translate('Request membership');?></button>
						</div>
					</form>
					<!--end::Form-->

					<!--begin::Action-->
					<div class="m-login__action">
						<div class="mt-5 d-none d-lg-block">
							<p class="text-left small">&copy; <script>document.write(new Date().getFullYear());</script> Risk Tick Credit Limited &middot; <a href="<?php echo site_url('terms_of_use') ?>" class="m-link"><?php echo translate('Terms of use');?></a></p>
						</div>
					</div>
					<!--end::Action-->
				</div>
				<!--end::Signin-->
			</div>
			<!--end::Body-->
		</div>
	</div>
	<div class="row pt-5 pb-5 d-block d-lg-none">
		<div class="col-md-12">
			<p class="text-center small">&copy; <script>document.write(new Date().getFullYear());</script> Risk Tick Credit Limited &middot; <a href="<?php echo site_url('terms_of_use') ?>" class="m-link"><?php echo translate('Terms of use');?></a></p>
		</div>
	</div>
</div>
