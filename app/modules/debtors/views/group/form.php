<div  id="">
    <div class="m-form__section m-form__section--first">                
        <div class="">
            <?php echo form_open($this->uri->uri_string(),'class=" add_new_member_form form_submit m-form m-form--state" id="create_external_loans" role="form"'); ?>
                <div id="external_loan_settings">
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Select Debtor');?>?: <span class="required">*</span></label>
                            <?php echo form_dropdown('debtor_id',array(''=>'--'.translate('Select a Debtor').'--')+translate($this->active_group_debtor_options)+array('0'=>'Add Debtor'),$this->input->post('debtor_id')?$this->input->post('debtor_id'):$post->debtor_id,'class="form-control debtor_form m-select2"');?>
                        </div>
                        <?php echo form_hidden('id',isset($post->id)?$post->id:'')?>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Loan Disbursement Date');?>: <span class="required">*</span></label>
                               <div class="input-group ">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                                <?php echo form_input('disbursement_date',$this->input->post('disbursement_date')?timestamp_to_datepicker(strtotime($this->input->post('disbursement_date'))):timestamp_to_datepicker(time()),'class="form-control m-input m-input--air date-picker" readonly data-date-end-date="0d" data-date-format="dd-mm-yyyy" data-date-viewmode="years"');?>
                                
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub pt-3">
                            <label class="form-control-label"><?php echo translate('Loan Amount');?>?: <span class="required">*</span></label>
                             <?php echo form_input('loan_amount',$this->input->post('loan_amount')?$this->input->post('loan_amount'):($post->loan_amount?$post->loan_amount:""),'class="form-control m-input m-input--air currency" placeholder="eg 10,000"');?>
                        </div>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub pt-3">
                            <label class="form-control-label"><?php echo translate('Loan Disbursing Account');?>?: <span class="required">*</span></label>
                            <?php echo form_dropdown('account_id',array(''=>'--'.translate('Select a Account').'--')+translate($active_accounts)+array('0'=>'Add Account'),$this->input->post('account_id')?$this->input->post('account_id'):$post->account_id,'class="form-control account_form m-select2 account_id" id = "account_id"');?>
                        </div>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub pt-3">
                            <label class="form-control-label"><?php echo translate('Loan Repayment Period (In Months)');?>?: <span class="required">*</span></label>
                             <?php echo form_input('repayment_period',$this->input->post('repayment_period')?$this->input->post('repayment_period'):($post->repayment_period?$post->repayment_period:""),'class="form-control m-input m-input--air " placeholder="'.translate('Loan Repayment Period').'"');?>
                        </div>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub m-form__group-sub pt-3">
                            <label><?php echo translate('Interest Type'); ?><span class="required">*</span>
                            </label>
                            <?php echo form_dropdown('interest_type',array(''=>'--'.translate('Select a Account').'--')+translate($interest_types),$this->input->post('interest_type')?$this->input->post('interest_type'):$post->interest_type,'class="form-control account_form m-select2 interest_type" id = "interest_type"');?>
                        </div>
                        <div class="col-sm-12 m-form__group-sub m-form__group-sub pt-3">
                            <div class="m-form__group form-group pt-0 m--padding-top-10" id="enable_reducing_balance_installment_recalculation">
                                <label>
                                    <?php echo translate('Select to enable');?>
                                </label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                        <?php echo form_checkbox('enable_reducing_balance_installment_recalculation',1,FALSE,"");?>
                                        <?php echo translate('Enable Reducing Balance Recalulation on Early Installment Repayment');?>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub m-form__group-sub pt-3">
                            <label>
                                <?php echo translate('Loan Interest Rate');?>
                                <span class="required">*</span>
                            </label>
                            <?php echo form_input('interest_rate',$this->input->post('interest_rate')?:$post->interest_rate?:'','  class="form-control numeric m-input--air" placeholder="'.translate('Loan Interest Rate').'"'); ?>
                        </div>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub m-form__group-sub pt-3">
                            <label>
                                <?php echo translate('Loan Interest Rate Per');?>
                                <span class="required">*</span>
                            </label>
                            <?php echo form_dropdown('loan_interest_rate_per',translate($loan_interest_rate_per),$this->input->post('loan_interest_rate_per')?:$post->loan_interest_rate_per?:'4','class="form-control m-select2 interest_type" id = "loan_interest_rate_per"  ') ?>
                        </div>
                        <div class="col-lg-12 col-sm-12 m-form__group-sub m-form__group-sub pt-3">
                            <label>
                                <?php echo translate('Loan Grace Period');?><span class="required">*</span>
                            </label>
                            <?php echo form_dropdown('grace_period',array(''=> translate('--Select Loan Grace Period--'))+translate($loan_grace_periods)+array('date'=>translate("Custom Date")),$this->input->post('grace_period')?:$post->grace_period?:'','class="form-control m-select2 grace_period" id = "grace_period" ') ?>
                        </div>

                    </div>
                </div>
                <div class="addition_loan_types_form_details">
                    <fieldset class="m--margin-top-20 m-3">
                        <legend>
                            <?php echo translate('Fine Details');?>
                        </legend>
                        <div class="form-group m-form__group row pt-0 m--padding-10">
                            <div class="col-lg-12 m-form__group-sub">
                                <label class="form-control-label">
                                    <?php echo translate('Do you charge fines for late monthly loan installment payments');?>?:
                                    
                                </label>
                                <div class="m-radio-inline">
                                    <?php 
                                 if($this->input->post('enable_loan_fines')?:$post->enable_loan_fines==1){
                                     $enabled_loan_fines = TRUE;
                                     $disabled_loan_fines = FALSE;
                                 }else if($this->input->post('enable_loan_fines')?:$post->enable_loan_fines==0){
                                     $enabled_loan_fines = FALSE;
                                     $disabled_loan_fines = TRUE;
                                 }else{
                                     $enabled_loan_fines = TRUE;
                                     $disabled_loan_fines = FALSE;
                                 }
                                 ?>
                                    <label class="m-radio m-radio--solid m-radio--brand">
                                        <?php echo form_radio('enable_loan_fines',1,$enabled_loan_fines," class='enable_loan_fines_radio'"); ?>
                                        <?php echo translate('Yes');?>
                                        <span></span>
                                    </label>
                                    <label class="m-radio m-radio--solid m-radio--brand">
                                        <?php echo form_radio('enable_loan_fines',0,$disabled_loan_fines,"class='disable_loan_fines_radio'"); ?>
                                        <?php echo translate('No');?>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="enable_loan_fines_settings">
                            <div class="m-form__group form-group row loan_fine_type pt-0 m--padding-10">
                                <div class="m-form__group-sub m-input--air col-sm-12 m-input--air">
                                    <label>
                                        <?php echo translate('What type of Late Loan Payment fine do you Charge');?>?
                                                
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('loan_fine_type',array(''=>'--Select  the Type of Fine Charged--')+translate($late_loan_payment_fine_types),$this->input->post('loan_fine_type')?:$post->loan_fine_type?:'','class="form-control m-select2 loan_fine_type" id = "loan_fine_type"  ') ?>
                                </div>
                            </div>
                            <div class="late_loan_payment_fixed_fine">
                                <div class="m-form__group form-group row pt-0 m--padding-10">
                                    <div class="m-form__group-sub m-input--air col-sm-4">
                                        <label>
                                            <?php echo translate('Fixed Amount charge');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_input('fixed_fine_amount',$this->input->post('fixed_fine_amount')?:$post->fixed_fine_amount?:'','  class="form-control currency fixed_fine_amount m-input--air" placeholder="Fixed Fine Amount"'); ?>
                                    </div>
                                    <div class="m-form__group-sub m-input--air col-sm-4 m-input--air">
                                        <label>
                                            <?php echo translate('Fixed Amount Fine Frequecy');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_dropdown('fixed_amount_fine_frequency',array(''=>'--Select  the fine frequency--')+translate($late_payments_fine_frequency),$this->input->post('fixed_amount_fine_frequency')?:$post->fixed_amount_fine_frequency?:'','class="form-control m-select2 fixed_amount_fine_frequency" id = "fixed_amount_fine_frequency"  ') ?>
                                    </div>
                                    <div class="m-form__group-sub m-input--air col-sm-4 m-input--air">
                                        <label>
                                            <?php echo translate('Fixed Amount Fine Frequecy On');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_dropdown('fixed_amount_fine_frequency_on',array(''=>'--Select  the fine frequency On--')+translate($fixed_amount_fine_frequency_on),$this->input->post('fixed_amount_fine_frequency_on')?:$post->fixed_amount_fine_frequency_on?:'','class="form-control m-select2 fixed_amount_fine_frequency_on" id = "fixed_amount_fine_frequency_on"  ') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="late_loan_payment_percentage_fine">
                                <div class="m-form__group form-group row pt-0 m--padding-10">
                                    <div class="m-form__group-sub col-sm-4">
                                        <label>
                                            <?php echo translate('Fine Percentage Rate');?>(%)
                                                    
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_input('percentage_fine_rate',$this->input->post('percentage_fine_rate')?:$post->percentage_fine_rate?:'','  class="form-control numeric percentage_fine_rate m-input--air" placeholder="Fine Percentage Rate"'); ?>
                                    </div>
                                    <div class="m-form__group-sub m-input--air col-sm-4">
                                        <label>
                                            <?php echo translate('Fine Frequecy');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_dropdown('percentage_fine_frequency',array(''=>'--Select  the fine frequecy--')+translate($late_payments_fine_frequency),$this->input->post('percentage_fine_frequency')?:$post->percentage_fine_frequency?:'','class="form-control m-select2 percentage_fine_frequency" id = "percentage_fine_frequency"  ') ?>
                                    </div>
                                    <div class="m-form__group-sub m-input--air col-sm-4">
                                        <label>
                                            <?php echo translate('Fine Charge on');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_dropdown('percentage_fine_on',array(''=>'--Select where is Fine Charged On--')+translate($percentage_fine_on)?:$post->percentage_fine_on?:'',$this->input->post('percentage_fine_on'),'class="form-control m-select2 percentage_fine_on" id = "percentage_fine_on"  ') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="late_loan_repayment_one_off_fine">
                                <div class="m-form__group form-group row pt-0 m--padding-10">
                                    <div class="m-form__group-sub col-sm-4 m-input--air">
                                        <label>
                                            <?php echo translate('Select One Off Fine Type');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_dropdown('one_off_fine_type',array(''=>'--Select one Off fine Type--')+translate($one_off_fine_types),$this->input->post('one_off_fine_type')?:$post->one_off_fine_type?:'','class="form-control m-select2 one_off_fine_type" id = "one_off_fine_type"  ') ?>
                                    </div>
                                    <div class="m-form__group-sub col-sm-8">
                                        <div class="row">
                                            <div class="one_off_fixed_amount_setting one_off_fixed_amount col-sm-12">
                                                <div class="m-form__group-sub">
                                                    <label>
                                                        <?php echo translate('One Off Fixed Amount');?>
                                                        <span class="required">*</span>
                                                    </label>
                                                    <?php echo form_input('one_off_fixed_amount',$this->input->post('one_off_fixed_amount')?:$post->one_off_fixed_amount?:'','  class="form-control currency fixed_fine_amount m-input--air" placeholder="One Off Fixed Amount"'); ?>
                                                </div>
                                            </div>
                                            <div class="one_off_percentage_setting">
                                                <div class="row">
                                                    <div class="m-form__group-sub col-sm-6">
                                                        <label>
                                                            <?php echo translate('One Off Percentage');?> (%)
                                                                    
                                                            <span class="required">*</span>
                                                        </label>
                                                        <?php echo form_input('one_off_percentage_rate',$this->input->post('one_off_percentage_rate')?:$post->one_off_percentage_rate?:'','  class="form-control numeric one_off_percentage_rate m-input--air" placeholder="One Off Percentage Rate"'); ?>
                                                    </div>
                                                    <div class="m-form__group-sub col-sm-6 m-input--air">
                                                        <label>
                                                            <?php echo translate('One Off Percentage on');?>
                                                            <span class="required">*</span>
                                                        </label>
                                                        <?php echo form_dropdown('one_off_percentage_rate_on',array(''=>'--Select One Off Percentage on--')+translate($one_off_percentage_rate_on),$this->input->post('one_off_percentage_rate_on')?:$post->one_off_percentage_rate_on?:'','class="one_off_percentage_rate_on form-control m-select2" id = "one_off_percentage_rate_on"  ') ?>
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
                                <label class="form-control-label">
                                    <?php echo translate('Do you charge fines for outstanding loan balance at the end of the loan');?>?:
                                    
                                </label>
                                <div class="m-radio-inline">
                                    <?php 
                                 if($this->input->post('enable_outstanding_loan_balance_fines')?:$post->enable_outstanding_loan_balance_fines ==1){
                                     $enabled_loan_outstanding_fines = TRUE;
                                     $disabled_loan_outstanding_fines = FALSE;
                                 }else if($this->input->post('enable_outstanding_loan_balance_fines')?:$post->enable_outstanding_loan_balance_fines ==0){
                                     $enabled_loan_outstanding_fines = FALSE;
                                     $disabled_loan_outstanding_fines = TRUE;
                                 }else{
                                     $enabled_loan_outstanding_fines = TRUE;
                                     $disabled_loan_outstanding_fines = FALSE;
                                 }
                                 ?>
                                    <label class="m-radio m-radio--solid m-radio--brand">
                                        <?php echo form_radio('enable_outstanding_loan_balance_fines',1,$enabled_loan_outstanding_fines,"class='enable_outstanding_loan_balance_fines_radio'"); ?>
                                        <?php echo translate('Yes');?>
                                        <span></span>
                                    </label>
                                    <label class="m-radio m-radio--solid m-radio--brand">
                                        <?php echo form_radio('enable_outstanding_loan_balance_fines',0,$disabled_loan_outstanding_fines,"class='disable_outstanding_loan_balance_fines_radio'"); ?>
                                        <?php echo translate('No');?>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="enable_outstanding_loan_balances_fines_settings">
                            <div class="m-form__group form-group row pt-0 m--padding-10">
                                <div class="m-form__group-sub m-input--air col-sm-12">
                                    <label>
                                        <?php echo translate('What type of fine do you charge for outstanding balances');?>?
                                                
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('outstanding_loan_balance_fine_type',array(''=>'--Select Oustanding Loan Balance fine type--')+translate($late_loan_payment_fine_types),$this->input->post('outstanding_loan_balance_fine_type')?:$post->outstanding_loan_balance_fine_type?:'','class="form-control m-select2 outstanding_loan_balance_fine_type" id = "outstanding_loan_balance_fine_type"  ') ?>
                                </div>
                            </div>
                            <div class="outstanding_loan_balance_fixed_fine">
                                <div class="m-form__group form-group row pt-0 m--padding-10">
                                    <div class="m-form__group-sub col-sm-6">
                                        <label>
                                            <?php echo translate('Fixed Fine Amount Charged for Outstanding Balances');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_input('outstanding_loan_balance_fine_fixed_amount',$this->input->post('outstanding_loan_balance_fine_fixed_amount')?:$post->outstanding_loan_balance_fine_fixed_amount?:'','  class="form-control m-input--air currency outstanding_loan_balance_fine_fixed_amount" placeholder="Outsanding Loan Balance Fixed Fine Amount"'); ?>
                                    </div>
                                    <div class="m-form__group-sub col-sm-6 m-input--air">
                                        <label>
                                            <?php echo translate('Frequecy to be Charged on Fixed Amount');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_dropdown('outstanding_loan_balance_fixed_fine_frequency',array(''=>'--Select  the fine frequency--')+translate($late_payments_fine_frequency),$this->input->post('outstanding_loan_balance_fixed_fine_frequency')?:$post->outstanding_loan_balance_fixed_fine_frequency?:'','class="form-control m-select2 outstanding_loan_balance_fixed_fine_frequency" id = "outstanding_loan_balance_fixed_fine_frequency"  ') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="outstanding_loan_balance_percentage_settings">
                                <div class="m-form__group form-group row pt-0 m--padding-10">
                                    <div class="m-form__group-sub col-sm-4">
                                        <label>
                                            <?php echo translate('Percentage Fine Rate');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_input('outstanding_loan_balance_percentage_fine_rate',$this->input->post('outstanding_loan_balance_percentage_fine_rate')?:$post->outstanding_loan_balance_percentage_fine_rate?:'','  class="form-control numeric outstanding_loan_balance_percentage_fine_rate m-input--air" placeholder="Percentage Fine Rate"'); ?>
                                    </div>
                                    <div class="m-form__group-sub col-sm-4">
                                        <label>
                                            <?php echo translate('Fine Frequecy');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_dropdown('outstanding_loan_balance_percentage_fine_frequency',array(''=>'--Select  the fine frequecy--')+translate($late_payments_fine_frequency),$this->input->post('outstanding_loan_balance_percentage_fine_frequency')?:$post->outstanding_loan_balance_percentage_fine_frequency?:'','class="form-control m-select2 outstanding_loan_balance_percentage_fine_frequency" id = "outstanding_loan_balance_percentage_fine_frequency"  ') ?>
                                    </div>
                                    <div class="m-form__group-sub col-sm-4 m-input--air">
                                        <label>
                                            <?php echo translate('Fine Charge on');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_dropdown('outstanding_loan_balance_percentage_fine_on',array(''=>'--Select where is Fine Charged On--')+translate($percentage_fine_on),$this->input->post('outstanding_loan_balance_percentage_fine_on')?:$post->outstanding_loan_balance_percentage_fine_on?:'','class="form-control m-select2 outstanding_loan_balance_percentage_fine_on" id = "outstanding_loan_balance_percentage_fine_on"  ') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="outstanding_loan_balance_fine_one_off_settings">
                                <div class="m-form__group form-group row pt-0 m--padding-10">
                                    <div class="m-form__group-sub col-sm-12">
                                        <label>
                                            <?php echo translate('One Off Amount Charged for Oustanding Balances');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_input('outstanding_loan_balance_fine_one_off_amount',$this->input->post('outstanding_loan_balance_fine_one_off_amount')?:$post->outstanding_loan_balance_fine_one_off_amount?:'','  class="form-control currency outstanding_loan_balance_fine_one_off_amount" placeholder="Outsanding Loan Balance One Off Fine Amount"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="form-group m-form__group row pt-0 m--padding-10">
                    <div class="col-lg-12 col-md-12">
                        <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_external_loan_button" type="button">
                                <?php echo translate('Save Changes & Submit');?>
                            </button>
                            &nbsp;&nbsp;
                            <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_create_contribution_form">
                                <?php echo translate('Cancel');?>
                            </button>
                        </span>
                    </div>
                </div>

            <?php echo form_close() ?>
        </div>
    </div>
</div>

<div class="guarantor_settings_addition" id="loan_guarantor_row" style="display: none;">
    <div class="row new_guarantor m-1">
        <div class="col-sm-4 m-form__group-sub">
            <label>
                <?php echo translate('Guarantor Name');?>
                <span class="required">*</span>
            </label>
            <?php echo form_dropdown('guarantor_id[]',array(''=>'--Select a Guarantor--')+translate($group_members),'',' class="form-control m-input m-select2-append guarantor_id" id=""');?>
        </div>
        <div class="col-sm-4 m-form__group-sub">
            <label>
                <?php echo translate('Guaranteed Amount');?>
                <span class="required">*</span>
            </label>
            <?php echo form_input('guaranteed_amount[]','','  class="form-control m-input--air currency" placeholder="Guarantor Amount" id="guaranteed_amount" '); ?>
        </div>
        <div class="col-sm-4 m-form__group-sub">
            <label>
                <?php echo translate('Comment');?>
            </label>
            <?php echo form_input('guarantor_comment[]','','  class="form-control  m-input--air" placeholder="Guarantor comment" id="guarantor_comment" '); ?>
        </div>
        <div class='m-form__group form-group m-1 pt-0 m--padding-10'>
            <a data-original-title="Remove Guarantor" href="javascript:;" class="btn-sm m-btn--square btn-danger btn-xs tooltips remove-guarantor-settings">
                <i class="fa fa-times"></i>
                <span class="hidden-380">
                    Remove Guarantor
                </span>
            </a>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

<div class="modal fade" id="create_new_debtor_model_pop_up" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Add Debtor');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div> 
            <div class="modal-body">
                <?php echo form_open($this->uri->uri_string(),'class=" add_new_member_form form_submit m-form m-form--state" id="add_new_debtor_form" role="form"'); ?>
                <span class="error"></span>
                <div class="m-form__section m-form__section--first">
                    <div class="form-group m-form__group row">
                        <div class="col-sm-12 m-form__group-sub pt-0 m--padding-0">
                            <label><?php echo translate('Debtor Name');?><span class="required">*</span></label>
                              <?php echo form_input('name','','class="form-control m-input m-input--air " placeholder="Debtor Name" ');?>
                        </div>
                        <div class="col-sm-12 m-form__group-sub pt-0 m--padding-0">
                            <label><?php echo translate('Phone number');?><span class="required">*</span></label>
                              <?php echo form_input('phone','','class="form-control m-input m-input--air " placeholder="Phone number" ');?>
                        </div>
                        <div class="col-sm-12 m-form__group-sub pt-0 m--padding-0">
                            <label><?php echo translate('Email Address');?></label>
                              <?php echo form_input('email','','class="form-control m-input m-input--air " placeholder="Email Address" ');?>
                        </div>
                        <div class="col-sm-12 m-form__group-sub pt-0 m--padding-0">
                            <label><?php echo translate('Description');?></label>
                            <?php 
                                $textarea = array(
                                    'name'  =>  'description',
                                    'class' =>  'form-control',
                                    'rows'  =>  6,
                                    'value' => '',
                                    'placeholder'=>'Debtor Description'
                                    );
                                echo form_textarea($textarea);
                            ?>
                        </div>
                    </div>                           
                </div>            
                <div class="modal-footer">
                    <span class="float-lg-right float-md-right float-sm-right float-xl-right">
                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_debtor_button" type="button">
                            <?php echo translate('Save Changes & Submit');?>
                        </button>
                        &nbsp;&nbsp;

                        <button type="button" class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm" data-dismiss="modal" aria-label="Close"  id="debtor_close_modal">
                            <?php echo translate('Cancel');?>
                        </button> 
                    </span>
                </div>
            <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<a class="inline d-none" data-toggle="modal" data-target="#create_new_debtor_model_pop_up" data-title="Add Debtor" data-id="create_Debtor" id="add_new_debtor_model"  data-backdrop="static" data-keyboard="false">
    <?php echo translate('Add Debtor');?>
</a>



<script type="text/javascript">
    $(document).ready(function(){
        
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
            width:'100%',
        });

        <?php if($this->input->post('grace_period') || $post->grace_period){?>
            $('#grace_period').slideDown();
            $('select[name="grace_period"]').trigger('change');
            $('.addition_loan_types_form_details').slideDown();
        <?php }else{?>
            $('#grace_period').slideUp();
        <?php }?>

        $(document).on('change','.debtor_form',function(){
            if($(this).val()=='0'){
                $('#add_new_debtor_model').trigger('click');
                $(this).val("").trigger('change');
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

        $(document).on('change','select[name="grace_period"]',function(){
            if($(this).val()){
                $('.addition_loan_types_form_details').slideDown();
            }else{
                $('.loan_repayment_period_input_group').slideUp();
                $('.addition_loan_types_form_details').slideUp();
            }
        })

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

        $(document).on('change','input[name="enable_loan_guarantors"]',function(){
            var element = $(this).val();
            if(element == 1){
                $('.loan_guarantor_member_details').slideDown();
            }else if(element == 2){
                $('.loan_guarantor_member_details').slideUp();
            }else{
                $('.loan_guarantor_member_details').slideUp(); 
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

        $(document).on('change','input[name="disbursement_date"]',function(){
            var grace_period = $('select[name="grace_period"]').val();
            var disbursement_date = $(this).val();
            date = new Date(disbursement_date.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
            if(grace_period == 'date'){
                $('input[name="grace_period_date"]').removeAttr('disabled');
            }else{
                grace_period = parseInt(grace_period);
                grace_period_date = new Date(date.setMonth(date.getMonth()+grace_period));
                $('input[name="grace_period_date"]').datepicker('setDate',grace_period_date);
                $('input[name="grace_period_date"]').attr('disabled',true);
            }
        });

        $(document).on('change','select[name="grace_period"]',function(){
            var grace_period = $(this).val();
            var disbursement_date = $('input[name="disbursement_date"]').val();
            date = new Date(disbursement_date.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
            if(grace_period == 'date'){
                $('input[name="grace_period_date"]').removeAttr('disabled');
            }else{
                grace_period = parseInt(grace_period);
                grace_period_date = new Date(date.setMonth(date.getMonth()+grace_period));
                $('input[name="grace_period_date"]').datepicker('setDate',grace_period_date);
                $('input[name="grace_period_date"]').attr('disabled',true);
            }
        });

        SnippetCreateDebtor.init(false,true);

        var id =  $('input[name="id"]').val()
        if(id==''){
            SnippetCreateDebtorLoan.init();
        }else{
            SnippetEditDebtorLoan.init();
        }
    });
    

</script>