<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_money_market_accounts" autocomplete="off" '); ?>

<div class="form-group m-form__group row pt-0 m--padding-10">
    <div class="col-sm-6 m-form__group-sub">
        <label><?php echo translate('Investment Institution Name');?><span class="required">*</span></label>
        <?php echo form_input('investment_institution_name',$this->input->post('investment_institution_name')?$this->input->post('investment_institution_name'):$post->investment_institution_name,'class="form-control investment_institution_name m-input--air" placeholder="'.translate('Investment Institution Name').'"'); ?>
    </div>

    <div class="col-sm-6 m-form__group-sub m-input--air">
        <label><?php echo translate('Investment Date');?><span class="required">*</span></label>
        <?php echo form_input('investment_date',$this->input->post('investment_date')?$this->input->post('investment_date'):$post->investment_date,'class="form-control m-input--air datepicker" placeholder="'.translate('Investment Date').'" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" style="width:100%"');?>
    </div>
</div>
<div class="form-group m-form__group row pt-0 m--padding-10">
    <div class="col-sm-6 m-form__group-sub">
        <label><?php echo translate('Amount');?><span class="required">*</span></label>
        <?php echo form_input('investment_amount',$this->input->post('investment_amount')?$this->input->post('investment_amount'):$post->investment_amount,'class="form-control investment_amount currency m-input--air" placeholder="'.translate('Amount').'"'); ?>
    </div>

    <div class="col-sm-6 m-form__group-sub m-input--air">
        <label><?php echo translate('Account');?> <span class="required">*</span></label>
        <?php echo form_dropdown('withdrawal_account_id',array(''=>translate('Select account'))+$account_options,$this->input->post('withdrawal_account_id')?$this->input->post('withdrawal_account_id'):$post->withdrawal_account_id,'class="form-control m-input m-select2 account " placeholder="'.translate('Account').'"');?>
    </div>
</div>
<div class="form-group m-form__group row pt-0 m--padding-10">
    <div class="col-sm-12 m-form__group-sub">
        <label><?php echo translate('Description');?></label>
        <?php echo form_textarea('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control" placeholder="'.translate('Investment Description').'"');?>
    </div>
</div>


<div class="form-group m-form__group row pt-0 m--padding-10">
    <div class="col-lg-12 col-md-12">
        <span class="float-lg-right float-md-left float-sm-left float-xl-right">
            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_money_market_investments_button" type="button">
                <?php echo translate('Save Changes & Submit');?>
            </button>
            &nbsp;&nbsp;
            <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_member_loan_button">
                <?php echo translate('Cancel');?>
            </button> 
        </span>
    </div>
</div>
<?php echo form_close() ?>

<script type="text/javascript">
    $(document).ready(function(){
        $('.m-select2').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });

        SnippetCreateMoneyMarketInvestments.init(true,false);
    });    

</script>