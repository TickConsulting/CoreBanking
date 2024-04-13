<div  id="">
    <div class="m-form__section m-form__section--first">                
        <div class="">
            <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_bank_loans"'); ?>
                <div id="bank_loan_settings">
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Bank loan description');?>?: <span class="required">*</span></label>
                            <?php echo form_input('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control m-input m-input--air" placeholder="'.translate('Bank loan description').'"');?>
                        </div>
                        <?php echo form_hidden('id',isset($post->id)?$post->id:'')?>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Bank Loan Received');?> (<?php echo $this->group_currency;?>): <span class="required">*</span></label>
                            <?php echo form_input('amount_loaned',$this->input->post('amount_loaned')?$this->input->post('amount_loaned'):$post->amount_loaned,'class="form-control m-input m-input--air currency" placeholder="'.translate('eg 2,000').'"');?>
                        </div>
                    </div>
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Total loan amount payable');?>  (<?php echo $this->group_currency;?>) <span class="required">*</span></label>
                            <?php echo form_input('total_loan_amount_payable',$this->input->post('total_loan_amount_payable')?:$post->total_loan_amount_payable?:'','class="form-control m-input m-input--air currency" placeholder="'.translate('Total loan payable').'"'); ?>
                        </div>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Bank Loan balance');?> (<?php echo $this->group_currency;?>): <span class="required">*</span></label>
                            <?php echo form_input('loan_balance',$this->input->post('loan_balance')?$this->input->post('loan_balance'):$post->loan_balance,'class="form-control m-input m-input--air currency" placeholder="'.translate('Total loan balance as at date').'"');?>
                        </div>
                    </div>
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Loan date');?> <span class="required">*</span></label>
                            <div class="input-daterange input-group date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy"  id="m_datepicker_5">
                                <?php echo form_input('loan_start_date',timestamp_to_datepicker(time()),' class="form-control m-input" '); ?>
                                <div class="input-group-append">
                                    <span class="input-group-text">to</i></span>
                                </div>
                                <?php echo form_input('loan_end_date',timestamp_to_datepicker(strtotime('+3 months')),' class="form-control" '); ?>
                            </div>

                        </div>
                        <div class="col-lg-6 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Account loan deposited to');?> : <span class="required">*</span></label>
                            <?php echo form_dropdown('account_id',array(''=>'--'.translate('Select an Account').'--')+translate($accounts),$this->input->post('account_id')?$this->input->post('account_id'):$post->account_id,'class="form-control m-select2"');?>
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row pt-0 m--padding-10">
                    <div class="col-lg-12 col-md-12">
                        <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_bank_loan_button" type="button">
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

<script type="text/javascript">
    $(document).ready(function(){
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        var id =  $('input[name="id"]').val()
        if(id==''){
            SnippetCreateBankLoan.init();
        }else{
            SnippetEditBankLoan.init();
        }
    });
    

</script>