<style type="text/css">
    .percentage-billing-type-settings{
        display: none;
    }
    .fixed-amount-settings,.charge-tax-settings{
        display: none;
    }
</style>
<?php if($post->name){$entry=$post->enable_tax;}else{if($this->input->post()){$entry=$this->input->post('enable_tax');}else{$entry=1;}}
    
    if($post->name){$extra_charge=$post->enable_extra_member_charge;}else{if($this->input->post()){$extra_charge=$this->input->post('enable_extra_member_charge');}else{$extra_charge=1;}}
?>
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
                            <label>Billing Package Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-file-text-o"></i>
                                </span>
                                <?php echo form_input('name',$post->name?:$this->input->post('name')?:'','class="form-control" placeholder="Menu Name"');?>
                            </div>
                        </div>

                        <?php echo form_hidden('slug',$this->input->post('slug')?:$post->slug?:'','class="form-control" placeholder="Slug" readonly="readonly"'); ?>
                        <?php echo form_hidden('id',$id,'class="form-control" placeholder="id" readonly="readonly"'); ?>

                        <div class="form-group">
                            <label>Billing Package Type <span class="required">*</span></label>
                            <?php echo form_dropdown('billing_type',array(''=>'--Select Billing Package Type --')+$billing_type,$post->billing_type?:$this->input->post('billing_type')?:'','class="form-control select2" placeholder="Select Billing Package Type"');?>
                        </div>

                        <div class="row percentage-billing-type-settings">
                            <div class="form-group col-md-6">
                                <label>Percentage Rate %<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                    <?php echo form_input('rate',$post->rate?:$this->input->post('rate')?:'','class="form-control" placeholder="Percentage rate"');?>
                                </div>
                                <span class="help-block">eg. 10</span>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Rate on<span class="required">*</span></label>
                                <?php echo form_dropdown('rate_on',array(''=>'--Select Percentage Rate On--')+$billing_percentage_on,$post->rate_on?:$this->input->post('rate_on')?:'','class="form-control select2" placeholder="Select Percentage Rate On"');?>
                            </div>

                        </div>

                        <div class="fixed-amount-settings row">
                            <div class="form-group col-md-4">
                                <label>Monthly Pay<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                    <?php echo form_input('monthly_amount',$this->input->post('monthly_amount')?:$post->monthly_amount,'class="form-control currency" placeholder="Monthly Pay"');?>
                                </div>
                                <span class="help-block">eg. 500</span>
                            </div>


                            <div class="form-group col-md-4">
                                <label>Quartely Pay<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                    <?php echo form_input('quarterly_amount',$this->input->post('quarterly_amount')?:$post->quarterly_amount,'class="form-control currency" placeholder="Quarterly Pay"');?>
                                </div>
                                <span class="help-block">eg. 1500</span>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Annual Pay<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                    <?php echo form_input('annual_amount',$this->input->post('annual_amount')?:$post->annual_amount,'class="form-control currency" placeholder="Annual Pay"');?>
                                </div>
                                <span class="help-block">eg. 6000</span>
                            </div>
                        </div>

                        <div class="row smses-settings">
                            <div class="form-group col-md-4">
                                <label>Monthly SMSes<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <?php echo form_input('monthly_smses',$this->input->post('monthly_smses')?:$post->monthly_smses,'class="form-control" placeholder="Monthly SMSes"');?>
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Quartely SMSes<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-envelope"></i>
                                    </span>
                                    <?php echo form_input('quarterly_smses',$this->input->post('quarterly_smses')?:$post->quarterly_smses,'class="form-control" placeholder="Quarterly SMSes"');?>
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Annual SMSes<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                    <?php echo form_input('annual_smses',$this->input->post('annual_smses')?:$post->annual_smses,'class="form-control" placeholder="Annual SMSes"');?>
                                </div>
                            </div>

                        </div>

                        <div class="form-group enable-tax-settings">
                            <label>Charge VAT Tax </label>
                            <div class="input-group checkbox-list col-xs-12 ">
                                <label class="checkbox-inline">
                                    <?php echo form_checkbox('enable_tax',1,$this->input->post('enable_tax')?:$post->enable_tax?:$entry,$this->input->post('enable_tax')?:$post->enable_tax?:'1',' id="enable_tax" class="enable_tax" '); ?> Charge Tax
                                </label>
                            </div>
                        </div>

                        <div class="charge-tax-settings">
                            <div class="form-group">
                                <label>Percentage Tax<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('percentage_tax',$post->percentage_tax?:$this->input->post('percentage_tax')?:'16','class="form-control" placeholder="Percentage Tax"');?>
                                </div>
                                <span class="help-block">eg. 16</span>
                            </div>
                        </div>

                        <div class="form-group enable-charge-cost-settings">
                            <label>Extra Members Charge </label>
                            <div class="input-group checkbox-list col-xs-12 ">
                                <label class="checkbox-inline">
                                    <?php echo form_checkbox('enable_extra_member_charge',1,$this->input->post('enable_extra_member_charge')?:$post->enable_extra_member_charge?:$extra_charge,$this->input->post('enable_extra_member_charge')?:$post->enable_extra_member_charge?:'1',' id="enable_extra_member_charge" class="enable_extra_member_charge" '); ?> Charge Extra Cost
                                </label>
                            </div>
                        </div>
                        <div class="charge-extra-cost-settings">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Monthly Pay Over 20 Members<span class="required">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-money"></i>
                                        </span>
                                        <?php echo form_input('monthly_pay_over',$this->input->post('monthly_pay_over')?:$post->monthly_pay_over,'class="form-control currency" placeholder="Monthly Pay over 20"');?>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Quartely Pay Over 20<span class="required">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-money"></i>
                                        </span>
                                        <?php echo form_input('quarterly_pay_over',$this->input->post('quarterly_pay_over')?:$post->quarterly_pay_over,'class="form-control currency" placeholder="Quarterly Pay over 20"');?>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Annual Pay over 20<span class="required">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-money"></i>
                                        </span>
                                        <?php echo form_input('annual_pay_over',$this->input->post('annual_pay_over')?:$post->annual_pay_over,'class="form-control currency" placeholder="Annual Pay over 20"');?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group enable-charge-cost-settings">
                            <label>Display in Reports </label>
                            <div class="input-group checkbox-list col-xs-12 ">
                                <label class="checkbox-inline">
                                    <?php echo form_checkbox('display_reports',1,$this->input->post('display_reports')?:$post->display_reports?:'',$this->input->post('display_reports')?:$post->display_reports?:'1',' class="display_reports" '); ?> Display in Reports
                                </label>
                            </div>
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


<script type="text/javascript">
    $(document).ready(function(){
        $('select[name="billing_type"]').on('change',function(){
            var billing_type = $(this).val();
            if(billing_type==1){
                $('.fixed-amount-settings').slideDown();
                $('.percentage-billing-type-settings').slideUp();
            }else if(billing_type==2){
                $('.percentage-billing-type-settings').slideDown();
                $('.fixed-amount-settings').slideUp();
            }else{
                $('.percentage-billing-type-settings').slideUp();
                $('.fixed-amount-settings').slideUp();
            }
        });

        var billing_type = $('select[name="billing_type"]').val();
        if(billing_type==1){
            $('.fixed-amount-settings').slideDown();
            $('.percentage-billing-type-settings').slideUp();
        }else if(billing_type==2){
            $('.percentage-billing-type-settings').slideDown();
            $('.fixed-amount-settings').slideUp();
        }else{
            $('.percentage-billing-type-settings').slideUp();
            $('.fixed-amount-settings').slideUp();
        }

         $('input[name="enable_tax"]').on('click',function(){
            var charge_tax =$(this).prop('checked');
            if(charge_tax==true){
                $('.charge-tax-settings').slideDown();
            }else{
                $('.charge-tax-settings').slideUp();
            }
        });

         $('input[name="enable_extra_member_charge"]').on('click',function(){
            var charge_extra_cost =$(this).prop('checked');
            if(charge_extra_cost==true){
                $('.charge-extra-cost-settings').slideDown();
            }else{
                $('.charge-extra-cost-settings').slideUp();
            }
        });

         function enable_tax_settings(){
            if($('input[name="enable_tax"]').prop('checked') == true){
                $('.charge-tax-settings').slideDown();
            }else{
                $('.charge-tax-settings').slideUp();
            }
         }

         function enable_extra_charge_cost(){
            if($('input[name="enable_extra_member_charge"]').prop('checked') == true){
                $('.charge-extra-cost-settings').slideDown();
            }else{
                $('.charge-extra-cost-settings').slideUp();
            }
         }
         enable_tax_settings();
         enable_extra_charge_cost();
    });
</script>