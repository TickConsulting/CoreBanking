<!--Create group--->
<link rel="stylesheet" href="<?php echo site_url('templates/admin_themes/admin/intl-tel-input/css/intlTelInput.min.css');?>">
<div class="m-portlet__body m-portlet__body--no-padding py-4 px-3">
	<!--Create Members-->
	<div id="create_group_members_panel">
		<div class="m-form__section m-form__section--first">
			<div class="m-form__heading pb-3">
				<div class="row">
					<div class="col-lg-6 col-sm-12">
						<h3 class="m-form__heading-title"><?php echo translate($this->application_settings->entity_name .' Applicant Details
');?></h3>
					</div>
					<div class="col-lg-6 col-sm-12">
						<a href="#" class="btn btn-primary float-right btn-sm" id="add_member_form">
							<span>
								<i class="la la-user-plus"></i>
								<span>Add Member(s)</span>
							</span>
						</a>
					</div>
				</div>
			</div>
			<div class="datatable_members table-responsive-sm">
				<table class="table table-striped- table-bordered table-hover table-checkable members setup_table" id="members_listing" >
				   	<thead>
				      	<tr>
					        <th class="numbering">#</th>
					        <th class="name"><?php echo translate('Name');?></th>
					        <th><?php echo translate('Phone');?></th>
					        <th><?php echo translate('Email Address');?></th>
					        <th><?php echo translate($this->application_settings->entity_name . ' Role');?></th>
					        <th><?php echo translate('Actions');?></th>
				      </tr>
				   </thead>
				   <tbody style="padding-bottom:20px!important;">
				   </tbody>
				</table>
			</div>

			<div class="add_new_members" >
				<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="add_new_members_line"'); ?>
				<div class="table-responsive-sm">
					<table class="table" id="multiple-entries">
						<thead>
							<tr>
								<th width="2%">#</th>
								<th width="24%"><?php echo translate('Full Names');?> <span class="required">*</span></th>
								<th width="22%"><?php echo translate('Phone Number');?> <span class="required">*</span></th>
								<th width="22%"><?php echo translate('Email Address');?> </th>
								<th width="24%"><?php echo translate($this->application_settings->entity_name . ' Role');?> <span class="required">*</span></th>
								<th width="5%"></th>
							</tr>
						</thead>
						<tbody id="append-place-holder">
							<tr>
								<td class="number" style="padding: 1.0rem;">1.</td>
								<td>
									<?php echo form_input('names[]','','class="form-control form-control-sm m-input names m-input--air capitalize" placeholder=""');?>
								</td>
								<td>
									<?php echo form_input("phones[]",'','class="form-control form-control-sm m-input--air cust_login_phone phones"');?>
								</td>
								<td>
									<?php echo form_input('email_addresses[]','','class="form-control form-control-sm m-input m-input--air email_addresses lowercase" placeholder=""');?>
								</td>
								<td>
									<span class="m-select2-sm m-input--air">
										<?php echo form_dropdown('group_role_ids[0]',translate($group_roles),'','class="form-control m-input m-select2-append m-input--air group_role_ids"');?>
									</span>
								</td>
								<td style="padding: 1.0rem;">
									<button data-original-title="Remove line" href="javascript:;" class="remove-line btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air float-right btn-xs">
		                                <i class="fa fa-times"></i>
		                            </button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
					<a type="button" class="btn btn-primary myModal d-none" data-keyboard="false" data-backdrop="static" data-toggle="modal" data-target="#add_new_role" id="add_new_role_hidden"> Add New Role</a>
					<div class="row">
						<div class="col-md-12">
							<a class="btn btn-default btn-sm" id="add-new-line">
								<i class="la la-plus"></i><?php echo translate('Add New Line');?>
							</a>
						</div>
					</div>
				
					<div class="m--margin-top-50">
						<div class="col-lg-12 col-md-12">
							<span class="float-lg-left float-md-right float-sm-right float-xl-left">
								<button class="btn btn-info m-btn m-btn--custom m-btn--icon btn-sm" type="button" id="upload_members_excel_form">
									<i class="fa fa-file-upload"></i> <?php echo translate('Upload Excel');?>
								</button>
							</span>
							<span class="float-lg-right float-md-left float-sm-left float-xl-right">
								<button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="add_new_members_line_button" type="button">
									<?php echo translate('Save Changes');?>
								</button>
								&nbsp;&nbsp;
								<button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form"  type="button" id="cancel_add_members_form">
									<?php echo translate('Cancel');?>
								</button>
							</span>
						</div>
					</div>

				<?php echo form_close();?>
			</div>

			<!--begin::Modal-->
			<div class="modal fade" id="add_new_role" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  	<div class="modal-dialog modal-dialog-centered" role="document">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<h5 class="modal-title" id="exampleModalLabel">Add New <?php echo $this->application_settings->entity_name ?> Role</h5>
			        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          			<span aria-hidden="true">&times;</span>
			        		</button>
			      		</div>
			      		<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="add_new_member_role_form"'); ?>
				      	<div class="modal-body">
				       		<div class="form-group m-form__group row pt-0">
								<div class="col-lg-12 m-input--air">
									<label class="form-control-label"><?php echo translate($this->application_settings->entity_name.' Role Name');?>: <span class="required">*</span></label>
									<?php echo form_input('name','','class="form-control"');?>
								</div>
							</div>
							<div class="form-group m-form__group row pt-0">
								<div class="col-lg-12 m-input--air">
									<label class="form-control-label"><?php echo translate('Description');?></label>
									<?php
					                    $textarea = array(
					                        'name'=>'description',
					                        'id'=>'',
					                        'value'=> '',
					                        'cols'=>40,
					                        'rows'=>4,
					                        'maxlength'=>160,
					                        'class'=>'form-control maxlength',
					                        'placeholder'=>$this->application_settings->entity_name.' role description'
					                    ); 
					                echo form_textarea($textarea); ?>
					            </div>
					        </div>
				      	</div>
			      		<div class="modal-footer">
			        		<button type="button" class="btn btn-secondary cancel_form" id="close_add_role" data-dismiss="modal">Close</button>
			        		<button type="button" class="btn btn-primary" id="add_new_role_btn">Save changes</button>
			      		</div>
		      			<?php echo form_close();?>
			    	</div>
			  	</div>
			</div>
			<!--end::Modal-->



			<div class="upload_members_excel">
				<div class="m-portlet m-portlet--creative m-portlet--bordered-semi mt-0 pt-0">
					<div class="m-portlet__body m-demo__preview">
						<h4>Import Member Instructions</h4>
						<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="upload_excel_file"'); ?>
						<ol style="line-height: 2.2rem;">
							<li>
								<?php echo translate('Download sample import template');?>
				                <a class='m-badge m-badge--metal m-badge--wide' href='<?php echo site_url('uploads/files/csvs/import_member_template.xlsx'); ?>'><i class='la la-download'></i> <?php echo $this->lang->line("download")?:"Download";?></a>
				            </li>
				            <li>
				            	<?php echo translate('Open downloaded file and fill in the details for each member as described');?>
				            </li>
				            <li>
				               	<?php
				               		$default_message='Save the Excel Sheet and select it below to upload it.';
				                    $this->languages_m->translate('save_upload_excel',$default_message);?>
				            </li>
				       	</ol>
				       	<div class="row">
				       		<div class="col-lg-12">
				       			<label for="exampleInputEmail1">Member list file</label>
				       		</div>
				       		<div class="col">
				       			<div class="form-group m-form__group">
									<div></div>
									<div class="custom-file">
									  	<input type="file" class="custom-file-input member_import_file" id="customFile">
									  	<label class="custom-file-label" id="choose_member_file" for="customFile">Choose excel file</label>
									</div>
								</div>
				       		</div>
				       		<div class="col">
				       			<span class="float-lg-right float-md-left float-sm-left float-xl-right">
									<button class="btn btn-primary m-btn m-btn--custom m-btn--icon" id="upload_excel_file_button">
										<?php echo translate('Submit File');?>
									</button>
									&nbsp;&nbsp;
									<button class="btn btn-metal m-btn m-btn--custom m-btn--icon cancel_form"  type="button" id="cancel_upload_members_excel">
										<?php echo translate('Cancel');?>
									</button>
								</span>
				       		</div>
				       	</div>
				       	<?php echo form_close();?>
					</div>
				</div>
			</div>
		</div>

		<div id="add_new_members_settings">
			<table>
				<tbody>
					<tr>
						<td class="number" style="padding: 1.0rem;"> 1. </td>
						<td>
							<?php echo form_input('names','','class="form-control form-control-sm m-input names m-input--air capitalize " placeholder=""');?>
						</td>
						<td>
							<?php echo form_input("phones[]",'','class="form-control form-control-sm m-input--air cust_login_phone phones"');?>
						</td>
						<td>
							<?php echo form_input('email_addresses','','class="form-control form-control-sm m-input email_addresses m-input--air lowercase" placeholder=""');?>
						</td>
						<td>
							<span class="m-select2-sm m-input--air">
								<?php echo form_dropdown('group_role_ids',translate($group_roles),'','class="form-control m-input m-select2-append group_role_ids m-input--air"');?>
							</span>
						</td>
						<td style="padding: 1.0rem;">
							<button data-original-title="Remove line" href="javascript:;" class="remove-line btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--air float-right btn-xs">
			                    <i class="fa fa-times"></i>
			                </button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<!---->
	<!--create contribution-->
	<div  id="create_group_contribution_panel">
		<div class="m-form__section m-form__section--first">
			<div class="m-form__heading pb-3">
				<div class="row">
					<div class="col-lg-6 col-sm-12">
						<h3 class="m-form__heading-title"><?php echo translate($this->application_settings->entity_name.' Contribution Setup');?></h3>
					</div>
					<div class="col-lg-6 col-sm-12">
						<a href="#" class="btn btn-primary m-btn--icon float-right btn-sm" id="create_contribution_form">
							<span>
								<i class="la la-plus"></i>
								<span>Create Contribution</span>
							</span>
						</a>
					</div>
				</div>
			</div>
			<div class="datatable_contributions table-responsive-sm">
				<table class="table table-striped- table-bordered table-hover table-checkable contributions setup_table" id="contribution_listing">
				   	<thead>
				      	<tr>
					        <th class="numbering">#</th>
					        <th class="name"><?php echo translate('Name');?></th>
					        <th><?php echo translate('Contribution Details');?></th>
					        <th><?php echo translate('Fine Details');?></th>
					        <th><?php echo translate('Amount').' ('.$this->group_currency.')';?></th>
					        <th><?php echo translate('Actions');?></th>
				      </tr>
				   </thead>
				   <tbody>
				   </tbody>
				</table>
			</div>
			<div class="create_contribution_settings">
				<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_contribution"'); ?>
					<div id="contribution_settings">
						<div class="form-group m-form__group row pt-0 m--padding-10">
							<div class="col-lg-6 col-sm-12 m-form__group-sub">
								<label class="form-control-label"><?php echo translate('Contribution Name');?>?: <span class="required">*</span></label>
								<?php echo form_input('name','','class="form-control m-input m-input--air" placeholder="eg Savings"');?>
							</div>
							<div class="col-lg-6 col-sm-12 m-form__group-sub">
								<label class="form-control-label"><?php echo translate('Contribution Amount per Member');?> (<?php echo $this->group_currency;?>): <span class="required">*</span></label>
								<?php echo form_input('amount','','class="form-control m-input m-input--air currency" placeholder="eg 2,000"');?>
							</div>
							<?php echo form_hidden('id','');?>
						</div>

						<div class="form-group m-form__group row pt-0 m--padding-10">
							<div class="col-lg-6 col-sm-12 m-form__group-sub">
								<label class="form-control-label"><?php echo translate('Contribution Category');?>: <span class="required">*</span></label>
								<?php echo form_dropdown('category',array(''=>'--'.translate('Select category').'--')+translate($contribution_category_options),'','class="form-control m-select2"');?>
							</div>
							<div class="col-lg-6 col-sm-12 m-form__group-sub m-input--air">
								<label class="form-control-label"><?php echo translate('Contribution Type');?>: <span class="required">*</span></label>
								<?php echo form_dropdown('type',array(''=>'--'.translate('Select contribution type').'--')+translate($contribution_type_options),'','class="form-control m-select2"');?>
							</div>
						</div>


						<div class="form-group m-form__group row pt-0 m--padding-10">
							
						</div>

						<div id='regular_invoicing_active_holder' class="form-group m-form__group row pt-0 m--padding-10">
							<div class="col-lg-12 m-form__group-sub">
								<label><?php echo translate('Do you wish');?> <?php echo $this->application_settings->application_name;?> <?php echo translate('to be sending regular invoices and reminders to members?');?></label>
								<div class="m-radio-inline">
									<?php 
				                        if($this->input->post('regular_invoicing_active')==1){
					                        $enable_activate_invoicing = TRUE;
					                        $disable_activate_invoicing = FALSE;
					                    }else if($this->input->post('regular_invoicing_active')==0){
					                        $enable_activate_invoicing = FALSE;
					                        $disable_activate_invoicing = TRUE;
					                    }else{
					                        $enable_activate_invoicing = TRUE;
					                        $disable_activate_invoicing = FALSE;
					                    }
						                    ?>
				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('regular_invoicing_active',1,$enable_activate_invoicing,""); ?>
				                    	<?php echo translate('Yes');?>
				                    	<span></span>
				                    </label>

				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('regular_invoicing_active',0,$disable_activate_invoicing,""); ?>
				                    	<?php echo translate('No');?>
				                    	<span></span>
				                    </label>
								</div>
							</div>
						</div>

				        <div id='one_time_invoicing_active_holder' class="form-group m-form__group row pt-0 m--padding-10">
				        	<div class="col-lg-12 m-form__group-sub">
			            		<label><?php echo translate('Do you wish');?> <?php echo $this->application_settings->application_name;?> <?php echo translate('to send automatic invoices and reminders to members?');?></label>
				                <div class="m-radio-inline">
									<?php 
				                        if($this->input->post('one_time_invoicing_active')==1){
					                        $one_time_enable_activate_invoicing = TRUE;
					                        $one_time_disable_activate_invoicing = FALSE;
					                    }else if($this->input->post('one_time_invoicing_active')==0){
					                        $one_time_enable_activate_invoicing = FALSE;
					                        $one_time_disable_activate_invoicing = TRUE;
					                    }else{
					                        $one_time_enable_activate_invoicing = TRUE;
					                        $one_time_disable_activate_invoicing = FALSE;
					                    }
						            ?>
				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('one_time_invoicing_active',1,$one_time_enable_activate_invoicing,""); ?>
				                    	<?php echo translate('Yes');?>
				                    	<span></span>
				                    </label>

				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('one_time_invoicing_active',0,$one_time_disable_activate_invoicing,""); ?>
				                    	<?php echo translate('No');?>
				                    	<span></span>
				                    </label>
								</div>
								<span class="m-form__help"><?php echo translate('Please select one option');?></span>
					        </div>
				        </div>

				        <div id='regular_invoicing_settings'>
				        	<div class="form-group m-form__group row pt-0 m--padding-10">
								<div class="col-lg-12 m-form__group-sub m-input--air ">
									<label><?php echo translate('How often do members contribute?');?><span class="required">*</span></label>
									<?php echo form_dropdown('contribution_frequency',array(''=>'--'.translate('Select contribution frequency').'--')+translate($contribution_frequency_options),"",' id="contribution_frequency" class="form-control m-select2" data-placeholder="Select contribution frequency..."'); ?>
								</div>
							</div>

							<div id='once_a_month' class="form-group m-form__group row pt-0 m--padding-10">
								<div class="col-lg-12 m-form__group-sub">
									<label><?php echo translate('When do members contribute?');?><span class="required">*</span></label>
									<div class="row">
										<div class="col-lg-6 col-sm-12 m-form__group-sub m-input--air ">
											<?php echo form_dropdown('month_day_monthly',array(''=>'--'.translate('Select day').'--')+translate($days_of_the_month),"",' id="month_day_monthly" class=" form-control m-select2" data-placeholder="Select day..."'); ?>
										</div>
										<div class="col-lg-6 col-sm-12 m-form__group-sub m-input--air">
											<?php echo form_dropdown('week_day_monthly',array(''=>'--'.translate('Select day of the month').'--')+translate($month_days),"",' id="week_day_monthly" class="form-control m-select2" data-placeholder="Select day of the month..."'); ?>
										</div>
									</div>
								</div>
							</div>

							<div id='twice_every_one_month'>
								<div class="form-group m-form__group row pt-0 m--padding-10">
									<div class="col-lg-12 m-form__group-sub m-input--air ">
										<label><?php echo translate('When do members contribute');?>?<span class="required">*</span></label>
									</div>
									<div class="col-lg-12" id="append_contribution">
										<div class="row first_row">
											<div class="col-sm-4 m-form__group-sub m-input--air ">
												<?php echo form_dropdown('after_first_contribution_day_option',translate($contribution_days_option),'','id="after_first_contribution_day_option" class="form-control m-select2" data-placeholder="Select..."'); ?>
											</div>

											<div class="col-sm-4 m-form__group-sub m-input--air ">
												<?php echo form_dropdown('after_first_day_week_multiple',translate($month_days),'','id="after_first_day_week_multiple" class="form-control m-select2" data-placeholder="Select..."'); ?>
											</div>

											<div class="col-sm-4 m-form__group-sub m-input--air ">
												<?php echo form_dropdown('after_first_starting_day',translate($starting_days),'','id="after_first_starting_day" class="form-control m-select2" data-placeholder="Select..."'); ?>
											</div>
										</div>
										<div class="row second_row pt-0 m--padding-top-10">
											<div class="col-sm-4 m-form__group-sub m-input--air ">
												<?php echo form_dropdown('after_second_contribution_day_option',translate($contribution_days_option),'','id="after_second_contribution_day_option" class="form-control m-select2" data-placeholder="Select..."'); ?>
											</div>

											<div class="col-sm-4 m-form__group-sub m-input--air ">
												<?php echo form_dropdown('after_second_day_week_multiple',translate($month_days),'','id="after_second_day_week_multiple" class="form-control m-select2" data-placeholder="Select..."'); ?>
											</div>

											<div class="col-sm-4 m-form__group-sub m-input--air">
												<?php echo form_dropdown('after_second_starting_day',translate($starting_days),'','id="after_second_starting_day" class="form-control m-select2" data-placeholder="Select..."'); ?>
											</div>
										</div>
									</div>
								</div>
		                    </div>
                    

							<div id='once_a_week' class="form-group m-form__group row pt-0 m--padding-10">
								<div class="col-lg-12 m-form__group-sub m-input--air">
									<label><?php echo translate('When do members contribute?');?><span class="required">*</span></label>
		                            <?php echo form_dropdown('week_day_weekly',translate($week_days),"",'id="week_day_weekly" class="form-control m-select2" data-placeholder="Select day of the week..."'); ?>
								</div>
							</div>

							<div id='once_every_two_weeks' class="form-group m-form__group row pt-0 m--padding-10">
								<div class="col-lg-12 m-form__group-sub">
									<label><?php echo translate('When do members contribute?');?><span class="required">*</span></label>
		                    		<div class="row">
		                        		<div class="col-lg-6 col-sm-12 m-form__group-sub m-input--air">
		                                	<?php echo form_dropdown('week_day_fortnight',translate($every_two_week_days),"",'id="week_day_fortnight" class="form-control m-select2" data-placeholder="Select day of the week..."'); ?>
		                            	</div>
		                            	<div class="col-lg-6 col-sm-12 m-form__group-sub m-input--air">
		                            		<?php echo form_dropdown('week_number_fortnight',translate($week_numbers),"",'id="week_number_fortnight" class="form-control m-select2" data-placeholder="Select week..."'); ?>
		                            	</div>
		                            </div>
		                        </div>
		                    </div>

		                    <div id='once_every_multiple_months' class="form-group m-form__group row pt-0 m--padding-10">
		                    	<div class="col-lg-12 m-form__group-sub">
		                        	<label><?php echo translate('When do members contribute?');?><span class="required">*</span></label>
		                        	<div class="row">
		                            	<div class="col-md-4 col-sm-12 m-form__group-sub m-input--air">
		                                    <?php echo form_dropdown('month_day_multiple',translate($days_of_the_month),"",'id="month_day_multiple" class="form-control m-select2" data-placeholder="Select day of the month..."'); ?>
		                                </div>
		                                <div class="col-md-4 col-sm-12 m-form__group-sub m-input--air">
		                                	<?php echo form_dropdown('week_day_multiple',translate($month_days),"",'id="week_day_multiple" class="form-control m-select2" data-placeholder="Select day of the month..."'); ?>
		                                </div>
		                                <div class="col-md-4 col-sm-12 m-form__group-sub m-input--air">
		                                	<?php echo form_dropdown('start_month_multiple',translate($starting_months),"",'id="start_month_multiple" class="form-control m-select2" data-placeholder="Select starting month.."'); ?>
		                                </div>
		                            </div>
		                        </div>
			                </div>
			        	</div>

			        	<div id='one_time_invoicing_settings'>
			        		<div class="form-group m-form__group row pt-0 m--padding-10" >
			        			<div class="col-sm-6 m-form__group-sub">
			        				<label><?php echo translate('Invoice Date');?><span class="required">*</span></label>
			        				<div class="input-group ">
			        					<?php echo form_input('invoice_date',$this->input->post('invoice_date')?timestamp_to_datepicker(strtotime($this->input->post('invoice_date'))):timestamp_to_datepicker(time()),'class="form-control m-input datepicker" readonly');?>
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="la la-calendar-check-o"></i>
											</span>
										</div>
									</div>
			        			</div>

			        			<div class="col-sm-6 m-form__group-sub">
			        				<label><?php echo translate('Contribution Date');?><span class="required">*</span></label>
			        				<div class="input-group">
										<?php echo form_input('contribution_date',$this->input->post('contribution_date')?timestamp_to_datepicker(strtotime($this->input->post('contribution_date'))):timestamp_to_datepicker(time()),'class="form-control m-input datepicker" readonly');
                   						?> 
										<div class="input-group-append">
											<span class="input-group-text">
												<i class="la la-calendar-check-o"></i>
											</span>
										</div>
									</div>
			        			</div>
			        		</div>
			        	</div>

			        	<div class="form-group m-form__group row pt-0 m--padding-10" id='sms_email_notifications'>
							<div class="col-lg-12 m-form__group-sub">
								<label><?php echo translate('Do you want to send email and/or SMS notification reminders to Members?');?><span class="required">*</span></label>
				                <div class="m-radio-inline">
									<?php 
					                    if($this->input->post('sms_notifications_enabled')==1){
					                        $enable_sms_notification_enable_email_notification = TRUE;
					                        $disable_sms_notification_disable_email_notification = FALSE;
					                    }else if($this->input->post('email_notifications_enabled')==1){
					                        $enable_sms_notification_enable_email_notification = TRUE;
					                        $disable_sms_notification_disable_email_notification = FALSE;
					                    }else{
					                        $enable_sms_notification_enable_email_notification = FALSE;
					                        $disable_sms_notification_disable_email_notification = TRUE;
					                    }
					                ?>
				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('sms_notification_email_notification',1,$enable_sms_notification_enable_email_notification,""); ?>
				                    	<?php echo translate('Yes');?>
				                    	<span></span>
				                    </label>

				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('sms_notification_email_notification',0,$disable_sms_notification_disable_email_notification,""); ?>
				                    	<?php echo translate('No');?>
				                    	<span></span>
				                    </label>
								</div>
							</div>
						</div>

						<div class="m-form__group form-group pt-0 m--padding-10" id="sms_email_notifications_settings">
							<label ><?php echo translate('Select Notification Reminder Option');?></label>
							<div class="m-checkbox-inline">
								<label class="m-checkbox m-checkbox--solid m-checkbox--brand">
									<?php echo form_checkbox('sms_notifications_enabled',1,FALSE,"");?><?php echo translate('Enable SMS Notifications');?>
									<span></span>
								</label>
								<label class="m-checkbox m-checkbox--solid m-checkbox--brand">
									<?php echo form_checkbox('email_notifications_enabled',1,FALSE,"");?><?php echo translate('Enable Email Notifications');?> 
									<span></span>
								</label>
							</div>
						</div>

						<div id="contribution_member_list_settings"  class="form-group m-form__group  pt-0 m--padding-10 row">
							<div class="col-lg-12 m-form__group-sub">
								<label><?php echo translate('Do you wish to limit invoicing for this contribution to specific members');?>?</label>
								<div class="m-radio-inline">
									<?php 
				                        if($this->input->post('enable_contribution_member_list')==1){
					                        $enable_contribution_member_list = TRUE;
					                        $disable_contribution_member_list = FALSE;
					                    }else if($this->input->post('enable_contribution_member_list')==0){
					                        $enable_contribution_member_list = FALSE;
					                        $disable_contribution_member_list = TRUE;
					                    }else{
					                        $enable_contribution_member_list = TRUE;
					                        $disable_contribution_member_list = FALSE;
					                    }
						                    ?>
				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('enable_contribution_member_list',1,$enable_contribution_member_list,""); ?>
				                    	<?php echo translate('Yes');?>
				                    	<span></span>
				                    </label>

				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('enable_contribution_member_list',0,$disable_contribution_member_list,""); ?>
				                    	<?php echo translate('No');?>
				                    	<span></span>
				                    </label>
								</div>
							</div>
						</div>

						<div id='contribution_member_list' class="form-group m-form__group pt-0 m--padding-10 row">
		                   	<div class="col-lg-12 m-form__group-sub m-input--air">
		                   		<label><?php echo translate('Select Members');?></label>
		                        <?php echo form_dropdown('contribution_member_list[]',$this->active_group_member_options,$this->input->post('contribution_member_list'),' id="" class=" form-control m-select2" multiple="multiple" data-placeholder="Select..."'); ?>
		                    </div>
		                </div>
		                <div id="disable_contribution_arrears" class="form-group m-form__group pt-0 m--padding-10 row">
		                	<div class="col-lg-12 m-form__group-sub m-input--air">
		                   		<label><?php echo translate('Do you wish to disable contribution arrears for this contribution');?></label>
		                   		<div class="m-radio-inline">
									<?php 
					                    if($this->input->post('display_contribution_arrears_cumulatively')==1){
					                        $display_contribution_arrears_cumulatively = TRUE;
					                        $disable_contribution_arrears_cumulatively = FALSE;
					                    }else if($this->input->post('display_contribution_arrears_cumulatively')==1){
					                        $display_contribution_arrears_cumulatively = TRUE;
					                        $disable_contribution_arrears_cumulatively = FALSE;
					                    }else{
					                        $display_contribution_arrears_cumulatively = FALSE;
					                        $disable_contribution_arrears_cumulatively = TRUE;
					                    }
					                ?>
				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('display_contribution_arrears_cumulatively',1,$display_contribution_arrears_cumulatively,""); ?>
				                    	<?php echo translate('Yes');?>
				                    	<span></span>
				                    </label>

				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('display_contribution_arrears_cumulatively',0,$disable_contribution_arrears_cumulatively,""); ?>
				                    	<?php echo translate('No');?>
				                    	<span></span>
				                    </label>
								</div>
		                   	</div>
			            </div>

			            <div id="disable_contribution_refund" class="form-group m-form__group pt-0 m--padding-10 row">
		                	<div class="col-lg-12 m-form__group-sub m-input--air">
		                   		<label><?php echo translate('Disable contribution refunds for this contribution.');?></label>
		                   		<div class="m-radio-inline">
									<?php 
					                    if($this->input->post('is_non_refundable')==1){
					                        $display_is_non_refundable = TRUE;
					                        $disable_is_non_refundable = FALSE;
					                    }else if($this->input->post('is_non_refundable')==1){
					                        $display_is_non_refundable = TRUE;
					                        $disable_is_non_refundable = FALSE;
					                    }else{
					                        $display_is_non_refundable = FALSE;
					                        $disable_is_non_refundable = TRUE;
					                    }
					                ?>
				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('is_non_refundable',1,$display_is_non_refundable,""); ?>
				                    	<?php echo translate('Yes');?>
				                    	<span></span>
				                    </label>

				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('is_non_refundable',0,$disable_is_non_refundable,""); ?>
				                    	<?php echo translate('No');?>
				                    	<span></span>
				                    </label>
								</div>
		                   	</div>
			            </div>

			            <div id="disable_enable_is_equity" class="form-group m-form__group pt-0 m--padding-10 row">
		                	<div class="col-lg-12 m-form__group-sub m-input--air">
		                   		<label><?php echo translate('Is this contribution considered as Equity?');?></label>
		                   		<div class="m-radio-inline">
									<?php 
					                    if($this->input->post('is_equity')==1){
					                        $enable_is_equity = TRUE;
					                        $disable_is_equity = FALSE;
					                    }else if($this->input->post('is_equity')==1){
					                        $enable_is_equity = TRUE;
					                        $disable_is_equity = FALSE;
					                    }else{
					                        $enable_is_equity = FALSE;
					                        $disable_is_equity = TRUE;
					                    }
					                ?>
				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('is_equity',1,$enable_is_equity,""); ?>
				                    	<?php echo translate('Yes');?>
				                    	<span></span>
				                    </label>

				                    <label class="m-radio m-radio--solid m-radio--brand">
				                    	<?php echo form_radio('is_equity',0,$disable_is_equity,""); ?>
				                    	<?php echo translate('No');?>
				                    	<span></span>
				                    </label>
								</div>
		                   	</div>
			            </div>

			            <div id="enable_checkoff" class="form-group m-form__group pt-0 m--padding-10 row">
	                        <div class="col-lg-12 m-form__group-sub m-input--air">
	                            <label><?php echo translate('Do you wish to enable checkoff for this contribution');?></label>
	                            <div class="m-radio-inline">
	                                <?php 
	                                    if($this->input->post('enable_checkoff')==1){
	                                        $enable_contribution_checkoff = TRUE;
	                                        $disable_contribution_checkoff = FALSE;
	                                    }else{
	                                        $enable_contribution_checkoff = FALSE;
	                                        $disable_contribution_checkoff = TRUE;
	                                    }
	                                ?>
	                                <label class="m-radio m-radio--solid m-radio--brand">
	                                    <?php echo form_radio('enable_checkoff',1,$enable_contribution_checkoff,""); ?>
	                                    <?php echo translate('Yes');?>
	                                    <span></span>
	                                </label>

	                                <label class="m-radio m-radio--solid m-radio--brand">
	                                    <?php echo form_radio('enable_checkoff',0,$disable_contribution_checkoff,""); ?>
	                                    <?php echo translate('No');?>
	                                    <span></span>
	                                </label>
	                            </div>
	                        </div>
	                    </div>

	                    <div id="enable_deposit_statement_display" class="form-group m-form__group pt-0 m--padding-10 row">
	                        <div class="col-lg-12 m-form__group-sub m-input--air">
	                            <label><?php echo translate('Do you wish to display this report in the member\'s statement report');?></label>
	                            <div class="m-radio-inline">
	                                <?php 
	                                    if($this->input->post('enable_deposit_statement_display')==1){
	                                        $enable_deposit_statement_display = TRUE;
	                                        $disable_deposit_statement_display = FALSE;
	                                    }else{
	                                        $enable_deposit_statement_display = FALSE;
	                                        $disable_deposit_statement_display = TRUE;
	                                    }
	                                ?>
	                                <label class="m-radio m-radio--solid m-radio--brand">
	                                    <?php echo form_radio('enable_deposit_statement_display',1,$enable_deposit_statement_display,""); ?>
	                                    <?php echo translate('Yes');?>
	                                    <span></span>
	                                </label>

	                                <label class="m-radio m-radio--solid m-radio--brand">
	                                    <?php echo form_radio('enable_deposit_statement_display',0,$disable_deposit_statement_display,""); ?>
	                                    <?php echo translate('No');?>
	                                    <span></span>
	                                </label>
	                            </div>
	                        </div>
	                    </div>
					</div>

					<div id ="contribution_fines">
						<fieldset class="m--margin-top-20">
							<legend>Contribution Fines</legend>
							<div class="form-group m-form__group row pt-0 m--padding-10">
								<div class="col-lg-12 m-form__group-sub">
									<label><?php echo translate('Do you charge contribution fines for late payment?');?></label>
									<div class="m-radio-inline">
										<?php 
					                        if($this->input->post('enable_fines')==1){
					                            $enabled_enable_fines = TRUE;
					                            $disable_enable_fines = FALSE;
					                        }else if($this->input->post('enable_fines')==0){
					                            $enabled_enable_fines = FALSE;
					                            $disable_enable_fines = TRUE;
					                        }else{
					                            $enabled_enable_fines = TRUE;
					                            $disable_enable_fines = FALSE;
					                        }
							            ?>
					                    <label class="m-radio m-radio--solid m-radio--brand">
					                    	<?php echo form_radio('enable_fines',1,$enabled_enable_fines,""); ?>
					                    	<?php echo translate('Yes');?>
					                    	<span></span>
					                    </label>

					                    <label class="m-radio m-radio--solid m-radio--brand">
					                    	<?php echo form_radio('enable_fines',0,$disable_enable_fines,""); ?>
					                    	<?php echo translate('No');?>
					                    	<span></span>
					                    </label>
									</div>
								</div>
							</div>

							<div id="fine_setting_row">
								<div class="m-portlet m-portlet--creative m-portlet--bordered-semi pt-0 mt-0 pb-0 mb-5">
									<div class="m-portlet__body m-demo__preview add_setting_fine">
										<div class="form-group m-form__group row">
											<div class="col-lg-12 m-form__group-sub">
												<label><?php echo translate('We charge a');?></label>
							                    <div class="row">
							                        <div class="col-md-4 col-sm-12 m-form__group-sub m-input--air">
							                            <?php echo form_dropdown('fine_type[0]',array(''=>translate('--Select fine type--'))+translate($fine_types),'','id="" class="form-control fine_types m-select2" data-placeholder="--Select fine type--"'); ?>
							                        </div>

							                        <div id='' class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_settings fine_percentage_rate m-input--air">
							                            <?php echo form_input('percentage_rate[0]','','class="form-control percentage_rates" placeholder="Percentage(%) Rate"'); ?>
							                        </div>

							                        <div id='' class="col-md-4 col-sm-12 m-form__group-sub fixed_fine_settings fine_fixed_amount m-input--air">
							                            <?php echo form_input('fixed_amount[0]','','class="form-control currency fixed_amounts m-input--air" placeholder="Enter fixed fine amount"'); ?>
							                        </div>

							                        <div class="col-md-4 col-sm-12 m-form__group-sub fixed_fine_settings fixed_fine_mode m-input--air">
							                            <?php echo form_dropdown('fixed_fine_mode[0]',array(''=>translate('--Select how fines behave--'))+translate($fine_mode_options),'','id="" class="form-control m-select2 fixed_fine_modes" data-placeholder="--Fine charged for?--"'); ?>
							                        </div>

							                        <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_settings percentage_fine_on m-input--air">
							                            <?php echo form_dropdown('percentage_fine_on[0]',array(''=>translate('--Select when fines is calculated based on--'))+translate($percentage_fine_on_options),'','id="" class="form-control percentage_fine_ons m-select2" data-placeholder="Fine charged on?..."'); ?>
							                        </div>
							                    </div>

							                    <div class='percentage_fine_settings m--margin-top-10'>
							                    	<div class="row">
								                        <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_chargeable_on m-input--air">
								                            <?php echo form_dropdown('percentage_fine_chargeable_on[0]',array(''=>translate('--Select when fines are charged--'))+translate($fine_chargeable_on_options),'','id="" class="form-control percentage_fine_chargeable_ons m-select2" data-placeholder="When is fine charged?..."'); ?>
								                        </div>

								                        <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_mode m-input--air">
								                            <?php echo form_dropdown('percentage_fine_mode[0]',array(''=>translate('--Select how fines behave--'))+translate($fine_mode_options),'','id="" class="form-control percentage_fine_modes m-select2" data-placeholder="--Fine charged for?-- "'); ?>
								                        </div>

								                        <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_frequency m-input--air">
								                             <?php echo form_dropdown('percentage_fine_frequency[0]',array(''=>translate('--Select fine frequency--'))+translate($fine_frequency_options),'','id="" class="form-control percentage_fine_frequencies m-select2" data-placeholder="--Select fine charge frequency--"'); ?>
								                        </div>
								                    </div>
							                    </div>
							                    <div class="fixed_fine_settings m--margin-top-10">
								                    <div class='row'>
								                        <div class="col-lg-6 col-sm-12 m-form__group-sub fixed_fine_chargeable_on m-input--air">
								                            <?php echo form_dropdown('fixed_fine_chargeable_on[0]',array(''=>translate('--Select when fines are charged--'))+translate($fine_chargeable_on_options),'','id="" class="form-control fixed_fine_chargeable_ons m-select2" data-placeholder="--When is fine charged?"'); ?>
								                        </div>
								                        <div class="col-lg-6 col-sm-12 m-form__group-sub fixed_fine_settings fixed_fine_frequency m-input--air">
								                            <?php echo form_dropdown('fixed_fine_frequency[0]',array(''=>translate('--Select fine frequency--'))+translate($fine_frequency_options),'','id="" class="form-control fixed_fine_frequencies m-select2" data-placeholder="--Select fine charge frequency"'); ?>
								                        </div>
								                    </div>
								                </div>

							                    <div class='row m--margin-top-10 m--margin-bottom-10 fine_limits_settings'>
							                        <div class="col-lg-12 m-form__group-sub fine_limit m-input--air">
							                            <?php echo form_dropdown('fine_limit[0]',translate($fine_limit_options),'','id="" class="form-control fine_limits m-select2" data-placeholder="--Limit fine to"'); ?>
							                        </div>
							                    </div>
							                </div>					
							            </div>
									</div>
								</div>

								<div id="append-new-fine-setting">
					            </div>


					            <div class="row">
					                <div class="col-md-12">
										<a class="btn btn-default btn-sm" id="add-new-fine-line">
											<i class="la la-plus"></i><?php echo translate('Add Another Fine');?>
										</a>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
					<div class="m--margin-top-50">
						<div class="col-lg-12 col-md-12">
							<span class="float-lg-right float-md-left float-sm-left float-xl-right">
								<button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_contribution_button" type="button">
									<?php echo translate('Save Changes');?>
								</button>
								&nbsp;&nbsp;
								<button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_create_contribution_form">
									<?php echo translate('Cancel');?>
								</button>
							</span>
						</div>
					</div>
				<?php echo form_close();?>
			</div>
		</div>

		<div class="fine_settings_addition d-none m--hide">	
			<div class="new_fine">
				<div class="m-portlet m-portlet--creative m-portlet--bordered-semi pt-0 mt-0 pb-0 mb-5">
					<div class="m-portlet__body m-demo__preview add_setting_fine">
						<div class="form-group m-form__group row">
							<div class="col-lg-12 m-form__group-sub">
								<label><?php echo translate('We charge a');?></label>
								<a href="javascript:;" class="m-badge m-badge--danger m-badge--wide bage-link float-right remove-fine-setting">Remove</a>
			                    <div class="row">
			                        <div class="col-md-4 col-sm-12 m-form__group-sub m-input--air">
			                            <?php echo form_dropdown('fine_type[]',array(''=>translate('--Select fine type--'))+translate($fine_types),'','id="" class="form-control fine_types m-select2-append" data-placeholder="--Select fine type--"'); ?>
			                        </div>

			                        <div id='' class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_settings fine_percentage_rate m-input--air">
			                            <?php echo form_input('percentage_rate[]','','class="form-control percentage_rates" placeholder="Percentage(%) Rate"'); ?>
			                        </div>

			                        <div id='' class="col-md-4 col-sm-12 m-form__group-sub fixed_fine_settings fine_fixed_amount m-input--air">
			                            <?php echo form_input('fixed_amount[]','','class="form-control currency fixed_amounts m-input--air" placeholder="Enter fixed fine amount"'); ?>
			                        </div>

			                        <div class="col-md-4 col-sm-12 m-form__group-sub fixed_fine_settings fixed_fine_mode m-input--air">
			                            <?php echo form_dropdown('fixed_fine_mode[]',array(''=>translate('--Select how fines behave--'))+translate($fine_mode_options),'','id="" class="form-control m-select2-append fixed_fine_modes" data-placeholder="--Fine charged for?--"'); ?>
			                        </div>

			                        <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_settings percentage_fine_on m-input--air">
			                            <?php echo form_dropdown('percentage_fine_on[]',array(''=>translate('--Select when fines is calculated based on--'))+translate($percentage_fine_on_options),'','id="" class="form-control percentage_fine_ons m-select2-append" data-placeholder="Fine charged on?..."'); ?>
			                        </div>
			                    </div>

			                    <div class='percentage_fine_settings m--margin-top-10'>
			                    	<div class="row">
				                        <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_chargeable_on m-input--air">
				                            <?php echo form_dropdown('percentage_fine_chargeable_on[]',array(''=>translate('--Select when fines are charged--'))+translate($fine_chargeable_on_options),'','id="" class="form-control percentage_fine_chargeable_ons m-select2-append" data-placeholder="When is fine charged?..."'); ?>
				                        </div>

				                        <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_mode m-input--air">
				                            <?php echo form_dropdown('percentage_fine_mode[]',array(''=>translate('--Select how fines behave--'))+translate($fine_mode_options),'','id="" class="form-control percentage_fine_modes m-select2-append" data-placeholder="--Fine charged for?-- "'); ?>
				                        </div>

				                        <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_frequency m-input--air">
				                             <?php echo form_dropdown('percentage_fine_frequency[]',array(''=>translate('--Select fine frequency--'))+translate($fine_frequency_options),'','id="" class="form-control percentage_fine_frequencies m-select2-append" data-placeholder="--Select fine charge frequency--"'); ?>
				                        </div>
				                    </div>
			                    </div>
			                    <div class="fixed_fine_settings m--margin-top-10">
				                    <div class='row'>
				                        <div class="col-lg-6 col-sm-12 m-form__group-sub fixed_fine_chargeable_on m-input--air">
				                            <?php echo form_dropdown('fixed_fine_chargeable_on[]',array(''=>translate('--Select when fines are charged--'))+translate($fine_chargeable_on_options),'','id="" class="form-control fixed_fine_chargeable_ons m-select2-append" data-placeholder="--When is fine charged?"'); ?>
				                        </div>
				                        <div class="col-lg-6 col-sm-12 m-form__group-sub fixed_fine_settings fixed_fine_frequency m-input--air">
				                            <?php echo form_dropdown('fixed_fine_frequency[]',array(''=>translate('--Select fine frequency--'))+translate($fine_frequency_options),'','id="" class="form-control fixed_fine_frequencies m-select2-append" data-placeholder="--Select fine charge frequency"'); ?>
				                        </div>
				                    </div>
				                </div>

			                    <div class='row m--margin-top-10 m--margin-bottom-10 fine_limits_settings'>
			                        <div class="col-lg-12 m-form__group-sub fine_limit m-input--air">
			                            <?php echo form_dropdown('fine_limit[]',translate($fine_limit_options),'','id="" class="form-control fine_limits m-select2-append" data-placeholder="--Limit fine to"'); ?>
			                        </div>
			                    </div>
			                </div>
			            </div>
		            </div>
		        </div>
	        </div>
		</div>	
	</div>
	<!---->

	<!-- create loan types-->
	<div id="create_group_loan_types_panel">
		<div class="m-form__section m-form__section--first">
			<div class="m-form__heading pb-3">
				<div class="row">
					<div class="col-lg-6 col-sm-12">
						<h3 class="m-form__heading-title"><?php echo translate($this->application_settings->entity_name . ' Loan Types');?></h3>
					</div>
					<div class="col-lg-6 col-sm-12">
						<a href="#" class="btn btn-primary m-btn m-btn--icon float-right btn-sm" id="create_loan_type_header">
							<span>
								<i class="la la-plus"></i>
								<span><?php echo translate('Create Loan Type');?></span>
							</span>
						</a>
					</div>
				</div>
			</div>
			<div class="load_group_loan_types datatable_loan_types table-responsive-sm">
				<table class="table table-striped- table-bordered table-hover table-checkable loan_types setup_table" id="loan_types_listing">
				   	<thead>
				      	<tr>
					        <th class="numbering">#</th>
					        <th class="name"><?php echo translate('Name');?></th>
					        <th><?php echo translate('Loan Details');?></th>
					        <th><?php echo translate('Other Details');?></th>
					        <th><?php echo translate('Actions');?></th>
				      </tr>
				   </thead>
				   <tbody>
				   </tbody>
				</table>
			</div>
			<div class="create_loan_type_settings_layout">
				<div class="form-group m-form__group row pt-0 m--padding-10" id="create_loan_type_options">
					<div class="col-lg-12 m-form__group-sub">
						<label><?php echo translate('Does your group give loans to members or debtors');?>?</label>
						<div class="m-radio-inline">
							<?php 
		                        if($this->group->group_offer_loans==1){
			                        $enable_activate_loans = TRUE;
			                        $disable_activate_loans = FALSE;
			                    }else{
			                        $enable_activate_loans = FALSE;
			                        $disable_activate_loans = TRUE;
			                    }
				                    ?>
		                    <label class="m-radio m-radio--solid m-radio--brand">
		                    	<?php echo form_radio('group_offer_loans',1,$enable_activate_loans,""); ?>
		                    	<?php echo translate('Yes');?>
		                    	<span></span>
		                    </label>

		                    <label class="m-radio m-radio--solid m-radio--brand">
		                    	<?php echo form_radio('group_offer_loans',0,$disable_activate_loans,""); ?>
		                    	<?php echo translate('No');?>
		                    	<span></span>
		                    </label>
						</div>
					</div>
				</div>

				<div id="create_loan_type_setting">
					<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_loan_type"'); ?>
						<div class="form-group m-form__group row pt-0 m--padding-10">
							<div class="col-sm-6 m-form__group-sub">
								<label><?php echo translate('Loan Type Name');?><span class="required">*</span></label>
								<?php echo form_input('name',$this->input->post('name'),'class="form-control name m-input--air" placeholder="Loan Type Name"'); ?>
								<span class="m-form__help"><?php echo translate('eg. Emergency Loan');?></span>
							</div>
				
							<div class="col-sm-6 m-form__group-sub m-input--air">
								<label><?php echo translate('Loan Amount Type');?><span class="required">*</span></label>
								 <?php echo form_dropdown('loan_amount_type',array(''=>'--'.translate('Select Loan Amount Type').'--')+translate($loan_amount_type),$this->input->post('loan_amount_type'),'class="m-select2 form-control " id ="loan_amount_type" data-placeholder="Select..."  ');?>
							</div>
						</div>
						<?php echo form_hidden('id','');?>
						<div class="loan_amount_savings_input_group form-group m-form__group pt-0 m--padding-10" id='loan_amount_savings_input_group'>
							<div class=" row">
								<div class="col-lg-12 m-form__group-sub">
									<label><?php echo translate('How many times on member savings');?> (Eg. 3)?<span class="required">*</span> </label>
									<div class="row">
										<div class="col-lg-4 col-sm-4 m-form__group-sub"> 
					                        <?php echo form_input('',"",'  class="form-control currency" id="" autocomplete="off" disabled placeholder="Times"'); ?>                   
					                    </div>
					                    <div class="col-lg-4 col-sm-4 m-form__group-sub"> 
					                    	<?php echo form_input('loan_times_number',$this->input->post('loan_times_number'),'  class="form-control currency m-input--air" id="loan_times_number" autocomplete="off"  placeholder="Times Number"'); ?>
					                    </div>
					                    <div class="col-lg-4 col-sm-4 m-form__group-sub">
					                        <?php echo form_input('',"",'  class="form-control currency" id="" autocomplete="off"  disabled placeholder="of Member savings"'); ?>
					                    </div>
									</div>
								</div>
							</div>
						</div>

						<div class="loan_amount_input_group form-group m-form__group pt-0 m--padding-10" id='loan_amount_input_group'>
							<div class=" row">
								<div class="col-sm-6 m-form__group-sub">
			            			<label><?php echo translate('Minimum loan amount');?><span class="required">*</span></label>
			            			<?php echo form_input('minimum_loan_amount',$this->input->post('minimum_loan_amount'),'  class="form-control currency m-input--air" placeholder="Minimum Amount"'); ?>
			            		</div>
			            		<div class="col-sm-6 m-form__group-sub">
			            			<label><?php echo translate('Maximum loan amount');?><span class="required">*</span></label>
			            			<?php echo form_input('maximum_loan_amount',$this->input->post('maximum_loan_amount'),'  class="form-control currency m-input--air" placeholder="Maximum Amount"'); ?>
			            		</div>
							</div>
						</div>

						<div class="form-group m-form__group interest_type_input_group pt-0 m--padding-10">
							<div class="row">
								<div class="col-sm-4 m-form__group-sub">
									<label>Interest Type<span class="required">*</span></label>
									<?php echo form_dropdown('interest_type',array(''=>'--Select Loan Interest Type--')+$interest_types,$this->input->post('interest_type'),'class="form-control m-select2 interest_type" id = "interest_type"  ');?>
								</div>
								<div class="col-sm-8 m-form__group-sub">
									<div class="not_for_custom_settings">
										<div class="row">
											<div class="col-sm-6 m-form__group-sub">
												<label><?php echo translate('Loan Interest Rate');?><span class="required">*</span></label>
												<?php echo form_input('interest_rate',$this->input->post('interest_rate'),'  class="form-control numeric m-input--air" placeholder="Loan Interest Rate"'); ?>
											</div>
											<div class="col-sm-6 m-form__group-sub m-input--air">
												<label ><?php echo translate('Loan Interest Rate Per');?><span class="required">*</span></label>   
												<?php echo form_dropdown('loan_interest_rate_per',translate($loan_interest_rate_per),4,'class="form-control m-select2 interest_type" id = "loan_interest_rate_per"  ') ?>
											</div>
										</div>
									</div>

									<div class="for_custom_settings">
										<div class="row">
											<div class="col-sm-12 m-form__group-sub">
												<label><?php echo translate('Loan Interest Rate');?><span class="required">*</span></label>
												<?php echo form_input('interest_rate',$this->input->post('interest_rate'),'  class="form-control numeric" disabled="disabled" placeholder="Custom fields"'); ?>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="m-form__group form-group pt-0 m--padding-top-10" id="enable_reducing_balance_installment_recalculation">
								<label ><?php echo translate('Select to enable');?></label>
								<div class="m-checkbox-inline">
									<label class="m-checkbox m-checkbox--solid m-checkbox--brand">
										<?php echo form_checkbox('enable_reducing_balance_installment_recalculation',1,FALSE,"");?><?php echo translate('Enable Reducing Balance Recalulation on Early Installment Repayment');?>
										<span></span>
									</label>
								</div>
							</div>
						</div>

						<div class="m-form__group form-group row pt-0 m--padding-10" id="grace_period">
							<div class="col-sm-12 m-form__group-sub m-input--air">
								<label><?php echo translate('Loan Grace Period');?><span class="required">*</span></label>
								<?php echo form_dropdown('grace_period',array(''=> '--Select Loan Grace Period--')+translate($loan_grace_periods)+array('date'=>translate("Custom Date")),$this->input->post('grace_period'),'class="form-control m-select2 grace_period" id = "grace_period"  ') ?>
							</div>
			            </div> 

			            <div class="m-form__group form-group loan_repayment_period_input_group pt-0 m--padding-10">
			            	<div class="row">
						        <div class="m-form__group-sub m-input--air col-sm-4">
					                <label>
					                	<?php echo translate('Loan Repayment Period Type');?><span class="required">*</span>
					                </label>
			                        <?php echo form_dropdown('loan_repayment_period_type',array(''=>'--Select repayment period type--')+translate($loan_repayment_period_type),$this->input->post('loan_repayment_period_type'),'class="form-control m-select2 loan_repayment_period_type" id = "loan_repayment_period_type"  ') ?>
						        </div> 


						        <div class="col-sm-8">
						        	<div class="fixed_repayment_period">
							         	<label><?php echo translate('Fixed Repayment Period');?><span class="required">*</span></label>
							         	<?php echo form_input('fixed_repayment_period',$this->input->post('fixed_repayment_period'),'  class="form-control numeric m-input--air" placeholder="Fixed Repayment Period"'); ?>
							        		<span class="m-form__help"><?php echo translate('Value in months eg.2');?></span>
						        	</div> 

						        	<div class="varying_repayment_period">
							        	<div class="row">
							        		<div class="m-form__group-sub col-sm-6">
							        			<label><?php echo translate('Minimum Repayment Period');?><span class="required">*</span></label>
							        			<?php echo form_input('minimum_repayment_period',$this->input->post('minimum_repayment_period'),'  class="form-control numeric m-input--air" placeholder="Minimum Repayment Period"'); ?>
							        			<span class="m-form__help"><?php echo translate('Eg.2');?></span>
							        		</div>
							        		<div class="m-form__group-sub col-sm-6">
							        			<label><?php echo translate('Maximum Repayment Period');?><span class="required">*</span></label>
							        			<?php echo form_input('maximum_repayment_period',$this->input->post('maximum_repayment_period'),'  class="form-control numeric m-input--air" placeholder="Maximum Repayment Period"'); ?>
							        			<span class="m-form__help"><?php echo translate('Eg.5');?></span>
							        		</div>
							        	</div>
						        	</div>
						    	</div>  
						   	</div>   
				        </div> 

				        <div class="addition_loan_types_form_details">   
				        	<fieldset class="m--margin-top-20">
				        		<legend><?php echo translate('Fine Details');?></legend>
				        		<div class="form-group m-form__group row pt-0 m--padding-10">
									<div class="col-lg-12 m-form__group-sub">
										<label class="form-control-label"><?php echo translate('Do you charge fines for late monthly loan installment payments');?>?:</label>
										<div class="m-radio-inline">
											<?php 
						                        if($this->input->post('enable_loan_fines')==1){
						                            $enabled_loan_fines = TRUE;
						                            $disabled_loan_fines = FALSE;
						                        }else if($this->input->post('enable_loan_fines')==0){
						                            $enabled_loan_fines = FALSE;
						                            $disabled_loan_fines = TRUE;
						                        }else{
						                            $enabled_loan_fines = TRUE;
						                            $disabled_loan_fines = FALSE;
						                        }
						                    ?>
						                    <label class="m-radio m-radio--solid m-radio--brand">
						                    	<?php echo form_radio('enable_loan_fines',1,$enabled_loan_fines,""); ?>
						                    	<?php echo translate('Yes');?>
						                    	<span></span>
						                    </label>

						                    <label class="m-radio m-radio--solid m-radio--brand">
						                    	<?php echo form_radio('enable_loan_fines',0,$disabled_loan_fines,""); ?>
						                    	<?php echo translate('No');?>
						                    	<span></span>
						                    </label>
										</div>
									</div>
								</div>

								<div class="enable_loan_fines_settings">
									<div class="m-form__group form-group row loan_fine_type pt-0 m--padding-10">
										<div class="m-form__group-sub m-input--air col-sm-12 m-input--air">
				                        	<label><?php echo translate('What type of Late Loan Payment fine do you Charge');?>? <span class="required">*</span></label>
				                        	<?php echo form_dropdown('loan_fine_type',array(''=>'--Select  the Type of Fine Charged--')+translate($late_loan_payment_fine_types),$this->input->post('loan_fine_type'),'class="form-control m-select2 loan_fine_type" id = "loan_fine_type"  ') ?>
				                        </div>
				                    </div>

				                    <div class="late_loan_payment_fixed_fine">
				                     	<div class="m-form__group form-group row pt-0 m--padding-10">
				                     		<div class="m-form__group-sub m-input--air col-sm-4">
				                     			<label><?php echo translate('Fixed Amount charge');?><span class="required">*</span></label>
				                     			<?php echo form_input('fixed_fine_amount',$this->input->post('fixed_fine_amount'),'  class="form-control currency fixed_fine_amount m-input--air" placeholder="Fixed Fine Amount"'); ?>
				                     		</div>

				                     		<div class="m-form__group-sub m-input--air col-sm-4 m-input--air">
				                     			 <label><?php echo translate('Fixed Amount Fine Frequecy');?><span class="required">*</span></label>
				                     			 <?php echo form_dropdown('fixed_amount_fine_frequency',array(''=>'--Select  the fine frequency--')+translate($late_payments_fine_frequency),$this->input->post('fixed_amount_fine_frequency'),'class="form-control m-select2 fixed_amount_fine_frequency" id = "fixed_amount_fine_frequency"  ') ?>
				                     		</div>

				                     		<div class="m-form__group-sub m-input--air col-sm-4 m-input--air">
				                     			<label><?php echo translate('Fixed Amount Fine Frequecy On');?><span class="required">*</span></label>
				                     			<?php echo form_dropdown('fixed_amount_fine_frequency_on',array(''=>'--Select  the fine frequency On--')+translate($fixed_amount_fine_frequency_on),$this->input->post('fixed_amount_fine_frequency_on'),'class="form-control m-select2 fixed_amount_fine_frequency_on" id = "fixed_amount_fine_frequency_on"  ') ?>
				                     		</div>
				                     	</div>
				                    </div>


				                    <div class="late_loan_payment_percentage_fine">
				                    	<div class="m-form__group form-group row pt-0 m--padding-10">
				                    		<div class="m-form__group-sub col-sm-4">
				                    			<label><?php echo translate('Fine Percentage Rate');?>(%)<span class="required">*</span></label>
				                    			<?php echo form_input('percentage_fine_rate',$this->input->post('percentage_fine_rate'),'  class="form-control numeric percentage_fine_rate m-input--air" placeholder="Fine Percentage Rate"'); ?>
				                    		</div>

				                    		<div class="m-form__group-sub m-input--air col-sm-4">
				                    			<label><?php echo translate('Fine Frequecy');?><span class="required">*</span></label>
				                    			<?php echo form_dropdown('percentage_fine_frequency',array(''=>'--Select  the fine frequecy--')+translate($late_payments_fine_frequency),$this->input->post('percentage_fine_frequency'),'class="form-control m-select2 percentage_fine_frequency" id = "percentage_fine_frequency"  ') ?>
				                    		</div>

				                    		<div class="m-form__group-sub m-input--air col-sm-4">
				                    			<label><?php echo translate('Fine Charge on');?> <span class="required">*</span></label>
				                    			<?php echo form_dropdown('percentage_fine_on',array(''=>'--Select where is Fine Charged On--')+translate($percentage_fine_on),$this->input->post('percentage_fine_on'),'class="form-control m-select2 percentage_fine_on" id = "percentage_fine_on"  ') ?>
				                    		</div>
				                    	</div>
				                    </div>

				                    <div class="late_loan_repayment_one_off_fine">
				                    	<div class="m-form__group form-group row pt-0 m--padding-10">
				                    		<div class="m-form__group-sub col-sm-4 m-input--air">
				                    			<label><?php echo translate('Select One Off Fine Type');?> <span class="required">*</span></label>
				                    			<?php echo form_dropdown('one_off_fine_type',array(''=>'--Select one Off fine Type--')+translate($one_off_fine_types),$this->input->post('one_off_fine_type'),'class="form-control m-select2 one_off_fine_type" id = "one_off_fine_type"  ') ?>
				                    		</div>

				                    		<div class="m-form__group-sub col-sm-8">
				                    			<div class="row">
				                    				<div class="one_off_fixed_amount_setting one_off_fixed_amount col-sm-12">
					                    				<div class="m-form__group-sub">
					                    					<label><?php echo translate('One Off Fixed Amount');?><span class="required">*</span></label>
					                    					<?php echo form_input('one_off_fixed_amount',$this->input->post('one_off_fixed_amount'),'  class="form-control currency fixed_fine_amount m-input--air" placeholder="One Off Fixed Amount"'); ?>
					                    				</div>
					                    			</div>
				                    				<div class="one_off_percentage_setting">
				                    					<div class="row">
				                    						<div class="m-form__group-sub col-sm-6">
				                    							<label><?php echo translate('One Off Percentage');?> (%)<span class="required">*</span></label>
				                    							<?php echo form_input('one_off_percentage_rate',$this->input->post('one_off_percentage_rate'),'  class="form-control numeric one_off_percentage_rate m-input--air" placeholder="One Off Percentage Rate"'); ?>
				                    						</div>

				                    						<div class="m-form__group-sub col-sm-6 m-input--air">
				                    							<label><?php echo translate('One Off Percentage on');?><span class="required">*</span></label>
				                    							<?php echo form_dropdown('one_off_percentage_rate_on',array(''=>'--Select One Off Percentage on--')+translate($one_off_percentage_rate_on),$this->input->post('one_off_percentage_rate_on'),'class="one_off_percentage_rate_on form-control m-select2" id = "one_off_percentage_rate_on"  ') ?>
				                    						</div>
				                    					</div>
				                    				</div>
				                    			</div>
				                    		</div>
				                    	</div>
				                    </div>
								</div>

								<div class="form-group m-form__group row pt-0 m--padding-10">
									<div class="col-lg-12 m-form__group-sub">
										<label class="form-control-label"><?php echo translate('Do you charge fines for outstanding loan balance at the end of the loan');?>?:</label>
										<div class="m-radio-inline">
											<?php 
						                        if($this->input->post('enable_outstanding_loan_balance_fines')==1){
						                            $enabled_loan_outstanding_fines = TRUE;
						                            $disabled_loan_outstanding_fines = FALSE;
						                        }else if($this->input->post('enable_outstanding_loan_balance_fines')==0){
						                            $enabled_loan_outstanding_fines = FALSE;
						                            $disabled_loan_outstanding_fines = TRUE;
						                        }else{
						                            $enabled_loan_outstanding_fines = TRUE;
						                            $disabled_loan_outstanding_fines = FALSE;
						                        }
						                    ?>
						                    <label class="m-radio m-radio--solid m-radio--brand">
						                    	<?php echo form_radio('enable_outstanding_loan_balance_fines',1,$enabled_loan_outstanding_fines,""); ?>
						                    	<?php echo translate('Yes');?>
						                    	<span></span>
						                    </label>

						                    <label class="m-radio m-radio--solid m-radio--brand">
						                    	<?php echo form_radio('enable_outstanding_loan_balance_fines',0,$disabled_loan_outstanding_fines,""); ?>
						                    	<?php echo translate('No');?>
						                    	<span></span>
						                    </label>
										</div>
									</div>
								</div>

								<div class="enable_outstanding_loan_balances_fines_settings">
									<div class="m-form__group form-group row pt-0 m--padding-10">
										<div class="m-form__group-sub m-input--air col-sm-12">
											<label><?php echo translate('What type of fine do you charge for outstanding balances');?>? <span class="required">*</span></label>
											<?php echo form_dropdown('outstanding_loan_balance_fine_type',array(''=>'--Select Oustanding Loan Balance fine type--')+translate($late_loan_payment_fine_types),$this->input->post('outstanding_loan_balance_fine_type'),'class="form-control m-select2 outstanding_loan_balance_fine_type" id = "outstanding_loan_balance_fine_type"  ') ?>
										</div>
									</div>

									<div class="outstanding_loan_balance_fixed_fine">
										<div class="m-form__group form-group row pt-0 m--padding-10">
											<div class="m-form__group-sub col-sm-6">
												<label><?php echo translate('Fixed Fine Amount Charged for Outstanding Balances');?><span class="required">*</span></label>
												 <?php echo form_input('outstanding_loan_balance_fine_fixed_amount',$this->input->post('outstanding_loan_balance_fine_fixed_amount'),'  class="form-control m-input--air currency outstanding_loan_balance_fine_fixed_amount" placeholder="Outsanding Loan Balance Fixed Fine Amount"'); ?>
											</div>
											<div class="m-form__group-sub col-sm-6 m-input--air">
												<label><?php echo translate('Frequecy to be Charged on Fixed Amount');?><span class="required">*</span></label>
												<?php echo form_dropdown('outstanding_loan_balance_fixed_fine_frequency',array(''=>'--Select  the fine frequency--')+translate($late_payments_fine_frequency),$this->input->post('outstanding_loan_balance_fixed_fine_frequency'),'class="form-control m-select2 outstanding_loan_balance_fixed_fine_frequency" id = "outstanding_loan_balance_fixed_fine_frequency"  ') ?>
											</div>
										</div>
									</div>

									<div class="outstanding_loan_balance_percentage_settings">
										<div class="m-form__group form-group row pt-0 m--padding-10">
											<div class="m-form__group-sub col-sm-4">
												<label><?php echo translate('Percentage Fine Rate');?><span class="required">*</span></label>
												<?php echo form_input('outstanding_loan_balance_percentage_fine_rate',$this->input->post('outstanding_loan_balance_percentage_fine_rate'),'  class="form-control numeric outstanding_loan_balance_percentage_fine_rate m-input--air" placeholder="Percentage Fine Rate"'); ?>
											</div>
											<div class="m-form__group-sub col-sm-4">
												<label><?php echo translate('Fine Frequecy');?><span class="required">*</span></label>
												<?php echo form_dropdown('outstanding_loan_balance_percentage_fine_frequency',array(''=>'--Select  the fine frequecy--')+translate($late_payments_fine_frequency),$this->input->post('outstanding_loan_balance_percentage_fine_frequency'),'class="form-control m-select2 outstanding_loan_balance_percentage_fine_frequency" id = "outstanding_loan_balance_percentage_fine_frequency"  ') ?>
											</div>
											<div class="m-form__group-sub col-sm-4 m-input--air">
												<label><?php echo translate('Fine Charge on');?><span class="required">*</span></label>
												<?php echo form_dropdown('outstanding_loan_balance_percentage_fine_on',array(''=>'--Select where is Fine Charged On--')+translate($percentage_fine_on),$this->input->post('outstanding_loan_balance_percentage_fine_on'),'class="form-control m-select2 outstanding_loan_balance_percentage_fine_on" id = "outstanding_loan_balance_percentage_fine_on"  ') ?>
											</div>
										</div>
									</div>

									<div class="outstanding_loan_balance_fine_one_off_settings">
										<div class="m-form__group form-group row pt-0 m--padding-10">
											<div class="m-form__group-sub col-sm-12">
												<label><?php echo translate('One Off Amount Charged for Oustanding Balances');?><span class="required">*</span></label>
												<?php echo form_input('outstanding_loan_balance_fine_one_off_amount',$this->input->post('outstanding_loan_balance_fine_one_off_amount'),'  class="form-control currency outstanding_loan_balance_fine_one_off_amount" placeholder="Outsanding Loan Balance One Off Fine Amount"'); ?>
											</div>
										</div>
									</div>
								</div>
				        	</fieldset>

				        	<fieldset class="m--margin-top-20">
				        		<legend><?php echo translate('General Details');?></legend>
				        		<div class="form-group m-form__group row pt-0 m--padding-10">
									<div class="col-lg-12 m-form__group-sub">
										<label class="form-control-label"><?php echo translate('Do you wish to enable guarantors for this loan type');?>?:</label>
										<div class="m-radio-inline">
											<?php 
						                        if($this->input->post('enable_loan_guarantors')==1){
						                            $enable_loan_guarantors = TRUE;
						                            $disabled_loan_guarantor = FALSE;
						                        }else if($this->input->post('enable_loan_guarantors')==0){
						                            $enable_loan_guarantors = FALSE;
						                            $disabled_loan_guarantor = TRUE;
						                        }else{
						                            $enable_loan_guarantors = TRUE;
						                            $disabled_loan_guarantor = FALSE;
						                        }
						                    ?>
						                    <label class="m-radio m-radio--solid m-radio--brand">
						                    	<?php echo form_radio('enable_loan_guarantors',1,$enable_loan_guarantors,""); ?>
						                    	<?php echo translate('Yes');?>
						                    	<span></span>
						                    </label>

						                    <label class="m-radio m-radio--solid m-radio--brand">
						                    	<?php echo form_radio('enable_loan_guarantors',0,$disabled_loan_guarantor,""); ?>
						                    	<?php echo translate('No');?>
						                    	<span></span>
						                    </label>
										</div>
									</div>
								</div>

								<div class="loan_guarantor_additional_details">
									<?php 
					                    if($this->input->post('loan_guarantors_type') == 1){
					                        $enable_loan_guarantor_every_time = TRUE;
					                        $enable_loan_guarantor_over_maximum = FALSE;
					                    }else if($this->input->post('loan_guarantors_type') == 2){
					                        $enable_loan_guarantor_every_time = FALSE;
					                        $enable_loan_guarantor_over_maximum = TRUE;
					                    }else{
					                        $enable_loan_guarantor_every_time = FALSE;
					                        $enable_loan_guarantor_over_maximum = FALSE;
					                    }
					                ?>

					                <div class="form-group m-form__group row pt-0 m--padding-10">
										<div class="col-lg-12 m-form__group-sub">
											<label class="form-control-label"><?php echo translate('When are guarantors required');?>?:</label>
											<div class="m-radio-inline">
												<label class="m-radio m-radio--solid m-radio--brand">
							                    	<?php echo form_radio('loan_guarantors_type',1,$enable_loan_guarantor_every_time,""); ?>
							                    	<?php echo translate('Every time applicant is  applying  a loan');?>
							                    	<span></span>
							                    </label>

							                    <label class="m-radio m-radio--solid m-radio--brand">
							                    	<?php echo form_radio('loan_guarantors_type',2,$enable_loan_guarantor_over_maximum,""); ?>
							                    	<?php echo translate('When an applicant loan request exceeds loan limit ');?>
							                    	<span></span>
							                    </label>
							                </div>
							            </div>
							        </div>

							        <div class="guarantor_settings_holder_every_time">
							        	<div class="m-form__group form-group row pt-0 m--padding-10">
											<div class="m-form__group-sub col-sm-12">
												<label><?php echo translate('Minimum Allowed Guarantors');?><span class="required">*</span></label>
												<?php echo form_input('minimum_guarantors',$this->input->post('minimum_guarantors'),'  class="form-control numeric m-input--air" placeholder="Minimum Allowed Guarantors"'); ?>
											</div>
										</div>
									</div>

									<div class="guarantor_settings_holder_every_savings">
										<div class="m-form__group form-group row pt-0 m--padding-10">
											<div class="m-form__group-sub col-sm-12">
												<label><?php echo translate('Minimum Allowed Guarantors');?><span class="required">*</span></label>
												<?php echo form_input('minimum_guarantors_exceed_amount',$this->input->post('minimum_guarantors_exceed_amount'),'  class="form-control numeric m-input--air" placeholder="Minimum Allowed Guarantors"'); ?>
											</div>
										</div>
									</div>
								</div>

								<div class="m-form__group form-group row pt-0 m--padding-10">
									<div class="col-lg-12 m-form__group-sub">
										<label class="form-control-label"><?php echo translate('Do you charge loan processing fee');?>?:</label>
										<div class="m-radio-inline">
											<?php 
						                        if($this->input->post('enable_loan_processing_fee')==1){
						                            $enable_loan_processing_fee = TRUE;
						                            $disabled_loan_processing_fee = FALSE;
						                        }else if($this->input->post('enable_loan_processing_fee')==0){
						                            $enable_loan_processing_fee = FALSE;
						                            $disabled_loan_processing_fee = TRUE;
						                        }else{
						                            $enable_loan_processing_fee = TRUE;
						                            $disabled_loan_processing_fee = FALSE;
						                        }
						                    ?>
						                    <label class="m-radio m-radio--solid m-radio--brand">
						                    	<?php echo form_radio('enable_loan_processing_fee',1,$enable_loan_processing_fee,""); ?>
						                    	<?php echo translate('Yes');?>
						                    	<span></span>
						                    </label>

						                    <label class="m-radio m-radio--solid m-radio--brand">
						                    	<?php echo form_radio('enable_loan_processing_fee',0,$disabled_loan_processing_fee,""); ?>
						                    	<?php echo translate('No');?>
						                    	<span></span>
						                    </label>
										</div>
									</div>
								</div>

								<div class="loan_processing_fee_settings">
									<div class="m-form__group form-group pt-0 m--padding-10">
										<div class="row">
											<div class="m-form__group-sub col-sm-4">
												<label><?php echo translate('Loan processing fee type');?><span class="required">*</span></label>
												<?php echo form_dropdown('loan_processing_fee_type',array(''=>'--Select Type--')+translate($loan_processing_fee_types),$this->input->post('loan_processing_fee_type'),'class="form-control m-select2 loan_processing_fee_type" id = "loan_processing_fee_type"  ') ?>
											</div>
											<div class="m-form__group-sub col-sm-8">
												<div class="fixed_amount_processing_fee_settings">
													<div class="m-form__group-sub col-sm-12">
														<label><?php echo translate('Processing fee');?><span class="required">*</span></label>
														<?php echo form_input('loan_processing_fee_fixed_amount',$this->input->post('loan_processing_fee_fixed_amount'),'  class="form-control currency loan_processing_fee_fixed_amount m-input--air" id="loan_processing_fee_fixed_amount" placeholder="Enter processing fee amount"'); ?>
													</div>
												</div>

												<div class="percentage_loan_processing_fee">
													<div class="row">
														<div class="m-form__group-sub col-sm-6 m-input--air">
															<label><?php echo translate('Processing fee');?> (%)<span class="required">*</span></label>
															<?php echo form_input('loan_processing_fee_percentage_rate',$this->input->post('loan_processing_fee_percentage_rate'),'  class="form-control numeric loan_processing_fee_percentage_rate m-input--air" placeholder="Processing Fee Percentage"'); ?>
														</div>
														<div class="m-form__group-sub col-sm-6 m-input--air">
															<label><?php echo translate('Charge on');?><span class="required">*</span></label>
															<?php echo form_dropdown('loan_processing_fee_percentage_charged_on',array(''=>'--Select where Percentage is charged on--')+translate($loan_processing_fee_percentage_charged_on),$this->input->post('loan_processing_fee_percentage_charged_on'),'class="form-control m-select2 loan_processing_fee_percentage_charged_on" id = "loan_processing_fee_percentage_charged_on"  ') ?>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

				        	</fieldset>
				        </div>

				        <div class="m--margin-top-50">
							<div class="col-lg-12 col-md-12">
								<span class="float-lg-right float-md-left float-sm-left float-xl-right">
									<button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_loan_type_button" type="button">
										<?php echo translate('Save Changes');?>
									</button>
									&nbsp;&nbsp;
									<button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_create_loan_type_form">
										<?php echo translate('Cancel');?>
									</button>
								</span>
							</div>
						</div>
					<?php echo form_close();?>
				</div>
			</div>
		</div>
	</div>
	<!---->

	<!---Create group bank accounts-->
	<div id="create_group_bank_account_panel">
		<div class="m-form__section m-form__section--first">
			<div class="m-form__heading pb-3">
				<div class="row">
					<div class="col-lg-6 col-sm-12">
						<h3 class="m-form__heading-title"><?php echo translate($this->application_settings->entity_name. ' Accounts');?></h3>
					</div>
					<div class="col-lg-6 col-sm-12">
						<a href="#" class="btn btn-primary m-btn m-btn--custom m-btn--icon float-right btn-sm" id="add_bank_account_header">
							<span>
								<i class="la la-plus"></i>
								<span><?php echo translate('Add '.$this->application_settings->entity_name.' Account');?></span>
							</span>
						</a>
					</div>
				</div>
			</div>
			<div class="datatable_bank_accounts table-responsive-sm">
				<table class="table table-striped- table-bordered table-hover table-checkable bank_accounts setup_table" id="bank_account_listing">
				   	<thead>
				      	<tr>
					        <th class="numbering">#</th>
					        <th class="name"><?php echo translate('Account Details');?></th>
					        <th><?php echo translate('Signatories');?></th>
					        <th ><?php echo translate('Balances') .'('.$this->group_currency.')';?></th>
					        <th><?php echo translate('Actions');?></th>
				      </tr>
				   </thead>
				   <tbody>
				   </tbody>
				</table>
			</div>
			<div id="add_bank_account_setting">
				<ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active show" data-toggle="tab" href="#bank_account_tab" onClick="handle_tab_switch('bank_account')">
                            <?php echo translate('Bank Account');?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#sacco_account_tab" onClick="handle_tab_switch('sacco_account')">
                            <?php echo translate('SACCO Account');?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#mobile_money_account_tab" onClick="handle_tab_switch('mobile_money_account')">
                            <?php echo translate('Mobile Money Account');?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#petty_cash_account_tab" onClick="handle_tab_switch('petty_cash_account')">
                            <?php echo translate('Petty Cash Account');?>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active show" id="bank_account_tab" role="tabpanel">
						<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="add_bank_account"'); ?>
							<div class="bank_change_options">
								<div class="form-group m-form__group row pt-0 m--padding-10 ">
									<div class="col-sm-12 m-form__group-sub m-input--air">
										<label><?php echo translate('Bank Name');?><span class="required">*</span></label>
										<?php echo form_dropdown('bank_id',array(''=>'--Select Bank--')+$banks,$default_bank->id,'class="form-control bank_id m-select2" placeholder="Bank Name" id="bank_id"'); ?>
										<span class="m-form__help"><?php echo translate('Select option');?></span>
									</div>
								</div>
								<?php echo form_hidden('id','');?>
								<div class="form-group m-form__group row pt-0 m--padding-10 ">
									<div class="col-sm-6 m-form__group-sub m-input--air">
										<label><?php echo translate('Bank Branch');?><span class="required">*</span></label>
										<?php echo form_dropdown('bank_branch_id',array(''=>'--Select bank first--'),'','class="form-control bank_branch_id m-select2 bank_branches_space" placeholder="Bank Branch Name"'); ?>
										<span class="m-form__help"><?php echo translate('Select option');?></span>
									</div>
									<div class="col-sm-6">
										<label><?php echo translate('Bank Account Number');?><span class="required">*</span></label>
										<?php echo form_input('account_number','','class="form-control bank_account_number account_name m-input--air" id="bank_account_number" placeholder="Bank Account Number"'); ?>
									</div>
								</div>
								<div class="form-group m-form__group row pt-0 m--padding-10" style="display:none" id="account_name_currency_space">
									<div class="col-sm-6">
										<label><?php echo translate('Bank Account Name');?><span class="required">*</span></label>
										<?php echo form_input('account_name','','class="form-control account_name m-input--air" placeholder="Bank Account Name" readonly="readonly" id="account_name"'); ?>
									</div>
									<div class="col-sm-6 m-form__group-sub m-input--air">
										<label><?php echo translate('Account Currency');?><span class="required">*</span></label>
										<?php echo form_dropdown('account_currency_id',array(''=>'--Select account currency--')+$currencies,'','class="form-control account_currency_id m-select2 account_currency_id_space" placeholder="Account Currency" id="account_currency_id"'); ?>
										<span class="m-form__help"><?php echo translate('Select option');?></span>
									</div>
								</div>

								<div  class="form-group m-form__group pt-0 m--padding-10 row" id="signatories_account_balance_space" style="display:none">
				                   	<div class="col-lg-6 m-form__group-sub m-input--air">
				                   		<label><?php echo translate('Select Bank Account Signatories');?></label>
				                   		<div class="signatories_tab">
				                        	<?php echo form_dropdown('account_signatories[]',$this->active_group_member_options,$this->input->post('contribution_member_list'),' id="" class=" form-control m-select2 account_signatories" multiple="multiple" data-placeholder="Select..."'); ?>
				                        </div>
				                    </div>
				                    <div class="col-sm-6">
										<label><?php echo translate('Initial/Current Account Balances');?><span class="required">*</span></label>
										<?php echo form_input('initial_balance','','class="form-control initial_balance currency m-input--air" placeholder="Bank Account Balance"'); ?>
									</div>
				                </div>

								<div class="m--margin-top-50">
									<div class="col-lg-12 col-md-12">
										<span class="float-lg-right float-md-left float-sm-left float-xl-right">
											<button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="add_bank_account_button" type="button">
												<?php echo translate('Save Changes');?>
											</button>
											&nbsp;&nbsp;
											<button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_add_bank_account_form">
												<?php echo translate('Cancel');?>
											</button>
										</span>
									</div>
								</div>
							</div>
						<?php echo form_close();?>
					</div>
					<div class="tab-pane" id="sacco_account_tab" role="tabpanel">
						<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_sacco_accounts"'); ?>
							<div class="sacco_change_options">
								<div class="form-group m-form__group row pt-0 m--padding-10 ">
									<div class="col-sm-12 m-form__group-sub m-input--air">
										<label><?php echo translate($this->application_settings->entity_name.' Name');?><span class="required">*</span></label>
										<?php echo form_dropdown('sacco_id',array(''=>'--Select '.$this->application_settings->entity_name.'--')+$saccos,'','class="form-control bank_id m-select2" placeholder="Sacco Name"'); ?>
										<span class="m-form__help"><?php echo translate('Select option');?></span>
									</div>
								</div>
								<div class="form-group m-form__group row pt-0 m--padding-10 ">
									<div class="col-sm-6 m-form__group-sub m-input--air">
										<label><?php echo translate($this->application_settings->entity_name.' Branch');?><span class="required">*</span></label>
										<?php echo form_dropdown('sacco_branch_id',array(''=>'--Select '.$this->application_settings->entity_name.' first--'),'','class="form-control sacco_branch_id m-select2 sacco_branches_space" placeholder="'.$this->application_settings->entity_name.' Branch Name"'); ?>
										<span class="m-form__help"><?php echo translate('Select option');?></span>
									</div>
									<div class="col-sm-6">
										<label><?php echo translate($this->application_settings->entity_name.' Account Name');?><span class="required">*</span></label>
										<?php echo form_input('account_name','','class="form-control account_name m-input--air" placeholder="'.$this->application_settings->entity_name.' Account Name"'); ?>
									</div>
								</div>
								<?php echo form_hidden('id','');?>
								<div class="form-group m-form__group row pt-0 m--padding-10">
									<div class="col-sm-6">
										<label><?php echo translate($this->application_settings->entity_name.' Account Number');?><span class="required">*</span></label>
										<?php echo form_input('account_number','','class="form-control account_name m-input--air" placeholder="'.$this->application_settings->entity_name.' Account Number"'); ?>
									</div>

									<div class="col-sm-6">
										<label><?php echo translate('Initial/Current Account Balances');?><span class="required">*</span></label>
										<?php echo form_input('initial_balance','','class="form-control initial_balance currency m-input--air" placeholder="'.$this->application_settings->entity_name.' Account Balance"'); ?>
									</div>
								</div>
								<div class="m--margin-top-50">
									<div class="col-lg-12 col-md-12">
										<span class="float-lg-right float-md-left float-sm-left float-xl-right">
											<button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_sacco_account_button" type="button">
												<?php echo translate('Save Changes');?>
											</button>
											&nbsp;&nbsp;
											<button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_add_bank_account_form">
												<?php echo translate('Cancel');?>
											</button>
										</span>
									</div>
								</div>
							</div>
						<?php echo form_close();?>
					</div>

					<div class="tab-pane" id="mobile_money_account_tab" role="tabpanel">
						<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="mobile_money_form"'); ?>
							<div class="mobile_money_account_change_options">
								<div class="form-group m-form__group row pt-0 m--padding-10 ">
									<div class="col-sm-12 m-form__group-sub m-input--air">
										<label><?php echo translate('Mobile Money Provider');?><span class="required">*</span></label>
										<?php echo form_dropdown('mobile_money_provider_id',array(''=>'--Select Mobile Money Provider--')+$mobile_money_providers,'','class="form-control mobile_money_provider_id m-select2" placeholder="Mobile Money Provider Name"'); ?>
										<span class="m-form__help"><?php echo translate('Select option');?></span>
									</div>
								</div>
								<div class="form-group m-form__group row pt-0 m--padding-10 ">
									<div class="col-sm-12">
										<label><?php echo translate('Mobile Money Account Name');?><span class="required">*</span></label>
										<?php echo form_input('account_name','','class="form-control account_name m-input--air" placeholder="Account Name"'); ?>
									</div>
								</div>
								<?php echo form_hidden('id','');?>
								<div class="form-group m-form__group row pt-0 m--padding-10">
									<div class="col-sm-6">
										<label><?php echo translate('Account Number');?><span class="required">*</span></label>
										<?php echo form_input('account_number','','class="form-control account_name m-input--air" placeholder="Account Number / Phone Number / Till Number"'); ?>
									</div>

									<div class="col-sm-6">
										<label><?php echo translate('Initial/Current Account Balances');?><span class="required">*</span></label>
										<?php echo form_input('initial_balance','','class="form-control initial_balance currency m-input--air" placeholder="Account Balance"'); ?>
									</div>
								</div>
								<div class="m--margin-top-50">
									<div class="col-lg-12 col-md-12">
										<span class="float-lg-right float-md-left float-sm-left float-xl-right">
											<button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_mobile_money_form_button" type="button">
												<?php echo translate('Save Changes');?>
											</button>
											&nbsp;&nbsp;
											<button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_add_bank_account_form">
												<?php echo translate('Cancel');?>
											</button>
										</span>
									</div>
								</div>
							</div>
						<?php echo form_close();?>
					</div>

					<div class="tab-pane" id="petty_cash_account_tab" role="tabpanel">
						<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="petty_cash_form"'); ?>
							<div class="petty_cash_account_change_options">
								<div class="form-group m-form__group row pt-0 m--padding-10 ">
									<div class="col-sm-12">
										<label><?php echo translate('Petty Cash Account Name');?><span class="required">*</span></label>
										<?php echo form_input('account_name','','class="form-control account_name m-input--air" placeholder="Account Name"'); ?>
									</div>
								</div>
								<div class="form-group m-form__group row pt-0 m--padding-10">
									<div class="col-sm-12">
										<label><?php echo translate('Initial/Current Account Balances');?><span class="required">*</span></label>
										<?php echo form_input('initial_balance','','class="form-control initial_balance currency m-input--air" placeholder="Account Balance"'); ?>
									</div>
								</div>
								<?php echo form_hidden('id','');?>
								<div class="m--margin-top-50">
									<div class="col-lg-12 col-md-12">
										<span class="float-lg-right float-md-left float-sm-left float-xl-right">
											<button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_petty_cash_form_button" type="button">
												<?php echo translate('Save Changes');?>
											</button>
											&nbsp;&nbsp;
											<button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_add_bank_account_form">
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
		</div>

		<a type="button" class="btn btn-primary myModal d-none" data-keyboard="false" data-backdrop="static" data-toggle="modal" data-target="#connect_bank_account" value="" id="connect_account"> Add New Role</a>

		<!--begin::Modal-->
		<div class="modal fade" id="connect_bank_account" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px;">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h5 class="modal-title" id="exampleModalLabel"><?php echo translate('Link and Connect you account to start online transacting');?></h5>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">&times;</span>
		        		</button>
		      		</div>

			      	<div class="modal-body page_menus">
		      		<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="connect_equity_account_form"'); ?>
			      		<div class="withdrawal_item">
	                        <div class="menu_cont" style="padding-left: 7px;">
	                            <div class="menu_cont_descr" style="height: 50px;">
	                               <strong>Account Name: </strong> <span class="account_name">Test Account</span>
	                               <br/>
	                               <br/>
	                               <strong>Account Number: </strong> <span class="account_number">100000000000</span>
	                            </div>
	                        </div>
	                    </div>
	                    <?php echo form_hidden('id',0);?>
	                    <?php echo form_hidden('bank_id',0);?>
	                    <?php echo form_hidden('account_number',0);?>
	                    <div class ="row" id="otp_message_space" style="display:none">
	                    	<div class="col-md-12">
	                    		<h4>Receive OTP confirmation code via?</h4>
	                    		<br/>
	                    	</div>
	                    	<div class="col-md-12" id="account_notificaton_space">
		                    	<div class="m-radio-list">
	                                <?php 
	                                    if($this->input->post('enable_checkoff')==1){
	                                        $enable_contribution_checkoff = TRUE;
	                                        $disable_contribution_checkoff = FALSE;
	                                    }else{
	                                        $enable_contribution_checkoff = FALSE;
	                                        $disable_contribution_checkoff = TRUE;
	                                    }
	                                ?>
	                                <label class="m-radio m-radio--solid m-radio--brand">
	                                    <?php echo form_radio('enable_checkoff',1,$enable_contribution_checkoff,""); ?>
	                                    <?php echo translate('Yes');?>
	                                    <span></span>
	                                </label>

	                                <label class="m-radio m-radio--solid m-radio--brand">
	                                    <?php echo form_radio('enable_checkoff',0,$disable_contribution_checkoff,""); ?>
	                                    <?php echo translate('No');?>
	                                    <span></span>
	                                </label>
	                            </div>
	                        </div>
	                    </div>
	                    <div class ="row" id="signatory_phone_number_space" style="display:none">
	                    	<div class="col-sm-12 mt-5 m-form__group-sub m-input--air"  style="display:none" id="otp-not">
					            <label>
					                <?php echo translate('Signatory Phone Number');?>
					                <span class="required">*</span>
					            </label>
					            <?php echo form_input('phone',$this->input->post('phone')?$this->input->post('phone'):'','class="form-control cust_login_phone phone m-input--air" style="padding-left: 71px !important;" placeholder="Signatory phone number"'); ?>
					        </div>
	                    </div>
	                    <div class="row verification_settings" style="display:none">
	                    	<div class="col-md-12">
	                    		<div class="form-group m-form__group row pt-0">
									<div class="col-lg-12 m-input--air">
										<label class="form-control-label"><?php echo translate('Verification Code');?>: <span class="required">*</span></label>
										<?php echo form_input('verification_code','','class="form-control verification_code"');?>
									</div>
								</div>
	                    	</div>
	                    </div>
			      	</div>
		      		<div class="modal-footer">
		        		<button type="button" class="btn btn-secondary cancel_connect_equity_account_form" id="close_add_role" data-dismiss="modal">Close</button>
		        		<button type="button" class="btn btn-primary" id="connect_equity_account_btn">Submit to Validate</button>
		      		</div>
	      			<?php echo form_close();?>
		    	</div>
		  	</div>
		</div>
		<!--end::Modal-->
	</div>
	<!------>

	<!-- Complete and confirm-->
	<div id="confirm_group_setup_panel">
		<div class="m-form__section mb-1">
			<div class="m-form__heading pb-0 mb-0">
				<div class="row">
					<div class="col-lg-12 col-sm-12">
						<h3 class="m-form__heading-title"><?php echo translate('Summary & Confirmation');?></h3>
						<p>That's all we need for now. Kindly review your data below before you submit.</p>
					</div>
				</div>
			</div>
		</div>
		<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="accept_complete_setup_form"'); ?>
			<!--begin::Section-->                                            
			<div class="m-accordion m-accordion--bordered" id="m_accordion_1" role="tablist">
				<!--begin::Item-->              
				<div class="m-accordion__item active">
					<div class="m-accordion__item-head"  role="tab" id="m_accordion_1_item_1_head" data-toggle="collapse" href="#m_accordion_1_item_1_body" aria-expanded="  false">
						<span class="m-accordion__item-icon"><i class="mdi mdi-home-group"></i></span>
						<span class="m-accordion__item-title">1. <?php echo translate($this->application_settings->entity_name. ' Information');?></span>
						<span class="m-accordion__item-mode"></span>     
					</div>
					<div class="m-accordion__item-body collapse show" id="m_accordion_1_item_1_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_1_head" data-parent="#m_accordion_1">
						<!--begin::Content--> 	  
						<div class="tab-content active  m--padding-30">
							<div class="m-form__section m-form__section--first">
								<div class="form-group m-form__group m-form__group--sm row">
									<label class="col-xl-4 col-lg-4 col-form-label"><?php echo translate($this->application_settings->entity_name.' Name');?>:</label>
									<div class="col-xl-8 col-lg-8">
										<span class="m-form__control-static"><?php echo ucwords($this->group->name);?></span>
									</div>
								</div>
								<div class="form-group m-form__group m-form__group--sm row">
									<label class="col-xl-4 col-lg-4 col-form-label"><?php echo translate('Country');?>:</label>
									<div class="col-xl-8 col-lg-8">
										<span class="m-form__control-static"><?php echo isset($coutries[$this->group->country_id])?$coutries[$this->group->country_id]:'Not Set';?></span>
									</div>
								</div>
								<div class="form-group m-form__group m-form__group--sm row">
									<label class="col-xl-4 col-lg-4 col-form-label"><?php echo translate('Currency');?>:</label>
									<div class="col-xl-8 col-lg-8">
										<span class="m-form__control-static"><?php echo isset($currencies[$this->group->currency_id])?$currencies[$this->group->country_id]:'Not Set';?></span>
									</div>
								</div>
								<div class="form-group m-form__group m-form__group--sm row">
									<label class="col-xl-4 col-lg-4 col-form-label"><?php echo translate($this->application_settings->entity_name.' Size');?>:</label>
									<div class="col-xl-8 col-lg-8">
										<span class="m-form__control-static"><?php echo $this->group->size;?> <?php echo translate('Members');?> </span>
									</div>
								</div>
								<div class="form-group m-form__group m-form__group--sm row">
									<label class="col-xl-4 col-lg-4 col-form-label"><?php echo translate($this->application_settings->entity_name.' Type');?>:</label>
									<div class="col-xl-8 col-lg-8">
										<span class="m-form__control-static"><?php echo isset($type_of_groups[$this->group->group_type])?$type_of_groups[$this->group->group_type]:'Not Set';?></span>
									</div>
								</div>

								<?php if($this->group->group_is_registered){?>
									<div class="form-group m-form__group m-form__group--sm row">
										<label class="col-xl-4 col-lg-4 col-form-label"><?php echo translate('Registration Certificate Number');?>:</label>
										<div class="col-xl-8 col-lg-8">
											<span class="m-form__control-static"><?php echo $this->group->group_registration_certificate_number;?></span>
										</div>
									</div>
								<?php }?>

								<?php if($this->group->group_offer_loans){?>
									<div class="form-group m-form__group m-form__group--sm row">
										<label class="col-xl-4 col-lg-4 col-form-label">Allow <?php echo $this->application_settings->entity_name ?> members to apply for loans</label>
										<div class="col-xl-8 col-lg-8">
											<span class="m-switch m-switch--sm m-switch--icon">
												<label>
													<?php echo form_checkbox('allow_members_request_loan',1,$this->group->allow_members_request_loan?:FALSE,''); ?>
						                        <span></span>
						                        </label>
						                    </span>
										</div>
									</div>
								<?php }?>
								<div class="form-group m-form__group m-form__group--sm row d-none">
									<label class="col-xl-4 col-lg-4 col-form-label">Enable Member Information Privacy</label>
									<div class="col-xl-8 col-lg-8">
										<span class="m-switch m-switch--sm m-switch--icon">
											<label>
												<?php echo form_checkbox('enable_member_information_privacy',1,$this->group->enable_member_information_privacy?:FALSE,''); ?>
					                        <span></span>
					                        </label>
					                    </span>
									</div>
								</div>								
								<div class="form-group m-form__group m-form__group--sm row d-none">
									<label class="col-xl-4 col-lg-4 col-form-label">Disable "Member Directory" from members</label>
									<div class="col-xl-8 col-lg-8">
										<span class="m-switch m-switch--sm m-switch--icon">
											<label>
												<?php echo form_checkbox('disable_member_directory',1,$this->group->disable_member_directory?:FALSE,''); ?>
					                        <span></span>
					                        </label>
					                    </span>
									</div>
								</div>

	                        </div>
						</div>
						<!--end::Section-->	 
					</div>
				</div>
				<!--end::Item-->
				<!--begin::Item--> 
				<div class="m-accordion__item">
					<div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_1_item_2_head" data-toggle="collapse" href="#m_accordion_1_item_2_body" aria-expanded="    false">
						<span class="m-accordion__item-icon"><i class=" mdi mdi-account-group"></i></span>
						<span class="m-accordion__item-title">2. <?php echo translate($this->application_settings->entity_name.' Members');?></span>
						<span class="m-accordion__item-icon"><i class="mdi mdi-bell section-has-error m-animate-blink d-none"></i></span>
						<span class="m-accordion__item-mode"></span>     
					</div>
					<div class="m-accordion__item-body collapse" id="m_accordion_1_item_2_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_2_head" data-parent="#m_accordion_1">
						<!--begin::Content--> 
						<div class="tab-content  m--padding-30">
							<div class="m-form__section m-form__section--first">
								<table class="table table-hover table-striped table-condensed table-statement">
								    <thead>
								        <tr>
								            <th class="invoice-title" width="2%">#</th>
								            <th class="invoice-title">
								                Member Name
								            </th>
								            <th class="invoice-title ">
								                Email Address
								            </th>
								            <th class="invoice-title ">
								                Phone Number
								            </th>
								            <th class="invoice-title">
								               	<?php echo $this->application_settings->entity_name ?> Role
								            </th>
								        </tr>
								    </thead>
								    <tbody class="members_complete_setup">
								        <tr>
								        	<td valign="top" colspan="5" class="dataTables_empty">No data available in table</td>
								        </tr>
								    </tbody>
								</table>
							</div>	
						</div>
						<!--end::Content--> 
					</div>
				</div>
				<!--end::Item--> 
				<!--begin::Item--> 
				<div class="m-accordion__item">
					<div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_1_item_3_head" data-toggle="collapse" href="#m_accordion_1_item_3_body" aria-expanded="    false">
						<span class="m-accordion__item-icon"><i class="mdi mdi-cellphone-settings-variant"></i></span>
						<span class="m-accordion__item-title">3. <?php echo translate('Contribution Settings');?></span>
						<span class="m-accordion__item-icon"><i class="mdi mdi-bell section-has-error m-animate-blink d-none"></i></span>
						<span class="m-accordion__item-mode"></span>     
					</div>
					<div class="m-accordion__item-body collapse" id="m_accordion_1_item_3_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_3_head" data-parent="#m_accordion_1">
						<!--begin::Content--> 
						<div class="tab-content  m--padding-30">
							<div class="contributions_complete_setup">
							</div>
						</div>
						<!--end::Content--> 
					</div>
				</div>
				<!--end::Item--> 
				<!--begin::Item--> 
				<div class="m-accordion__item">
					<div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_1_item_4_head" data-toggle="collapse" href="#m_accordion_1_item_4_body" aria-expanded="    false">
						<span class="m-accordion__item-icon"><i class="mdi mdi-subtitles-outline"></i></span>
						<span class="m-accordion__item-title">4. <?php echo translate('Loan Types');?></span>
						<span class="m-accordion__item-mode"></span>     
					</div>
					<div class="m-accordion__item-body collapse" id="m_accordion_1_item_4_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_4_head" data-parent="#m_accordion_1">
						<!--begin::Content--> 
						<div class="tab-content  m--padding-30">
							<div class="loan_types_complete_setup">
							</div>
						</div>
						<!--end::Content--> 
					</div>
				</div>
				<!--end::Item-->
				<div class="m-accordion__item">
					<div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_1_item_5_head" data-toggle="collapse" href="#m_accordion_1_item_5_body" aria-expanded="    false">
						<span class="m-accordion__item-icon"><i class="mdi mdi-bank"></i></span>
						<span class="m-accordion__item-title">5. <?php echo translate('Bank Account Details');?></span>
						<span class="m-accordion__item-mode"></span>     
					</div>
					<div class="m-accordion__item-body collapse" id="m_accordion_1_item_5_body" class=" " role="tabpanel" aria-labelledby="m_accordion_1_item_5_head" data-parent="#m_accordion_1">
						<!--begin::Content--> 
						<div class="tab-content  m--padding-30">
							<div class="bank_accounts_complete_setup">
							</div>
						</div>
						<!--end::Content--> 
					</div>
				</div>
				<!--end::Item-->
			</div>
			<!--end::Section-->

			<!--end::Section-->	                
			<!-- <div class="m-separator m-separator--dashed m-separator--lg"></div> -->

			<div class="form-group m-form__group m-form__group--sm pt-2 row">
				<div class="col-lg-12 m-form__group-sub">
					<label class="form-control-label"><?php echo translate('Do you have a referral code');?>?:</label>
					<div class="m-radio-inline">
						<?php 
							$reffral_code = isset($_COOKIE['REFFERAL_CODE'])?$_COOKIE['REFFERAL_CODE']:'';
	                        if($this->input->post('enable_referral_code')==1 || $reffral_code){
	                            $enable_referral_code = TRUE;
	                            $disabled_referral_code = FALSE;
	                        }else if($this->input->post('enable_referral_code')==0){
	                            $enable_referral_code = FALSE;
	                            $disabled_referral_code = TRUE;
	                        }else{
	                            $enable_referral_code = TRUE;
	                            $disabled_referral_code = FALSE;
	                        } 
	                    ?>
	                    <label class="m-radio m-radio--solid m-radio--brand">
	                    	<?php echo form_radio('enable_referral_code',1,$enable_referral_code,""); ?>
	                    	<?php echo translate('Yes');?>
	                    	<span></span>
	                    </label>

	                    <label class="m-radio m-radio--solid m-radio--brand">
	                    	<?php echo form_radio('enable_referral_code',0,$disabled_referral_code,""); ?>
	                    	<?php echo translate('No');?>
	                    	<span></span>
	                    </label>
					</div>
				</div>
			</div>
			<div class="enable_reffaral_code_holder" >
				<div class="m-form__group form-group row pt-0 m--padding-10">
					<div class="m-form__group-sub m-input--air col-sm-12 m-input--air">
                    	<label><?php echo translate('Enter Code');?> ? <span class="required">*</span></label>
                    	<?php echo form_input('referral_code','','class="form-control m-input--air" placeholder="Enter referral code"'); ?>
                    </div>
                </div>
            </div>
            <?php if($this->ion_auth->is_bank_admin() || $this->ion_auth->is_admin()){?>
				<div class="set_admin_member_holder">
					<div class="form-group m-form__group m-form__group--sm mt-5 row admin_member_id">
						<div class="col-lg-12 m-form__group-sub m-input--air col-sm-12 m-input--air admin_member_id">
							<label><?php echo translate('Set The '.$this->application_settings->entity_name.' Administrator');?><span class="required">*</span></label>
							<?php echo form_dropdown('admin_member_id',array(''=>'- '.translate('Select').' -'),'','class="form-control m-input--air admin_member_id" id="admin_member_id" '); ?>
						</div>
					</div>
				</div>
			<?php }else{?>
				<?php echo form_hidden('admin_member_id',$this->member->user_id) ?>
			<?php  }?>
			<div class="form-group m-form__group m-form__group--sm row">
				<div class="col-xl-12 mt-5">
					<div class="m-checkbox-inline ">
						<label class="m-checkbox m-checkbox--solid m-checkbox--brand form-control-label">
							<input type="checkbox" name="accept_complete_setup" value="1"> 
								I have read and agree to the terms presented in the Terms and Conditions agreement 
							<span></span>
						</label>
					</div>
				</div>
			</div>
		<?php echo form_close();?>
		<!--end: Form Wizard Step 4-->	
	</div>
	<!----->
</div>

<div class="modal fade" id="view_contribution_details" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header  m--padding-15">
            	<span class="contribution_name"></span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body block_content "style="min-height: 100px;">
                <div class="row">
                	<div class="col-md-12 contribution_details_content" style="display: none;">
                	</div>                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_loan_type_details" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header  m--padding-15">
            	<span class="loan_type_name"></span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body block_content "style="min-height: 100px;">
                <div class="row">
                	<div class="col-md-12 loan_type_details_content" style="display: none;">
                	</div>                    
                </div>
            </div>
        </div>
    </div>
</div>



<script src="<?php echo site_url('templates/admin_themes/admin/intl-tel-input/js/intlTelInput.min.js');?>"></script>
<script type="text/javascript">
	var init_form = $('#append-place-holder').html();
	var create_contribution_settings = $('.create_contribution_settings').html();
	var create_loan_type_setting = $('#create_loan_type_setting').html();
	var add_bank_account_setting = $('#add_bank_account_setting').html();
	var add_new_role_position = '';
	var account_notification_keys_bodies = [];
	var entity_name = `<?php echo $this->application_settings->entity_name;?>`;
	$(document).ready(function(){
		$(document).on('click','#add_member_form',function(){
        	$('.datatable_members').hide();
        	$(this).hide();
        	$('.btn.btn-setup-next').attr('style','display:none !important');
        	$('.add_new_members').slideDown();
        	//Select2.init('#multiple-entries .m-select2-append');
        	$('#multiple-entries .m-select2-append').select2({
            	placeholder:{
				    id: '-1',
				    text: "--Select option--",
				}
            });
        	PhoneInputCountry.init();
        });

        $(document).on('click','#cancel_add_members_form',function(){
        	$('.upload_members_excel').hide();
        	$('.add_new_members').hide();
        	$('.datatable_members').slideDown();
        	html = init_form.replace_all('checker','');
			$('#append-place-holder').html(html);
			$('#multiple-entries .m-select2-append').select2({
            	placeholder:{
				    id: '-1',
				    text: "--Select option--",
				}
            });
            PhoneInputCountry.init();
            Inputmask.init()
        	$('#add_member_form').slideDown();
        	$('.btn.btn-setup-next').show();
        });

        $(document).on('click','#upload_members_excel_form',function(){        
        	$('.add_new_members').hide();
        	$('.btn.btn-setup-next').attr('style','display:none !important');
        	$('.upload_members_excel').slideDown();
        });

        $(document).on('click','#cancel_upload_members_excel',function(){
        	$('.upload_members_excel').hide();
        	$('.datatable_members').slideDown();
        	$('#add_member_form').slideDown();
        	$('.btn.btn-setup-next').attr('style','display:inline-block !important');
        });


        $(document).on('click','#create_contribution_form',function(){
        	$('.datatable_contributions').hide();
        	$(this).hide();
        	$('.btn.btn-setup-next,.btn.btn-setup-previous').attr('style','display:none !important');
        	$('.create_contribution_settings').slideDown();
        });

        $(document).on('click','#cancel_create_contribution_form',function(){
        	$('#create_contribution_form').hide();
        	$('.create_contribution_settings').hide();
        	$('#create_contribution_form').show();
        	html = create_contribution_settings.replace_all('checker','');
			$('.create_contribution_settings').html(html);
			$(".create_contribution_settings .m-select2").select2({
				width: "100%",
	            placeholder: {
                  	id: '-1',
                  	text: "--Select option--",
               	}
			});
			Inputmask.init();
        	$('.datatable_contributions').slideDown();
        	$('.btn.btn-setup-next,.btn.btn-setup-previous').attr('style','display:inline-block !important');
        });


        $(document).on('click','#add-new-line',function(){
        	var html = $('#add_new_members_settings tbody').html();
            html = html.replace_all('checker','');
            $('#append-place-holder').append(html);
            var number = 1;
            $('.number').each(function(){
            	$(this).text(number);
            	$(this).parent().find('input.names').attr('name','names['+(number-1)+']');
            	$(this).parent().find('input.phones').attr('name','phones['+(number-1)+']');
            	$(this).parent().find('input.email_addresses').attr('name','email_addresses['+(number-1)+']');
            	$(this).parent().find('select.group_role_ids').attr('name','group_role_ids['+(number-1)+']');
            	++number;
            });
            $('#multiple-entries .m-select2-append').select2({
            	placeholder:{
				    id: '-1',
				    text: "--Select option--",
				}
            });
            PhoneInputCountry.init();
        });

        $(document).on('change click','.group_role_ids',function(){
        	var group_role_id = $(this).val();
        	if(group_role_id == "0"){
        		add_new_role_position = parseInt($(this).parent().parent().parent().find('.number').html().replace('.','')) - 1;
        		$('#add_new_member_role_form input[name=name]').val('');
        		$('#add_new_member_role_form textarea[name=description]').val('');
        		$(this).val('').trigger('change');
        		$('#add_new_role_hidden').trigger('click');
        	}
        })

        $(document).on('click','#multiple-entries button.remove-line',function(){
        	$(this).parent().parent().remove();
            var number = 1;
            $('.number').each(function(){
                $(this).text(number);
                $(this).parent().find('input.names').attr('name','names['+(number-1)+']');
            	$(this).parent().find('input.phones').attr('name','phones['+(number-1)+']');
            	$(this).parent().find('input.email_addresses').attr('name','email_addresses['+(number-1)+']');
            	$(this).parent().find('select.group_role_ids').attr('name','group_role_ids['+(number-1)+']');
                number++;
            });
        });

        $(document).on('change','select[name="category"]',function(){
        	category = $(this).val();
        	type = $('select[name="type"]').val();
        	id = $('select[name="id"]').val();
        	if(type && id){

        	}else{
        		$('select[name="type"]').val('').trigger('change').removeAttr('readonly');
        		// $('input[name="is_non_refundable"]').prop('checked',true).trigger("change").attr('disabled',false);
        		// $('input[name="is_equity"]').prop('checked',true).trigger("change").attr('disabled',false);
        		if(category == 1){
	        		$('select[name="type"]').val('1').trigger('change').attr('readonly','readonly');
	        		$('input[name="is_equity"][value="0"]').prop('checked',false).trigger("change").attr('disabled',true);
	        		$('input[name="is_equity"][value="1"]').prop('checked',true).trigger("change").attr('disabled',false);
	        	}else if(category == 2){

	        	}else if(category == 3){ 

	        	}else if(category == 4){
	        		$('select[name="type"]').val('2').trigger('change').attr('readonly','readonly');
	        		$('input[name="is_non_refundable"][value="0"]').prop('checked',false).trigger("change").attr('disabled',true);
	        		$('input[name="is_non_refundable"][value="1"]').prop('checked',true).trigger("change").attr('disabled',false);
	        	}
        	}
        	
        });

        $(document).on('change','select[name="type"]',function(){
        	var type = $(this).val();
        	if(type==1){
        		$('#one_time_invoicing_active_holder').slideUp();
        		$('#one_time_invoicing_settings').slideUp();
        		$('#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
        		$('#regular_invoicing_active_holder').slideDown();
        		$('#disable_contribution_arrears,#disable_contribution_refund,#disable_enable_is_equity').slideDown();
        	}else if(type == 2){
        		$('#regular_invoicing_settings').slideUp();
        		$('#regular_invoicing_active_holder').slideUp();
        		$('#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
        		$('#one_time_invoicing_active_holder').slideDown();
        		$('#disable_contribution_arrears,#disable_contribution_refund,#disable_enable_is_equity').slideDown();
        	}else if(type == 3){
        		$('#regular_invoicing_settings').slideUp();
        		$('#one_time_invoicing_active_holder').slideUp();
        		$('#regular_invoicing_active_holder').slideUp();
        		$('#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
        		$('#one_time_invoicing_settings').slideUp();
        		$('#disable_contribution_arrears,#disable_contribution_refund,#disable_enable_is_equity').slideDown();
        	}else{
        		$('#one_time_invoicing_active_holder').slideUp();
        		$('#regular_invoicing_settings').slideUp();
        		$('#one_time_invoicing_settings').slideUp();
        		$('#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
        		$('#regular_invoicing_active_holder').slideUp();
        		$('#disable_contribution_arrears,#disable_contribution_refund,#disable_enable_is_equity').slideUp();
        	}
        });

        $(document).on('change','input[name="regular_invoicing_active"]',function(){
            if($(this).val() == 1){
            	var type = $('select[name="type"]').val();
                if(type== 1){
                    $('#regular_invoicing_settings,#invoice_notifications,#fines,#advanced_settings').slideDown();
                }else{
                	$('#regular_invoicing_settings,#invoice_notifications,#fines,#advanced_settings').slideUp();
                }
            }else{
                $('#regular_invoicing_settings,#invoice_notifications,#fines,#advanced_settings').slideUp();
            }
        });

        $(document).on('change','input[name="one_time_invoicing_active"]',function(){
            if($(this).val() == 1){
            	var type = $('select[name="type"]').val();
                if(type== 2){
                    $('#one_time_invoicing_settings,#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideDown();
                }else{
                	$('#one_time_invoicing_settings,#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
                }
            }else{
                $('#one_time_invoicing_settings,#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
            }
        });

        $(document).on('change','#contribution_frequency',function(){
        	if($(this).val()){
        		$('#sms_email_notifications').slideDown();
        		$('#contribution_member_list_settings,#contribution_fines').slideDown();
        	}
            if($(this).val()==1){
                //once a month
                $('#once_a_month').slideDown();
                $('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideDown();
                $('#once_a_week,#once_every_two_weeks,#once_every_multiple_months,#twice_every_one_month').slideUp();
            }else if($(this).val()==6){
                //once a week
                $('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideDown();
                $('#once_a_week').slideDown();
                $('#once_every_two_weeks,#once_a_month,#once_every_multiple_months,#twice_every_one_month').slideUp();
            }else if($(this).val()==7){
                //once every two weeks
                $('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideDown();
                $('#once_every_two_weeks').slideDown();
                $('#once_every_multiple_months,#once_a_week,#once_a_month,#twice_every_one_month').slideUp();
            }else if($(this).val()==2||$(this).val()==3||$(this).val()==4||$(this).val()==5){
                //once every two months, once every three months,once every six months, once a year
                $('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideDown();
                $('#once_every_multiple_months').slideDown();
                $('#once_a_month,#once_every_two_weeks,#once_a_week,#twice_every_one_month').slideUp();
            }else if($(this).val()==8){
                $('select[name=invoice_days]').val(1).trigger('change');
                $('#invoice_days').slideDown();
                $('#once_a_month,#once_every_two_weeks,#once_a_week,#once_every_multiple_months,#twice_every_one_month').slideUp();
            }else if($(this).val()==9){
                $('select[name=invoice_days]').val(1).trigger('change');
                $('#invoice_days,#twice_every_one_month').slideDown();
                $('#once_a_month,#once_every_two_weeks,#once_a_week,#once_every_multiple_months').slideUp();
            }else{
                $('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideUp();
                $('#sms_email_notifications,#contribution_fines').slideUp();
                $('#contribution_member_list_settings').slideUp();
                $('#once_a_month,#once_every_two_weeks,#once_a_week,#once_every_multiple_months,#twice_every_one_month').slideUp();
            }
        });

        $(document).on('change','#month_day_monthly',function(){
            if($(this).val()>4 && $(this).val()<32){
                $('#week_day_monthly').val("0").attr('disabled','disabled').trigger('change');
            }else{
                $('#week_day_monthly').val('').removeAttr('disabled','disabled').trigger('change');
            }
        });

        $(document).on('change','#month_day_multiple',function(){
            if($(this).val()>4 && $(this).val()<32){
                $('#week_day_multiple').val("0").attr('disabled','disabled').trigger('change');
            }else{
                $('#week_day_multiple').val('').removeAttr('disabled','disabled').trigger('change');
            }
        });

        $(document).on('change','input[name="sms_notification_email_notification"]',function(){
        	var sms_notification_email_notification = $(this).val();
        	if(sms_notification_email_notification == 1){
        		$('#sms_email_notifications_settings').slideDown();
        	}else{
        		$('#sms_email_notifications_settings').slideUp();
        	}
        });

        $(document).on('change','input[name="enable_contribution_member_list"]',function(){
        	var enable_contribution_member_list = $(this).val();
            if(enable_contribution_member_list == 1){
                $('#contribution_member_list').slideDown();
            }else{
                $('#contribution_member_list').slideUp();
            }
        });

        $(document).on('change','input[name="enable_fines"]',function(){
        	var enable_fines = $(this).val();
        	if(enable_fines==1){
        		$('#fine_setting_row').slideDown();
        	}else{	
				$('#fine_setting_row').slideUp();
        	}
        });

        $(document).on('change','.fine_types',function(){
            var fine_setting_row_element = $(this).parent().parent().parent();
            fine_setting_row_element.find('.fixed_fine_settings,.percentage_fine_settings,.fine_limit').slideUp('fast');
            fine_setting_row_element.find('.fine_limits_settings').hide();
            if($(this).val()==1){
                fine_setting_row_element.find('.fixed_fine_settings').slideDown();
            }else if($(this).val()==2){
                fine_setting_row_element.find('.percentage_fine_settings').slideDown();
            }
        });

        $(document).on('change','.fixed_fine_modes',function(){
            var fine_setting_row_element = $(this).parent().parent().parent().parent().parent().parent();
            if($(this).val()==1){
            	fine_setting_row_element.find('.fine_limits_settings').show();
                fine_setting_row_element.find('.fine_limit').slideDown();
            }else{
                fine_setting_row_element.find('.fine_limit').slideUp();
                fine_setting_row_element.find('.fine_limits_settings').hide();
            }
        });

        $(document).on('change','.percentage_fine_modes',function(){
            var fine_setting_row_element = $(this).parent().parent().parent().parent();
            if($(this).val()==1){
            	fine_setting_row_element.find('.fine_limits_settings').show();
                fine_setting_row_element.find('.fine_limit').slideDown();
            }else{
                fine_setting_row_element.find('.fine_limit').slideUp();
                fine_setting_row_element.find('.fine_limits_settings').hide();
            }
        });

        $(document).on('click','#add-new-fine-line',function(){
        	var html = $('.fine_settings_addition .new_fine').html();
        	html = html.replace_all('checker','');
        	$('#append-new-fine-setting').append(html);
        	number = 0;
        	$('.fine_types').each(function(){
        		$(this).attr('name','fine_type['+(number)+']');
        		$(this).parent().parent().parent().find('input.fixed_amounts').attr('name','fixed_amount['+(number)+']');
        		$(this).parent().parent().parent().find('select.fixed_fine_modes').attr('name','fixed_fine_mode['+(number)+']');
        		$(this).parent().parent().parent().find('select.fixed_fine_chargeable_ons').attr('name','fixed_fine_chargeable_on['+(number)+']');
        		$(this).parent().parent().parent().find('select.fixed_fine_frequencies').attr('name','fixed_fine_frequency['+(number)+']');
        		$(this).parent().parent().parent().find('select.fine_limits').attr('name','fine_limit['+(number)+']');
        		$(this).parent().parent().parent().find('input.percentage_rates').attr('name','percentage_rate['+(number)+']');
        		$(this).parent().parent().parent().find('select.percentage_fine_ons').attr('name','percentage_fine_on['+(number)+']');
        		$(this).parent().parent().parent().find('select.percentage_fine_chargeable_ons').attr('name','percentage_fine_chargeable_on['+(number)+']');
        		$(this).parent().parent().parent().find('select.percentage_fine_modes').attr('name','percentage_fine_mode['+(number)+']');
        		$(this).parent().parent().parent().find('select.percentage_fine_frequencies').attr('name','percentage_fine_frequency['+(number)+']');
        		number++;
        	});
        	$('#append-new-fine-setting select.m-select2-append').select2({
            	placeholder:{
				    id: '-1',
				    text: "--Select option--",
				},
				width: "100%",
        	});
        });

        $(document).on('click','.remove-fine-setting',function(){
        	$(this).parent().parent().parent().parent().remove();
        });


        $(document).on('change','input[name="group_offer_loans"]',function(){
        	var group_offer_loans = $(this).val();
        	if(group_offer_loans == 1){
        		$('.btn.btn-setup-next,.btn.btn-setup-previous').attr('style','display:none !important');
        		$('#create_loan_type_options').hide();
        		$('#create_loan_type_setting').slideDown();
        	}else{
        		$('#create_loan_type_setting').slideUp();
        		$('.btn.btn-setup-next,.btn.btn-setup-previous').attr('style','display:inline-block !important');
        	}
        });

        $(document).on('click','#create_loan_type_header',function(){
        	$('.load_group_loan_types').hide();
        	$(this).hide();
        	$('.create_loan_type_settings_layout').slideDown();
        	if(group_offer_loans || (group  && group.group_offer_loans == 1)){
        		$('input[name="group_offer_loans"]').val('1').trigger('change');
        	}else{
        		$('input[name="group_offer_loans"]').val('0').trigger('change');
        	}
        });

        $(document).on('click','#cancel_create_loan_type_form',function(){
        	$('#create_loan_type_setting').hide();
        	html = create_loan_type_setting.replace_all('checker','');
			$('#create_loan_type_setting').html(html);
			$("#create_loan_type_setting .m-select2").select2({
				width: "100%",
	            placeholder: {
                  	id: '-1',
                  	text: "--Select option--",
               	}
			});
			Inputmask.init();
			$('.create_loan_type_settings_layout').slideDown();
			if(group_offer_loans == 1 || (group  && group.group_offer_loans == 1)){
				$('#create_loan_type_options').slideUp();
				$('#create_loan_type_header').slideDown();
				$('.load_group_loan_types').slideDown();
        	}else{
				$('.load_group_loan_types').hide();
				$('#create_loan_type_options').slideDown();
				$('input[name="group_offer_loans"][value="1"]').prop('checked',false);
				$('input[name="group_offer_loans"][value="0"]').prop('checked',true).trigger("change");
        	}        	
        	$('.btn.btn-setup-next,.btn.btn-setup-previous').attr('style','display:inline-block !important');
        });


        $(document).on('change','select[name="loan_amount_type"]',function(){
            var element = $(this).val();
            if(element == 1){
                $('.loan_amount_input_group').slideDown(); 
                $('.loan_amount_savings_input_group').slideUp();
            }else if(element == 2){
                $('.loan_amount_input_group').slideUp(); 
                $('.loan_amount_savings_input_group').slideDown();
            }else{
               $('.loan_amount_input_group').slideUp();
               $('.loan_amount_savings_input_group').slideUp(); 
            }

        });


        $(document).on('keyup keydown','input[name="maximum_loan_amount"],input[name="loan_times_number"]',function(){
            var element = $(this);            
            if(element.val()){
                $('.interest_type_input_group').slideDown();
            }else{
               $('.interest_type_input_group').slideUp();
            }                
        });


        $(document).on('change','select[name="interest_type"]',function(){
            var interest_type = $(this).val();
            if(interest_type == 1){
            	$('.for_custom_settings').slideUp();  
                $('.not_for_custom_settings').slideDown();
                $('#enable_reducing_balance_installment_recalculation').slideUp();
            }else if(interest_type == 2){
                $('.not_for_custom_settings').slideDown();
                $('.for_custom_settings').slideUp();  
                $('#enable_reducing_balance_installment_recalculation').slideDown();
            }else if(interest_type == 3){
                $('.not_for_custom_settings').slideUp();
                $('#enable_reducing_balance_installment_recalculation').slideUp();
                $('.for_custom_settings').slideUp();  
                $('.for_custom_settings').slideDown();
                $('#grace_period').slideDown();
            }else{
                $('.not_for_custom_settings').slideUp();
                $('#enable_reducing_balance_installment_recalculation').slideUp();
                $('.for_custom_settings').slideUp();  
            }
        });

        if($('select[name="interest_type"]').val()){
        	$(this).trigger('change');
        }

        $(document).on('keydown keyup','input[name="interest_rate"]',function(){
            $('#grace_period').slideDown();
        });

        if($('select[name="interest_rate"]').val()){
        	$(this).trigger('change');
        }

        $(document).on('change','select[name="grace_period"]',function(){
            if($(this).val()){
                $('.loan_repayment_period_input_group').slideDown();
            }else{
                $('.loan_repayment_period_input_group').slideUp();
            }
        });

        if($('select[name="grace_period"]').val()){
        	$(this).trigger('change');
        }

        $(document).on('change','.loan_repayment_period_type',function(){
            var loan_type_options_id =  $(this).val();
            if(loan_type_options_id == 1){
                $('.fixed_repayment_period').slideDown();
                $('.varying_repayment_period').slideUp();
            }else if(loan_type_options_id == 2){
                $('.fixed_repayment_period').slideUp();
                $('.varying_repayment_period').slideDown();
            }
        });

        $(document).on('change','input[name="enable_loan_fines"]',function(){
        	if($(this).val()==1){
        		$('.enable_loan_fines_settings').slideDown();
        	}else{
        		$('.enable_loan_fines_settings').slideUp();
        	}
        });

        $(document).on('change','input[name="enable_referral_code"]',function(){
        	if($(this).val()==1){
        		$('.enable_reffaral_code_holder').slideDown();
        	}else{
        		$('.enable_reffaral_code_holder').slideUp();
        	}
        });

        var enable_referral_code = '<?php echo isset($_COOKIE['REFFERAL_CODE'])?$_COOKIE['REFFERAL_CODE']:'';?>';
        if(enable_referral_code !=''){
        	$('input[name="referral_code"]').val(enable_referral_code);
        	$('.enable_reffaral_code_holder').slideDown();
        }

        $(document).on('change','select[name="loan_fine_type"]',function(){
            var loan_fine_type = $(this).val();
            if(loan_fine_type==1){
                $('.late_loan_payment_fixed_fine').slideDown();
                $('.late_loan_payment_percentage_fine').slideUp();
                $('.late_loan_repayment_one_off_fine').slideUp();
            }else if(loan_fine_type==2){
                $('.late_loan_payment_percentage_fine').slideDown();
                $('.late_loan_payment_fixed_fine').slideUp();
                $('.late_loan_repayment_one_off_fine').slideUp();
            }else if(loan_fine_type==3){
                $('.late_loan_repayment_one_off_fine').slideDown();
                $('.late_loan_payment_percentage_fine').slideUp();
                $('.late_loan_payment_fixed_fine').slideUp();
            }else{
                $('.late_loan_payment_percentage_fine').slideUp();
                $('.late_loan_payment_fixed_fine').slideUp();
                $('.late_loan_repayment_one_off_fine').slideUp();
            }
        });

        $(document).on('change','select[name="one_off_fine_type"]',function(){
            var one_off_fine_type = $(this).val();
            if(one_off_fine_type==1){
                $('.one_off_fine_type_settings').show();
                $('.one_off_percentage_setting').hide();
                $('.one_off_fixed_amount_setting').show();
            }else if(one_off_fine_type==2){
                $('.one_off_fine_type_settings').show();
                $('.one_off_fixed_amount_setting').hide();
                $('.one_off_percentage_setting').show();
            }else if(one_off_fine_type=='')
            {
                $('.one_off_percentage_setting').hide();
                $('.one_off_fixed_amount_setting').hide();
                $('.one_off_fine_type_settings').hide();
            }
        });

        $(document).on('change','input[name="enable_outstanding_loan_balance_fines"]',function(){
        	var enable_outstanding_loan_balance_fines = $(this).val();
        	if(enable_outstanding_loan_balance_fines == 1){
        		$('.enable_outstanding_loan_balances_fines_settings').slideDown();
        	}else{
				$('.enable_outstanding_loan_balances_fines_settings').slideUp();
        	}
        });


        $(document).on('change','select[name=outstanding_loan_balance_fine_type]',function(){
           var outstanding_loan_balance_fine_type =$(this).val();
           if(outstanding_loan_balance_fine_type==1){
                $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
                $('.outstanding_loan_balance_percentage_settings').slideUp();
                $('.outstanding_loan_balance_fixed_fine').slideDown();
           }else if(outstanding_loan_balance_fine_type==2){
                $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
                $('.outstanding_loan_balance_fixed_fine').slideUp();
                $('.outstanding_loan_balance_percentage_settings').slideDown();
           }else if(outstanding_loan_balance_fine_type==3){
                $('.outstanding_loan_balance_percentage_settings').slideUp();
                $('.outstanding_loan_balance_fixed_fine').slideUp();
                $('.outstanding_loan_balance_fine_one_off_settings').slideDown();
           }else{
                $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
                $('.outstanding_loan_balance_percentage_settings').slideUp();
                $('.outstanding_loan_balance_fixed_fine').slideUp();
           }
        });

        $(document).on('change','input[name="loan_guarantors_type"]',function(){
            var loan_guarantors_type = $(this).val();
            if(loan_guarantors_type == '1'){
                $('.guarantor_settings_holder_every_savings').slideUp();
                $('.guarantor_settings_holder_every_time').slideDown();
            }else if(loan_guarantors_type == '2'){
                $('.guarantor_settings_holder_every_time').slideUp();
                $('.guarantor_settings_holder_every_savings').slideDown();
            }else{
                $('.guarantor_settings_holder_every_savings').slideUp();
                $('.guarantor_settings_holder_every_time').slideUp();
            }
        });

        $(document).on('change','input[name="enable_loan_guarantors"]',function(){
            var element = $(this).val();
            if(element == 1){
                $('.loan_guarantor_additional_details').slideDown();
            }else if(element == 2){
                $('.loan_guarantor_additional_details').slideUp();
            }else{
                $('.loan_guarantor_additional_details').slideUp(); 
            }

        });

        $(document).on('change','select[name="loan_processing_fee_type"]',function(){
            var loan_processing_fee_type = $(this).val();
            if(loan_processing_fee_type == 1){
                $('.percentage_loan_processing_fee').slideUp();
                $('.fixed_amount_processing_fee_settings').slideDown();
            }else if(loan_processing_fee_type==2){
                $('.fixed_amount_processing_fee_settings').slideUp();
                $('.percentage_loan_processing_fee').slideDown();
            }else{
                $('.fixed_amount_processing_fee_settings').slideUp();
                $('.percentage_loan_processing_fee').slideUp();
            }
        });

        $(document).on('change','input[name="enable_loan_processing_fee"]',function(){
        	var enable_loan_processing_fee = $(this).val();
        	if(enable_loan_processing_fee==1){
        		$('.loan_processing_fee_settings').slideDown();
        	}else{
        		$('.loan_processing_fee_settings').slideUp();
        	}
        });

        $(document).on('keyup keydown','input[name="maximum_repayment_period"],input[name="fixed_repayment_period"]',function(){
            if($(this).val()){
                $('.addition_loan_types_form_details,.form-actions').slideDown();
            }else{
                $('.addition_loan_types_form_details,form-actions').slideUp();
            }
        });

        $(document).on('change','input[name="allow_members_request_loan"]',function(){
        	var allow_members_request_loan;
        	if($(this).prop("checked") == true){
        		allow_members_request_loan  = 1;
        	}else{
        		allow_members_request_loan = 0;
        	}
        	$.post({
                url: base_url+"/ajax/update_group_details",
                data: {
                	"allow_members_request_loan":allow_members_request_loan,
                	"disable_member_directory": '<?php echo $this->group->disable_member_directory;?>',
                	"enable_member_information_privacy":'<?php echo $this->group->enable_member_information_privacy;?>'
                },
                type: "POST",
            });
        });

        $(document).on('change','input[name="disable_member_directory"]',function(){
        	var disable_member_directory;
        	if($(this).prop("checked") == true){
        		disable_member_directory  = 1;
        	}else{
        		disable_member_directory = 0;
        	}

        	$.post({
                url: base_url+"/ajax/update_group_details",
                data: {
                	"allow_members_request_loan":'<?php echo $this->group->allow_members_request_loan;?>',
                	"enable_member_information_privacy":'<?php echo $this->group->enable_member_information_privacy;?>',
                	"disable_member_directory":disable_member_directory
                },
                type: "POST",
            }); 	
        });

        $(document).on('change','input[name="enable_member_information_privacy"]',function(){
        	var enable_member_information_privacy;
        	if($(this).prop("checked") == true){
        		enable_member_information_privacy  = 1;
        	}else{
        		enable_member_information_privacy = 0;
        	}
        	$.post({
                url: base_url+"/ajax/update_group_details",
                data: {
                	"allow_members_request_loan":'<?php echo $this->group->allow_members_request_loan;?>',
                	"disable_member_directory": '<?php echo $this->group->disable_member_directory;?>',
                	"enable_member_information_privacy":enable_member_information_privacy
                },
                type: "POST",
            });
        });

        var position = '<?php echo ($this->group->group_setup_position<=1?2:$this->group->group_setup_position);?>';
        $(document).on('submit','#add_new_member_role_form',function(e){
        	e.preventDefault();
        });

        $(document).on('click','#add_bank_account_header',function(){
        	$('.datatable_bank_accounts').hide();
        	$(this).hide();
        	$('.btn.btn-setup-next,.btn.btn-setup-previous').attr('style','display:none !important');
        	$('#add_bank_account_setting').slideDown();
        	Inputmask.init();
        });

        $(document).on('click','#cancel_add_bank_account_form',function(){
        	$('#add_bank_account_setting').hide();
			Inputmask.init();
			$('#add_bank_account_header').show();
			html = add_bank_account_setting.replace_all('checker','');
			$('#add_bank_account_setting').html(html);
			$("#add_bank_account_setting .m-select2").select2({
				width: "100%",
	            placeholder: {
                  	id: '-1',
                  	text: "--Select option--",
               	}
			});
        	$('.datatable_bank_accounts').slideDown();
        	$('.btn.btn-setup-next,.btn.btn-setup-previous').attr('style','display:inline-block !important');
        });
        var bank_branch_id = '';

        $(document).on('change','select[name="bank_id"]',function(){
        	var bank_id = $(this).val();
        	mApp.block('.bank_change_options', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary'
            });
            if(bank_id){
                $.post('<?php echo site_url('ajax/bank_accounts/ajax_get_bank_branches');?>',{'bank_id':bank_id,'branch_id':bank_branch_id},
                function(data){
                    $('.bank_branches_space').html(data);
                    $('#bank_branch_id').select2({width:'100%'});
                    mApp.unblock('.bank_change_options');
                });
            }else{
            	var empty_branch_list = '<option>--Select bank first--</option>';
                $('.bank_branches_space').html('<select name="bank_id" class="form-control select2" id="bank_branch_id">'+empty_branch_list+'</select>');
                $('#bank_branch_id').select2();
                mApp.unblock('.bank_change_options');
            }
        });
        var sacco_branch_id = '';
        $(document).on('change','select[name="sacco_id"]',function(){
        	var sacco_id = $(this).val();
        	mApp.block('.sacco_change_options', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary'
            });
            if(sacco_id){
                $.post('<?php echo site_url('ajax/sacco_accounts/ajax_get_sacco_branches');?>',
                	{'sacco_id':sacco_id,'branch_id':sacco_branch_id},
                function(data){
                    $('.sacco_branches_space').html(data);
                    $('#sacco_branch_id').select2({width:'100%'});
                    mApp.unblock('.sacco_change_options');
                });
            }else{
            	var empty_branch_list = `<option>--Select ${entity_name} first--</option>`;
                $('.sacco_branches_space').html('<select name="sacco_id" class="form-control select2" id="sacco_branch_id">'+empty_branch_list+'</select>');
                $('#sacco_branch_id').select2();
                mApp.unblock('.sacco_change_options');
            }
        });
        if($('select[name="bank_id"]').val()){
        	$('select[name="bank_id"]').trigger('change');
        }
        if($('select[name="sacco_id"]').val()){
        	$('select[name="sacco_id"]').trigger('change');
        }

        $(document).on('click','.delete_member',function(){
        	var element = $(this);
        	element.parent().removeClass('show');
        	var member = element.data('memberName');
		    var message = 'Enter your password to confirm your request to delete member: "'+member+'" ?';
		    bootbox.prompt({
                title: message,
                inputType: 'password',
                required: true,
		        callback: function(password) {
		            if(password){
	                    var member_id = element.data('id');
	                    var content = $('.datatable_members');
	                    if(member_id){
	                    	mApp.block(content, {
				                overlayColor: 'grey',
				                animate: true,
				                type: 'loader',
				                state: 'primary',
				                'message': 'Processing...'
				            });
			        		$.ajax({
					            type: "POST",
					            data: {id:member_id,password:password},
					            url: "<?php echo site_url('ajax/members/delete');?>",
					            success: function (t, i, n, r) {
					            	mApp.unblock(content);
					            	if(isJson(t)){
					            		response = $.parseJSON(t);
					            		if(response.status == '1'){
					            			Toastr.show("Member Removed",response.message,'success');
					            			load_members();
					            		}else if(response.status == '202'){
					            			Toastr.show("Session Expired",response.message,'error');
					            			window.location.href = response.refer;
					            		}else{
					            			Toastr.show("Error occurred",response.message,'error');
					            		}
					            	}else{
					            		Toastr.show("Error occurred",'We could not complete the process at the moment. Try again later','error');
					            	}
					            },
					            error: function(){
					            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
					            	mApp.unblock(content);
					            },
					            always: function(){
					            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
					            	mApp.unblock(content);
					            }
					        });
			        	}
		            }else{
		                return true;
		            }
		        }
		    });
		    return false;
        });

        $(document).on('click','.delete_contribution',function(){
        	var element = $(this);
        	element.parent().removeClass('show');
        	var contribution = element.data('contributionName');
		    var message = 'Enter your password to confirm your request to delete contibution: "'+contribution+'" ?';
		    bootbox.prompt({
                title: message,
                inputType: 'password',
                required: true,
		        callback: function(password) {
		            if(password){
		                var contribution_id = element.data('id');
	                    var content = $('#contribution_listing_wrapper');
	                    if(contribution_id){
	                    	mApp.block(content, {
				                overlayColor: 'grey',
				                animate: true,
				                type: 'loader',
				                state: 'primary',
				                'message': 'Processing...'
				            });
			        		$.ajax({
					            type: "POST",
					            data: {id:contribution_id,password:password},
					            url: "<?php echo site_url('ajax/contributions/delete');?>",
					            success: function (t, i, n, r) {
					            	mApp.unblock(content);
					            	if(isJson(t)){
					            		response = $.parseJSON(t);
					            		if(response.status == '1'){
					            			Toastr.show("Contribution Removed",response.message,'success');
					            			load_contributions();
					            		}else if(response.status == '202'){
					            			Toastr.show("Session Expired",response.message,'error');
					            			window.location.href = response.refer;
					            		}else{
					            			Toastr.show("Error occurred",response.message,'error');
					            		}
					            	}else{
					            		Toastr.show("Error occurred",'We could not complete the process at the moment. Try again later','error');
					            	}
					            },
					            error: function(){
					            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
					            	mApp.unblock(content);
					            },
					            always: function(){
					            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
					            	mApp.unblock(content);
					            }
					        });
			        	}
		            }else{
		                return true;
		            }
		        }
		    });
		    return false;
        });

        $(document).on('click','.edit_contribution',function(){
        	var element = $(this);
        	var id = element.data('id');
        	var form = $('.create_contribution_settings');
        	if(id){
        		$('.datatable_contributions').hide();
	        	$("#create_contribution_form").hide();
	        	$('.btn.btn-setup-next,.btn.btn-setup-previous').attr('style','display:none !important');
	        	$('.create_contribution_settings').slideDown();
	        	mApp.block(form, {
	                overlayColor: 'grey',
	                animate: true,
	                type: 'loader',
	                state: 'primary',
	                'message': 'Loading contribution details...'
	            });
        		$.ajax({
		            type: "POST",
		            data: {id:id},
		            url: "<?php echo site_url('ajax/contributions/get');?>",
		            success: function (t, i, n, r) {
		            	mApp.unblock(form);
		            	var data = $.parseJSON(t);
		            	$(form).find('input[name="name"]').val(data.name);delete data['name'];
		            	$(form).find('input[name="id"]').val(data.id);delete data['id'];
		            	$(form).find('input[name="amount"]').val(data.amount);delete data['amount'];
		            	$(form).find('select[name="category"]').val(data.category).trigger('change');delete data['category'];
		            	$(form).find('select[name="type"]').val(data.type).trigger('change');
		            	if(data.type == 1){
		            		if(data.regular_invoicing_active == 1){
		            			$(form).find('input[name="regular_invoicing_active"][value="1"]').attr("checked",true).trigger('change');
		            		}else{
		            			$(form).find('input[name="regular_invoicing_active"][value="0"]').attr("checked",false).trigger('change');
		            		}
		            		delete data['regular_invoicing_active'];
		            		$(form).find('select[name="contribution_frequency"]').val(data.contribution_frequency).trigger('change');
		            		delete data['contribution_frequency'];
		            		delete data['type'];
		            		$.each(data,function(key,value){
		            			if(value){
		            				$(form).find('select[name="'+key+'"]').val(value).trigger('change');
		            			}
		            		});
		            	}
		            	if(data.email_notifications_enabled==1 || data.sms_notifications_enabled==1){
		            		$(form).find('input[name="sms_notification_email_notification"][value="1"]').attr("checked",true).trigger('change');
		            		delete data['sms_notification_email_notification'];
		            		if(data.email_notifications_enabled==1){
		            			$(form).find('input[name="email_notifications_enabled"][value="1"]').attr("checked",true).trigger('change');
		            			delete data['email_notifications_enabled'];
		            		}
		            		if(data.sms_notifications_enabled==1){
		            			$(form).find('input[name="sms_notifications_enabled"][value="1"]').attr("checked",true).trigger('change');
		            			delete data['sms_notifications_enabled'];
		            		}
		            	}
		            	if(data.enable_contribution_member_list==1){
		            		$(form).find('input[name="enable_contribution_member_list"][value="1"]').attr("checked",true).trigger('change');
		            		delete data['enable_contribution_member_list'];
		            		$(form).find('select[name="contribution_member_list[]"]').val(data.selected_group_members).trigger("change");
		            		delete data['selected_group_members'];
		            	}
		            	$.each(data,function(key,value){
		            		if(value==1){
		            			$(form).find('input[name="'+key+'"][value="1"]').attr("checked",true).trigger('change');
		            		}
		            	});
		            	if(data.enable_fines == 1){
		            		var contribution_fines = data.contribution_fine_settings;
		            		$.each(contribution_fines,function(key,contribution_fine){
		            			if(key>0){
		            				$(form).find('#add-new-fine-line').trigger('click');
		            			}
		            			var show_fine_type = false;
		            			var show_fixed_fine_mode = false;
		            			var show_fixed_fine_mode_val = 0;
		            			var show_percentage_fine_mode = 0;
		            			$.each(contribution_fine,function(new_key,new_value){
		            				if(new_key == 'fine_type'){
		            					show_fine_type = true;
		            					$(form).find('select[name="'+new_key+'['+key+']"]').val(new_value).trigger("change");
		            				}else{
		            					if(new_key == 'fixed_fine_mode'){
		            						show_fixed_fine_mode = true;
		            						show_fixed_fine_mode_val = new_value;
		            					}else if(new_key == 'percentage_fine_mode'){
		            						show_fixed_fine_mode = true;
		            						show_percentage_fine_mode = new_value;
		            					}else{
		            						$(form).find('select[name="'+new_key+'['+key+']"]').val(new_value).trigger("change");
			            					$(form).find('input[name="'+new_key+'['+key+']"]').val(new_value).trigger("change");
		            					}
		            				}
		            			});	
		            			if(show_fine_type==true && show_fixed_fine_mode==true){
		            				$(form).find('select[name="fixed_fine_mode['+key+']"]').val(show_fixed_fine_mode_val).trigger("change");
		            				$(form).find('select[name="percentage_fine_mode['+key+']"]').val(show_percentage_fine_mode).trigger("change");
		            			}	            			
		            		});
		            	}
		            	SnippetEditContribution.init(false);
					},
		            error: function(){
		            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
		            	mApp.unblock(form);
		            	$('#cancel_create_contribution_form').trigger('click');
		            },
		            always: function(){
		            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
		            	mApp.unblock(form);
		            	$('#cancel_create_contribution_form').trigger('click');
		            }
		        });
        	}else{

        	}
        });

		$(document).on('click','.view_contribution',function(){
			var element = $(this);
			$('.contribution_details_content').html('').hide();
			$('.contribution_name').text('');
        	element.parent().removeClass('show');
        	var contribution = element.data('contributionName');
        	var content = $('.block_content');
        	var id = element.data('id');
        	if(id){
        		$('.contribution_name').text(contribution);
        		$.ajax({
		            type: "GET",
		            url: '<?php echo base_url("ajax/contributions/get_contribution_view/"); ?>'+id,
	                success: function(data) {
	                	if(isJson(data)){
	                		response = $.parseJSON(data);
	                		if(response.status == '1'){
	                			mApp.unblock('#view_contribution_details .modal-body');
	                   			$('.contribution_details_content').html(response.message).slideDown();
	                		}else if(response.status == '202'){
								Toastr.show("Session Expired",response.message,'error');
								window.location.href = response.refer;
							}else{
								mApp.unblock('#view_contribution_details .modal-body');
	                   			$('.modal .close').trigger('click');
	                    		Toastr.show("Error occured",response.message,'error');
							}
	                	}else{
	                   		mApp.unblock('#view_contribution_details .modal-body');
	                   		$('.modal .close').trigger('click');
	                    	Toastr.show("Error occured",'Could not complete getting contribution details. Data not received. Try again later','error');
	                   	}
	                },
	                error: function(){
	                    mApp.unblock('#view_contribution_details .modal-body');
	                    $('.modal .close').trigger('click');
	                    Toastr.show("Error occured",'Could not complete getting contribution details. Try again later','error');
	                },
	                always: function(){
	                    mApp.unblock('#view_contribution_details .modal-body');
	                    $('.modal .close').trigger('click');
	                    Toastr.show("Error occured",'Could not complete getting contribution details. Try again later','error');
	                }
		        });
        	}else{
        		//could not get contributions
        	}
		});

		$(document).on('click','.delete_loan_type',function(){
        	var element = $(this);
        	element.parent().removeClass('show');
        	var loan_type = element.data('loanType');
		    var message = 'Enter your password to confirm your request to delete loan type: "'+loan_type+'" ?';
		    bootbox.prompt({
                title: message,
                inputType: 'password',
                required: true,
		        callback: function(password) {
		            if(password){
		                var loan_type_id = element.data('id');
	                    var content = $('#loan_types_listing_wrapper');
	                    if(loan_type_id){
	                    	mApp.block(content, {
				                overlayColor: 'grey',
				                animate: true,
				                type: 'loader',
				                state: 'primary',
				                'message': 'Processing...'
				            });
			        		$.ajax({
					            type: "POST",
					            data: {id:loan_type_id,password:password},
					            url: "<?php echo site_url('ajax/loan_types/delete');?>",
					            success: function (t, i, n, r) {
					            	mApp.unblock(content);
					            	if(isJson(t)){
					            		response = $.parseJSON(t);
					            		if(response.status == '1'){
					            			Toastr.show("Loan Type Removed",response.message,'success');
					            			load_loan_types();
					            		}else if(response.status == '202'){
					            			Toastr.show("Session Expired",response.message,'error');
					            			window.location.href = response.refer;
					            		}else{
					            			Toastr.show("Error occurred",response.message,'error');
					            		}
					            	}else{
					            		Toastr.show("Error occurred",'We could not complete the process at the moment. Try again later','error');
					            	}
					            },
					            error: function(){
					            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
					            	mApp.unblock(content);
					            },
					            always: function(){
					            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
					            	mApp.unblock(content);
					            }
					        });
			        	}
		            }else{
		                return true;
		            }
		        }
		    });
		    return false;
        });

        $(document).on('click','.view_loan_type',function(){
			var element = $(this);
			$('.loan_type_details_content').html('').hide();
			$('.loan_type_name').text('');
        	element.parent().removeClass('show');
        	var contribution = element.data('loanType');
        	var content = $('.block_content');
        	var id = element.data('id');
        	if(id){
        		$('.loan_type_name').text(contribution);
        		$.ajax({
		            type: "GET",
		            url: '<?php echo base_url("ajax/loan_types/get_loan_type_view/"); ?>'+id,
	                success: function(data) {
	                	if(isJson(data)){
	                		response = $.parseJSON(data);
	                		if(response.status == '1'){
	                			mApp.unblock('#view_loan_type_details .modal-body');
	                   			$('.loan_type_details_content').html(response.message).slideDown();
	                		}else if(response.status == '202'){
								Toastr.show("Session Expired",response.message,'error');
								window.location.href = response.refer;
							}else{
								mApp.unblock('#view_loan_type_details .modal-body');
	                   			$('.modal .close').trigger('click');
	                    		Toastr.show("Error occured",response.message,'error');
							}
	                	}else{
	                   		mApp.unblock('#view_loan_type_details .modal-body');
	                   		$('.modal .close').trigger('click');
	                    	Toastr.show("Error occured",'Could not complete getting loan type details. Data not received. Try again later','error');
	                   	}
	                },
	                error: function(){
	                    mApp.unblock('#view_loan_type_details .modal-body');
	                    $('.modal .close').trigger('click');
	                    Toastr.show("Error occured",'Could not complete getting loan type details. Try again later','error');
	                },
	                always: function(){
	                    mApp.unblock('#view_loan_type_details .modal-body');
	                    $('.modal .close').trigger('click');
	                    Toastr.show("Error occured",'Could not complete getting loan type details. Try again later','error');
	                }
		        });
        	}else{
        		//could not get contributions
        	}
		});

		$(document).on('click','.edit_loan_type',function(){
			var element = $(this);
        	var id = element.data('id');
        	element.parent().removeClass('show');
        	var form = $('#create_loan_type');
        	if(id){
        		$('.load_group_loan_types').hide();
	        	$('#create_loan_type_header').hide();
	        	$('.create_loan_type_settings_layout').slideDown();
	        	if(group_offer_loans){
	        		$('input[name="group_offer_loans"]').val('1').trigger('change');
	        	}else{
	        		$('input[name="group_offer_loans"]').val('0').trigger('change');
	        	}
	        	mApp.block(form, {
	                overlayColor: 'grey',
	                animate: true,
	                type: 'loader',
	                state: 'primary',
	                'message': 'Loading loan type details...'
	            });
	            $.ajax({
		            type: "POST",
		            data: {id:id},
		            url: "<?php echo site_url('ajax/loan_types/get');?>",
		            success: function (t, i, n, r) {
		            	mApp.unblock(form);
		            	var data = $.parseJSON(t);
		            	$.each(data,function(key,value){
		            		if(value){
		            			if(key=='enable_loan_fines' && value==1){
		            				$(form).find('input[name="'+key+'"][value="'+value+'"]').attr("checked",true).trigger('change');
		            			}else if(key == 'enable_outstanding_loan_balance_fines'  && value==1){
		            				$(form).find('input[name="'+key+'"][value="'+value+'"]').attr("checked",true).trigger('change');
		            			}else if(key =='enable_loan_guarantors' && value==1){
		            				$(form).find('input[name="'+key+'"][value="'+value+'"]').attr("checked",true).trigger('change');
		            			}else if(key=='loan_guarantors_type'){
		            				if(value == '1'){
		            					$(form).find('input[name="'+key+'"][value="'+value+'"]').attr("checked",false);
		            					$(form).find('input[name="'+key+'"][value="'+value+'"]').attr("checked",true).trigger('change');
		            					$(form).find('input[name="minimum_guarantors"]').val(data.minimum_guarantors);
		            					delete data["minimum_guarantors"];
		            				}else if(value==2){
		            					$(form).find('input[name="'+key+'"][value="'+value+'"]').attr("checked",false);
		            					$(form).find('input[name="'+key+'"][value="'+value+'"]').attr("checked",true).trigger('change');
		            					$(form).find('input[name="minimum_guarantors_exceed_amount"]').val(data.minimum_guarantors).trigger('change');
		            					delete data["minimum_guarantors"];
		            				}
		            			}else if(key == 'enable_loan_processing_fee'){
		            				$(form).find('input[name="'+key+'"][value="'+value+'"]').attr("checked",true).trigger('change');
		            			}else{
		            				$(form).find('input[name="'+key+'"]').val(value).triggerAll('change keydown keyup');
		            				$(form).find('select[name="'+key+'"]').val(value).triggerAll('change keydown keyup');
		            			}
		            		}else{
		            			if(key=='enable_loan_fines' || key == 'enable_outstanding_loan_balance_fines' || key =='enable_loan_guarantors' || key == 'enable_loan_processing_fee'){
		            				$(form).find('input[name="'+key+'"][value="'+value+'"]').attr("checked",true).trigger('change');
		            			}
		            		}
		            	});

		            	SnippetEditLoanType.init(false);
					},
		            error: function(){
		            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
		            	mApp.unblock(form);
		            	$('#cancel_create_loan_type_form').trigger('click');
		            },
		            always: function(){
		            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
		            	mApp.unblock(form);
		            	$('#cancel_create_loan_type_form').trigger('click');
		            }
		        });
			}else{

        	}
		});

		$(document).on('click','.disconnect_account',function(){
			var element = $(this);
        	element.parent().removeClass('show');
        	var account_name = element.data('accountName');
        	var message = 'Enter your password to disconnect the account to stop receiving alerts';
        	bootbox.prompt({
                title: message,
                inputType: 'password',
                required: true,
		        callback: function(password) {
		            if(password){
		                var id = element.data('id');
	                    var content = $('#bank_account_listing_wrapper');
	                    if(id){
	                    	mApp.block(content, {
				                overlayColor: 'grey',
				                animate: true,
				                type: 'loader',
				                state: 'primary',
				                'message': 'Processing...'
				            });
                    		$.ajax({
					            type: "POST",
					            data: {id:id,password:password},
					            url: "<?php echo site_url('ajax/bank_accounts/disconnect_account');?>",
					            success: function (t, i, n, r) {
					            	mApp.unblock(content);
					            	if(isJson(t)){
					            		response = $.parseJSON(t);
					            		if(response.status == '1'){
					            			Toastr.show(`${entity_name} Account Disconnected`,response.message,'success');
					            			load_bank_accounts();
					            		}else if(response.status == '202'){
					            			Toastr.show("Session Expired",response.message,'error');
					            			window.location.href = response.refer;
					            		}else{
					            			Toastr.show("Error occurred",response.message,'error');
					            		}
					            	}else{
					            		Toastr.show("Error occurred",'We could not complete the process at the moment. Try again later','error');
					            	}
					            },
					            error: function(){
					            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
					            	mApp.unblock(content);
					            },
					            always: function(){
					            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
					            	mApp.unblock(content);
					            }
					        });	
			        	}
		            }else{
		                return true;
		            }
		        }
		    });
		    return false;
		});

		$(document).on('click','.delete_account',function(){
        	var element = $(this);
        	element.parent().removeClass('show');
        	var account_name = element.data('accountName');
		    var message = 'Enter your password to confirm your request to delete group account: "'+account_name+'" ?';
		    bootbox.prompt({
                title: message,
                inputType: 'password',
                required: true,
		        callback: function(password) {
		            if(password){
		                var id = element.data('id');
	                    var content = $('#bank_account_listing_wrapper');
	                    if(id){
	                    	mApp.block(content, {
				                overlayColor: 'grey',
				                animate: true,
				                type: 'loader',
				                state: 'primary',
				                'message': 'Processing...'
				            });
				            url = '';
	                    	if(id.match(/bank/g)){
	                    		url = "<?php echo site_url('ajax/bank_accounts/delete');?>";
	                    	}else if(id.match(/sacco/g)){
	                    		url = "<?php echo site_url('ajax/sacco_accounts/delete');?>";
	                    	}else if(id.match(/mobile/g)){
	                    		url = "<?php echo site_url('ajax/mobile_money_accounts/delete');?>";
	                    	}else if(id.match(/petty/g)){
	                    		url = "<?php echo site_url('ajax/petty_cash_accounts/delete');?>";
	                    	}else{
	                    		Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
					        	mApp.unblock(content);
	                    	}
	                    	if(url){
	                    		$.ajax({
						            type: "POST",
						            data: {id:id,password:password},
						            url: url,
						            success: function (t, i, n, r) {
						            	mApp.unblock(content);
						            	if(isJson(t)){
						            		response = $.parseJSON(t);
						            		if(response.status == '1'){
						            			Toastr.show(`${entity_name} Account Removed`,response.message,'success');
						            			load_bank_accounts();
						            		}else if(response.status == '202'){
						            			Toastr.show("Session Expired",response.message,'error');
						            			window.location.href = response.refer;
						            		}else{
						            			Toastr.show("Error occurred",response.message,'error');
						            		}
						            	}else{
						            		Toastr.show("Error occurred",'We could not complete the process at the moment. Try again later','error');
						            	}
						            },
						            error: function(){
						            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
						            	mApp.unblock(content);
						            },
						            always: function(){
						            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
						            	mApp.unblock(content);
						            }
						        });
	                    	}	
			        	}
		            }else{
		                return true;
		            }
		        }
		    });
		    return false;
		});

		$(document).on('click','.connect_account',function(){
        	$(".signatory_phone_number").each(function(){
                if(this.hasAttribute("data-intl-tel-input-id")){
                }else{
                    window.intlTelInput(this, {
                        allowDropdown: true,
                        formatOnDisplay: true,
                        preferredCountries: ['ke', 'tz', 'ug'],
                        separateDialCode: true,
                        utilsScript: "<?php echo site_url('templates/admin_themes/admin/intl-tel-input/js/utils.js');?>",
                    });
                 }
             });
            EmailPhoneValidation.init();
            var element = $(this);
            var rawId = element.data('id');
        	var id = (rawId).replace ( /[^\d.]/g, '' );
        	var account_name = element.data('account-name');
        	var account_number = element.data('account-number');
        	var bank_id = element.data('bank-id');
        	SnippetLinkBankAccount.init(true,id,bank_id,true);
        	// $('#account_notificaton_space,#otp_message_space').slideDown();
    //     	if(account_notification_keys_bodies.hasOwnProperty(rawId)){
    //     		var account_notification_body = account_notification_keys_bodies[rawId];
    //     		if(account_notification_body){
	   //      		var notificationSpace = '<div class="m-radio-list">';
	   //      		for(var i=0;i<account_notification_body.length;i++){
				// 		notificationSpace+='<label class="m-radio m-radio--solid m-radio--brand"><input type="radio" name="notification_channel" value="'+(account_notification_body[i].recipientKey)+'">'+(account_notification_body[i].destination)+' ('+(account_notification_body[i].recipientValue)+')'+'<span></span></label>';
				// 	}
				// 	notificationSpace+='</div>';
				// 	$('#account_notificaton_space').html(notificationSpace);
				// }
    //     	}
        	$('#connect_account').trigger('click');
        	$('.menu_cont_descr .account_name').text(account_name);
        	$('.menu_cont_descr .account_number').text(account_number);
        	$('#connect_equity_account_form input[name="account_number"]').val(account_number).attr("readonly","readonly");
        	$('#connect_equity_account_form input[name="id"]').val(id).attr("readonly","readonly");
        	$('#connect_equity_account_form input[name="account_name"]').val(account_name).attr("readonly","readonly");
        	$('#connect_equity_account_form input[name="bank_id"]').val(bank_id).attr("readonly","readonly");
        	$(".signatory_phone_number").attr("style","padding-left: 80px;");
        	$('.iti-arrow').attr("style","right: -7px !important;");
        	// $('#connect_equity_account_form input[name="phone"]').removeAttr("readonly");
			$('.btn#verify_equity_account_btn').removeAttr("id").attr('id',"connect_equity_account_btn");
			$('.verification_settings').addClass("d-none");
		});

		$(document).on('click','.edit_account',function(){
			var element = $(this);
        	var id = element.data('id');
        	element.parent().removeClass('show');
        	if(id){
        		$('#add_bank_account_header').trigger('click');
        		$('a[href="#bank_account_tab"]').addClass('disabled');
        		$('a[href="#sacco_account_tab"]').addClass('disabled');
        		$('a[href="#mobile_money_account_tab"]').addClass('disabled');
        		$('a[href="#petty_cash_account_tab"]').addClass('disabled');
        		if(id.match(/bank/g)){
            		url = "<?php echo site_url('ajax/bank_accounts/get');?>";
        			$('a[href="#bank_account_tab"]').removeClass('disabled').trigger('click');
        			$('#account_signatories,#account_name,.btn#add_bank_account_button').removeAttr('readonly');
			    	$('.bank_account_hidden_fields').slideDown();
			    	$('#account_name,#account_currency_id').attr("readonly","readonly");
		    		$('#account_name_currency_space,#signatories_account_balance_space').slideDown();
            		form = $('#add_bank_account');
            		SnippetEditBankAccount.init(false,false);
            	}else if(id.match(/sacco/g)){
            		url = "<?php echo site_url('ajax/sacco_accounts/get');?>";
            		$('a[href="#sacco_account_tab"]').removeClass('disabled').trigger('click');
            		form = $('#create_sacco_accounts');
            		SnippetEditSaccoAccount.init(false,false);
            	}else if(id.match(/mobile/g)){
            		$('a[href="#mobile_money_account_tab"]').removeClass("disabled").trigger('click');
            		url = "<?php echo site_url('ajax/mobile_money_accounts/get');?>";
            		form = $('#mobile_money_form');
            		SnippetEditMobileMoneyAccount.init(false,false);
            	}else if(id.match(/petty/g)){
            		$('a[href="#petty_cash_account_tab"]').removeClass('disabled').trigger('click');
            		url = "<?php echo site_url('ajax/petty_cash_accounts/get');?>";
            		form = $('#petty_cash_form');
            		SnippetEditPettyCashAccount.init(false,false);
            	}else{
            		
            	}
            	if(url){
	            	mApp.block(form, {
		                overlayColor: 'grey',
		                animate: true,
		                type: 'loader',
		                state: 'primary',
		                'message': 'Loading account details...'
		            });
		            $.ajax({
			            type: "POST",
			            data: {id:id},
			            url: url,
			            success: function (t, i, n, r) {
			            	mApp.unblock(form);
			            	var data = $.parseJSON(t);
			            	var sacco_id = 0;
			            	bank_branch_id = '';
			            	sacco_branch_id = '';
			            	$.each(data,function(key,value){
			            		if(value){
			            			if(key == 'sacco_id'){
			            				sacco_branch_id = data.sacco_branch_id;
			            				sacco_id = value;
			            			}else if(key == 'bank_id'){
			            				bank_branch_id = data.bank_branch_id;
			            				bank_id = value;
			            			}else if(key == 'signatories'){
			            				$(form).find('select[name="account_signatories[]"]').val(data.signatories).trigger("change");
			            			}else{
			            				$(form).find('select[name="'+key+'"]').val(value).trigger('change');
			            				$(form).find('input[name="'+key+'"]').val(value).trigger('change');
			            			}
			            		}
			            	});
			            	if(sacco_branch_id){
			            		$(form).find('select[name="sacco_id"]').val(sacco_id).trigger('change');
			            	}else if(bank_branch_id){
								$(form).find('select[name="bank_id"]').val(bank_id).trigger('change');
			            	}
						},
			            error: function(){
			            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
			            	mApp.unblock(form);
			            	$('#cancel_add_bank_account_form').trigger('click');
			            },
			            always: function(){
			            	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
			            	mApp.unblock(form);
			            	$('#cancel_add_bank_account_form').trigger('click');
			            }
			        });
		        }else{
		        	Toastr.show("Error Occured",'We could not complete the process at the moment. Try again later','error');
	            	mApp.unblock(form);
	            	$('#cancel_add_bank_account_form').trigger('click');
		        }
        	}
		});
		
			

		$('#view_contribution_details').on('shown.bs.modal',function(){
	        mApp.block('#view_contribution_details .modal-body', {
	            overlayColor: 'grey',
	            animate: true,
	            type: 'loader',
	            state: 'primary',
	            message: 'Fetching contribution details...'
	        });
	    });

	    $('#view_loan_type_details').on('shown.bs.modal',function(){
	        mApp.block('#view_loan_type_details .modal-body', {
	            overlayColor: 'grey',
	            animate: true,
	            type: 'loader',
	            state: 'primary',
	            message: 'Fetching loan type details...'
	        });
	    });


	    $(document).on('keyup','#bank_account_number',function(){
	    	var bank_account_number = $(this);
	    	var account_number = $(bank_account_number).val();
	    	var default_bank = <?php echo $default_bank->id;?>;
	    	console.log(account_number);
	    	var bank_id = $("#bank_id").val();
	    	console.log("bank id"+bank_id);
	    	console.log("default_bank id"+default_bank);
	    	if(bank_id == default_bank){
	    		console.log("We in");
		    	if(account_number.length == 13){
		    		$(bank_account_number).attr("disabled","disabled").unbind('keyup');
		    		RemoveDangerClass();
		    		var e = $(".btn#add_bank_account_button"),
	                a = $("#add_bank_account");
	                mApp.block(a, {
	                    overlayColor: 'grey',
	                    animate: true,
	                    type: 'loader',
	                    state: 'primary',
	                    message: 'Validating Account Details...'
	                });
	                var data = {"account_number":account_number,"recipient_bank_id":bank_id}
	                console.log("data",data);
	                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
	                	data: data,
	                    type: "POST",
	                    url:   base_url+"/ajax/recipients/lookup_account_details",
	                    success: function (t, i, n, r) {
	                        if(isJson(t)){
	                            response = $.parseJSON(t);
	                            if(response.status == '1'){
	                                setTimeout(function () {
	                                	$.ajax({
								            type: "POST",
								            data: {"currency_code":response.account_currency},
								            url: "<?php echo site_url('ajax/countries/get_currency_option_by_code');?>",
								            success: function(response2){
								            	e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),bank_account_number.removeAttr("disabled"),
		                                        a.find(".alert").html('').slideUp(),mApp.unblock(a);
		                                        Toastr.show("Success",response.message,'success');
		                                        console.log(response);
		                                        $('.bank_account_hidden_fields').slideDown();
		                                        $('#account_name').val(response.account_name);
		                                        console.log('currencyId : '+response2);
		                                        $('#account_currency_id').val(response2+"").trigger('change').attr("readonly","readonly");
		                                        $('#account_signatories').removeAttr('readonly');
		                                        $('#account_name_currency_space,#signatories_account_balance_space').slideDown();
		                                        //$('#cancel_add_bank_account_form').trigger('click');
		                                        load_bank_accounts();     
								            },
								            error:function(){

								            },
								            always:function(){

								            }
								        });  
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
	                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),bank_account_number.removeAttr("disabled"),
	                                    function (t, e, a) {
	                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
	                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
	                                    }(a, "danger", message),mApp.unblock(a);
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
	                                    mUtil.scrollTop();
	                                }, 2e3)
	                            }
	                        }else{
	                            setTimeout(function () {
	                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),bank_account_number.removeAttr("disabled"),
	                                    function (t, e, a) {
	                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
	                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
	                                    }(a, "danger", "Could not complete processing the request at the moment.")
	                            }, 2e3)
	                        }
	                    },
	                    error: function(){
	                        setTimeout(function () {
	                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),bank_account_number.removeAttr("disabled"),
	                                function (t, e, a) {
	                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
	                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
	                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
	                        }, 2e3)
	                    },
	                    always: function(){
	                        setTimeout(function () {
	                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),bank_account_number.removeAttr("disabled"),
	                                function (t, e, a) {
	                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
	                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
	                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
	                        }, 2e3)
	                    }
	                }));
		    	}else{
		    		$('#account_name').val("").trigger('change').attr("readonly","readonly");
                    $('#account_currency_id').val("").trigger('change').attr("readonly","readonly");
		    		$('#account_name_currency_space,#signatories_account_balance_space').slideUp();
		    	}
		    }else{
	    		console.log("We out");
		    	$('#account_signatories,#account_name,.btn#add_bank_account_button').removeAttr('readonly');
		    	$('.bank_account_hidden_fields').slideDown();
		    	$('#account_name').removeAttr("readonly");
                $('#account_currency_id').removeAttr("readonly");
	    		$('#account_name_currency_space,#signatories_account_balance_space').slideDown();
		    }
	    });

        ManageSetupWizard.init(position,group_offer_loans);
        SnippetImportMembers.init(false);
        SnippetAddMembersLine.init(false);
        SnippetCreateContribution.init(false);
        SnippetCreateLoanType.init(false);
        SnippetCreateGroupRole.init(false,true);
        SnippetCompleteGroupSetup.init();
        SnippetAddBankAccount.init(false,false);
        SnippetCreatePettyCashAccount.init(false,false);
        SnippetCreateMobileMoneyAccount.init(false,false);
        SnippetCreateSaccoAccount.init(false,false);
        SnippetValidateAccountSignatory.init();
        SnippetConfirmAccountSignatory.init();
	});

	function handle_tab_switch(tab){
        //check tab
        //clear values on other tabs
        //slide up on other tabs
        $('#create_new_account_pop_up .error').html('').slideUp();
        if(tab == 'bank_account'){
        	//alert('we here');
            $(':input','#mobile_money_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#mobile_money_account_tab .mobile_money_account_number').slideUp();
            $(':input','#sacco_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }else if(tab == 'sacco_account'){
            $(':input','#mobile_money_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#mobile_money_account_tab .mobile_money_account_number').slideUp();
            $(':input','#bank_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            // $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }else if(tab == 'mobile_money_account'){
            $(':input','#sacco_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number').slideUp();
            $(':input','#bank_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            // $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }else if(tab == 'petty_cash_account'){
            $(':input','#sacco_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number').slideUp();
            $(':input','#bank_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            // $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number').slideUp();
            $(':input','#mobile_money_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#mobile_money_account_tab .mobile_money_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }
    }
	
	function load_members(){
		if ( $.fn.DataTable.isDataTable('.members') ) {
		  $('.members').DataTable().destroy();
		}
		var membersTable = $('.members').DataTable({
        	scrollY: "50vh",
           	scrollX: !0,
           	scrollCollapse: !0,
           	searching: !0,
           	orderable: !1,
           	'serverSide' : true,
           	'serverMethod' : 'post',
           	"processing": true,
           	"initComplete": function(){initSearch('.members');},
           	"lengthMenu": [[10,20,50,100, -1], [10,20, 50, 100, "All"]],
           	"ajax": '<?php echo base_url("ajax/members/get_members_setup_listing"); ?>',
           	columnDefs: [{
				targets: -1,
				title: "Actions",
				orderable: !1,
				render: function (a, e, t, n) {
					var id = t['5'];
					var member_name = t['1'];
					return '\n                        <span class="dropdown">\n                            <a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true">\n                              <i class="la la-ellipsis-h"></i>\n                            </a>\n                            <div class="dropdown-menu dropdown-menu-right">\n                                <a class="dropdown-item edit_member" data-id="'+id+'" href="#"><i class="la la-edit"></i> Edit Details</a>\n                                <a class="dropdown-item delete_member" data-id="'+id+'" data-member-name="'+member_name+'" href="#"><i class="la la-trash"></i> Remove Member</a>\n </div>\n                        </span>'
				}
			},{ 
				orderable: !1, 
				targets: [0,2,3,4] 
			},{
				searching: !1,
				targets: [0,3,4] 
			}
			],
			"pageLength":10
        });
	}

	function load_contributions(){
		if ( $.fn.DataTable.isDataTable('.contributions') ) {
		  $('.contributions').DataTable().destroy();
		}
		$('.contributions').DataTable({
        	scrollY: "50vh",
           	scrollX: !0,
           	scrollCollapse: !0,
           	searching: !0,
           	orderable: !1,
           	'serverSide' : true,
           	'serverMethod' : 'post',
           	"processing": true,
           	"initComplete": function(){initSearch('.contributions');},
           	"lengthMenu": [[10,20,50,100, -1], [10,20, 50, 100, "All"]],
           	"ajax": '<?php echo base_url("ajax/contributions/setup_listing"); ?>',
           	columnDefs: [{
				targets: -1,
				title: "Actions",
				orderable: !1,
				render: function (a, e, t, n) {
					var id = t['5'];
					var contribution = t['1'];
					return '\n                        <span class="dropdown">\n                            <a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true">\n                              <i class="la la-ellipsis-h"></i>\n                            </a>\n                            <div class="dropdown-menu dropdown-menu-right">\n                                <a class="dropdown-item edit_contribution" data-id="'+id+'" href="#"><i class="la la-edit"></i> Edit Contribution</a>\n 	<a class="dropdown-item view_contribution" data-id="'+id+'" data-contribution-name="'+contribution+'" href="#" data-toggle="modal" data-target="#view_contribution_details" data-keyboard="false" data-backdrop="static"><i class="mdi mdi-eye-check-outline"></i> View More Details</a>\n 	<div class="dropdown-divider"></div>\n		<a class="dropdown-item delete_contribution" data-id="'+id+'" data-contribution-name="'+contribution+'" href="#"><i class="la la-trash"></i> Delete Contribution</a>\n </div>\n                        </span>'
				}
			},{ 
				orderable: !1, 
				targets: [0,2,3,4] 
			},{
				searching: !1,
				targets: [0,3,4] 
			}
			],
			"pageLength":10
        });
	}

	function load_loan_types(){
		if ( $.fn.DataTable.isDataTable('.loan_types') ) {
		  $('.loan_types').DataTable().destroy();
		}
		$('.loan_types').DataTable({
        	scrollY: "50vh",
           	scrollX: !0,
           	scrollCollapse: !0,
           	searching: !0,
           	orderable: !1,
           	'serverSide' : true,
           	'serverMethod' : 'post',
           	"processing": true,
           	"initComplete": function(){initSearch('.loan_types');},
           	"lengthMenu": [[10,20,50,100, -1], [10,20, 50, 100, "All"]],
           	"ajax": '<?php echo base_url("ajax/loan_types/setup_listing"); ?>',
           	columnDefs: [{
				targets: -1,
				title: "Actions",
				orderable: !1,
				render: function (a, e, t, n) {
					var id = t['4'];
					var loan_type = t['1'];
					return '\n                        <span class="dropdown">\n                            <a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true">\n                              <i class="la la-ellipsis-h"></i>\n                            </a>\n                            <div class="dropdown-menu dropdown-menu-right">\n                                <a class="dropdown-item edit_loan_type" data-id="'+id+'" href="#"><i class="la la-edit"></i> Edit Loan Type</a>\n 	<a class="dropdown-item view_loan_type" data-id="'+id+'" data-loan-type="'+loan_type+'" href="#" data-toggle="modal" data-target="#view_loan_type_details" data-keyboard="false" data-backdrop="static"><i class="mdi mdi-eye-check-outline"></i> View More Details</a>\n 	<div class="dropdown-divider"></div>\n		<a class="dropdown-item delete_loan_type" data-id="'+id+'" data-loan-type="'+loan_type+'" href="#"><i class="la la-trash"></i> Delete Loan Type</a>\n </div>\n                        </span>'
				}
			},{ 
				orderable: !1, 
				targets: [0,2,3,4] 
			},{
				searching: !1,
				targets: [0,3,4] 
			}
			],
			"pageLength":10
        });
	}

	function load_bank_accounts(id=0,isDefault=false){
        if ( $.fn.DataTable.isDataTable('.bank_accounts') ) {
		  $('.bank_accounts').DataTable().destroy();
		}
		$('.bank_accounts').DataTable({
        	scrollY: "50vh",
           	scrollX: !0,
           	scrollCollapse: !0,
           	searching: !0,
           	orderable: !1,
           	"lengthChange": false,
           	'serverSide' : true,
           	'serverMethod' : 'post',
           	"processing": true,
           	"initComplete": function(){initSearch('.bank_accounts');},
           	"lengthMenu": [[10,20,50,100, -1], [10,20, 50, 100, "All"]],
           	"ajax": '<?php echo base_url("ajax/bank_accounts/setup_listing"); ?>',
           	columnDefs: [{
				targets: -1,
				title: "Actions",
				orderable: !1,
				render: function (a, e, t, n) {
					var id = t['4'];
					var account_name = t['1'];
					var account_number = t['7'];
					var account_name_full = t['8'];
					var account_notification = t['9'];
					var bank_id = t['10'];
					var is_wallet = t['11'];
					var is_default = t['5']?1:0;
					var is_verified = t['6']?1:0;
					// if(isJson(account_notification)){
					// 	account_notification_keys_bodies[id] = $.parseJSON(account_notification);
					// }
					if(is_wallet){
						return '';
					}else if(is_default==1){
						if(is_verified ==1){
							return '';                        
							// <span class="dropdown">\n                            <a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true">\n                              <i class="la la-ellipsis-h"></i>\n                            </a>\n                            <div class="dropdown-menu dropdown-menu-right">\n                                <a class="dropdown-item disconnect_account" id="disconnect_account_'+id+'" data-id="'+id+'" href="#"><i class="la la-pencil"></i> Disconnect Account</a>\n </div>\n                        </span>';
						}else{
							//<a class="dropdown-item disconnect_account" id="disconnect_account_'+id+'" data-id="'+id+'" href="#"><i class="la la-pencil"></i> Disconnect Account</a>\n
							return '\n                        <span class="dropdown">\n                            <a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true">\n                              <i class="la la-ellipsis-h"></i>\n                            </a>\n                            <div class="dropdown-menu dropdown-menu-right">\n                                <a class="dropdown-item edit_account" data-id="'+id+'" href="#"><i class="la la-edit"></i> Edit Account</a>\n		<a class="dropdown-item delete_account" data-id="'+id+'" data-account-name="'+account_name+'" href="#"><i class="la la-trash"></i> Delete Account</a>\n </div>\n                        </span>';
						}
					}else{
						return '\n                        <span class="dropdown">\n                            <a href="#" class="btn m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown" aria-expanded="true">\n                              <i class="la la-ellipsis-h"></i>\n                            </a>\n                            <div class="dropdown-menu dropdown-menu-right">\n                                <a class="dropdown-item edit_account" data-id="'+id+'" href="#"><i class="la la-edit"></i> Edit Account</a>\n		<a class="dropdown-item delete_account" data-id="'+id+'" data-account-name="'+account_name+'" href="#"><i class="la la-trash"></i> Delete Account</a>\n </div>\n                        </span>';
					}
				}
			},{ 
				orderable: !1, 
				targets: [0,2,3,4] 
			},{
				searching: !1,
				targets: [0,3,4] 
			}
			],
			"pageLength":10,
			"initComplete" : function(){
				if(isDefault){
		            // $('#connect_account_'+id).trigger('click');
				}
			},
        });

		
	}

	function load_group_members(){
		$.ajax({
            type: "POST",
            data: '',
            url: "<?php echo site_url('ajax/members/get_group_member_options');?>",
            success: function(response){
            	if(isJson(response)){
            		var members = $.parseJSON(response);
            		var html = '<select name="account_signatories[]" class="form-control account_signatories" multiple="" data-placeholder="Select...">';
            		$.each(members,function(key,value){
            			html+='<option value="'+key+'">'+value+'</option>';
            		});
            		html+="</select>";
            		$('.signatories_tab').html(html);
            		$('.account_signatories').select2({
            			width : '100%',
			            placeholder:{
			                id: '-1',
			                text: "--Select option--",
			            }, 
			            allowClear: !0
			        });
            	}
            },
            error:function(){

            },
            always:function(){

            }
        });
	}


	var setupIsComplete;
	function load_complete_setup(){
		setupIsComplete = true;
		$('#admin_member_id').empty();
		$('#admin_member_id').append('<option value=""><?php echo '--'.translate('Select').'--'; ?></option>');
        $(document).find('#complete_group_setup').removeClass('disabled');
		$.post('<?php echo site_url('ajax/setup_tasks/get_group_setup_complete_data');?>','',
        function(data){
        	if(isJson(data)){
        		var server_data = $.parseJSON(data);
        		var group_roles = '';
        		if(server_data.hasOwnProperty('group_roles')){
        			group_roles = server_data.group_roles;
        		}
        		if(server_data.hasOwnProperty('members')){
        			member_data = '';
        			var members= server_data.members;
        			if(members.length){
        				$(members).each(function(i){
        					var role = 'Member';
        					if(group_roles){
        						if(group_roles.hasOwnProperty(members[i].group_role_id)){
        							role = group_roles[members[i].group_role_id];
        						}
        					}
        					member_data+='<tr>'
        						+'<td>'+(i+1)+'</td><td>'+members[i].first_name+' '+members[i].last_name+'</td><td>'+members[i].email+'</td><td>'+members[i].phone+'</td><td>'+role+'</td>'+
        					'</tr>';
        					$('#admin_member_id').append('<option value="'+members[i].user_id+'">'+members[i].first_name+' '+members[i].last_name+'</option>');
        				});
        				$('#admin_member_id').val('<?php echo $this->group->owner;?>').change().select2();
        				if(members.length<3){
        					$('.members_complete_setup').parent().before('<div class="m-alert m-alert--outline alert alert-danger fade show" role="alert">\n<strong>Ooops!</strong> Please add atleast 3 group members to complete setup.\n</div>')
        					$('.members_complete_setup').parent().parent().parent().parent().parent().find('.section-has-error').removeClass('d-none');
        					$(document).find('#complete_group_setup').addClass('disabled');
        					setupIsComplete = false;
        				}else{
        					$('.members_complete_setup').parent().parent().parent().parent().parent().find('.section-has-error').addClass('d-none');
        				}
        			}else{
	    				$('.members_complete_setup').parent().before('<div class="m-alert m-alert--outline alert alert-danger fade show" role="alert">\n<strong>Ooops!</strong> Please add atleast 3 group members to complete setup.\n</div>');
        				$('.members_complete_setup').parent().parent().parent().parent().parent().find('.section-has-error').removeClass('d-none');
        				$(document).find('#complete_group_setup').addClass('disabled');
        				setupIsComplete = false;
	        		}
        			$('.members_complete_setup').html(member_data);
        		}
        		if(server_data.hasOwnProperty('contributions')){
        			if(!(server_data.contributions.length === 0)){
        				html = server_data.contributions;
        				$('.contributions_complete_setup').parent().parent().parent().find('.section-has-error').addClass('d-none');
        			}else{
        				html = '<div class="m-alert m-alert--outline alert alert-danger fade show" role="alert">\n<strong>Ooops!</strong> Please set up atleast one group contribution to proceed.\n</div>';
        				$('.contributions_complete_setup').parent().parent().parent().find('.section-has-error').addClass('m-animate-blink');

        				$('.contributions_complete_setup').parent().parent().parent().find('.section-has-error').removeClass('d-none');
        				$(document).find('#complete_group_setup').addClass('disabled');
        				setupIsComplete = false;
        			}
        			$('.contributions_complete_setup').html(html);
        		}

        		if(server_data.hasOwnProperty('loan_types')){
        			if(!(server_data.loan_types.length === 0)){
        				html2 = server_data.loan_types;
        			}else{
        				html2 = '<div class="m-alert m-alert--outline alert alert-info fade show" role="alert">\n<strong>Sorry!</strong> There are no loan types records to display.\n</div>';
        			}
        			$('.loan_types_complete_setup').html(html2);
        		}

        		if(server_data.hasOwnProperty('bank_accounts')){
        			if(!(server_data.bank_accounts.length === 0)){
        				html3 = server_data.bank_accounts;
        			}else{
        				html3 = '<div class="m-alert m-alert--outline alert alert-info fade show" role="alert">\n<strong>Sorry!</strong> There are no group bank account records to display.\n</div>';
        			}
        			$('.bank_accounts_complete_setup').html(html3);
        		}
        	}else{

        	}
        });
	}


	var SnippetCreateGroupRole = ()=>{
		$("#add_new_member_role_form");
		var t = () => {
			$(document).on('click',".btn#add_new_role_btn",function (t) {
				t.preventDefault();
				var e = $(this),
					a = $("#add_new_member_role_form");
				RemoveDangerClass(a);
				(e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
		            type: "POST",
		            url: "<?php echo site_url('ajax/group_roles/create');?>",
		            success: function (t, i, n, r) {
						if(isJson(t)){
							response = $.parseJSON(t);
							if(response.status == '1'){
								setTimeout(function () {
									e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
										a.find(".alert").html('').slideUp();
										Toastr.show("Success",response.message,'success');
										$('select.group_role_ids').each(function(){
											$(this).prepend('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
										});
										$('select[name="group_role_ids['+add_new_role_position+']"]').val(response.id).trigger('change');
										$('#close_add_role').trigger('click');
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
									mUtil.scrollTop();
								}, 2e3)
							}
						}else{
							setTimeout(function () {
								e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
									function (t, e, a) {
										var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
										t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
									}(a, "danger", "Could not complete processing the request at the moment.")
							}, 2e3)
						}
					},
					error: function(){
						setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
						}, 2e3)
					},
					always: function(){
						setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
						}, 2e3)
					}
		        }));
			})
		};
		return {
			init: function () {
				t()
			}
		}
	}

	var SnippetValidateAccountSignatory = function(){
		$("#connect_equity_account_form");
		var t = function(){
			$(document).on('click',".btn#connect_equity_account_btn",function (t) {
				t.preventDefault();
				var e = $(this),
					a = $("#connect_equity_account_form");
				mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Submitting data for verification...'
                });
				RemoveDangerClass(a);
				var dial_code = $('#connect_equity_account_form .selected-dial-code').text();
				(e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
		            type: "POST",
		            data: {dial_code:dial_code},
		            url: "<?php echo site_url('ajax/bank_accounts/connect_bank_account');?>",
		            success: function (t, i, n, r) {
						if(isJson(t)){
							response = $.parseJSON(t);
							if(response.status == '1'){
								setTimeout(function () {
									e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
										a.find(".alert").html('').slideUp(),mApp.unblock(a);
										Toastr.show("Success",response.message,'success');
										$('#connect_equity_account_form input[name="phone"]').attr("readonly","readonly");
										$('.btn#connect_equity_account_btn').removeAttr("id").attr('id',"verify_equity_account_btn");
										$('.verification_settings').removeClass("d-none");
										$('#account_notificaton_space,#otp_message_space').slideUp();
								}, 2e3)
							}else if(response.status == '2'){
								Toastr.show("Try Again",response.message,'information');
								if(response.hasOwnProperty('updated_keys')){
									account_notification_body = $.parseJSON(response.updated_keys);
									var notificationSpace = '<div class="m-radio-list">';
									if(account_notification_body){
						        		for(var i=0;i<account_notification_body.length;i++){
											notificationSpace+='<label class="m-radio m-radio--solid m-radio--brand"><input type="radio" name="notification_channel" value="'+(account_notification_body[i].recipientKey)+'">'+(account_notification_body[i].destination)+' ('+(account_notification_body[i].recipientValue)+')'+'<span></span></label>';
										}
										notificationSpace+='</div>';
									}
									$('#account_notificaton_space').html(notificationSpace);
									$('#connect_equity_account_btn').trigger('click');
								}
								setTimeout(function () {
									e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
										function (t, e, a) {
											var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
											t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
										}(a, "danger", response.message)
								}, 2e3)
							}else if(response.status == '3'){
								setTimeout(function () {
									e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
										a.find(".alert").html('').slideUp(),mApp.unblock(a);
										Toastr.show("Success",response.message,'success');
										$('.cancel_connect_equity_account_form').trigger('click');
                                        load_bank_accounts();
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
									e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
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
									mUtil.scrollTop();
								}, 2e3)
							}
						}else{
							setTimeout(function () {
								e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
									function (t, e, a) {
										var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
										t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
									}(a, "danger", "Could not complete processing the request at the moment.")
							}, 2e3)
						}
					},
					error: function(){
						setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
						}, 2e3)
					},
					always: function(){
						setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
						}, 2e3)
					}
		        }));
			})
		};
		return {
			init: function () {
				t()
			}
		}
	}();

	var SnippetConfirmAccountSignatory = function(){
		$("#connect_equity_account_form");
		var t = function(){
			$(document).on('click',".btn#verify_equity_account_btn",function (t) {
				t.preventDefault();
				var e = $(this),
					a = $("#connect_equity_account_form");
				mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Submitting data for confirmation...'
                });
				RemoveDangerClass(a);
				var dial_code = $('#connect_equity_account_form .selected-dial-code').text();
				(e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
		            type: "POST",
		            data: {dial_code:dial_code},
		            url: "<?php echo site_url('ajax/bank_accounts/verify_ownership');?>",
		            success: function (t, i, n, r) {
						if(isJson(t)){
							response = $.parseJSON(t);
							if(response.status == '1'){
								setTimeout(function () {
									e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
										a.find(".alert").html('').slideUp(),mApp.unblock(a);
										Toastr.show("Success",response.message,'success');
										$('.cancel_connect_equity_account_form').trigger('click');
                                        load_bank_accounts();
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
									e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
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
									mUtil.scrollTop();
								}, 2e3)
							}
						}else{
							setTimeout(function () {
								e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
									function (t, e, a) {
										var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
										t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
									}(a, "danger", "Could not complete processing the request at the moment.")
							}, 2e3)
						}
					},
					error: function(){
						setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
						}, 2e3)
					},
					always: function(){
						setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
						}, 2e3)
					}
		        }));
			})
		};
		return {
			init: function () {
				t()
			}
		}
	}();

	var SnippetCompleteGroupSetup = function(){
		$("#accept_complete_setup_form");
		var t = function () {
			$(document).on('click',".btn#complete_group_setup",function (t) {
				t.preventDefault();
				var e = $(this),
					a = $("#accept_complete_setup_form");
				$('.m-accordion__item-head').addClass('collapsed').attr('aria-expanded','false');
				$('.m-accordion__item-body').removeClass('show');
				mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: `Submitting ${entity_name} Setup Data...`
                });
				RemoveDangerClass(a);
				(e.addClass("m-loader m-loader--right m-loader--light disabled"),a.ajaxSubmit({
		            type: "POST",
		            url: "<?php echo site_url('ajax/complete_setup');?>",
		            success: function (t, i, n, r) {
						if(isJson(t)){
							response = $.parseJSON(t);
							if(response.status == '1'){
								setTimeout(function () {
									e.removeClass("m-loader m-loader--right m-loader--light disabled"),$(".cancel_form").removeAttr("disabled"),
										a.find(".alert").html('').slideUp(), mApp.unblock(a);
										Toastr.show("Success",response.message,'success');
										window.location.href = response.refer;
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
									e.removeClass("m-loader m-loader--right m-loader--light disabled"),$(".cancel_form").removeAttr("disabled"), mApp.unblock(a),
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
									//mUtil.scrollTop();
								}, 2e3)

								if(response.hasOwnProperty('refer')){
									Toastr.show("Setup Message",response.message,'info');
									window.location.href = response.refer;
								}
							}
						}else{
							setTimeout(function () {
								e.removeClass("m-loader m-loader--right m-loader--light disabled"),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"), mApp.unblock(a),
									function (t, e, a) {
										var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
										t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
									}(a, "danger", "Could not complete processing the request at the moment.")
							}, 2e3)
						}
					},
					error: function(){
						setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light disabled"),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"), mApp.unblock(a),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
						}, 2e3)
					},
					always: function(){
						setTimeout(function () {
							e.removeClass("m-loader m-loader--right m-loader--light disabled"),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"), mApp.unblock(a),
								function (t, e, a) {
									var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
									t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
								}(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
						}, 2e3)
					}
		        }));
			})
		};
		return {
			init: function () {
				t()
			}
		}
	}();
</script>
