<div class="row">
    <div class="col-md-12"> 
        <div class="m-dropdown m-dropdown--inline m-dropdown--large m-dropdown--arrow" m-dropdown-toggle="click" m-dropdown-persistent="1">
            <a href="#" class="m-dropdown__toggle btn btn-primary btn-sm dropdown-toggle">
                <?php echo translate('Search');?>
            </a>
            <div class="m-dropdown__wrapper">
                <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                <div class="m-dropdown__inner">
                    <div class="m-dropdown__body">              
                        <div class="m-dropdown__content">
                            <?php echo form_open(current_url(),'method="GET" class="filter"');?>
                                <div class="form-group m-form__group">
                                    <label>
                                        <?php echo translate('Asset Purchase Payment Date Range');?>
                                    </label>
                                    <div class="input-daterange input-group" id="m_datepicker_5">
                                        <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" readonly'); ?>
                                        <div class="input-group-append">
                                            <span class="input-group-text">to</i></span>
                                        </div>
                                        <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control input m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" readonly'); ?>
                                    </div>
                                </div>
                                <div class="form-group m-form__group pt-0">
                                    <label>
                                        <?php echo translate('Accounts');?>
                                    </label>
                                    <?php echo form_dropdown('accounts[]',array()+translate($account_options),$this->input->get('accounts')?:'','class="form-control m-select2" multiple="multiple"'); ?>
                                </div>
                                <div class="form-group m-form__group pt-0">
                                    <label>
                                        <?php echo translate('Assets');?>
                                    </label>
                                    <?php echo form_dropdown('assets[]',array()+$asset_options,$this->input->get('assets')?:'','class="form-control m-select2" multiple="multiple"'); ?>
                                </div>
                                <div class="m-form__actions m-form__actions pt-0 m--align-right">
                                    <button name="filter" value="filter" type="submit"  class="btn btn-sm btn-primary"><i class="fa fa-filter"></i>&nbsp;<?php echo translate('Search');?>
                                    </button>
                                    <button  type="button"  readonly="readonly" class="btn btn-sm btn-info processing"><i class="fa fa-spinner fa-spin"></i> </button>
                                    <button class="btn btn-sm btn-danger close-filter" type="button"><i class="fa fa-close"></i>&nbsp;<?php echo translate('Close');?></button>
                                </div>

                            <?php echo form_close() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
          $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1'; ?>
            <div class="btn-group margin-bottom-20 search-button">
                <a href="<?php echo site_url('group/assets/asset_purchase_payments').$query; ?>" class="btn btn-sm btn-primary m-btn m-btn--icon">
                    <span>
                        <i class="la la-file-excel-o"></i>
                        <span>
                            <?php echo translate('Export To Excel'); ?>
                        </span>
                    </span>
                </a>
            </div>
    </div>
</div>
<div id="asset_purchase_payments_listing" class="table-responsive">
</div>
<script>

$(document).ready(function(){
    $(".m-select2").select2({
        placeholder:{
            id: '-1',
            text: "--Select option--",
        }, 
        width: "100%"
    });
});
$(window).on('load',function(){
    load_asset_purchase_payments_listing();
});

var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

function load_asset_purchase_payments_listing(){
    mApp.block('#asset_purchase_payments_listing', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Processing...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("group/assets/ajax_get_asset_purchase_payments_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
        dataType : "html",
            success: function(response) {
                $('#asset_purchase_payments_listing').html(response);
                mApp.unblock('#asset_purchase_payments_listing');
            }
        }
    );
}
</script>