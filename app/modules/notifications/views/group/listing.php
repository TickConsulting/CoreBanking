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
                                    <?php echo form_open(site_url($this->uri->segment(1).'/notifications/listing'),'method="GET" class="filter"');?>
                                        <div class="form-body">
                                            <div class="form-group m-form__group">
                                                <label>
                                                    <?php echo translate('Notification Date Range');?>
                                                </label>
                                                <div class="input-daterange input-group" id="m_datepicker_5">
                                                    <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" '); ?>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">to</i></span>
                                                    </div>
                                                    <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control input m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" '); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label ">Notification Status</label>
                                            <div class="">
                                            <?php
                                                echo form_dropdown('is_read',array(""=>"Select Notification Status")+$notification_status_options,$this->input->get('is_read'),'class="form-control m-select2" ');
                                            ?>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button name="filter" value="filter" type="submit"  class="btn btn-sm btn-info submit_form_button btn-sm"><i class="fa fa-filter"></i>
                                                <?php
                                                    $default_message='Filter';
                                                    $this->languages_m->translate('filter',$default_message);
                                                ?>
                                            </button>
                                            <button  type="button"  readonly="readonly" class="btn btn-success processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                                            <button class="btn btn-sm btn-danger close-filter" type="button"><i class="la la-close"></i></button>
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
                <div id="notifications_listing"></div>
            </div>
        </div>
    </div>
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

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    $(window).on('load',function(){
        load_notifications_listing();
    });

    function load_notifications_listing(){
        mApp.block('#notifications_listing', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'loading...'
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/notifications/get_notifications_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#notifications_listing').html(response);
                    // $('input[type=checkbox]').uniform();
                    // $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#notifications_listing');
                }
            }
        );
    }

</script>