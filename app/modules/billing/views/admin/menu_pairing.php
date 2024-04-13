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
                <div class="form-group last">
                    <!-- BEGIN FORM-->
                    
                    <?php echo form_open(current_url(),'class="form-horizontal form-row-seperated form_submit" name="menus"');?>
                        <div class="form-body">
                            <div class="form-group last">
                                <label class="col-md-12"><h4>Sidebar Menus</h4></label>
                                <div class="col-md-12">
                                    <?php echo form_dropdown('menu_id[]',$menu_options,$this->input->post('menu_id[]')?:$menu_pairings?:'','multiple="multiple" class="multi-select" id="my_multi_select2"');?>
                                    <!--<?php echo form_dropdown('menu_id[]',$menu_options,'','class="form-control select2" ')?>-->
                                </div>
                                <div class="clearfix"></div>
                                <label class="col-md-12 margin-top-25"><h4>Quick Action Menus</h4></label>
                                <div class="col-md-12">
                                    <?php echo form_dropdown('quick_action_menu_id[]',$quick_action_menu_options,$this->input->post('quick_action_menu_id[]')?:$qucik_action_menu_pairings?:'','multiple="multiple" class="multi-select" id="my_multi_select1"');?>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="submit"  class="btn blue submit_form_button">Save Changes</button>

                            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                            <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                            
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
</div>