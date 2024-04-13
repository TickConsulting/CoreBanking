<?php if($this->input->get('set_date')):?>
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
                                    <label class="control-label ">Income Statement Date Range</label>
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
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
            echo '
            <div class="btn-group margin-bottom-20 search-button">
                <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.current_url().$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Deposits">
                    Export To Excel <i class="fa fa-file-excel-o"></i>
                </a>
            </div>';
        ?>
    </div>
</div>

<div id="comprehensive_income_statement">
</div>


<?php else:?>
    <div class="col-md-6 col-md-offset-3">
        <h4> 
            <?php
                $default_message='Select Date Range';
                $this->languages_m->translate('select_date_range',$default_message);
            ?>
        </h4>
        <?php echo form_open_multipart($this->uri->uri_string(), ' role="form" class="form_submit" method="GET"'); ?>
        <div class="form-body">
            <div class="form-group">
                <label>
                    <?php
                        $default_message='Date From';
                        $this->languages_m->translate('date_from',$default_message);
                    ?>
                    <span class="required">*</span></label>
                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date="<?php echo timestamp_to_datepicker(strtotime('-1 year'));?>">
                    <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                    </span>
                    <?php echo form_input('from',timestamp_to_datepicker(strtotime('-1 years',time())),'class="form-control"'); ?>
                </div>
            </div> 

            <div class="form-group">
                <label>
                    <?php
                        $default_message='Date To';
                        $this->languages_m->translate('date_to',$default_message);
                    ?>
                    <span class="required">*</span></label>
                <div class="input-group date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date="<?php echo timestamp_to_datepicker(strtotime('today'));?>">
                    <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                    </span>
                    <?php echo form_input('to',timestamp_to_datepicker(strtotime('today')),'class="form-control"'); ?>
                </div>
            </div>
            
        </div>
        <div class="form-actions">
            <button type="submit" name="set_date"  value="set_date" class="btn blue submit_form_button">
                <?php
                    $default_message='Set Dates';
                    $this->languages_m->translate('set_dates',$default_message);
                ?>
            </button>

            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                <?php
                    $default_message='Processing';
                    $this->languages_m->translate('processing',$default_message);
                ?>
            </button>
        </div>
        <?php echo form_close(); ?>
    </div>
<?php endif;?>


<script>
<?php if($this->input->get('set_date')):?>
    $(window).on('load',function(){
        load_account_balances();
    });
<?php endif;?>
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";


    function load_account_balances(){
        App.blockUI({
            target: '#comprehensive_income_statement',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_comprehensive_income_statement?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#comprehensive_income_statement').html(response);
                    $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    App.unblockUI('#comprehensive_income_statement');
                    $('.tooltips').tooltip();
                }
            }
        );
    }
</script>




