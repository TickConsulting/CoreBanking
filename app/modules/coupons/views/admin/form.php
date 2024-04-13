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
                            <label>Coupon Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-map"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Coupon Name"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Coupon Type<span class="required">*</span></label>
                            <div class="input-group col-xs-12">
                                <?php echo form_dropdown('type',array(''=>'--Select type--')+$coupon_types,$this->input->post('type')?:$post->type,'class="form-control select2" placeholder="Coupon Type"');?>
                            </div>
                        </div>

                        <div class="percentage_setting">
                            <div class="form-group">
                                <label>Percentage Coupon Value<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('percentage_value',$this->input->post('percentage_value')?:$post->percentage_value,'class="form-control currency" placeholder="Coupon Percentage Value"');?>
                                </div>
                            </div>
                        </div>

                        <div class="fixed_amount_setting">
                            <div class="form-group">
                                <label>Fixed Coupon Amount<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-money"></i>
                                    </span>
                                    <?php echo form_input('fixed_amount',$this->input->post('fixed_amount')?:$post->fixed_amount,'class="form-control currency" placeholder="Coupon Fixed Amount"');?>
                                </div>
                            </div>
                        </div>

                        <div class="waiver_subscription_setting">
                            <div class="form-group">
                                <label>Coupon Waiver Type<span class="required">*</span></label>
                                <div class="input-group col-xs-12">
                                    <?php echo form_dropdown('coupon_waiver_type',array(''=>'--Select type--')+$coupon_waiver_types,$this->input->post('coupon_waiver_type')?:$post->coupon_waiver_type,'class="form-control select2" placeholder="Coupon Waiver Type"');?>
                                </div>
                            </div>

                            <div class="partial_waiver_settings">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Upaid Subscription Waiver Period (Months)<span class="required">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa  fa-calendar"></i>
                                                </span>
                                                <?php echo form_input('partial_waiver_period',$this->input->post('partial_waiver_period')?:$post->partial_waiver_period,'class="form-control numeric" placeholder="Waiver Period"');?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Waiver Period Start Date<span class="required">*</span></label>
                                            <div class="input-group date-time-range">
                                                <?php echo form_input('partial_waiver_start_date',timestamp_to_monthpicker($this->input->post('partial_waiver_start_date'))?:timestamp_to_monthpicker($post->partial_waiver_start_date),'class="form-control date-picker-month-year input-sm" placeholder="Select waiver start period"'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="free_subscription_setting">
                            <div class="form-group">
                                <label>Months to give free<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-calendar"></i>
                                    </span>
                                    <?php echo form_input('free_months',$this->input->post('free_months')?:$post->free_months,'class="form-control numeric" placeholder="Months free"');?>
                                </div>
                            </div>
                        </div>

                        
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Coupon Distribution Active Date<span class="required">*</span></label>
                                    <div class="input-group date-time-range">
                                        <span class="input-group-addon">
                                            <i class="fa  fa-calendar"></i>
                                        </span>
                                        <?php echo form_input('date_active_from',$post->date_active_from?timestamp_to_datepicker($post->date_active_from):timestamp_to_datepicker(time()),'class="form-control date-picker input-sm" data-date-format="dd-mm-yyyy" placeholder="Select date coupon active from"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label>Coupon Distribution Expiry Date<span class="required">*</span></label>
                                    <div class="input-group date-time-range">
                                        <span class="input-group-addon">
                                            <i class="fa  fa-calendar"></i>
                                        </span>
                                        <?php echo form_input('expiry_date',$post->expiry_date?timestamp_to_datepicker($post->expiry_date):timestamp_to_datepicker(strtotime('+ 1 month'),time()),'class="form-control date-picker input-sm" data-date-format="dd-mm-yyyy" placeholder="Select expiry date"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Distribution Limit Type<span class="required">*</span></label>
                            <div class="input-group col-xs-12">
                                <?php echo form_dropdown('distribution_limit',array(''=>'--Select type--')+array(1 => 'Unlimited distribution', 2=> 'Limited distribution'),$this->input->post('distribution_limit')?:$post->distribution_limit,'class="form-control select2" placeholder="Coupon Distribution Limit"');?>
                            </div>
                        </div>

                        <div class="limited_distribution_setting">
                            <div class="form-group">
                                <label>Number of users to use coupon<span class="required">*</span></label>
                                <div class="input-group date-time-range">
                                    <span class="input-group-addon">
                                        <i class="fa  fa-user"></i>
                                    </span>
                                    <?php echo form_input('limited_users',$this->input->post('limited_users')?:$post->limited_users,'class="form-control input-sm numeric"  placeholder="Limited distribution"'); ?>
                                </div>
                            </div>
                        </div>
                        <?php echo form_hidden('id',$id); ?>
                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Coupon"/>
                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <button type="button" class="btn default">Cancel</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('change','select[name="type"]',function(){
            var type = $(this).val();
            if(type == 1){
                $('.percentage_setting').slideUp();
                $('.waiver_subscription_setting').slideUp();
                $('.free_subscription_setting').slideUp();
                $('.fixed_amount_setting').slideDown();
            }else if(type==2){
                $('.fixed_amount_setting').slideUp();
                $('.waiver_subscription_setting').slideUp();
                $('.free_subscription_setting').slideUp();
                $('.percentage_setting').slideDown();
            }else if(type == 3){
                $('.percentage_setting').slideUp();
                $('.fixed_amount_setting').slideUp();
                $('.free_subscription_setting').slideUp();
                $('.waiver_subscription_setting').slideDown();
            }else if(type==4){
                $('.percentage_setting').slideUp();
                $('.fixed_amount_setting').slideUp();
                $('.waiver_subscription_setting').slideUp();
                $('.free_subscription_setting').slideDown();
            }else{
                $('.percentage_setting').slideUp();
                $('.fixed_amount_setting').slideUp();
                $('.waiver_subscription_setting').slideUp();
                $('.free_subscription_setting').slideUp();
            }
        });

        var type = '<?php echo $this->input->post('type')?:$post->type;?>';
        if(type !=''){
            $('select[name="type"]').trigger('change');
        }

        $(document).on('change','select[name="coupon_waiver_type"]',function(){
            var coupon_waiver_type = $(this).val();
            if(coupon_waiver_type == 1){
                $('.partial_waiver_settings').slideUp();
            }else if(coupon_waiver_type == 2){
                $('.partial_waiver_settings').slideDown();
            }else{
                $('.partial_waiver_settings').slideUp();
            }
        });

        var coupon_waiver_type = '<?php echo $this->input->post('coupon_waiver_type')?:$post->coupon_waiver_type;?>';
        if(coupon_waiver_type !=''){
            $('select[name="coupon_waiver_type"]').trigger('change');
        }


        $(document).on('change','select[name = "distribution_limit"]',function(){
            var distribution_limit = $(this).val();
            if(distribution_limit == 1){
                $('.limited_distribution_setting').slideUp();
            }else if(distribution_limit == 2){
                $('.limited_distribution_setting').slideDown();
            }else{
                $('.limited_distribution_setting').slideUp();
            }
        });

        var distribution_limit = '<?php echo $this->input->post('distribution_limit')?:$post->distribution_limit;?>';
        if(distribution_limit !=''){
            $('select[name="distribution_limit"]').trigger('change');
        }

    });
</script>