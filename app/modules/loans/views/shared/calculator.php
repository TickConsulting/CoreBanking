<?php echo form_open(current_url(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit" id="form_calculate_installments" role="form"'); ?>

<div id="loan_type_form_holder" class="loan_type_form_holder">

    <div class="alert alert-danger data_error alert-dismissible fade show" id="loan_calculator_holder" role="alert" style="display:none;">
    </div>
    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-sm-12 m-form__group-sub">
            <label><?php echo translate('Loan Type');?><span class="required">*</span></label>
            <?php echo form_dropdown('loan_type_id',array(''=>'Select Loan Type')+translate($loan_types)+array('0'=>"Add Loan Type"),'',' class="form-control m-input m-select2 loan_type" id="loan_type"');?>
            <span class="m-form__help"><?php echo translate('eg. Emergency Loan');?></span>
        </div>
    </div>

    <div class="row pt-0 m--padding-10 loan_details_holder ml-3">
        
    </div>
    <?php echo form_hidden('maximum_loan_amount_from_savings','');?>
    <?php echo form_hidden('loan_amount_type','');?> 
    <?php echo form_hidden('loan_guarantor_type',''); ?>
    <?php echo form_hidden('loan_repayment_type',''); ?>

    <div class="row pt-0 m--padding-10" id='loan_amount_form_holder' style="display: none;">
        <div class="col-sm-12 m-form__group-sub">
            <label><?php echo translate('Loan Amount');?><span class="required">*</span></label>

            <?php echo form_input('loan_amount',$this->input->post('loan_amount'),'  class="form-control currency"  '); ?>
            <span class="m-form__help"><?php echo translate('eg. 200');?></span>
        </div>
    </div>

    <div class="row pt-0 form-group m-form__group row pt-0 m--padding-10" id='repayment_periods' style="display: none;">
        <div class="col-sm-12 m-form__group-sub">
            <label><?php echo translate('Repayment Period');?><span class="required">*</span></label>

            <?php echo form_input('repayment_period',$this->input->post('repayment_period'),'  class="form-control numeric"  '); ?>
            <span class="m-form__help"><?php echo translate('eg. 1 Month');?></span>
        </div>
    </div>                      

    <div class="m--margin-top-20 form-group m-form__group row pt-0 m--padding-10">
        <div class="col-lg-12 col-md-12">
            <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="calculate_installments" type="button">
                    <?php echo translate('Save Changes');?>
                </button>
                &nbsp;&nbsp;
                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form"  type="button" id="cancel_calculator">
                    <?php echo translate('Cancel');?>
                </button>
            </span>
        </div>
    </div>

</div>                        
<?php echo form_close(); ?>
        

<div class="m-portlet amortization_holder" style="display: none;" id="amortization_holder">
    
    <div class="m-portlet__body">
        <!--begin::Section-->
        <div class="m-section__content">
            <div class="amortized_schedule_display" id="amortized_schedule_display">
            </div>
        </div>
        <!--end::Section-->
    </div>
</div>

<div class="modal fade" id="create_new_loan_type_pop_up" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Add Loan Type
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger data_error" id="" style="display:none;">
                </div>
                <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_loan_type"'); ?>
                    <div  class="form-body">
                        <div id="create_new_loan_type_setting">
                            <div class="form-group m-form__group row pt-0 m--padding-10">
                                <div class="col-sm-6 m-form__group-sub">
                                    <label><?php echo translate('Loan Type Name');?><span class="required">*</span></label>
                                    <?php echo form_input('name',$this->input->post('name'),'class="form-control name m-input--air" placeholder="Loan Type Name"'); ?>
                                    <span class="m-form__help"><?php echo translate('eg. Emergency Loan');?></span>
                                </div>
                    
                                <div class="col-sm-6 m-form__group-sub m-input--air">
                                    <label><?php echo translate('Loan Amount Type');?> (Eg. Based on member Savings)<span class="required">*</span></label>
                                     <?php echo form_dropdown('loan_amount_type',array(''=>'--'.translate('Select Loan Amount Type').'--')+translate($loan_amount_type),$this->input->post('loan_amount_type'),'class="m-select2 form-control " id ="loan_amount_type" data-placeholder="Select..."  ');?>
                                </div>
                            </div>
                            
                            <div class="loan_amount_savings_input_group form-group m-form__group pt-0 m--padding-10" id='loan_amount_savings_input_group'>
                                <div class=" row">
                                    <div class="col-lg-12 m-form__group-sub">
                                        <label><?php echo translate('How many times on member savings');?> (Eg. 3)?<span class="required">*</span> </label>
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-4 m-form__group-sub"> 
                                                <?php echo form_input('',"",'  class="form-control currency" id="" autocomplete="off" disabled placeholder="Times"'); ?>                   
                                            </div>
                                            <div class="col-lg-4 col-sm-4 m-form__group-sub"> 
                                                <?php echo form_input('loan_times_number',$this->input->post('loan_times_number'),'  class="form-control currency m-input--air" id="loan_times_number" autocomplete="off"  placeholder="Times Number"'); ?>
                                            </div>
                                            <div class="col-lg-4 col-sm-4 m-form__group-sub">
                                                <?php echo form_input('',"",'  class="form-control currency" id="" autocomplete="off"  disabled placeholder="of Member savings"'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="loan_amount_input_group form-group m-form__group pt-0 m--padding-10" id='loan_amount_input_group'>
                                <div class=" row">
                                    <div class="col-sm-6 m-form__group-sub">
                                        <label><?php echo translate('Minimum loan amount');?><span class="required">*</span></label>
                                        <?php echo form_input('minimum_loan_amount',$this->input->post('minimum_loan_amount'),'  class="form-control currency m-input--air" placeholder="Minimum Amount"'); ?>
                                    </div>
                                    <div class="col-sm-6 m-form__group-sub">
                                        <label><?php echo translate('Maximum loan amount');?><span class="required">*</span></label>
                                        <?php echo form_input('maximum_loan_amount',$this->input->post('maximum_loan_amount'),'  class="form-control currency m-input--air" placeholder="Maximum Amount"'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group m-form__group interest_type_input_group pt-0 m--padding-10">
                                <div class="row">
                                    <div class="col-sm-4 m-form__group-sub">
                                        <label>Interest Type<span class="required">*</span></label>
                                        <?php echo form_dropdown('interest_type',array(''=>'--Select Loan Interest Type--')+$interest_types,$this->input->post('interest_type'),'class="form-control m-select2 interest_type" id = "interest_type"  ');?>
                                    </div>
                                    <div class="col-sm-8 m-form__group-sub">
                                        <div class="not_for_custom_settings">
                                            <div class="row">
                                                <div class="col-sm-6 m-form__group-sub">
                                                    <label><?php echo translate('Loan Interest Rate');?><span class="required">*</span></label>
                                                    <?php echo form_input('interest_rate',$this->input->post('interest_rate'),'  class="form-control numeric m-input--air" placeholder="Loan Interest Rate"'); ?>
                                                </div>
                                                <div class="col-sm-6 m-form__group-sub m-input--air">
                                                    <label ><?php echo translate('Loan Interest Rate Per');?><span class="required">*</span></label>   
                                                    <?php echo form_dropdown('loan_interest_rate_per',translate($loan_interest_rate_per),4,'class="form-control m-select2 interest_type" id = "loan_interest_rate_per"  ') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="for_custom_settings">
                                            <div class="row">
                                                <div class="col-sm-12 m-form__group-sub">
                                                    <label><?php echo translate('Loan Interest Rate');?><span class="required">*</span></label>
                                                    <?php echo form_input('interest_rate',$this->input->post('interest_rate'),'  class="form-control numeric" disabled="disabled" placeholder="Custom fields"'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="m-form__group form-group pt-0 m--padding-top-10" id="enable_reducing_balance_installment_recalculation">
                                    <label ><?php echo translate('Select to enable');?></label>
                                    <div class="m-checkbox-inline">
                                        <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                            <?php echo form_checkbox('enable_reducing_balance_installment_recalculation',1,FALSE,"");?><?php echo translate('Enable Reducing Balance Recalulation on Early Installment Repayment');?>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="m-form__group form-group row pt-0 m--padding-10" id="grace_period">
                                <div class="col-sm-12 m-form__group-sub m-input--air">
                                    <label><?php echo translate('Loan Grace Period');?><span class="required">*</span></label>
                                    <?php echo form_dropdown('grace_period',array(''=> '--Select Loan Grace Period--')+translate($loan_grace_periods)+array('date'=>translate("Custom Date")),$this->input->post('grace_period'),'class="form-control m-select2 grace_period" id = "grace_period"  ') ?>
                                </div>
                            </div> 

                            <div class="m-form__group form-group loan_repayment_period_input_group pt-0 m--padding-10">
                                <div class="row">
                                    <div class="m-form__group-sub m-input--air col-sm-4">
                                        <label>
                                            <?php echo translate('Loan Repayment Period Type');?><span class="required">*</span>
                                        </label>
                                        <?php echo form_dropdown('loan_repayment_period_type',array(''=>'--Select repayment period type--')+translate($loan_repayment_period_type),$this->input->post('loan_repayment_period_type'),'class="form-control m-select2 loan_repayment_period_type" id = "loan_repayment_period_type"  ') ?>
                                    </div> 


                                    <div class="col-sm-8">
                                        <div class="fixed_repayment_period">
                                            <label><?php echo translate('Fixed Repayment Period');?><span class="required">*</span></label>
                                            <?php echo form_input('fixed_repayment_period',$this->input->post('fixed_repayment_period'),'  class="form-control numeric m-input--air" placeholder="Fixed Repayment Period"'); ?>
                                                <span class="m-form__help"><?php echo translate('Value in months eg.2');?></span>
                                        </div> 

                                        <div class="varying_repayment_period">
                                            <div class="row">
                                                <div class="m-form__group-sub col-sm-6">
                                                    <label><?php echo translate('Minimum Repayment Period');?><span class="required">*</span></label>
                                                    <?php echo form_input('minimum_repayment_period',$this->input->post('minimum_repayment_period'),'  class="form-control numeric m-input--air" placeholder="Minimum Repayment Period"'); ?>
                                                    <span class="m-form__help"><?php echo translate('Eg.2');?></span>
                                                </div>
                                                <div class="m-form__group-sub col-sm-6">
                                                    <label><?php echo translate('Maximum Repayment Period');?><span class="required">*</span></label>
                                                    <?php echo form_input('maximum_repayment_period',$this->input->post('maximum_repayment_period'),'  class="form-control numeric m-input--air" placeholder="Maximum Repayment Period"'); ?>
                                                    <span class="m-form__help"><?php echo translate('Eg.5');?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>  
                                </div>   
                            </div> 

                            <div class="addition_loan_types_form_details">   
                                <fieldset class="m--margin-top-20">
                                    <legend><?php echo translate('Fine Details');?></legend>
                                    <div class="form-group m-form__group row pt-0 m--padding-10">
                                        <div class="col-lg-12 m-form__group-sub">
                                            <label class="form-control-label"><?php echo translate('Do you charge fines for late monthly loan installment payments');?>?:</label>
                                            <div class="m-radio-inline">
                                                <?php 
                                                    if($this->input->post('enable_loan_fines')==1){
                                                        $enabled_loan_fines = TRUE;
                                                        $disabled_loan_fines = FALSE;
                                                    }else if($this->input->post('enable_loan_fines')==0){
                                                        $enabled_loan_fines = FALSE;
                                                        $disabled_loan_fines = TRUE;
                                                    }else{
                                                        $enabled_loan_fines = TRUE;
                                                        $disabled_loan_fines = FALSE;
                                                    }
                                                ?>
                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <?php echo form_radio('enable_loan_fines',1,$enabled_loan_fines,""); ?>
                                                    <?php echo translate('Yes');?>
                                                    <span></span>
                                                </label>

                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <?php echo form_radio('enable_loan_fines',0,$disabled_loan_fines,""); ?>
                                                    <?php echo translate('No');?>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="enable_loan_fines_settings">
                                        <div class="m-form__group form-group row loan_fine_type pt-0 m--padding-10">
                                            <div class="m-form__group-sub m-input--air col-sm-12 m-input--air">
                                                <label><?php echo translate('What type of Late Loan Payment fine do you Charge');?>? <span class="required">*</span></label>
                                                <?php echo form_dropdown('loan_fine_type',array(''=>'--Select  the Type of Fine Charged--')+translate($late_loan_payment_fine_types),$this->input->post('loan_fine_type'),'class="form-control m-select2 loan_fine_type" id = "loan_fine_type"  ') ?>
                                            </div>
                                        </div>

                                        <div class="late_loan_payment_fixed_fine">
                                            <div class="m-form__group form-group row pt-0 m--padding-10">
                                                <div class="m-form__group-sub m-input--air col-sm-4">
                                                    <label><?php echo translate('Fixed Amount charge');?><span class="required">*</span></label>
                                                    <?php echo form_input('fixed_fine_amount',$this->input->post('fixed_fine_amount'),'  class="form-control currency fixed_fine_amount m-input--air" placeholder="Fixed Fine Amount"'); ?>
                                                </div>

                                                <div class="m-form__group-sub m-input--air col-sm-4 m-input--air">
                                                     <label><?php echo translate('Fixed Amount Fine Frequecy');?><span class="required">*</span></label>
                                                     <?php echo form_dropdown('fixed_amount_fine_frequency',array(''=>'--Select  the fine frequency--')+translate($late_payments_fine_frequency),$this->input->post('fixed_amount_fine_frequency'),'class="form-control m-select2 fixed_amount_fine_frequency" id = "fixed_amount_fine_frequency"  ') ?>
                                                </div>

                                                <div class="m-form__group-sub m-input--air col-sm-4 m-input--air">
                                                    <label><?php echo translate('Fixed Amount Fine Frequecy On');?><span class="required">*</span></label>
                                                    <?php echo form_dropdown('fixed_amount_fine_frequency_on',array(''=>'--Select  the fine frequency On--')+translate($fixed_amount_fine_frequency_on),$this->input->post('fixed_amount_fine_frequency_on'),'class="form-control m-select2 fixed_amount_fine_frequency_on" id = "fixed_amount_fine_frequency_on"  ') ?>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="late_loan_payment_percentage_fine">
                                            <div class="m-form__group form-group row pt-0 m--padding-10">
                                                <div class="m-form__group-sub col-sm-4">
                                                    <label><?php echo translate('Fine Percentage Rate');?>(%)<span class="required">*</span></label>
                                                    <?php echo form_input('percentage_fine_rate',$this->input->post('percentage_fine_rate'),'  class="form-control numeric percentage_fine_rate m-input--air" placeholder="Fine Percentage Rate"'); ?>
                                                </div>

                                                <div class="m-form__group-sub m-input--air col-sm-4">
                                                    <label><?php echo translate('Fine Frequecy');?><span class="required">*</span></label>
                                                    <?php echo form_dropdown('percentage_fine_frequency',array(''=>'--Select  the fine frequecy--')+translate($late_payments_fine_frequency),$this->input->post('percentage_fine_frequency'),'class="form-control m-select2 percentage_fine_frequency" id = "percentage_fine_frequency"  ') ?>
                                                </div>

                                                <div class="m-form__group-sub m-input--air col-sm-4">
                                                    <label><?php echo translate('Fine Charge on');?> <span class="required">*</span></label>
                                                    <?php echo form_dropdown('percentage_fine_on',array(''=>'--Select where is Fine Charged On--')+translate($percentage_fine_on),$this->input->post('percentage_fine_on'),'class="form-control m-select2 percentage_fine_on" id = "percentage_fine_on"  ') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="late_loan_repayment_one_off_fine">
                                            <div class="m-form__group form-group row pt-0 m--padding-10">
                                                <div class="m-form__group-sub col-sm-4 m-input--air">
                                                    <label><?php echo translate('Select One Off Fine Type');?> <span class="required">*</span></label>
                                                    <?php echo form_dropdown('one_off_fine_type',array(''=>'--Select one Off fine Type--')+translate($one_off_fine_types),$this->input->post('one_off_fine_type'),'class="form-control m-select2 one_off_fine_type" id = "one_off_fine_type"  ') ?>
                                                </div>

                                                <div class="m-form__group-sub col-sm-8">
                                                    <div class="row">
                                                        <div class="one_off_fixed_amount_setting one_off_fixed_amount col-sm-12">
                                                            <div class="m-form__group-sub">
                                                                <label><?php echo translate('One Off Fixed Amount');?><span class="required">*</span></label>
                                                                <?php echo form_input('one_off_fixed_amount',$this->input->post('one_off_fixed_amount'),'  class="form-control currency fixed_fine_amount m-input--air" placeholder="One Off Fixed Amount"'); ?>
                                                            </div>
                                                        </div>
                                                        <div class="one_off_percentage_setting">
                                                            <div class="row">
                                                                <div class="m-form__group-sub col-sm-6">
                                                                    <label><?php echo translate('One Off Percentage');?> (%)<span class="required">*</span></label>
                                                                    <?php echo form_input('one_off_percentage_rate',$this->input->post('one_off_percentage_rate'),'  class="form-control numeric one_off_percentage_rate m-input--air" placeholder="One Off Percentage Rate"'); ?>
                                                                </div>

                                                                <div class="m-form__group-sub col-sm-6 m-input--air">
                                                                    <label><?php echo translate('One Off Percentage on');?><span class="required">*</span></label>
                                                                    <?php echo form_dropdown('one_off_percentage_rate_on',array(''=>'--Select One Off Percentage on--')+translate($one_off_percentage_rate_on),$this->input->post('one_off_percentage_rate_on'),'class="one_off_percentage_rate_on form-control m-select2" id = "one_off_percentage_rate_on"  ') ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group row pt-0 m--padding-10">
                                        <div class="col-lg-12 m-form__group-sub">
                                            <label class="form-control-label"><?php echo translate('Do you charge fines for outstanding loan balance at the end of the loan');?>?:</label>
                                            <div class="m-radio-inline">
                                                <?php 
                                                    if($this->input->post('enable_outstanding_loan_balance_fines')==1){
                                                        $enabled_loan_outstanding_fines = TRUE;
                                                        $disabled_loan_outstanding_fines = FALSE;
                                                    }else if($this->input->post('enable_outstanding_loan_balance_fines')==0){
                                                        $enabled_loan_outstanding_fines = FALSE;
                                                        $disabled_loan_outstanding_fines = TRUE;
                                                    }else{
                                                        $enabled_loan_outstanding_fines = TRUE;
                                                        $disabled_loan_outstanding_fines = FALSE;
                                                    }
                                                ?>
                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <?php echo form_radio('enable_outstanding_loan_balance_fines',1,$enabled_loan_outstanding_fines,""); ?>
                                                    <?php echo translate('Yes');?>
                                                    <span></span>
                                                </label>

                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <?php echo form_radio('enable_outstanding_loan_balance_fines',0,$disabled_loan_outstanding_fines,""); ?>
                                                    <?php echo translate('No');?>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="enable_outstanding_loan_balances_fines_settings">
                                        <div class="m-form__group form-group row pt-0 m--padding-10">
                                            <div class="m-form__group-sub m-input--air col-sm-12">
                                                <label><?php echo translate('What type of fine do you charge for outstanding balances');?>? <span class="required">*</span></label>
                                                <?php echo form_dropdown('outstanding_loan_balance_fine_type',array(''=>'--Select Oustanding Loan Balance fine type--')+translate($late_loan_payment_fine_types),$this->input->post('outstanding_loan_balance_fine_type'),'class="form-control m-select2 outstanding_loan_balance_fine_type" id = "outstanding_loan_balance_fine_type"  ') ?>
                                            </div>
                                        </div>

                                        <div class="outstanding_loan_balance_fixed_fine">
                                            <div class="m-form__group form-group row pt-0 m--padding-10">
                                                <div class="m-form__group-sub col-sm-6">
                                                    <label><?php echo translate('Fixed Fine Amount Charged for Outstanding Balances');?><span class="required">*</span></label>
                                                     <?php echo form_input('outstanding_loan_balance_fine_fixed_amount',$this->input->post('outstanding_loan_balance_fine_fixed_amount'),'  class="form-control m-input--air currency outstanding_loan_balance_fine_fixed_amount" placeholder="Outsanding Loan Balance Fixed Fine Amount"'); ?>
                                                </div>
                                                <div class="m-form__group-sub col-sm-6 m-input--air">
                                                    <label><?php echo translate('Frequecy to be Charged on Fixed Amount');?><span class="required">*</span></label>
                                                    <?php echo form_dropdown('outstanding_loan_balance_fixed_fine_frequency',array(''=>'--Select  the fine frequency--')+translate($late_payments_fine_frequency),$this->input->post('outstanding_loan_balance_fixed_fine_frequency'),'class="form-control m-select2 outstanding_loan_balance_fixed_fine_frequency" id = "outstanding_loan_balance_fixed_fine_frequency"  ') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="outstanding_loan_balance_percentage_settings">
                                            <div class="m-form__group form-group row pt-0 m--padding-10">
                                                <div class="m-form__group-sub col-sm-4">
                                                    <label><?php echo translate('Percentage Fine Rate');?><span class="required">*</span></label>
                                                    <?php echo form_input('outstanding_loan_balance_percentage_fine_rate',$this->input->post('outstanding_loan_balance_percentage_fine_rate'),'  class="form-control numeric outstanding_loan_balance_percentage_fine_rate m-input--air" placeholder="Percentage Fine Rate"'); ?>
                                                </div>
                                                <div class="m-form__group-sub col-sm-4">
                                                    <label><?php echo translate('Fine Frequecy');?><span class="required">*</span></label>
                                                    <?php echo form_dropdown('outstanding_loan_balance_percentage_fine_frequency',array(''=>'--Select  the fine frequecy--')+translate($late_payments_fine_frequency),$this->input->post('outstanding_loan_balance_percentage_fine_frequency'),'class="form-control m-select2 outstanding_loan_balance_percentage_fine_frequency" id = "outstanding_loan_balance_percentage_fine_frequency"  ') ?>
                                                </div>
                                                <div class="m-form__group-sub col-sm-4 m-input--air">
                                                    <label><?php echo translate('Fine Charge on');?><span class="required">*</span></label>
                                                    <?php echo form_dropdown('outstanding_loan_balance_percentage_fine_on',array(''=>'--Select where is Fine Charged On--')+translate($percentage_fine_on),$this->input->post('outstanding_loan_balance_percentage_fine_on'),'class="form-control m-select2 outstanding_loan_balance_percentage_fine_on" id = "outstanding_loan_balance_percentage_fine_on"  ') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="outstanding_loan_balance_fine_one_off_settings">
                                            <div class="m-form__group form-group row pt-0 m--padding-10">
                                                <div class="m-form__group-sub col-sm-12">
                                                    <label><?php echo translate('One Off Amount Charged for Oustanding Balances');?><span class="required">*</span></label>
                                                    <?php echo form_input('outstanding_loan_balance_fine_one_off_amount',$this->input->post('outstanding_loan_balance_fine_one_off_amount'),'  class="form-control currency outstanding_loan_balance_fine_one_off_amount" placeholder="Outsanding Loan Balance One Off Fine Amount"'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset class="m--margin-top-20">
                                    <legend><?php echo translate('General Details');?></legend>
                                    <div class="form-group m-form__group row pt-0 m--padding-10">
                                        <div class="col-lg-12 m-form__group-sub">
                                            <label class="form-control-label"><?php echo translate('Do you wish to enable guarantors for this loan type');?>?:</label>
                                            <div class="m-radio-inline">
                                                <?php 
                                                    if($this->input->post('enable_loan_guarantor')==1){
                                                        $enable_loan_guarantor = TRUE;
                                                        $disabled_loan_guarantor = FALSE;
                                                    }else if($this->input->post('enable_loan_guarantor')==0){
                                                        $enable_loan_guarantor = FALSE;
                                                        $disabled_loan_guarantor = TRUE;
                                                    }else{
                                                        $enable_loan_guarantor = TRUE;
                                                        $disabled_loan_guarantor = FALSE;
                                                    }
                                                ?>
                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <?php echo form_radio('enable_loan_guarantor',1,$enable_loan_guarantor,""); ?>
                                                    <?php echo translate('Yes');?>
                                                    <span></span>
                                                </label>

                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <?php echo form_radio('enable_loan_guarantor',0,$disabled_loan_guarantor,""); ?>
                                                    <?php echo translate('No');?>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="loan_guarantor_additional_details">
                                        <?php 
                                            if($this->input->post('loan_guarantors_type') == 1){
                                                $enable_loan_guarantor_every_time = TRUE;
                                                $enable_loan_guarantor_over_maximum = FALSE;
                                            }else if($this->input->post('loan_guarantors_type') == 2){
                                                $enable_loan_guarantor_every_time = FALSE;
                                                $enable_loan_guarantor_over_maximum = TRUE;
                                            }else{
                                                $enable_loan_guarantor_every_time = FALSE;
                                                $enable_loan_guarantor_over_maximum = FALSE;
                                            }
                                        ?>

                                        <div class="form-group m-form__group row pt-0 m--padding-10">
                                            <div class="col-lg-12 m-form__group-sub">
                                                <label class="form-control-label"><?php echo translate('Do you charge fines for late monthly loan installment payments');?>?:</label>
                                                <div class="m-radio-inline">
                                                    <label class="m-radio m-radio--solid m-radio--brand">
                                                        <?php echo form_radio('loan_guarantors_type',1,$enable_loan_guarantor_every_time,""); ?>
                                                        <?php echo translate('Every time applicant is  applying  a loan');?>
                                                        <span></span>
                                                    </label>

                                                    <label class="m-radio m-radio--solid m-radio--brand">
                                                        <?php echo form_radio('loan_guarantors_type',2,$enable_loan_guarantor_over_maximum,""); ?>
                                                        <?php echo translate('When an applicant loan request exceeds loan limit ');?>
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="guarantor_settings_holder_every_time">
                                            <div class="m-form__group form-group row pt-0 m--padding-10">
                                                <div class="m-form__group-sub col-sm-12">
                                                    <label><?php echo translate('Minimum Allowed Guarantors');?><span class="required">*</span></label>
                                                    <?php echo form_input('minimum_guarantors',$this->input->post('minimum_guarantors'),'  class="form-control numeric m-input--air" placeholder="Minimum Allowed Guarantors"'); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="guarantor_settings_holder_every_savings">
                                            <div class="m-form__group form-group row pt-0 m--padding-10">
                                                <div class="m-form__group-sub col-sm-12">
                                                    <label><?php echo translate('Minimum Allowed Guarantors');?><span class="required">*</span></label>
                                                    <?php echo form_input('minimum_guarantors_exceed_amount',$this->input->post('minimum_guarantors_exceed_amount'),'  class="form-control numeric m-input--air" placeholder="Minimum Allowed Guarantors"'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="m-form__group form-group row pt-0 m--padding-10">
                                        <div class="col-lg-12 m-form__group-sub">
                                            <label class="form-control-label"><?php echo translate('Do you charge loan processing fee');?>?:</label>
                                            <div class="m-radio-inline">
                                                <?php 
                                                    if($this->input->post('enable_loan_processing_fee')==1){
                                                        $enable_loan_processing_fee = TRUE;
                                                        $disabled_loan_processing_fee = FALSE;
                                                    }else if($this->input->post('enable_loan_processing_fee')==0){
                                                        $enable_loan_processing_fee = FALSE;
                                                        $disabled_loan_processing_fee = TRUE;
                                                    }else{
                                                        $enable_loan_processing_fee = TRUE;
                                                        $disabled_loan_processing_fee = FALSE;
                                                    }
                                                ?>
                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <?php echo form_radio('enable_loan_processing_fee',1,$enable_loan_processing_fee,""); ?>
                                                    <?php echo translate('Yes');?>
                                                    <span></span>
                                                </label>

                                                <label class="m-radio m-radio--solid m-radio--brand">
                                                    <?php echo form_radio('enable_loan_processing_fee',0,$disabled_loan_processing_fee,""); ?>
                                                    <?php echo translate('No');?>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="loan_processing_fee_settings">
                                        <div class="m-form__group form-group pt-0 m--padding-10">
                                            <div class="row">
                                                <div class="m-form__group-sub col-sm-4">
                                                    <label><?php echo translate('Loan processing fee type');?><span class="required">*</span></label>
                                                    <?php echo form_dropdown('loan_processing_fee_type',array(''=>'--Select Type--')+translate($loan_processing_fee_types),$this->input->post('loan_processing_fee_type'),'class="form-control m-select2 loan_processing_fee_type" id = "loan_processing_fee_type"  ') ?>
                                                </div>
                                                <div class="m-form__group-sub col-sm-8">
                                                    <div class="fixed_amount_processing_fee_settings">
                                                        <div class="m-form__group-sub col-sm-12">
                                                            <label><?php echo translate('Processing fee');?><span class="required">*</span></label>
                                                            <?php echo form_input('loan_processing_fee_fixed_amount',$this->input->post('loan_processing_fee_fixed_amount'),'  class="form-control currency loan_processing_fee_fixed_amount m-input--air" id="loan_processing_fee_fixed_amount" placeholder="Enter processing fee amount"'); ?>
                                                        </div>
                                                    </div>

                                                    <div class="percentage_loan_processing_fee">
                                                        <div class="row">
                                                            <div class="m-form__group-sub col-sm-6 m-input--air">
                                                                <label><?php echo translate('Processing fee');?> (%)<span class="required">*</span></label>
                                                                <?php echo form_input('loan_processing_fee_percentage_rate',$this->input->post('loan_processing_fee_percentage_rate'),'  class="form-control numeric loan_processing_fee_percentage_rate m-input--air" placeholder="Processing Fee Percentage"'); ?>
                                                            </div>
                                                            <div class="m-form__group-sub col-sm-6 m-input--air">
                                                                <label><?php echo translate('Charge on');?><span class="required">*</span></label>
                                                                <?php echo form_dropdown('loan_processing_fee_percentage_charged_on',array(''=>'--Select where Percentage is charged on--')+translate($loan_processing_fee_percentage_charged_on),$this->input->post('loan_processing_fee_percentage_charged_on'),'class="form-control m-select2 loan_processing_fee_percentage_charged_on" id = "loan_processing_fee_percentage_charged_on"  ') ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </fieldset>
                            </div>

                            <div class="m--margin-top-50">
                                <div class="col-lg-12 col-md-12">
                                    <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_loan_type_button" type="button">
                                            <?php echo translate('Save Changes');?>
                                        </button>
                                        &nbsp;&nbsp;
                                        <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" data-dismiss="modal" type="button" id="cancel_create_loan_type_form">
                                            <?php echo translate('Cancel');?>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>

</div>
<a class="d-none inline" data-toggle="modal" data-target="#create_new_loan_type_pop_up" data-title="dd New Loan Type" data-id="create_new_loan_type" id="add_new_loan_type"  >
    <?php echo translate('Add Loan Type');?>
</a>





<script>
$(window).on('load',function(){ 
    //load_loan_type_options();
});

$(document).ready(function(){
    $(".m-select2").select2({
        placeholder:{
            id: '-1',
            text: "--Select option--",
        }, 
        width:'100%',
    });

    $(document).on('click','#add_new_loan_type',function(){
        $(".loan_type_form_holder .loan_type").select2({
            width:'100%',
            language: 
                {
                noResults: function() {
                    return '<a class="inline" data-toggle="modal" data-content="#create_new_loan_type_pop_up" data-title="Add New Loan Type" data-id="create_new_loan_type" id="add_new_loan_type"  >Add New Loan Type</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).trigger("select2:close");
    });

    $(document).on('click',".btn#calculate_installments",function (t) { 
        $('#amortization_holder').slideUp();
        $('#amortized_schedule_display').html('');
        var form = $("#form_calculate_installments");
        $('#calculate_installments').addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0);
        $.ajax({
            type:'POST',
            url:'<?php echo base_url("ajax/loans/ajax_loan_calculator"); ?>',           
            data:form.serialize(),
            success: function(response){
                if(isJson(response)){
                    var result = $.parseJSON(response);
                    if(result.status == 1){
                        $('#amortized_schedule_display').html(result.html);
                        $('#amortization_holder').slideDown();
                        $("html, body").animate({ scrollTop: $('#amortized_schedule_display').offset().top }, 800);
                        $('#calculate_installments').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1);
                        /*$('.loandatatable').DataTable({
                            scrollY: "50vh",
                            scrollX: !0,
                            scrollCollapse: !0,
                            "processing": true,
                            "lengthMenu": [[20,50,100, -1], [20, 50, 100, "All"]],
                        });*/
                        var ChartsAmcharts = function() {
                            var loan_calculator = function() {
                                var chart = AmCharts.makeChart("loan_interest_principle", {
                                    "type": "pie",
                                    "theme": "light",

                                    "fontFamily": 'Open Sans',
                                    
                                    "color":    '#888',

                                    "dataProvider": [{
                                        "Loan_title": "Principle",
                                        "value": result.total_principle_amount
                                    }, {
                                        "Loan_title": "Interest",
                                        "value": result.total_interest
                                    }],
                                    "valueField": "value",
                                    "titleField": "Loan_title",
                                    "outlineAlpha": 0.4,
                                    "depth3D": 15,
                                    "balloonText": "[[title]]<br><span style='font-size:14px'><b><?php echo $this->group_currency;?> [[value]]</b> ([[percents]]%)</span>",
                                    "angle": 30,
                                    "exportConfig": {
                                        menuItems: [{
                                            icon: '/lib/3/images/export.png',
                                            format: 'png'
                                        }]
                                    }
                                });

                                jQuery('.cloan_interest_principle_chart_input').off().on('input change', function() {
                                    var property = jQuery(this).data('property');
                                    var target = chart;
                                    var value = Number(this.value);
                                    chart.startDuration = 0;

                                    if (property == 'innerRadius') {
                                        value += "%";
                                    }

                                    target[property] = value;
                                    chart.validateNow();
                                });

                                $('#loan_interest_principle').closest('.portlet').find('.fullscreen').click(function() {
                                    chart.invalidateSize();
                                });
                            }
                            return {
                                //main function to initiate the module
                                init: function() {
                                    loan_calculator();
                                }

                            };

                        }();
                    }else{
                        $('.data_error').each(function(){
                            $('#loan_calculator_holder').slideDown().html(result.message);
                        });
                        $('#calculate_installments').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1);
                    }
                }else{
                    $('#calculate_installments').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1);
                }
            },
            error: function(response){
                $('#calculate_installments').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1);
            },
        });
    });

    $(document).on('change','.loan_type',function(){
        hide_fields();
        var loan_type_id = $('.loan_type').val();
        if($(this).val()=='0'){
            $('#add_new_loan_type').trigger('click');
            $(this).val("").trigger('change');
        }else{
            loan_type_details($(this).val());
        }
    });

    $(document).on('change','select[name="loan_amount_type"]',function(){
        var element = $(this).val();
        if(element == 1){
            $('.loan_amount_input_group').slideDown(); 
            $('.loan_amount_savings_input_group').slideUp();
        }else if(element == 2){
            $('.loan_amount_input_group').slideUp(); 
            $('.loan_amount_savings_input_group').slideDown();
        }else{
           $('.loan_amount_input_group').slideUp();
           $('.loan_amount_savings_input_group').slideUp(); 
        }

    });

    $(document).on('keyup keydown','input[name="maximum_loan_amount"],input[name="loan_times_number"]',function(){
        var element = $(this);            
        if(element.val()){
            $('.interest_type_input_group').slideDown();
        }else{
           $('.interest_type_input_group').slideUp();
        }                
    });

    $(document).on('change','select[name="interest_type"]',function(){
        var interest_type = $(this).val();
        if(interest_type == 1){
            $('.for_custom_settings').slideUp();  
            $('.not_for_custom_settings').slideDown();
            $('#enable_reducing_balance_installment_recalculation').slideUp();
        }else if(interest_type == 2){
            $('.not_for_custom_settings').slideDown();
            $('.for_custom_settings').slideUp();  
            $('#enable_reducing_balance_installment_recalculation').slideDown();
        }else if(interest_type == 3){
            $('.not_for_custom_settings').slideUp();
            $('#enable_reducing_balance_installment_recalculation').slideUp();
            $('.for_custom_settings').slideUp();  
            $('.for_custom_settings').slideDown();
            $('#grace_period').slideDown();
        }else{
            $('.not_for_custom_settings').slideUp();
            $('#enable_reducing_balance_installment_recalculation').slideUp();
            $('.for_custom_settings').slideUp();  
        }
    });

    if($('select[name="interest_type"]').val()){
        $(this).trigger('change');
    }

    $(document).on('keydown keyup','input[name="interest_rate"]',function(){
        $('#grace_period').slideDown();
    });

    if($('select[name="interest_rate"]').val()){
        $(this).trigger('change');
    }

    $(document).on('change','select[name="grace_period"]',function(){
        if($(this).val()){
            $('.loan_repayment_period_input_group').slideDown();
        }else{
            $('.loan_repayment_period_input_group').slideUp();
        }
    });

    if($('select[name="grace_period"]').val()){
        $(this).trigger('change');
    }

    $(document).on('change','.loan_repayment_period_type',function(){
        var loan_type_options_id =  $(this).val();
        if(loan_type_options_id == 1){
            $('.fixed_repayment_period').slideDown();
            $('.varying_repayment_period').slideUp();
        }else if(loan_type_options_id == 2){
            $('.fixed_repayment_period').slideUp();
            $('.varying_repayment_period').slideDown();
        }
    });

    $(document).on('change','input[name="enable_loan_fines"]',function(){
        if($(this).val()==1){
            $('.enable_loan_fines_settings').slideDown();
        }else{
            $('.enable_loan_fines_settings').slideUp();
        }
    });

    $(document).on('change','select[name="loan_fine_type"]',function(){
        var loan_fine_type = $(this).val();
        if(loan_fine_type==1){
            $('.late_loan_payment_fixed_fine').slideDown();
            $('.late_loan_payment_percentage_fine').slideUp();
            $('.late_loan_repayment_one_off_fine').slideUp();
        }else if(loan_fine_type==2){
            $('.late_loan_payment_percentage_fine').slideDown();
            $('.late_loan_payment_fixed_fine').slideUp();
            $('.late_loan_repayment_one_off_fine').slideUp();
        }else if(loan_fine_type==3){
            $('.late_loan_repayment_one_off_fine').slideDown();
            $('.late_loan_payment_percentage_fine').slideUp();
            $('.late_loan_payment_fixed_fine').slideUp();
        }else{
            $('.late_loan_payment_percentage_fine').slideUp();
            $('.late_loan_payment_fixed_fine').slideUp();
            $('.late_loan_repayment_one_off_fine').slideUp();
        }
    });

    $(document).on('change','select[name="one_off_fine_type"]',function(){
        var one_off_fine_type = $(this).val();
        if(one_off_fine_type==1){
            $('.one_off_fine_type_settings').show();
            $('.one_off_percentage_setting').hide();
            $('.one_off_fixed_amount_setting').show();
        }else if(one_off_fine_type==2){
            $('.one_off_fine_type_settings').show();
            $('.one_off_fixed_amount_setting').hide();
            $('.one_off_percentage_setting').show();
        }else if(one_off_fine_type=='')
        {
            $('.one_off_percentage_setting').hide();
            $('.one_off_fixed_amount_setting').hide();
            $('.one_off_fine_type_settings').hide();
        }
    });

    $(document).on('change','input[name="enable_outstanding_loan_balance_fines"]',function(){
        var enable_outstanding_loan_balance_fines = $(this).val();
        if(enable_outstanding_loan_balance_fines == 1){
            $('.enable_outstanding_loan_balances_fines_settings').slideDown();
        }else{
            $('.enable_outstanding_loan_balances_fines_settings').slideUp();
        }
    });


    $(document).on('change','select[name=outstanding_loan_balance_fine_type]',function(){
       var outstanding_loan_balance_fine_type =$(this).val();
       if(outstanding_loan_balance_fine_type==1){
            $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
            $('.outstanding_loan_balance_percentage_settings').slideUp();
            $('.outstanding_loan_balance_fixed_fine').slideDown();
       }else if(outstanding_loan_balance_fine_type==2){
            $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
            $('.outstanding_loan_balance_fixed_fine').slideUp();
            $('.outstanding_loan_balance_percentage_settings').slideDown();
       }else if(outstanding_loan_balance_fine_type==3){
            $('.outstanding_loan_balance_percentage_settings').slideUp();
            $('.outstanding_loan_balance_fixed_fine').slideUp();
            $('.outstanding_loan_balance_fine_one_off_settings').slideDown();
       }else{
            $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
            $('.outstanding_loan_balance_percentage_settings').slideUp();
            $('.outstanding_loan_balance_fixed_fine').slideUp();
       }
    });

    $(document).on('change','input[name="loan_guarantors_type"]',function(){
        var loan_guarantors_type = $(this).val();
        if(loan_guarantors_type == '1'){
            $('.guarantor_settings_holder_every_savings').slideUp();
            $('.guarantor_settings_holder_every_time').slideDown();
        }else if(loan_guarantors_type == '2'){
            $('.guarantor_settings_holder_every_time').slideUp();
            $('.guarantor_settings_holder_every_savings').slideDown();
        }else{
            $('.guarantor_settings_holder_every_savings').slideUp();
            $('.guarantor_settings_holder_every_time').slideUp();
        }
    });

    $(document).on('change','input[name="enable_loan_guarantor"]',function(){
        var element = $(this).val();
        if(element == 1){
            $('.loan_guarantor_additional_details').slideDown();
        }else if(element == 2){
            $('.loan_guarantor_additional_details').slideUp();
        }else{
            $('.loan_guarantor_additional_details').slideUp(); 
        }

    });

    $(document).on('change','select[name="loan_processing_fee_type"]',function(){
        var loan_processing_fee_type = $(this).val();
        if(loan_processing_fee_type == 1){
            $('.percentage_loan_processing_fee').slideUp();
            $('.fixed_amount_processing_fee_settings').slideDown();
        }else if(loan_processing_fee_type==2){
            $('.fixed_amount_processing_fee_settings').slideUp();
            $('.percentage_loan_processing_fee').slideDown();
        }else{
            $('.fixed_amount_processing_fee_settings').slideUp();
            $('.percentage_loan_processing_fee').slideUp();
        }
    });

    $(document).on('change','input[name="enable_loan_processing_fee"]',function(){
        var enable_loan_processing_fee = $(this).val();
        if(enable_loan_processing_fee==1){
            $('.loan_processing_fee_settings').slideDown();
        }else{
            $('.loan_processing_fee_settings').slideUp();
        }
    });

    $(document).on('keyup keydown','input[name="maximum_repayment_period"],input[name="fixed_repayment_period"]',function(){
        if($(this).val()){
            $('.addition_loan_types_form_details,.form-actions').slideDown();
        }else{
            $('.addition_loan_types_form_details,form-actions').slideUp();
        }
    });
    SnippetCreateLoanType.init(false,true);

});

function hide_fields(){
    $('.loan_details_holder ').slideUp();
    $('#loan_amount_form_holder').slideUp();
    $('#repayment_periods').slideUp();
    $('#amortization_holder').slideUp();      
}

function load_loan_type_options(){  
    $.ajax({
        type:"POST",
        url:"<?php echo base_url("ajax/loan_types/get_loan_types"); ?>",
        success: function(response){
            if(isJson(response)){               
                var loan_type = $.parseJSON(response);
                if(loan_type.result_code == 200){
                    loan_options = loan_type.data;
                    if(loan_options){
                        $('select.loan_type').html("");
                        var selected = '', a = 0;
                        var max_length = $.map(loan_options.length, function(n, i) { return i; }).length;console.log(max_length);
                        jQuery.each(loan_options, (index, option) => {
                            a++;
                            if(max_length == a){
                                selected = index;
                            }
                            $('.loan_type').append('<option value="' + index + '">' + option + '</option>');
                        });
                        $('select[name="loan_type"]').val(selected);//.trigger('change');
                        $('.loan_type').append('<option value="0">Add Loan Type</option>').trigger('change');

                    }else{
                        $('.data_error').each(function(){
                            $(this).slideDown().html(loan_type.result_description);
                        }); 
                    }
                }else{
                    $('.data_error').each(function(){
                        $(this).slideDown().html(loan_type.result_description);
                    });
                }
            }else{
                $('.data_error').each(function(){
                    $(this).slideDown().html(response);
                });
                $(".modal").animate({ scrollTop: 0 }, 600);
            }
        }
    });
}

function loan_type_details(loan_type_id){
    if(loan_type_id !=''){
        $('.loan_details_holder').html("");
        mApp.block('.loan_details_holder', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Processing...'
        });
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('ajax/loan_types/get_loan_type_information'); ?>",
            data: {'loan_type_id':loan_type_id},
            success: function(response){
                if(isJson(response)){
                    var result = $.parseJSON(response);
                    if(result.status == '200'){
                        $('input[name="maximum_loan_amount_from_savings"]').val(result.member_savings);
                        $('input[name="loan_guarantor_type"]').val(result.loan_guarantor_type);
                        $('input[name="loan_amount_type"]').val(result.loan_amount_type);
                        $('input[name="loan_repayment_type"]').val(result.repayment_period_type);
                        $('.loan_details_holder').html(result.html);  
                        $("#loan_amount_form_holder").slideDown();
                       add_repayment_type();
                    }else if(result.status == '0'){                 
                        $('.loan_details_holder').html(result.message);  
                    }
                }else{
                    //alert(response);
                }                 
                mApp.unblock('.loan_details_holder');
            }
        }); 
    }

}

function add_repayment_type(){
    var repayment_type = $('input[name="loan_repayment_type"]').val();
    if(repayment_type == '2'){
        $('#repayment_periods').slideDown();
    }else{
       $('#repayment_periods').slideUp(); 
    }
}

function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}


</script>