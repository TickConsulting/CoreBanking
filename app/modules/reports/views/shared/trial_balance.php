<div class="invoice-content-2 bordered document-border">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-dropdown m-dropdown--inline m-dropdown--large m-dropdown--arrow" m-dropdown-toggle="click" m-dropdown-persistent="1">
                <a href="#" class="m-dropdown__toggle btn btn-sm btn-primary dropdown-toggle">
                    <?php echo translate('Filter Records'); ?>
                </a>
                <div class="m-dropdown__wrapper">
                    <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                    <div class="m-dropdown__inner">
                        <div class="m-dropdown__body">              
                            <div class="m-dropdown__content">
                                <?php echo form_open(current_url(),'method="GET" class="filter m-form m-form--label-align-right"');?>
                                    <div class="form-group m-form__group row">
                                        <div class="col-lg-12">
                                            <label>
                                                <?php echo translate('Trial Balance Date Range'); ?>
                                            </label>
                                            <div class="input-daterange input-group date-picker" id="m_datepicker_5" data-date-format="dd-mm-yyyy">
                                                    <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control" '); ?>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                </div>
                                                <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control" '); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-form__actions m--align-right p-0">
                                        <button name="filter" value="filter" type="submit"  class="btn btn-primary btn-sm">
                                            <i class="fa fa-filter"></i>
                                            <?php echo translate('Filter'); ?>
                                        </button>
                                        <button class="btn btn-sm btn-danger close-filter d-none" type="reset">
                                            <i class="fa fa-close"></i>
                                            <?php echo translate('Reset'); ?>
                                        </button>
                                    </div>
                                <?php echo form_close();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>                    
    </div>
    <div class="clearfix"></div>
    <hr>
    <div id="statement_paper"  class="pt-3">
        <div id="trial_balance">
        </div> 
        <hr> 
        <div id="statement_footer" style="display: none;">
            <p style="text-align:center;">© <?php echo date('Y')?> . <?php echo translate('This statement was issued with no alteration') ?> </p>
            <p style="text-align:center;">
                <strong><?php echo translate('Powered by') ?>:</strong>
                <br>
                <img width="150px" src="<?php echo site_url('uploads/logos/'.$this->application_settings->paper_header_logo);?>" alt="<?php echo $this->application_settings->application_name;?> Logo" ?="">
            </p>
        </div>
    </div>  
    <div class="col-xs-12" style="display: none;" id="print_holder">
        <button class="btn btn-sm btn-info hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> 
            <?php echo translate('Print'); ?>
        </button>
        &nbsp;&nbsp;&nbsp;
        <?php $search_string = substr(basename($_SERVER['REQUEST_URI']),strpos(basename($_SERVER['REQUEST_URI']), "?"));?>
        <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().'/'.$this->member->id.'/'.TRUE.$search_string;?>" target="_blank"><i class='fa fa-file'></i>
            <?php echo translate('Generate PDF'); ?>
        </a>
    </div>
</div>
<script type="text/javascript">
    $(document).ready( function(){
        $(".m-select2-search").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
            width: "100%"
        });
        $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
    });

    $(window).on('load',function(){
        load_trial_balance();
    });
    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";
    function load_trial_balance(){
        mApp.block('#trial_balance',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_trial_balance/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#trial_balance').html(response);
                    $('#statement_footer').slideDown();
                    $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    mApp.unblock('#trial_balance');
                }
            }
        );
    }
</script>