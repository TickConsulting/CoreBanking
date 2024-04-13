<div class="portlet-body form">
    <?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state record_asset_sell" id="record_asset_sell"  role="form"'); ?>
        <div class="form-group m-form__group row pt-0 m--padding-10">
            <div class="col-sm-6 m-form__group-sub">
                <label><?php echo translate('Sale Date');?><span class="required">*</span></label>
                <?php echo form_input('sale_date',$this->input->post('sale_date')?timestamp_to_datepicker(strtotime($this->input->post('sale_date'))):timestamp_to_datepicker($post->sale_date)?:timestamp_to_datepicker(time()),'class="form-control datepicker" data-date-end-date="0d" readonly style="width:100%"' );?> 
            </div>
            <?php echo form_hidden('id',$post->id); ?>
            <div class="col-sm-6 m-form__group-sub m-input--air">
                <label><?php echo translate('Account');?> <span class="required">*</span></label>
                 <?php echo form_dropdown('account_id',array(''=>'--'.translate('Select account').'--')+translate($account_options),$this->input->post('account_id')?$this->input->post('account_id'):$post->account_id,'class="m-select2 form-control " id ="account" data-placeholder="Select..."  ');?>
            </div>

            <div class="col-sm-12 m-form__group-sub pt-0 m--padding-10">
                <label><?php echo translate('Amount');?><span class="required">*</span></label>
                <?php echo form_input('amount',$this->input->post('amount'),'  class="form-control currency m-input--air" id="amount" autocomplete="off"  placeholder="Amount"'); ?> 
            </div>
        </div>
        <div class="col-md-12 m-form__group-sub pt-0 m--padding-10">
            <span class="float-lg-right float-md-right float-sm-right float-xl-right">
                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="sell_asset_button" type="button">
                    <?php echo translate('Save Changes');?>
                </button>
                &nbsp;&nbsp;
                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_asset_button">
                    <?php echo translate('Cancel');?>
                </button> 
            </span>
        </div>
    <?php echo form_close(); ?>

</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.m-select2').select2({
        placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        SnippetSellAsset.init();
    });
</script>