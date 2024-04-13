<!-- create loan types-->
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                   <?php echo $this->admin_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form">
                <div id="create_group_loan_types_panel" >
                    <div class="m-form__section m-form__section--first">
                        <div class="create_loan_type_settings_layout" >
                            <div id="create_loan_type_setting" >
                                <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_loan_type"'); ?>
                                    <div class="form-group m-form__group row pt-0 m--padding-10">
                                        <div class="col-sm-12 m-form__group-sub">
                                            <label><?php echo translate('Select Groups');?><span class="required">*</span></label>
                                            <?php echo form_dropdown('group_ids[]',$group_options,$this->input->post('group_ids')?$this->input->post('group_ids'):$matches_options,'class="form-control select2 m-input--air" multiple="multiple" placeholder="Loan Type Name"'); ?>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn blue submit_form_button">Save Changes</button>
                                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                                        <a href="<?php echo $this->agent->referrer()?>">
                                            <button type="button" class="btn default">Cancel</button></a>
                                    </div>
                                <?php echo form_close();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!---->
