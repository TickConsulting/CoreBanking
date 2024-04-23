<div class="row">

	<div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
               	<div class="caption">
                   <?php echo $this->admin_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form">
                <?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
                    <div class="form-body">
                        <div class="form-group">
                            <label>Application Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-shield"></i>
                                </span>
                                <?php echo form_input('application_name',$this->input->post('application_name')?$this->input->post('application_name'):$post->application_name,'class="form-control" placeholder="Application Name"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Application Email<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-shield"></i>
                                </span>
                                <?php echo form_input('application_email',$this->input->post('application_email')?$this->input->post('application_email'):$post->application_email,'class="form-control" placeholder="Application email"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Application Phone<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-shield"></i>
                                </span>
                                <?php echo form_input('application_phone',$this->input->post('application_phone')?$this->input->post('application_phone'):$post->application_phone,'class="form-control" placeholder="254*********"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Sender ID<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-shield"></i>
                                </span>
                                <?php echo form_input('sender_id',$this->input->post('sender_id')?$this->input->post('sender_id'):$post->sender_id,'class="form-control" placeholder="Sender ID"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Home Page Controller</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-shield"></i>
                                </span>
                                <?php echo form_input('home_page_controller',$this->input->post('home_page_controller')?$this->input->post('home_page_controller'):$post->home_page_controller,'class="form-control" placeholder="Home Page Controller"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->application_settings->application_name; ?> URL<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-shield"></i>
                                </span>
                                <?php echo form_input('protocol',$this->input->post('protocol')?$this->input->post('protocol'):$post->protocol,'class="form-control" placeholder="<?php echo $post->application_name; ?> URL Protocol"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><?php echo $post->application_name; ?> URL<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-external-link"></i>
                                </span>
                                <?php echo form_input('url',$this->input->post('url')?$this->input->post('url'):$post->url,'class="form-control" placeholder="<?php echo $post->application_name; ?> URL"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><?php echo $post->application_name; ?> Entity Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-shield"></i>
                                </span>
                                <?php echo form_input('entity_name',$this->input->post('entity_name')?:($post->entity_name ?: 'Group' ),'class="form-control" placeholder="Entity name"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Group Trial Days<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-battery-3"></i>
                                </span>
                                <?php echo form_input('trial_days',$this->input->post('trial_days')?$this->input->post('trial_days'):$post->trial_days,'class="form-control" placeholder="Group Trial Days"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Group Billing Number Start<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar-check-o"></i>
                                </span>
                                <?php echo form_input('bill_number_start',$this->input->post('bill_number_start')?$this->input->post('bill_number_start'):$post->bill_number_start,'class="form-control" placeholder="Group Billing Start Number"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Application Session Length<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <?php echo form_input('session_length',$this->input->post('session_length')?$this->input->post('session_length'):$post->session_length,'class="form-control" placeholder="Logged In Session Length"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Application Session Timeout Length (in Milliseconds)<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <?php echo form_input('session_timeout',$this->input->post('session_timeout')?$this->input->post('session_timeout'):$post->session_timeout,'class="form-control" placeholder="Session  Timeout Length"');?>
                            </div>
                        </div>



                        <div class="form-group">
                            <label>Default language</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-edit"></i>
                                </span>
                                <?php echo form_dropdown('default_language_id',array(''=>'Select Default Language')+$language_options,$this->input->post('default_language_id')?:$post->default_language_id,'class="form-control select2" id="default_language_id" autocomplete="off" placeholder="" ');?>
                            </div>
                        </div>



                        <div class="form-group">
                            <label>Primary Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('primary_color',$this->input->post('primary_color')?$this->input->post('primary_color'):($post->primary_color?$post->primary_color:'#000000'),'class="form-control colorpicker" id="hue-demo" ');?>
                            </div>
                        </div>

                         <div class="form-group">
                            <label>Secondary Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('secondary_color',$this->input->post('secondary_color')?$this->input->post('secondary_color'):($post->secondary_color?$post->secondary_color:'#000000'),'class="form-control colorpicker" placeholder="Secondary Color"');?>
                            </div>
                        </div> 

                        <div class="form-group">
                            <label>Tertiary Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('tertiary_color',$this->input->post('tertiary_color')?$this->input->post('tertiary_color'):($post->tertiary_color?$post->tertiary_color:'#000000'),'class="form-control colorpicker" placeholder="Tertiary Color"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Text Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('text_color',$this->input->post('text_color')?$this->input->post('text_color'):($post->text_color?$post->text_color:'#000000'),'class="form-control colorpicker" placeholder="Text Color"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Link Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('link_color',$this->input->post('link_color')?$this->input->post('link_color'):($post->link_color?$post->link_color:'#37B7F3'),'class="form-control colorpicker" placeholder="Link Color"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo $post->application_name; ?> Favicon<span class="required">*</span></label><br/>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="max-width: 100px;">
                                    <img src="<?php echo $post->favicon?site_url($path.'/'.$post->favicon):site_url('templates/admin_themes/admin/img/no_image.png'); ?>" alt="" /> </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 50px;"> </div>
                                <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select Icon </span>
                                        <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="favicon"> 
                                        </span>
                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo $post->application_name; ?> Logo<span class="required">*</span></label><br/>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="max-width: 150px;">
                                    <img src="<?php echo $post->logo?site_url($path.'/'.$post->logo):site_url('templates/admin_themes/admin/img/no_image.png'); ?>" alt="" /> </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 50px;"> </div>
                                <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select Logo </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="logo"> </span>
                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo $post->application_name; ?> Responsive Logo<span class="required">*</span></label><br/>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="max-width: 150px;">
                                    <img src="<?php echo $post->responsive_logo?site_url($path.'/'.$post->responsive_logo):site_url('templates/admin_themes/admin/img/no_image.png'); ?>" alt="" /> </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 50px;"> </div>
                                <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select Responsive Logo </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="responsive_logo"> </span>
                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label><?php echo $post->application_name; ?> Paper Header<span class="required">*</span></label><br/>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width: 150px;">
                                    <img src="<?php echo $post->paper_header_logo?site_url($path.'/'.$post->paper_header_logo):site_url('templates/admin_themes/admin/img/no_image.png'); ?>" alt="" /> </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 50px;"> </div>
                                <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select Paper Header Logo </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="paper_header_logo"> </span>
                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label><?php echo $post->application_name; ?> Paper Footer<span class="required">*</span></label><br/>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="max-width: 150px;">
                                    <img src="<?php echo $post->paper_footer_logo?site_url($path.'/'.$post->paper_footer_logo):site_url('templates/admin_themes/admin/img/no_image.png'); ?>" alt="" /> </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 50px;"> </div>
                                <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select Paper Footer Logo </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="paper_footer_logo"> </span>
                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>Admin Login Page Logo:<span class="required">*</span></label><br/>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="max-width: 150px;">
                                    <img src="<?php echo $post->admin_login_logo?site_url($path.'/'.$post->admin_login_logo):site_url('templates/admin_themes/admin/img/no_image.png'); ?>" alt="" /> </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 50px;"> </div>
                                <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select Admin Login Logo </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="admin_login_logo"> </span>
                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Group Login Page Logo:<span class="required">*</span></label><br/>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="max-width: 150px;">
                                    <img src="<?php echo $post->group_login_logo?site_url($path.'/'.$post->group_login_logo):site_url('templates/admin_themes/admin/img/no_image.png'); ?>" alt="" /> </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 50px;"> </div>
                                <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select Group Login Logo </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="group_login_logo"> </span>
                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                </div>
                            </div>
                        </div>


                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enable_two_factor_auth">
                                    <label>Enable two factor authentication<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('enable_two_factor_auth')==1){
                                                $enable_two_factor_auth_yes_enabled = TRUE;
                                                $enable_two_factor_auth_no_enabled = FALSE;
                                            }else if($post->enable_two_factor_auth ==1){
                                                $enable_two_factor_auth_yes_enabled = TRUE;
                                                $enable_two_factor_auth_no_enabled = FALSE;
                                            }else if($this->input->post('enable_two_factor_auth')==0){
                                                $enable_two_factor_auth_yes_enabled = FALSE;
                                                $enable_two_factor_auth_no_enabled = TRUE;
                                            }else if($post->$enable_two_factor_auth==0){
                                                $enable_two_factor_auth_yes_enabled = FALSE;
                                                $enable_two_factor_auth_no_enabled = TRUE;
                                            }else{
                                                $enable_two_factor_auth_yes_enabled = TRUE;
                                                $enable_two_factor_auth_no_enabled = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enable_two_factor_auth',1,$enable_two_factor_auth_yes_enabled,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enable_two_factor_auth',0,$enable_two_factor_auth_no_enabled,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enable_referrers">
                                    <label>Enable Referrers<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('enable_referrers')==1){
                                                $enable_referrers_yes_enabled = TRUE;
                                                $enable_referrers_no_enabled = FALSE;
                                            }else if($post->enable_referrers==1){
                                                $enable_referrers_yes_enabled = TRUE;
                                                $enable_referrers_no_enabled = FALSE;
                                            }else if($this->input->post('enable_referrers')==0){
                                                $enable_referrers_yes_enabled = FALSE;
                                                $enable_referrers_no_enabled = TRUE;
                                            }else if($post->enable_referrers==0){
                                                $enable_referrers_yes_enabled = FALSE;
                                                $enable_referrers_no_enabled = TRUE;
                                            }else{
                                                $enable_referrers_yes_enabled = TRUE;
                                                $enable_referrers_no_enabled = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enable_referrers',1,$enable_referrers_yes_enabled,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enable_referrers',0,$enable_referrers_no_enabled,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enforce_default_country">
                                    <label>Enforce Default Country<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('enforce_default_country')==1){
                                                $enforce_default_country_yes_enabled = TRUE;
                                                $enforce_default_country_no_enabled = FALSE;
                                            }else if($post->enforce_default_country==1){
                                                $enforce_default_country_yes_enabled = TRUE;
                                                $enforce_default_country_no_enabled = FALSE;
                                            }else if($this->input->post('enforce_default_country')==0){
                                                $enforce_default_country_yes_enabled = FALSE;
                                                $enforce_default_country_no_enabled = TRUE;
                                            }else if($post->enforce_default_country==0){
                                                $enforce_default_country_yes_enabled = FALSE;
                                                $enforce_default_country_no_enabled = TRUE;
                                            }else{
                                                $enforce_default_country_yes_enabled = TRUE;
                                                $enforce_default_country_no_enabled = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enforce_default_country',1,$enforce_default_country_yes_enabled,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enforce_default_country',0,$enforce_default_country_no_enabled,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group display_group_information">
                                    <label>Display Group Information on Group Dashboard<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('display_group_information')==1){
                                                $display_group_information_yes_enabled = TRUE;
                                                $display_group_information_no_enabled = FALSE;
                                            }else if($post->display_group_information==1){
                                                $display_group_information_yes_enabled = TRUE;
                                                $display_group_information_no_enabled = FALSE;
                                            }else if($this->input->post('display_group_information')==0){
                                                $display_group_information_yes_enabled = FALSE;
                                                $display_group_information_no_enabled = TRUE;
                                            }else if($post->display_group_information==0){
                                                $display_group_information_yes_enabled = FALSE;
                                                $display_group_information_no_enabled = TRUE;
                                            }else{
                                                $display_group_information_yes_enabled = TRUE;
                                                $display_group_information_no_enabled = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('display_group_information',1,$display_group_information_yes_enabled,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('display_group_information',0,$display_group_information_no_enabled,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enforce_group_setup_tasks">
                                    <label>Enforce Group Setup Tasks<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('enforce_group_setup_tasks')==1){
                                                $enforce_group_setup_tasks_yes_enabled = TRUE;
                                                $enforce_group_setup_tasks_no_enabled = FALSE;
                                            }else if($post->enforce_group_setup_tasks==1){
                                                $enforce_group_setup_tasks_yes_enabled = TRUE;
                                                $enforce_group_setup_tasks_no_enabled = FALSE;
                                            }else if($this->input->post('enforce_group_setup_tasks')==0){
                                                $enforce_group_setup_tasks_yes_enabled = FALSE;
                                                $enforce_group_setup_tasks_no_enabled = TRUE;
                                            }else if($post->enforce_group_setup_tasks==0){
                                                $enforce_group_setup_tasks_yes_enabled = FALSE;
                                                $enforce_group_setup_tasks_no_enabled = TRUE;
                                            }else{
                                                $enforce_group_setup_tasks_yes_enabled = TRUE;
                                                $enforce_group_setup_tasks_no_enabled = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enforce_group_setup_tasks',1,$enforce_group_setup_tasks_yes_enabled,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enforce_group_setup_tasks',0,$enforce_group_setup_tasks_no_enabled,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group disable_smses">
                                    <label>Disable SMSes<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('disable_smses')==1){
                                                $disable_smses_yes_enabled = TRUE;
                                                $disable_smses_no_enabled = FALSE;
                                            }else if($post->disable_smses==1){
                                                $disable_smses_yes_enabled = TRUE;
                                                $disable_smses_no_enabled = FALSE;
                                            }else if($this->input->post('disable_smses')==0){
                                                $disable_smses_yes_enabled = FALSE;
                                                $disable_smses_no_enabled = TRUE;
                                            }else if($post->disable_smses==0){
                                                $disable_smses_yes_enabled = FALSE;
                                                $disable_smses_no_enabled = TRUE;
                                            }else{
                                                $disable_smses_yes_enabled = TRUE;
                                                $disable_smses_no_enabled = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('disable_smses',1,$disable_smses_yes_enabled,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('disable_smses',0,$disable_smses_no_enabled,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enable_language_change">
                                    <label>Enable Language Change<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('enable_language_change')==1){
                                                $enable_language_change_checked = TRUE;
                                                $enable_language_change_unchecked = FALSE;
                                            }else if($post->enable_language_change==1){
                                                $enable_language_change_checked = TRUE;
                                                $enable_language_change_unchecked = FALSE;
                                            }else if($this->input->post('enable_language_change')==0){
                                                $enable_language_change_checked = FALSE;
                                                $enable_language_change_unchecked = TRUE;
                                            }else if($post->enable_language_change==0){
                                                $enable_language_change_checked = FALSE;
                                                $enable_language_change_unchecked = TRUE;
                                            }else{
                                                $enable_language_change_checked = TRUE;
                                                $enable_language_change_unchecked = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enable_language_change',1,$enable_language_change_checked,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enable_language_change',0,$enable_language_change_unchecked,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enable_language_change">
                                    <label>Enable Self Onboarding<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('allow_self_onboarding')==1){
                                                $enable_self_onboarding = TRUE;
                                                $disable_self_onboarding = FALSE;
                                            }else if($post->allow_self_onboarding==1){
                                                $enable_self_onboarding = TRUE;
                                                $disable_self_onboarding = FALSE;
                                            }else if($this->input->post('allow_self_onboarding')==0){
                                                $enable_self_onboarding = FALSE;
                                                $disable_self_onboarding = TRUE;
                                            }else if($post->allow_self_onboarding==0){
                                                $enable_self_onboarding = FALSE;
                                                $disable_self_onboarding = TRUE;
                                            }else{
                                                $enable_self_onboarding = TRUE;
                                                $disable_self_onboarding = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('allow_self_onboarding',1,$enable_self_onboarding,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('allow_self_onboarding',0,$disable_self_onboarding,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enable_language_change">
                                    <label>Enable Google Recaptcha<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('enable_google_recaptcha')==1){
                                                $enable_google_recaptcha = TRUE;
                                                $disable_google_recaptcha = FALSE;
                                            }else if($post->enable_google_recaptcha==1){
                                                $enable_google_recaptcha = TRUE;
                                                $disable_google_recaptcha = FALSE;
                                            }else if($this->input->post('enable_google_recaptcha')==0){
                                                $enable_google_recaptcha = FALSE;
                                                $disable_google_recaptcha = TRUE;
                                            }else if($post->enable_google_recaptcha==0){
                                                $enable_google_recaptcha = FALSE;
                                                $disable_google_recaptcha = TRUE;
                                            }else{
                                                $enable_google_recaptcha = TRUE;
                                                $disable_google_recaptcha = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enable_google_recaptcha',1,$enable_google_recaptcha,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enable_google_recaptcha',0,$disable_google_recaptcha,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enable_language_change">
                                    <label>Show System Account Balance<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('show_system_account_balance')==1){
                                                $enable_show_system_account_balance = TRUE;
                                                $disable_show_system_account_balance = FALSE;
                                            }else if($post->allow_self_onboarding==1){
                                                $enable_show_system_account_balance = TRUE;
                                                $disable_show_system_account_balance = FALSE;
                                            }else if($this->input->post('show_system_account_balance')==0){
                                                $enable_show_system_account_balance = FALSE;
                                                $disable_show_system_account_balance = TRUE;
                                            }else if($post->allow_self_onboarding==0){
                                                $enable_show_system_account_balance = FALSE;
                                                $disable_show_system_account_balance = TRUE;
                                            }else{
                                                $enable_show_system_account_balance = TRUE;
                                                $disable_show_system_account_balance = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('show_system_account_balance',1,$enable_show_system_account_balance,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('show_system_account_balance',0,$disable_show_system_account_balance,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enable_language_change">
                                    <label>Enable Online Disbursements<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('enable_online_disbursement')==1){
                                                $enable_online_disbursement = TRUE;
                                                $disable_online_disbursement = FALSE;
                                            }else if($post->enable_online_disbursement==1){
                                                $enable_online_disbursement = TRUE;
                                                $disable_online_disbursement = FALSE;
                                            }else if($this->input->post('enable_online_disbursement')==0){
                                                $enable_online_disbursement = FALSE;
                                                $disable_online_disbursement = TRUE;
                                            }else if($post->enable_online_disbursement==0){
                                                $enable_online_disbursement = FALSE;
                                                $disable_online_disbursement = TRUE;
                                            }else{
                                                $enable_online_disbursement = TRUE;
                                                $disable_online_disbursement = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enable_online_disbursement',1,$enable_online_disbursement,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('enable_online_disbursement',0,$disable_online_disbursement,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enable_language_change">
                                    <label>Activate billing<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('activate_billing')==1){
                                                $activate_billing = TRUE;
                                                $deactivate_billing = FALSE;
                                            }else if($post->activate_billing==1){
                                                $activate_billing = TRUE;
                                                $deactivate_billing = FALSE;
                                            }else if($this->input->post('activate_billing')==0){
                                                $activate_billing = FALSE;
                                                $deactivate_billing = TRUE;
                                            }else if($post->activate_billing==0){
                                                $activate_billing = FALSE;
                                                $deactivate_billing = TRUE;
                                            }else{
                                                $activate_billing = TRUE;
                                                $deactivate_billing = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('activate_billing',1,$activate_billing,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('activate_billing',0,$deactivate_billing,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class="col-md-12">
                                <div class="form-group enable_language_change">
                                    <label>Enforce Login Attempts<span class='required'>*</span></label>
                                    <div class="margin-left-30">
                                        <?php 
                                            if($this->input->post('activate_login_attempts')==1){
                                                $activate_login_attempts = TRUE;
                                                $deactivate_login_attempts = FALSE;
                                            }else if($post->activate_login_attempts==1){
                                                $activate_login_attempts = TRUE;
                                                $deactivate_login_attempts = FALSE;
                                            }else if($this->input->post('activate_login_attempts')==0){
                                                $activate_login_attempts = FALSE;
                                                $deactivate_login_attempts = TRUE;
                                            }else if($post->activate_billing==0){
                                                $activate_login_attempts = FALSE;
                                                $deactivate_login_attempts = TRUE;
                                            }else{
                                                $activate_login_attempts = TRUE;
                                                $deactivate_login_attempts = FALSE;
                                            }
                                        ?>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('activate_login_attempts',1,$activate_login_attempts,""); ?>
                                                </span>
                                            </div> Yes </label>
                                        <label class="radio-inline">
                                            <div class="radio" id="">
                                                <span class="">
                                                    <?php echo form_radio('activate_login_attempts',0,$deactivate_login_attempts,""); ?>
                                                </span>
                                            </div> No </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php echo form_hidden('id',$id); ?>

                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
						<button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <button type="button" class="btn default">Cancel</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        
    </div>
</div>
