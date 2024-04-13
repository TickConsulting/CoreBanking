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

                                <?php echo form_open(current_url(),'method="GET" class="m-form m-form--fit m-form--label-align-right filter"'); ?>
                                    <div class="form-group m-form__group">
                                        <label>
                                            <?php echo translate('Asset Sale Date Range');?>
                                        </label>
                                        <div class="input-daterange input-group" id="m_datepicker_5">
                                            <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" readonly'); ?>
                                            <div class="input-group-append">
                                                <span class="input-group-text">to</i></span>
                                            </div>
                                            <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control input m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" readonly'); ?>
                                        </div>
                                    </div>

                                    <div class="form-group m-form__group pt-0" >
                                        <label for="">
                                            <?php echo translate('Assets');?>
                                        </label>
                                        <?php echo form_dropdown('assets[]',array()+$asset_options,$this->input->get('assets'),'class="form-control m-select2" multiple="multiple"'); ?>
                                    </div>
                                    <div class="form-group m-form__group pt-0" >
                                        <label for="">
                                            <?php echo translate('Accounts');?>
                                        </label>
                                        <?php echo form_dropdown('accounts[]',array()+$account_options,$this->input->get('accounts'),'class="form-control m-select2" multiple="multiple"'); ?>
                                    </div>
                                    <div class="m-form__actions m-form__actions pt-0 m--align-right">
                                        <button class="btn btn-sm btn-danger">
                                            <i class="la la-close"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary">
                                            <i class="la la-filter"></i>
                                            <?php echo translate('Search');?>
                                        </button>
                                    </div>
                                <?php  echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1'; ?>
            <div class="btn-group margin-bottom-20 search-button">
                <a href="<?php echo site_url('group/assets/asset_sales').$query; ?>" class="btn btn-sm btn-primary m-btn m-btn--icon">
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

<div id="asset_sales_listing" class="table-responsive">

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
    $(document).on('click','.confirmation_link',function(){
        var element = $(this);
        bootbox.confirm({
            message: "Are you sure you want to this ?",
            // title: "Before you proceed",
            callback: function(result) {
                if(result==true){
                    if (result === null) {
                        return true;
                    }else{
                        var href = element.attr('href');
                        window.location = href;
                    }
                }else{
                    return true;
                }
            }
        });
        return false;
    });
});

$(window).on('load',function(){

    load_asset_sales_listing();

});

var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

function load_asset_sales_listing(){
    mApp.block('#asset_sales_listing',{
        overlayColor: 'white',
        animate: true
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/assets/get_asset_sales_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
        dataType : "html",
            success: function(response) {
                $('#asset_sales_listing').html(response);
                mApp.unblock('#asset_sales_listing');
            }
        }
    );
}

</script>