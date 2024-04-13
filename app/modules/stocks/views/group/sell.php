    <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="sell_stock"'); ?>
        <div class="form-group m-form__group row pt-0 m--padding-10">
            <div class="col-sm-6 m-form__group-sub">
                <label><?php echo translate('Sale Date');?><span class="required">*</span></label>
                <?php echo form_input('sale_date',$this->input->post('sale_date')?timestamp_to_datepicker(strtotime($this->input->post('sale_date'))):timestamp_to_datepicker($post->sale_date)?:timestamp_to_datepicker(time()),'class="form-control datepicker " data-date-end-date="0d" readonly style="width:100%"' );?> 
            </div>
            <?php echo form_hidden('id',$post->id); ?>
            <?php echo form_hidden('number_of_shares_available',$post->number_of_shares-$post->number_of_shares_sold); ?>
            <?php echo form_hidden('number_of_previously_sold_shares',$post->number_of_shares_sold?$post->number_of_shares_sold:0); ?>
            <div class="col-sm-6 m-form__group-sub m-input--air">
                <label><?php echo translate('Account');?> <span class="required">*</span></label>
                 <?php echo form_dropdown('account_id',array(''=>'--'.translate('Select account').'--')+translate($account_options),$this->input->post('account_id')?$this->input->post('account_id'):$post->account_id,'class="m-select2 form-control " id ="account" data-placeholder="Select..."  ');?>
            </div>

            <div class="col-sm-6 m-form__group-sub pt-0 m--padding-10">
                <label><?php echo translate('Number of Shares Sold');?><span class="required">*</span></label>
                <?php echo form_input('number_of_shares_to_be_sold',$this->input->post('number_of_shares_to_be_sold'),'  class="form-control currency m-input--air" id="number_of_shares_to_be_sold" autocomplete="off"  placeholder="Number of shares"'); ?> 
            </div>

            <div class="col-sm-6 m-form__group-sub m-input--air pt-0 m--padding-10">
                <label><?php echo translate('Sale Price per Share ');?> (<?php echo $this->group_currency; ?>) <span class="required">*</span></label>
                <?php echo form_input('sale_price_per_share',$this->input->post('sale_price_per_share')?$this->input->post('sale_price_per_share'):$post->sale_price_per_share,'  class="form-control currency m-input--air" id="sale_price_per_share" autocomplete="off"  placeholder="Sale Price per Share"'); ?>
            </div>
        </div>
        <div class="col-md-12 m-form__group-sub pt-0 m--padding-10">
            <span class="float-lg-right float-md-right float-sm-right float-xl-right">
                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="sell_stock_button" type="button">
                    <?php echo translate('Save Changes');?>
                </button>
                &nbsp;&nbsp;
                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_sell_stock_button">
                    <?php echo translate('Cancel');?>
                </button> 
            </span>
        </div>

    <?php echo form_close(); ?>
</div>
<script>


$(document).ready(function(){
    $('.m-select2').select2({
        placeholder:{
            id: '-1',
            text: "--Select option--",
        }, 
    });
    SnippetSellStocks.init();
});

</script>