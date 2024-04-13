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
                            <label>Payment Date<span class="required">*</span></label>
                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <?php echo form_input('receipt_date',timestamp_to_datepicker($post->receipt_date)?:timestamp_to_datepicker(time()),'  class="form-control date-picker" readonly="readonly" data-date-format="dd-mm-yyyy"'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Group Name<span class="required">*</span></label>
                            <?php echo form_dropdown('group_id',array(''=>'--Select Group--')+$groups,$post->group_id?:$this->input->post('group_id')?:'','class="form-control select2" placeholder="Select Group"');?>
                        </div>

                        <div class="form-group">
                            <label>Billing Package<span class="required">*</span></label>
                            <?php echo form_dropdown('billing_package_id',array(''=>'--Select Billing Package--')+$billing_packages,$post->billing_package_id?:$this->input->post('billing_package_id')?:'','class="form-control select2" placeholder="Select Billing Package"');?>
                        </div>

                        <div class="form-group">
                            <label>Payment Method<span class="required">*</span></label>
                            <?php echo form_dropdown('payment_method',array(''=>'--Select Payment Method--')+$payment_methods,$post->payment_method?:$this->input->post('payment_method')?:'','class="form-control select2" placeholder="Select Payment Method"');?>
                        </div>

                         <div class="form-group">
                            <label>Transaction Code</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-phone"></i>
                                </span>
                                <?php echo form_input('ipn_transaction_code',$post->ipn_transaction_code?:$this->input->post('ipn_transaction_code')?:'','class="form-control ipn_transaction_code" placeholder="Transaction Code"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Amount Paid <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-money"></i>
                                </span>
                                <?php echo form_input('amount',$post->amount?:$this->input->post('amount')?:'','class="form-control currency" placeholder="Amount Paid"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Payment Description</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-money"></i>
                                </span>
                                <?php $textarea = array(
                                    'name'=>'description',
                                    'id'=>'',
                                    'value'=> $post->description?:'',
                                    'cols'=>25,
                                    'rows'=>5,
                                    'maxlength'=>'',
                                    'class'=>'form-control description',
                                    'placeholder'=>''
                                ); 
                                echo form_textarea($textarea);?> 
                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>

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
});
</script>