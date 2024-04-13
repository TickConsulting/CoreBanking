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
                <?php echo form_open(current_url(),'class="form_submit" role="form"');?>
                    <div class="form-body">
                        <div class="form-group">
                            <label>Menu Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-file-text-o"></i>
                                </span>
                                <?php echo form_input('name',$post->name?$post->name:'','class="form-control" placeholder="Menu Name"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Menu URL<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-link"></i>
                                </span>
                                <?php echo form_input('url',$post->url?$post->url:'','class="form-control" placeholder="Menu URL"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Menu Icon<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-empire"></i>
                                </span>
                                <?php echo form_input('icon',$post->icon?$post->icon:'','class="form-control" placeholder="Menu Icon"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Help Menu URL</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-link"></i>
                                </span>
                                <?php echo form_input('help_url',$post->help_url?$post->help_url:'','class="form-control" placeholder="Help Menu URL"');?>
                            </div>
                        </div>

      					<div class="form-group">
                            <label>Parent Menu</label>
                            <?php echo form_dropdown('parent_id',array(''=>'--Select Parent Menu --')+$member_menus,$post->parent_id?$post->parent_id:'','class="form-control select2" placeholder="Select Menu Parent"');?>
                        </div>
                        <div class="form-group">
                            <label>Enable Menu For</label>
                            <?php echo form_dropdown('enable_menu_for',array(''=>'All Users', 1 => 'Specific Users'),$post->enable_menu_for?$post->enable_menu_for:'','class="form-control select2" placeholder="Select Menu Parent"');?>
                        </div>

                        <div class="specific_user_menu_setting">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Menu Enabled/Disabled</label>
                                        <?php echo form_dropdown('enabled_or_disabled',array(1=>'Show Menu for Users ', 2 => 'Hide Menu for Users'),$post->enabled_or_disabled?$post->enabled_or_disabled:'','class="form-control select2" placeholder="Select Menu Parent"');?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Select Feature</label>
                                        <?php echo form_dropdown('enabled_disabled_feature',$enable_menu_options,$post->enabled_disabled_feature?:'','class="form-control select2" placeholder="Select Menu Parent"');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>

                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Menu"/>

						<button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('change','select[name="enable_menu_for"]',function(){
            var enable_menu_for = $(this).val();
            if(enable_menu_for == 1){
                $('.specific_user_menu_setting').slideDown();
            }else{
                $('.specific_user_menu_setting').slideUp();
            }
        });

        <?php if($post->enable_menu_for ==1){?>
            $('.specific_user_menu_setting').slideDown();
        <?php }else{?>
            $('.specific_user_menu_setting').slideUp();
        <?php }?>
    });
</script>