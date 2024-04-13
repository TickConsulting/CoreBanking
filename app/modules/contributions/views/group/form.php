<!--create contribution-->
<div  id="">
    <div class="m-form__section m-form__section--first">                
        <div class="">
            <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_contribution"'); ?>
                <div id="contribution_settings">
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Contribution Name');?>?: <span class="required">*</span></label>
                            <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control m-input m-input--air" placeholder="eg Savings"');?>
                        </div>
                        <?php echo form_hidden('id',isset($post->id)?$post->id:'')?>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Contribution Amount per Member');?> (<?php echo $this->group_currency;?>): <span class="required">*</span></label>
                            <?php echo form_input('amount',$this->input->post('amount')?$this->input->post('amount'):$post->amount,'class="form-control m-input m-input--air currency" placeholder="eg 2,000"');?>
                        </div>
                    </div>
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Contribution Category');?>: <span class="required">*</span></label>
                            <?php echo form_dropdown('category',array(''=>'--'.translate('Select category').'--')+translate($contribution_category_options),$this->input->post('category')?$this->input->post('category'):$post->category,'class="form-control m-select2"');?>
                        </div>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub m-input--air">
                            <label class="form-control-label"><?php echo translate('Contribution Type');?>: <span class="required">*</span></label>
                            <?php echo form_dropdown('type',array(''=>'--'.translate('Select contribution type').'--')+translate($contribution_type_options),$this->input->post('type')?$this->input->post('type'):$post->type,'class="form-control m-select2"');?>
                        </div>
                    </div>

                    <!-- <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-12 m-input--air">
                            <label class="form-control-label"><?php echo translate('Contribution Type');?>: <span class="required">*</span></label>
                            <?php echo form_dropdown('type',array(''=>'--'.translate('Select contribution type').'--')+translate($contribution_type_options),$this->input->post('type')?$this->input->post('type'):$post->type,'class="form-control m-select2"');?>
                        </div>
                    </div> -->

                    <div id='regular_invoicing_active_holder' class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-12 m-form__group-sub">
                            <label><?php echo translate('Do you wish');?> <?php echo $this->application_settings->application_name;?> <?php echo translate('to be sending regular invoices and reminders to members?');?></label>
                            <div class="m-radio-inline">
                                <?php 
                                    if($this->input->post('regular_invoicing_active')==1 || $post->regular_invoicing_active == 1){
                                        $enable_activate_invoicing = TRUE;
                                        $disable_activate_invoicing = FALSE;
                                    }else if($this->input->post('regular_invoicing_active')==0 || $post->regular_invoicing_active == 0){
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
                                    if($this->input->post('one_time_invoicing_active')==1 || $post->one_time_invoicing_active == 1){
                                        $one_time_enable_activate_invoicing = TRUE;
                                        $one_time_disable_activate_invoicing = FALSE;
                                    }else if($this->input->post('one_time_invoicing_active')==0 || $post->one_time_invoicing_active == 0){
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
                                <?php echo form_dropdown('contribution_frequency',array(''=>'--'.translate('Select contribution frequency').'--')+translate($contribution_frequency_options),$this->input->post('contribution_frequency')?$this->input->post('contribution_frequency'):$post->contribution_frequency,' id="contribution_frequency" class="form-control m-select2" data-placeholder="Select contribution frequency..."'); ?>
                            </div>
                        </div>
                        <div id='once_a_month' class="form-group m-form__group row pt-0 m--padding-10">
                            <div class="col-lg-12 m-form__group-sub">
                                <label><?php echo translate('When do members contribute?');?><span class="required">*</span></label>
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 m-form__group-sub m-input--air ">
                                        <?php echo form_dropdown('month_day_monthly',array(''=>'--'.translate('Select day').'--')+translate($days_of_the_month),$this->input->post('month_day_monthly')?$this->input->post('month_day_monthly'):$post->month_day_monthly,' id="month_day_monthly" class=" form-control m-select2" data-placeholder="Select day..."'); ?>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 m-form__group-sub m-input--air">
                                        <?php echo form_dropdown('week_day_monthly',array(''=>'--'.translate('Select day of the month').'--')+translate($month_days),$this->input->post('week_day_monthly')?$this->input->post('week_day_monthly'):$post->week_day_monthly,' id="week_day_monthly" class="form-control m-select2" data-placeholder="Select day of the month..."'); ?>
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
                                            <?php echo form_dropdown('after_first_contribution_day_option',translate($contribution_days_option),$this->input->post('first_contribution_days_option')?$this->input->post('first_contribution_days_option'):$post->after_first_contribution_day_option,'id="after_first_contribution_day_option" class="form-control m-select2" data-placeholder="Select..."'); ?>
                                        </div>

                                        <div class="col-sm-4 m-form__group-sub m-input--air ">
                                            <?php echo form_dropdown('after_first_day_week_multiple',translate($month_days),$this->input->post('after_first_day_week_multiple')?$this->input->post('after_first_day_week_multiple'):$post->after_first_day_week_multiple,'id="after_first_day_week_multiple" class="form-control m-select2" data-placeholder="Select..."'); ?>
                                        </div>

                                        <div class="col-sm-4 m-form__group-sub m-input--air ">
                                            <?php echo form_dropdown('after_first_starting_day',translate($starting_days),$this->input->post('after_first_starting_day')?$this->input->post('after_first_starting_day'):$post->after_first_starting_day,'id="after_first_starting_day" class="form-control m-select2" data-placeholder="Select..."'); ?>
                                        </div>
                                    </div>
                                    <div class="row second_row pt-0 m--padding-top-10">
                                        <div class="col-sm-4 m-form__group-sub m-input--air ">
                                            <?php echo form_dropdown('after_second_contribution_day_option',translate($contribution_days_option),$this->input->post('after_second_contribution_day_option')?$this->input->post('after_second_contribution_day_option'):$post->after_second_contribution_day_option,'id="after_second_contribution_day_option" class="form-control m-select2" data-placeholder="Select..."'); ?>
                                        </div>

                                        <div class="col-sm-4 m-form__group-sub m-input--air ">
                                            <?php echo form_dropdown('after_second_day_week_multiple',translate($month_days),$this->input->post('after_second_day_week_multiple')?$this->input->post('after_second_day_week_multiple'):$post->after_second_day_week_multiple,'id="after_second_day_week_multiple" class="form-control m-select2" data-placeholder="Select..."'); ?>
                                        </div>

                                        <div class="col-sm-4 m-form__group-sub m-input--air">
                                            <?php echo form_dropdown('after_second_starting_day',translate($starting_days),$this->input->post('after_second_starting_day')?$this->input->post('after_second_starting_day'):$post->after_second_starting_day,'id="after_second_starting_day" class="form-control m-select2" data-placeholder="Select..."'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                

                        <div id='once_a_week' class="form-group m-form__group row pt-0 m--padding-10">
                            <div class="col-lg-12 m-form__group-sub m-input--air">
                                <label><?php echo translate('When do members contribute?');?><span class="required">*</span></label>
                                <?php echo form_dropdown('week_day_weekly',translate($week_days),$this->input->post('week_day_weekly')?$this->input->post('week_day_weekly'):$post->week_day_weekly,'id="week_day_weekly" class="form-control m-select2" data-placeholder="Select day of the week..."'); ?>
                            </div>
                        </div>

                        <div id='once_every_two_weeks' class="form-group m-form__group row pt-0 m--padding-10">
                            <div class="col-lg-12 m-form__group-sub">
                                <label><?php echo translate('When do members contribute?');?><span class="required">*</span></label>
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 m-form__group-sub m-input--air">
                                        <?php echo form_dropdown('week_day_fortnight',translate($every_two_week_days),$this->input->post('week_day_fortnight')?$this->input->post('week_day_fortnight'):$post->week_day_fortnight,'id="week_day_fortnight" class="form-control m-select2" data-placeholder="Select day of the week..."'); ?>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 m-form__group-sub m-input--air">
                                        <?php echo form_dropdown('week_number_fortnight',translate($week_numbers),$this->input->post('week_number_fortnight')?$this->input->post('week_number_fortnight'):$post->week_number_fortnight,'id="week_number_fortnight" class="form-control m-select2" data-placeholder="Select week..."'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id='once_every_multiple_months' class="form-group m-form__group row pt-0 m--padding-10">
                            <div class="col-lg-12 m-form__group-sub">
                                <label><?php echo translate('When do members contribute?');?><span class="required">*</span></label>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12 m-form__group-sub m-input--air">
                                        <?php echo form_dropdown('month_day_multiple',translate($days_of_the_month),$this->input->post('month_day_multiple')?$this->input->post('month_day_multiple'):$post->month_day_multiple,'id="month_day_multiple" class="form-control m-select2" data-placeholder="Select day of the month..."'); ?>
                                    </div>
                                    <div class="col-md-4 col-sm-12 m-form__group-sub m-input--air">
                                        <?php echo form_dropdown('week_day_multiple',translate($month_days),$this->input->post('week_day_multiple')?$this->input->post('week_day_multiple'):$post->week_day_multiple,'id="week_day_multiple" class="form-control m-select2" data-placeholder="Select day of the month..."'); ?>
                                    </div>
                                    <div class="col-md-4 col-sm-12 m-form__group-sub m-input--air">
                                        <?php echo form_dropdown('start_month_multiple',translate($starting_months),$this->input->post('start_month_multiple')?$this->input->post('start_month_multiple'):$post->start_month_multiple,'id="start_month_multiple" class="form-control m-select2" data-placeholder="Select starting month.."'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id='invoice_days' class="form-group m-form__group row pt-0 m--padding-10">
                            <div class="col-lg-12 m-form__group-sub">
                                <label><?php echo translate('When do want to send invoices to members');?><span class="required">*</span></label>
                                <div class='row'>
                                    <div class="col-md-12 col-sm-12 m-form__group-sub m-input--air">
                                        <?php echo form_dropdown('invoice_days',translate($invoice_days),($this->input->post('invoice_days')?:($post->invoice_days?:3)),' id="invoice_days" class=" form-control m-select2" data-placeholder="Select..."'); ?>
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
                                    <?php echo form_input('invoice_date',$this->input->post('invoice_date')?timestamp_to_datepicker(strtotime($this->input->post('invoice_date'))):(timestamp_to_datepicker($post->invoice_date)?:timestamp_to_datepicker(time())),'class="form-control m-input datepicker" readonly');?>
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
                                    <?php echo form_input('contribution_date',$this->input->post('contribution_date')?timestamp_to_datepicker(strtotime($this->input->post('contribution_date'))):(timestamp_to_datepicker($post->contribution_date)?:timestamp_to_datepicker(time())),'class="form-control m-input datepicker" readonly');
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
                                    if($this->input->post('enable_contribution_member_list')==1 || $post->enable_contribution_member_list == 1){
                                        $enable_contribution_member_list = TRUE;
                                        $disable_contribution_member_list = FALSE;
                                    }else if($this->input->post('enable_contribution_member_list')== 0 || $post->enable_contribution_member_list == 0){
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
                            <?php echo form_dropdown('contribution_member_list[]',$this->active_group_member_options,$this->input->post('contribution_member_list')?$this->input->post('contribution_member_list'):$selected_group_members,' id="" class=" form-control m-select2" multiple="multiple" data-placeholder="Select..."'); ?>
                        </div>
                    </div>

                    <div id="disable_contribution_arrears" class="form-group m-form__group pt-0 m--padding-10 row">
                        <div class="col-lg-12 m-form__group-sub m-input--air">
                            <label><?php echo translate('Do you wish to disable contribution arrears for this contribution');?></label>
                            <div class="m-radio-inline">
                                <?php 
                                    if($this->input->post('display_contribution_arrears_cumulatively')==1 || $post->display_contribution_arrears_cumulatively == 1){
                                        $display_contribution_arrears_cumulatively = TRUE;
                                        $disable_contribution_arrears_cumulatively = FALSE;
                                    }else if($this->input->post('display_contribution_arrears_cumulatively')==1 || $post->display_contribution_arrears_cumulatively == 1){
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

                    <div id="enable_checkoff" class="form-group m-form__group pt-0 m--padding-10 row">
                        <div class="col-lg-12 m-form__group-sub m-input--air">
                            <label><?php echo translate('Do you wish to enable checkoff for this contribution');?></label>
                            <div class="m-radio-inline">
                                <?php 
                                    if($this->input->post('enable_checkoff')==1 || $post->enable_checkoff == 1){
                                        $enable_contribution_checkoff = TRUE;
                                        $disable_contribution_checkoff = FALSE;
                                    }else if($this->input->post('enable_checkoff')==1 || $post->enable_checkoff == 1){
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
                            <label><?php echo translate('Do you wish to display this contribution in the member\'s statement report');?></label>
                            <div class="m-radio-inline">
                                <?php 
                                    if($this->input->post('enable_deposit_statement_display')==1 || $post->enable_deposit_statement_display == 1){
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
                            <div class="has-danger">
                                <div class="form-control-feedback">Kindly note this field allows the contribution to be displayed on member statement. Select yes if you would want to display.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id ="contribution_fines">
                    <fieldset class="m--margin-top-20 m-3">
                        <legend>Contribution Fines</legend>
                        <div class="form-group m-form__group row pt-0 m--padding-10">
                            <div class="col-lg-12 m-form__group-sub">
                                <label><?php echo translate('Do you charge contribution fines for late payment?');?></label>
                                <div class="m-radio-inline">
                                    <?php 
                                        if($this->input->post('enable_fines')==1 || $post->enable_fines == 1){
                                            $enabled_enable_fines = TRUE;
                                            $disable_enable_fines = FALSE;
                                        }else if($this->input->post('enable_fines')==0 || $post->enable_fines == 0){
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
                                <?php  
                                if(isset($posts['fine_type'])){  ?>
                                    <div class="m-portlet__body m-demo__preview add_setting_fine">
                                        <div class="form-group m-form__group row">
                                            <?php $count = 0; 
                                            foreach($posts['fine_type'] as $fine_type):
                                            ?>

                                                <div class="col-lg-12 m-form__group-sub">
                                                    <label><?php echo translate('We charge a');?></label>
                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-12 m-form__group-sub m-input--air">
                                                            <?php echo form_dropdown('fine_type['.$count.']',array(''=>translate('--Select fine type--'))+translate($fine_types),isset($fine_type)?$fine_type:'','id="" class="form-control fine_types m-select2" data-placeholder="--Select fine type--"'); ?>
                                                        </div>

                                                        <div id='' <?php if($fine_type==2){ echo " style='display:block;' ";} ?> class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_settings fine_percentage_rate m-input--air">
                                                            <?php echo form_input('percentage_rate['.$count.']',isset($posts['percentage_rate'][$count])?$posts['percentage_rate'][$count]:'','class="form-control percentage_rates" placeholder="Percentage(%) Rate"'); ?>
                                                        </div>


                                                        <div id='' <?php if($fine_type==1){ echo " style='display:block;' ";} ?> class="col-md-4 col-sm-12 m-form__group-sub fixed_fine_settings fine_fixed_amount m-input--air">
                                                            <?php echo form_input('fixed_amount['.$count.']',isset($posts['fixed_amount'][$count])?$posts['fixed_amount'][$count]:'','class="form-control currency fixed_amounts m-input--air" placeholder="Enter fixed fine amount"'); ?>
                                                        </div>

                                                        <div <?php if($fine_type==1){ echo " style='display:block;' ";} ?> class="col-md-4 col-sm-12 m-form__group-sub fixed_fine_settings fixed_fine_mode m-input--air">
                                                            <?php echo form_dropdown('fixed_fine_mode['.$count.']',array(''=>translate('--Select how fines behave--'))+translate($fine_mode_options),isset($posts['fixed_fine_mode'][$count])?$posts['fixed_fine_mode'][$count]:'','id="" class="form-control m-select2 fixed_fine_modes" data-placeholder="--Fine charged for?--"'); ?>
                                                        </div>

                                                        <div <?php if($fine_type==2){ echo " style='display:block;' ";} ?> class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_settings percentage_fine_on m-input--air">
                                                            <?php echo form_dropdown('percentage_fine_on['.$count.']',array(''=>translate('--Select when fines is calculated based on--'))+translate($percentage_fine_on_options),isset($posts['percentage_fine_on'][$count])?$posts['percentage_fine_on'][$count]:'','id="" class="form-control percentage_fine_ons m-select2" data-placeholder="Fine charged on?..."'); ?>
                                                        </div>
                                                    </div>

                                                    <div <?php if($fine_type==2){ echo " style='display:block;' ";} ?> class='percentage_fine_settings m--margin-top-10'>
                                                        <div class="row">
                                                            <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_chargeable_on m-input--air">
                                                                <?php echo form_dropdown('percentage_fine_chargeable_on['.$count.']',array(''=>translate('--Select when fines are charged--'))+translate($fine_chargeable_on_options),isset($posts['percentage_fine_chargeable_on'][$count])?$posts['percentage_fine_chargeable_on'][$count]:'','id="" class="form-control percentage_fine_chargeable_ons m-select2" data-placeholder="When is fine charged?..."'); ?>
                                                            </div>

                                                            <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_mode m-input--air">
                                                                <?php echo form_dropdown('percentage_fine_mode['.$count.']',array(''=>translate('--Select how fines behave--'))+translate($fine_mode_options),isset($posts['percentage_fine_mode'][$count])?$posts['percentage_fine_mode'][$count]:'','id="" class="form-control percentage_fine_modes m-select2" data-placeholder="--Fine charged for?-- "'); ?>
                                                            </div>

                                                            <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_frequency m-input--air">
                                                                 <?php echo form_dropdown('percentage_fine_frequency['.$count.']',array(''=>translate('--Select fine frequency--'))+translate($fine_frequency_options),isset($posts['percentage_fine_frequency'][$count])?$posts['percentage_fine_frequency'][$count]:$percentage_fine_frequency,'id="" class="form-control percentage_fine_frequencies m-select2" data-placeholder="--Select fine charge frequency--"'); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div <?php if($fine_type==1){ echo " style='display:block;' ";} ?> class="fixed_fine_settings m--margin-top-10">
                                                        <div class='row'>
                                                            <div class="col-lg-6 col-sm-12 m-form__group-sub fixed_fine_chargeable_on m-input--air">
                                                                <?php echo form_dropdown('fixed_fine_chargeable_on['.$count.']',array(''=>translate('--Select when fines are charged--'))+translate($fine_chargeable_on_options),isset($posts['fixed_fine_chargeable_on'][$count])?$posts['fixed_fine_chargeable_on'][$count]:'','id="" class="form-control fixed_fine_chargeable_ons m-select2" data-placeholder="--When is fine charged?"'); ?>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-12 m-form__group-sub fixed_fine_settings fixed_fine_frequency m-input--air">
                                                                <?php echo form_dropdown('fixed_fine_frequency['.$count.']',array(''=>translate('--Select fine frequency--'))+translate($fine_frequency_options),isset($posts['fixed_fine_frequency'][$count])?$posts['fixed_fine_frequency'][$count]:$fixed_fine_frequency,'id="" class="form-control fixed_fine_frequencies m-select2" data-placeholder="--Select fine charge frequency"'); ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class='row m--margin-top-10 m--margin-bottom-10 fine_limits_settings'>
                                                        <div class="col-lg-12 m-form__group-sub fine_limit m-input--air">
                                                            <?php echo form_dropdown('fine_limit['.$count.']',translate($fine_limit_options),isset($posts['fine_limit'][$count])?$posts['fine_limit'][$count]:'','id="" class="form-control fine_limits m-select2" data-placeholder="--Limit fine to"'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php $count++; endforeach; ?>

                                        </div>
                                    </div>
                                    <?php 
                                }else if($contribution_fine_settings){ ?>
                                    <div class="m-portlet__body m-demo__preview add_setting_fine">
                                        <div class="form-group m-form__group row">
                                            <?php $count = 0; 
                                            foreach($contribution_fine_settings as $fine_setting):
                                            ?>

                                                <div class="col-lg-12 m-form__group-sub">
                                                    <label><?php echo translate('We charge a');?></label>
                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-12 m-form__group-sub m-input--air">
                                                            <?php echo form_dropdown('fine_type['.$count.']',array(''=>translate('--Select fine type--'))+translate($fine_types),isset($fine_setting->fine_type)?$fine_setting->fine_type:'','id="" class="form-control fine_types m-select2" data-placeholder="--Select fine type--"'); ?>
                                                        </div>

                                                        <div id='' <?php if($fine_setting->fine_type==2){ echo " style='display:block;' ";} ?> class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_settings fine_percentage_rate m-input--air">
                                                            <?php echo form_input('percentage_rate['.$count.']',isset($fine_setting->percentage_rate)?$fine_setting->percentage_rate:'','class="form-control percentage_rates" placeholder="Percentage(%) Rate"'); ?>
                                                        </div>


                                                        <div id='' <?php if($fine_setting->fine_type==1){ echo " style='display:block;' ";} ?> class="col-md-4 col-sm-12 m-form__group-sub fixed_fine_settings fine_fixed_amount m-input--air">
                                                            <?php echo form_input('fixed_amount['.$count.']',isset($fine_setting->fixed_amount)?$fine_setting->fixed_amount:'','class="form-control currency fixed_amounts m-input--air" placeholder="Enter fixed fine amount"'); ?>
                                                        </div>

                                                        <div <?php if($fine_setting->fine_type==1){ echo " style='display:block;' ";} ?> class="col-md-4 col-sm-12 m-form__group-sub fixed_fine_settings fixed_fine_mode m-input--air">
                                                            <?php echo form_dropdown('fixed_fine_mode['.$count.']',array(''=>translate('--Select how fines behave--'))+translate($fine_mode_options),isset($fine_setting->fixed_fine_mode)?$fine_setting->fixed_fine_mode:'','id="" class="form-control m-select2 fixed_fine_modes" data-placeholder="--Fine charged for?--"'); ?>
                                                        </div>

                                                        <div <?php if($fine_setting->fine_type==2){ echo " style='display:block;' ";} ?> class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_settings percentage_fine_on m-input--air">
                                                            <?php echo form_dropdown('percentage_fine_on['.$count.']',array(''=>translate('--Select when fines is calculated based on--'))+translate($percentage_fine_on_options),isset($fine_setting->percentage_fine_on)?$percentage_fine_on:'','id="" class="form-control percentage_fine_ons m-select2" data-placeholder="Fine charged on?..."'); ?>
                                                        </div>
                                                    </div>

                                                    <div <?php if($fine_setting->fine_type==2){ echo " style='display:block;' ";} ?> class='percentage_fine_settings m--margin-top-10'>
                                                        <div class="row">
                                                            <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_chargeable_on m-input--air">
                                                                <?php echo form_dropdown('percentage_fine_chargeable_on['.$count.']',array(''=>translate('--Select when fines are charged--'))+translate($fine_chargeable_on_options),isset($fine_setting->percentage_fine_chargeable_on)?$fine_setting->percentage_fine_chargeable_on:'','id="" class="form-control percentage_fine_chargeable_ons m-select2" data-placeholder="When is fine charged?..."'); ?>
                                                            </div>

                                                            <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_mode m-input--air">
                                                                <?php echo form_dropdown('percentage_fine_mode['.$count.']',array(''=>translate('--Select how fines behave--'))+translate($fine_mode_options),isset($fine_setting->percentage_fine_mode)?$fine_setting->percentage_fine_mode:'','id="" class="form-control percentage_fine_modes m-select2" data-placeholder="--Fine charged for?-- "'); ?>
                                                            </div>

                                                            <div class="col-md-4 col-sm-12 m-form__group-sub percentage_fine_frequency m-input--air">
                                                                 <?php echo form_dropdown('percentage_fine_frequency['.$count.']',array(''=>translate('--Select fine frequency--'))+translate($fine_frequency_options),isset($fine_setting->percentage_fine_frequency)?$fine_setting->percentage_fine_frequency:'','id="" class="form-control percentage_fine_frequencies m-select2" data-placeholder="--Select fine charge frequency--"'); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div <?php if($fine_setting->fine_type==1){ echo " style='display:block;' ";} ?> class="fixed_fine_settings m--margin-top-10">
                                                        <div class='row'>
                                                            <div class="col-lg-6 col-sm-12 m-form__group-sub fixed_fine_chargeable_on m-input--air">
                                                                <?php echo form_dropdown('fixed_fine_chargeable_on['.$count.']',array(''=>translate('--Select when fines are charged--'))+translate($fine_chargeable_on_options),isset($fine_setting->fixed_fine_chargeable_on)?$fine_setting->fixed_fine_chargeable_on:'','id="" class="form-control fixed_fine_chargeable_ons m-select2" data-placeholder="--When is fine charged?"'); ?>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-12 m-form__group-sub fixed_fine_settings fixed_fine_frequency m-input--air">
                                                                <?php echo form_dropdown('fixed_fine_frequency['.$count.']',array(''=>translate('--Select fine frequency--'))+translate($fine_frequency_options),isset($fine_setting->fixed_fine_frequency)?$fine_setting->fixed_fine_frequency:$fixed_fine_frequency,'id="" class="form-control fixed_fine_frequencies m-select2" data-placeholder="--Select fine charge frequency"'); ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class='row m--margin-top-10 m--margin-bottom-10 fine_limits_settings'>
                                                        <div class="col-lg-12 m-form__group-sub fine_limit m-input--air">
                                                            <?php echo form_dropdown('fine_limit['.$count.']',translate($fine_limit_options),isset($fine_setting->fine_limit)?$fine_setting->fine_limit:'','id="" class="form-control fine_limits m-select2" data-placeholder="--Limit fine to"'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php $count++; endforeach; ?>

                                        </div>
                                    </div>

                                    <?php 
                                 
                                }else{ ?>
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
                                <?php } ?>

                            </div>

                            <div id="append-new-fine-setting">
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <a  href="javascript:;" class="btn btn-default btn-sm add-new-line" id="add-new-fine-line">
                                        <i class="la la-plus"></i><?php echo translate('Add Another Fine');?>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </fieldset>
                </div>

                <div class="form-group m-form__group row pt-0 m--padding-10">
                    <div class="col-lg-12 col-md-12">
                        <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_contribution_button" type="button">
                                <?php echo translate('Save Changes & Submit');?>
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
<script type="text/javascript">
    $(document).ready(function(){
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        $(document).on('change','select[name="type"]',function(){
            var type = $(this).val();
            if(type==1){
                $('#one_time_invoicing_active_holder').slideUp();
                $('#one_time_invoicing_settings').slideUp();
                $('#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
                $('#regular_invoicing_active_holder').slideDown();
                $('#disable_contribution_arrears').slideDown();
            }else if(type == 2){
                $('#regular_invoicing_settings').slideUp();
                $('#regular_invoicing_active_holder').slideUp();
                $('#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
                $('#one_time_invoicing_active_holder').slideDown();
                $('#disable_contribution_arrears').slideDown();
            }else if(type == 3){
                $('#regular_invoicing_settings').slideUp();
                $('#one_time_invoicing_active_holder').slideUp();
                $('#regular_invoicing_active_holder').slideUp();
                $('#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
                $('#one_time_invoicing_settings').slideUp();
                $('#disable_contribution_arrears').slideDown();
            }else{
                $('#one_time_invoicing_active_holder').slideUp();
                $('#regular_invoicing_settings').slideUp();
                $('#one_time_invoicing_settings').slideUp();
                $('#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
                $('#regular_invoicing_active_holder').slideUp();
                $('#disable_contribution_arrears').slideUp();
            }
            Select2.init();
        });

        <?php if($this->input->post('type')||$post->type){ ?>
            $('select[name="type"]').trigger('change');
        <?php } ?>

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

        <?php if($this->input->post('regular_invoicing_active')==1||$post->regular_invoicing_active==1){ ?>
            $('#regular_invoicing_settings,#invoice_notifications,#fines,#advanced_settings').slideDown();
        <?php }else{ ?>
            $('#regular_invoicing_settings,#invoice_notifications,#fines,#advanced_settings').slideUp();
        <?php } ?>


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

        <?php if($this->input->post('type')==2||$post->type==2): ?>
            <?php if($this->input->post('one_time_invoicing_active')==1||$post->one_time_invoicing_active==1){ ?>
                $('#one_time_invoicing_settings,#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideDown();
            <?php }else{ ?>
                $('#one_time_invoicing_settings,#sms_email_notifications,#contribution_member_list_settings,#contribution_fines').slideUp();
            <?php } ?>
        <?php endif; ?>

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

        <?php if($this->input->post('type')==1||$post->type==1): ?>
                $('#sms_email_notifications').slideDown();
                $('#contribution_member_list_settings,#contribution_fines').slideDown();
            <?php if($this->input->post('contribution_frequency')==1||$post->contribution_frequency==1){ ?>
                //once a month
                $('#once_a_month').slideDown();
                $('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideDown();
                $('#once_a_week,#once_every_two_weeks,#once_every_multiple_months,#twice_every_one_month').slideUp();
            <?php }else if($this->input->post('contribution_frequency')==6||$post->contribution_frequency==6){ ?>
                //once a week
                $('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideDown();
                $('#once_a_week').slideDown();
                $('#once_every_two_weeks,#once_a_month,#once_every_multiple_months,#twice_every_one_month').slideUp();
            <?php }else if($this->input->post('contribution_frequency')==7||$post->contribution_frequency==7){ ?>
                //once every two weeks
                $('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideDown();
                $('#once_every_two_weeks').slideDown();
                $('#once_every_multiple_months,#once_a_week,#once_a_month,#twice_every_one_month').slideUp();
            <?php }else if(($this->input->post('contribution_frequency')==2||$post->contribution_frequency==2)||($this->input->post('contribution_frequency')==3||$post->contribution_frequency==3)||($this->input->post('contribution_frequency')==4||$post->contribution_frequency==4)||($this->input->post('contribution_frequency')==5||$post->contribution_frequency==5)){ ?>
                //once every two months, once every three months,once every six months, once a year
                $('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideDown();
                $('#once_every_multiple_months').slideDown();
                $('#once_a_month,#once_every_two_weeks,#once_a_week,#twice_every_one_month').slideUp();
                
            <?php }else if($this->input->post('contribution_frequency')==8||$post->contribution_frequency==8){ ?>
                $('select[name=invoice_days]').val(1).trigger('change');
                $('#invoice_days').slideDown();
                $('#once_a_month,#once_every_two_weeks,#once_a_week,#once_every_multiple_months,#twice_every_one_month').slideUp(); 
            <?php }else if($this->input->post('contribution_frequency')==9||$post->contribution_frequency==9){ ?>
                $('select[name=invoice_days]').val(1).trigger('change');
                $('#invoice_days,#twice_every_one_month').slideDown();
                $('#once_a_month,#once_every_two_weeks,#once_a_week,#once_every_multiple_months').slideUp();  
            <?php }else{ ?>
                $('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideUp();
                $('#sms_email_notifications,#contribution_fines').slideUp();
                $('#contribution_member_list_settings').slideUp();
                $('#once_a_month,#once_every_two_weeks,#once_a_week,#once_every_multiple_months,#twice_every_one_month').slideUp();
            <?php } ?> 
            $('#one_time_invoicing_settings').slideUp();
        <?php endif; ?>

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
        <?php if($this->input->post('enable_contribution_summary_display_configuration')==1||$post->enable_contribution_summary_display_configuration==1){ ?>
            $('#contribution_summary_display_configuration_settings').slideDown();
        <?php }else{ ?>
            $('#contribution_summary_display_configuration_settings').slideUp();
        <?php } ?>
        
        <?php if($this->input->post('sms_notifications_enabled')==1||$post->sms_notifications_enabled==1){ ?>
            $('#sms_template').slideDown();
        <?php }else{ ?>
            $('#sms_template').slideUp();
        <?php } ?>

        $(document).on('change','input[name="enable_contribution_member_list"]',function(){
            var enable_contribution_member_list = $(this).val();
            if(enable_contribution_member_list == 1){
                $('#contribution_member_list').slideDown();
            }else{
                $('#contribution_member_list').slideUp();
            }
        });

        <?php if($this->input->post('enable_contribution_member_list')==1||$post->enable_contribution_member_list==1){ ?>
            $('#contribution_member_list').slideDown();
        <?php }else{ ?>
            $('#contribution_member_list').slideUp();
        <?php } ?>

        $(document).on('change','input[name="enable_fines"]',function(){
            var enable_fines = $(this).val();
            if(enable_fines==1){

                $('#fine_setting_row').slideDown();
            }else{  
                $('#fine_setting_row').slideUp();
            }
        });

        <?php if($this->input->post('enable_fines')==1||$post->enable_fines==1){ ?>
            $('#fine_setting_row').slideDown();
            $('#contribution_fines').slideDown();
        <?php }else{ ?>
            $('#fine_setting_row').slideDown();
            $('#fine_setting_row').slideUp();
        <?php } ?>

        $(document).on('change','.fine_types',function(){
            var fine_setting_row_element = $(this).parent().parent().parent();
            fine_setting_row_element.find('.fixed_fine_settings,.percentage_fine_settings,.fine_limit').slideUp('fast');
            fine_setting_row_element.find('.fine_limits_settings').hide();
            if($(this).val()==1){
                fine_setting_row_element.find('.fixed_fine_settings').slideDown();
            }else if($(this).val()==2){
                fine_setting_row_element.find('.percentage_fine_settings').slideDown();
            }
            Select2.init();
        });

        $(document).on('change','.fixed_fine_modes',function(){
            var fine_setting_row_element = $(this).parent().parent().parent();
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
                width : "100%",
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }
            });
        });

        $(document).on('click','.remove-fine-setting',function(){
            $(this).parent().parent().parent().parent().remove();
        });

        var id =  $('input[name="id"]').val()
        if(id==''){
            SnippetCreateContribution.init();
        }else{
            SnippetEditContribution.init();
        }
    });

</script>
