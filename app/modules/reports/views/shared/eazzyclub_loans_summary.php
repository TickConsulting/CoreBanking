<div class="row">
    <div class="col-md-12"> 
        <div class="btn-group margin-bottom-20 search-button">
            <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle=""> Filter
                <i class="fa fa-angle-down"></i>
            </button>
            <div class="dropdown-menu dropdown-content input-large hold-on-click" role="menu">
                <?php echo form_open(current_url(),'method="GET" class="filter"');?>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label ">Disbursement Date Range</label>
                            <div class="">
                                <div class="input-group date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy">
                                    <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control" '); ?>
                                    <span class="input-group-addon"> to </span>
                                    <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control" '); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">
                                <?php
                                    $default_message='Select Member';
                                    $this->languages_m->translate('select_member',$default_message);
                                ?>

                            </label>
                            <div class="input-group col-md-12 col-sm-12 col-xs-12 ">
                                <?php echo form_dropdown('member_ids[]',array(''=>'All')+$this->group_member_options,$this->input->get('member_ids')?$this->input->get('member_ids'):'','class="form-control select2" multiple="multiple" id = "type"  ') ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button name="filter" value="filter" type="submit"  class="btn blue submit_form_button btn-sm"><i class="fa fa-filter"></i>
                                <?php
                                    $default_message='Filter';
                                    $this->languages_m->translate('filter',$default_message);
                                ?>
                        </button>
                        <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                        <button class="btn btn-xs btn-danger close-filter" type="button"><i class="fa fa-close"></i></button>
                    </div>

                <?php echo form_close();?>
            </div>
            
        </div>
        <?php 
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
            echo '
            <div class="btn-group margin-bottom-20 search-button">
                <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.current_url().$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document">
                    Export To Excel <i class="fa fa-file-excel-o"></i>
                </a>
            </div>';
        ?>
    </div>
</div>
<div id="loans_summary">
</div>

<script>

    $(document).ready(function(){

    });

    $(window).on('load',function(){
        load_loan_summary();
    });


    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";


    function load_loan_summary(){
        App.blockUI({
            target: '#loans_summary',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_eazzyclub_loans_summary/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#loans_summary').html(response);
                    $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    App.unblockUI('#loans_summary');
                    $('.tooltips').tooltip();
                }
            }
        );
    }
</script>