<div class="invoice-content-2 bordered document-border">
    <div class="row">
        <div class="col-lg-12">
        <?php 
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
        ?> 
        <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().$query;?>" target="_blank"><i class='fa fa-file'></i>&nbsp;
            <?php echo translate('Generate Excel'); ?>
        </a>
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
                                                <?php echo translate('Statement Date Range'); ?>
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
                                            <?php echo translate('Select Members');?>
                                        </label>
                                        <?php echo form_dropdown('member_ids[]',array(''=>'All')+$this->group_member_options,$this->input->get('member_ids')?$this->input->get('member_ids'):'','class="form-control select2" multiple="multiple"'); ?>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label for="">
                                            <?php
                                                $default_message='Select Contribution';
                                                $this->languages_m->translate('select_contribution',$default_message);
                                            ?>

                                        </label>
                                        <div class="input-group col-md-12 col-sm-12 col-xs-12 ">
                                            <?php echo form_dropdown('contribution_ids[]',array(''=>'All')+$contribution_options,$this->input->get('contribution_ids')?$this->input->get('contribution_ids'):'','class="form-control select2" multiple="multiple" id = "type"  ') ?>
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
    <div class="print_page">
        <div id="statement_paper"  class="pt-3">
            <div id="deposits_summary" style="min-height: 150px;">
            </div>
            <hr> 
            <div id="statement_footer">
                <p style="text-align:center;">© <?php echo date('Y')?> . This statement was issued with no alteration </p>
                <p style="text-align:center;">
                    <strong>Powered by:</strong>
                    <br>
                    <img width="150px" src="<?php echo site_url('uploads/logos/'.$this->application_settings->paper_header_logo);?>" alt="<?php echo $this->application_settings->application_name;?> Logo" ?="">
                </p>
            </div>
        </div>
    </div> 
</div>

<script>

    $(document).ready( function(){
        $('.select2').select2({width:"100%",allowClear:true});
        $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
    });

    $(window).on('load',function(){
        load_deposit_summary();
    });


    var from = "<?php echo date('d-m-Y',$from); ?>";
    var to = "<?php echo date('d-m-Y',$to); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";


    function load_deposit_summary(){
        mApp.block('#deposits_summary',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_deposits_summary/'+from+'/'+to+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#deposits_summary').html(response);
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#deposits_summary');
                    $('.tooltips').tooltip();
                }
            }
        );
    }
</script>