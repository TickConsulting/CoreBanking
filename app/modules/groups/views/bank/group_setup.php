<!--Create group--->
<div class="m-portlet__body m-portlet__body--no-padding">
	<div id="create_group_panel">
		<div class="m-form__section m-form__section--first">
			<div class="m-form__heading px-2 pt-4">
				<h3 class="m-form__heading-title"><?php echo translate('Group Setup Details');?></h3>
			</div>
			<?php echo form_open_multipart($this->uri->uri_string(),'class="m-form m-form--state m-form--label-align-left-" id="create_group_form" autocomplete="off"');?>
				<div class="form-group m-form__group row pt-0 m--padding-10">
					<div class="col-lg-6 col-sm-6 m-form__group-sub name">
						<label class="form-control-label"><?php echo translate($this->application_settings->entity_name.' Name');?>: <span class="required">*</span></label>
						<?php echo form_input('group_name','','class="form-control m-input m-input--air capitalize" placeholder="Name of the '.$this->application_settings->entity_name.'"');?>
						<span class="m-form__help"><?php echo translate('Please enter the '.$this->application_settings->entity_name.' name');?></span>
					</div>
					<div class="col-lg-6 col-sm-6 m-form__group-sub">
						<label class="form-control-label"><?php echo translate('How many members in the '.$this->application_settings->entity_name);?>?: <span class="required">*</span></label>
						<?php echo form_input('group_size','','class="form-control m-input m-input--air numeric" placeholder="eg 10"');?>
						<?php echo form_hidden('banker',1,'');?>
						<span class="m-form__help"><?php echo translate('how many members are there in the '.$this->application_settings->entity_name);?>?</span>
					</div>
				</div>
				<div class="form-group m-form__group row pt-0 m--padding-10">
					<div class="col-lg-6 col-sm-6 m-form__group-sub m-input--air">
						<label class="form-control-label"><?php echo translate('Type of Group');?>: <span class="required">*</span></label>
						<?php echo form_dropdown('group_type',array(''=>'--'.translate('Select type').'--')+translate($type_of_groups),'','class="full-width form-control m-select2"');?>
						<span class="m-form__help"><?php echo translate('Please select one option');?></span>
					</div>
					<div class="col-lg-6 col-sm-6 m-form__group-sub m-input--air">
						<label class="form-control-label"><?php echo translate('Country of Operation');?>:</label>
						<?php
							echo form_dropdown('country_id',array(''=>'--'.translate('Select country').'--')+translate($countries),$this->current_country?$this->current_country->id:$this->default_country->id,'class="full-width form-control m-select2"');?>
					</div>
				</div>
				<div class="row form-group m-form__group pt-0 m--padding-10">
					
					<div class="col-lg-6 col-sm-6 m-form__group-sub m-input--air">
						<label class="form-control-label"><?php echo translate('Group Currency');?>:</label>
						<?php echo form_dropdown('currency_id',array(''=>'--'.translate('Select operating currency').'--')+translate($currencies),$this->current_country?$this->current_country->id:$this->default_country->id,'class="full-width form-control m-select2"');?>
					</div>
					<div class="col-sm-6 m-form__group-sub">
						<label class="form-control-label"><?php echo translate('Is the group a registered entity');?>?: <span class="required">*</span></label>
						<div class="m-radio-inline">
							<?php 
		                        if($this->input->post('group_is_registered')==1){
		                            $enable_group_is_registered = TRUE;
		                            $enable_group_not_registered = FALSE;
		                        }else if($this->input->post('group_is_registered')==0){
		                            $enable_group_is_registered = FALSE;
		                            $enable_group_not_registered = TRUE;
		                        }else{
		                            $enable_group_is_registered = TRUE;
		                            $enable_group_not_registered = FALSE;
		                        }
		                    ?>
		                    <label class="m-radio m-radio--solid m-radio--brand">
		                    	<?php echo form_radio('group_is_registered',1,$enable_group_is_registered,""); ?>
		                    	<?php echo translate('Yes');?>
		                    	<span></span>
		                    </label>

		                    <label class="m-radio m-radio--solid m-radio--brand">
		                    	<?php echo form_radio('group_is_registered',0,$enable_group_not_registered,""); ?>
		                    	<?php echo translate('No');?>
		                    	<span></span>
		                    </label>
						</div>
						<span class="m-form__help"><?php echo translate('Please select one option');?></span>
					</div>
				</div>
				<div class="row form-group m-form__group pt-0 m--padding-10">
					
					<div class="col-sm-12 m-form__group-sub" id="group-registration-certificate">
						<label class="form-control-label"><?php echo translate('Enter the Group registration certificate number');?>: <span class="required">*</span></label>
						<?php echo form_input('group_registration_certificate_number','','class="form-control m-input m-input--air uppercase" placeholder="eg BazT567Lk/'.date('Y').'"');?>
						
					</div>
				</div>

				<div class="m--margin-top-50">
					<div class="col-lg-12 col-md-12">
						<span class="float-lg-right float-md-left float-sm-left float-xl-right">
							<button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" type="button" id="create_group">
								<?php echo translate('Save Changes & continue');?>
							</button>
						</span>
					</div>
				</div>
			<?php echo form_close();?>
		</div>   
	</div>         									 
	<!---->
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('input[name="group_is_registered"]').on('change',function(){
            var group_is_registered = $(this).val();
            if(group_is_registered==1){
                $('#group-registration-certificate').slideDown();
            } else{
                $('#group-registration-certificate').slideUp();
            }
        });
        ManageSetupWizard.init(1);
        SnippetCreateGroup.init();
	});

	function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    String.prototype.replace_all = function(search,replacement) {
        var target = this;
        return target.split(search).join(replacement);
    };

    var SnippetCreateGroup = function () {
		$("#create_group_form");
		var t = function () {
			$(document).on('click',".btn#create_group",function (t) {
				t.preventDefault();
				var e = $(this),
					a = $("#create_group_form");
				RemoveDangerClass();
				mApp.block(a, {
		            overlayColor: 'grey',
		            animate: true,
		            type: 'loader',
		            state: 'primary',
		            message: 'Processing.....'
		        });
				(e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0), a.ajaxSubmit({
					url: "<?php echo site_url('ajax/create_group');?>",
					success: function (t, i, n, r) {
						if(isJson(t)){
							response = $.parseJSON(t);
							if(response.status == '1'){
								Toastr.show("Success",response.message,'success');
								window.location.href = response.refer;
								// console.log(response);
							}else if(response.status == '202'){
								Toastr.show("Session Expired",response.message,'error');
								window.location.href = response.refer;
							}else{
								var message = response.message;
								if(response.hasOwnProperty('validation_errors')){
									validation_errors = response.validation_errors;
								}
								setTimeout(function () {
									mApp.unblock(a);
									e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),
										function (t, e, a) {
											var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible mx-2" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
											t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
										}(a, "danger",message);
										if(validation_errors){
											$.each(validation_errors, function( key, value ) {
												var error_message ='<div class="form-control-feedback">'+value+'</div>';
												$('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
												($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
												$('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
												($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
											});
										}
										mUtil.scrollTop();
								}, 2e3)
							}
						}else{
							setTimeout(function () {
								mApp.unblock(a);
								e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),
									function (t, e, a) {
										var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible mx-2" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
										t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
									}(a, "danger", "Could not complete processing the request at the moment.")
							}, 2e3)
						}
					},
					error: function(){
						setTimeout(function () {
							mApp.unblock(a);
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible mx-2" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
						}, 2e3)
					},
					always: function(){
						setTimeout(function () {
							mApp.unblock(a);
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible mx-2" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
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
</script>
