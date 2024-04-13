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

                        <div class="form-group col-md-12">
                            <label>
                                <?php echo translate('Group');?>
                                <span class="required">*</span>
                            </label>
                            <select id="group_search" name="group" class="form-control group-search">
                               
                                <?php 
                                    if($post->group){
                                ?>
                                    <option value="<?php echo $post->group->id ?>" selected="selected"><?php echo $post->group->name; ?></option>
                                <?php 
                                    }else{
                                ?>
                                    <option value="" selected="selected">Search for a Group</option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                        
                        
                        <div class="form-group col-md-12">
                            <label>Billing Package Type <span class="required">*</span></label>
                            <?php echo form_dropdown('billing_type',array(''=>'--Select Billing Package Type --')+$billing_packages,$this->input->post('billing_type')?:$post->billing_type ?: "",'class="form-control select2" placeholder="Select Billing Package Type"');?>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Billing date <span class="required">*</span></label>
                            <?php echo form_input('billing_date',timestamp_to_datepicker(time()),' class="form-control input-sm m-input fine_dates date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" ');?>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Due date <span class="required">*</span></label>
                            <?php echo form_input('due_date',timestamp_to_datepicker(time()),' class="form-control input-sm m-input fine_dates date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" ');?>
                        </div>  
                        
                        <div class="form-group col-md-12">
                            <label>Disable prorating</label>
                            <div class="input-group checkbox-list col-xs-12 ">
                                <label class="checkbox-inline">
                                    <?php echo form_checkbox('disable_prorating',1,$this->input->post('disable_prorating')?:$post->disable_prorating,$this->input->post('disable_prorating')?:$post->disable_prorating?:'1',' id="disable_prorating" class="disable_prorating" '); ?> Disable prorating
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Billing cycle<span class="required">*</span></label>
                            <?php echo form_dropdown('billing_cycle',array(''=>'--Select billing cycle --')+$billing_cycles,$this->input->post('billing_cycle')?:$post->billing_cycle ?: "",'class="form-control select2" name="billing_cycle" placeholder="Select Billing cycle"');?>
                        </div>

                        <div class="num-of-months col-md-12" style="display:none">
                            <div class="form-group">
                                <label>Number of months<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('num_of_months',$post->num_of_months?:$this->input->post('num_of_months')?:'1','class="form-control" placeholder="Number of months"');?>
                                </div>
                                <span class="help-block">eg. 1</span>
                            </div>
                        </div>

                        <div class="num-of-quarters col-md-12" style="display:none">
                            <div class="form-group">
                                <label>Number of quarters<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('num_of_quarters',$post->num_of_quarters?:$this->input->post('num_of_quarters')?:'1','class="form-control" placeholder="Number of quarters"');?>
                                </div>
                                <span class="help-block">eg. 1</span>
                            </div>
                        </div>

                        <div class="num-of-years col-md-12" style="display:none">
                            <div class="form-group">
                                <label>Number of years<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('num_of_years',$post->num_of_years?:$this->input->post('num_of_years')?:'1','class="form-control" placeholder="Number of years"');?>
                                </div>
                                <span class="help-block">eg. 1</span>
                            </div>
                        </div>


                        
                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>

                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Invoice"/>

                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                        
                    </div>
                <?php echo form_close();?>
            </div>
        </div>        
    </div>
</div>

<script>

    $(document).ready(function(){
        $('#group_search').change(function(){
            $('select[name="group-search"]').val($(this).val());
        });

        $("select[name='billing_cycle']").change(function(){
            var billing_cycle = $(this).val();

            if(billing_cycle == 1){

                $('.num-of-months').slideDown();
                $('.num-of-quarters').slideUp();
                $('.num-of-years').slideUp();

            } else if(billing_cycle == 2){

                $('.num-of-months').slideUp();
                $('.num-of-quarters').slideDown();
                $('.num-of-years').slideUp();

            } else if(billing_cycle == 3){

                $('.num-of-months').slideUp();
                $('.num-of-quarters').slideUp();
                $('.num-of-years').slideDown();

            }
        });
    });

</script>