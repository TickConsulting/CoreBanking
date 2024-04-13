<div class="row">
    <div class="col-md-12"> 
        <?php 
                // $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
                // echo '
                // <div class="btn-group margin-bottom-20 search-button">
                //     <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.current_url().$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Deposits">
                //         Export To Excel <i class="fa fa-file-excel-o"></i>
                //     </a>
                // </div>';
            ?> 
        
        <?php 
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
        ?> 
        <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().$query;?>" target="_blank"><i class='fa fa-file'></i>&nbsp;
            <?php echo translate('Generate Excel'); ?>
        </a>
        <!-- Filter -->
        <!-- <div class="m-dropdown m-dropdown--inline m-dropdown--large m-dropdown--arrow" m-dropdown-toggle="click" m-dropdown-persistent="1">
            <a href="#" class="m-dropdown__toggle btn btn-sm btn-primary dropdown-toggle">
                <?php echo translate('Filter'); ?>
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
                                            <?php echo translate('Income Statement Date Range'); ?>
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
                                <div class="form-group m-form__group pt-0">
                                    <label>
                                        <?php echo translate('Select Members with arears');?>
                                    </label>
                                    <?php echo form_dropdown('member_ids[]',array(''=>'All')+$this->group_member_options,$this->input->get('member_ids')?$this->input->get('member_ids'):'','class="form-control select2" multiple="multiple"'); ?>
                                </div>
                                
                                <div class="form-group m-form__group row">
                                    <div class="col-lg-12">
                                        <label>
                                            <?php echo translate('Filter All With Arrears'); ?>
                                        </label>
                                        <div class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                                <?php echo form_checkbox('filter_all_with_arrears',1,$filter_all_with_arrears); ?>
                                                <?php echo translate('Yes'); ?>
                                                <span></span>
                                            </label>
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
        </div> -->
    </div>
</div>
<div id="contributions_summary" class="pt-2" style="font-size: 12px !important;">
</div>
<script>

    $(document).ready(function(){
        $('.select2').select2({width:"100%",allowClear:true});
        $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
    });

    $(window).on('load',function(){
        load_contributions_summary();
    });


    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";
    
    var from = "<?php echo date('d-m-Y',$from); ?>";
    var to = "<?php echo date('d-m-Y',$to); ?>";
    


    function load_contributions_summary(){
        mApp.block('#contributions_summary',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_contributions_summary/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#contributions_summary').html(response);
                    // $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    DatatablesBasicScrollable.init();
                    // $('.member_name').css({'width':"180px"});
                    // $('.grand_total').css({'width':"190px"});
                    mApp.unblock('#contributions_summary');
                }
            }
        );
    }

    var DatatablesBasicScrollable = {
        init: function() {
            $("#m_table_2").DataTable({
                scrollY: "100vh",
                scrollX: !0,
                scrollCollapse: !0,
                searching: !1,
                "bPaginate": false,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": true,
                "ordering": false,
                "aoColumnDefs": [
                    { "bSortable": false}, 
                    { "bSearchable": false}
                ]
            })
        }
    };
</script>
       
