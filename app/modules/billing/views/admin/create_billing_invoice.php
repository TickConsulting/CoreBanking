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
                            <label>Group Name<span class="required">*</span></label>
                            <?php echo form_dropdown('group_id',array(''=>'--Select Group--')+$groups,$post->group_id?$post->group_id:($this->input->post('group_id')?$this->input->post('group_id'):''),'class="form-control select2" placeholder="Select Group"');?>
                        </div>

                        <div class="form-group">
                            <label>Billing Package<span class="required">*</span></label>
                            <?php echo form_dropdown('billing_package_id',array(''=>'--Select Billing Package--')+$billing_packages,$post->billing_package_id?$post->billing_package_id:($this->input->post('billing_package_id')?$this->input->post('billing_package_id'):''),'class="form-control select2" placeholder="Select Billing Package"');?>
                        </div>

                        <div class="form-group">
                            <label>Billing Date<span class="required">*</span></label>
                            <div class="input-group date date-picker">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <?php echo form_input('billing_date',$post->billing_date?timestamp_to_datepicker($post->billing_date):(timestamp_to_datepicker($this->input->post('billing_date'))?timestamp_to_datepicker($this->input->post('billing_date')):(timestamp_to_datepicker($post->billing_date)?:timestamp_to_datepicker(time()))),'  class="form-control date-picker" data-date-format="dd-mm-yyyy" readonly="readonly"'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Due Date<span class="required">*</span></label>
                            <div class="input-group date date-picker">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <?php echo form_input('due_date',$post->due_date?timestamp_to_datepicker($post->due_date):(timestamp_to_datepicker($this->input->post('due_date'))?:(timestamp_to_datepicker($post->due_date)?:timestamp_to_datepicker(strtotime('+ 7 days'),time()))),'  data-date-format="dd-mm-yyyy" class="form-control date-picker" readonly="readonly"'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Amount Paid <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-money"></i>
                                </span>
                                <?php echo form_input('amount_paid',$post->amount_paid?:$this->input->post('amount_paid')?:'','class="form-control currency" placeholder="Amount Paid"');?>
                            </div>
                            <span class="help-block">Amount paid for this specific invoice</span>
                        </div>
                        

                        <div class="form-group">
                            <label>Billing Cycle<span class="required">*</span></label>
                            <?php echo form_dropdown('billing_cycle',array(''=>'--Select Billing Cycle--')+$billing_cycle,$post->billing_cycle?:$this->input->post('billing_cycle')?:'','class="form-control select2" placeholder="Select Billing Cycle"');?>
                        </div>


                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>

                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Package"/>

						<button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
