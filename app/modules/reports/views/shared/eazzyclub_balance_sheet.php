
<div class="row">
    <div class="col-md-12"> 

        <?php
            echo '
                <div class="btn-group margin-bottom-20 search-button">
                    <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle=""> Filter
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <div class="dropdown-menu dropdown-content input-large hold-on-click" role="menu">';
                        echo form_open(current_url(),'method="GET" class="filter"');
                        echo '
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label ">Balance Sheet Date Range</label>
                                    <div class="">
                                        <div class="input-group date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy" data-date-end-date="0d">';
                                            echo form_input('from',timestamp_to_datepicker($from),' class="form-control" '); 
                                            echo '<span class="input-group-addon"> to </span>';
                                            echo form_input('to',timestamp_to_datepicker($to),' class="form-control" '); 
                                        echo '
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button name="set_date" value="set_date" type="submit"  class="btn blue submit_form_button btn-sm"><i class="fa fa-filter"></i> Filter</button>
                                <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                                <button class="btn btn-xs btn-danger close-filter" type="button"><i class="fa fa-close"></i></button>
                            </div>';

                        echo form_close();
                        echo '
                    </div>
                </div>';
        ?>
        <?php 
            // $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
            // echo '
            // <div class="btn-group margin-bottom-20 search-button">
            //     <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.current_url().$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Balance Sheet">
            //         Export To Excel <i class="fa fa-file-excel-o"></i>
            //     </a>
            // </div>';
        ?>
    </div>
</div>

<div id="balance_sheet">
</div>

<script>

    $(window).on('load',function(){
        load_account_balances();
    });

    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_account_balances(){
        App.blockUI({
            target: '#balance_sheet',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_eazzyclub_balance_sheet?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#balance_sheet').html(response);
                    $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    App.unblockUI('#balance_sheet');
                    $('.tooltips').tooltip();
                }
            }
        );
    }
    
</script>




