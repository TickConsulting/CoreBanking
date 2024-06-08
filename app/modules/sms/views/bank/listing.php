<div class="row">
    <div class="col-md-12">
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
                                    <?php echo form_open(site_url('group/sms/listing'),'method="GET" class="filter"');?>
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="control-label ">SMS Sent On Date Range</label>
                                                <div class="">
                                                    <div class="input-group date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy">
                                                        <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control" '); ?>
                                                        <span class="input-group-addon"> to </span>
                                                        <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control" '); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label ">
                                                    <?php
                                                        $default_message='Member';
                                                        $this->languages_m->translate('member',$default_message);
                                                    ?>

                                                </label>
                                                <div class="">
                                                <?php
                                                    echo form_dropdown('member_id[]',array()+$this->group_member_options,$this->input->get('member_id'),'class="form-control select2" multiple="multiple"');
                                                ?>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pt-2">
            <div class="col-md-12">
                <div id="sms_listing"></div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function(){

    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    $(window).on('load',function(){
        load_sms_listing();
    });

    function load_sms_listing(){
        mApp.block('#sms_listing',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/sms/get_sms_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#sms_listing').html(response);
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#sms_listing');
                }
            }
        );
    }

</script>            
