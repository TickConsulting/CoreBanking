<div class="m-3">
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
                                        <?php echo translate('Contribution Transfer Date Range');?>
                                    </label>
                                    <div class="input-daterange input-group" id="m_datepicker_5">
                                        <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" '); ?>
                                        <div class="input-group-append">
                                            <span class="input-group-text">to</i></span>
                                        </div>
                                        <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control input m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" '); ?>
                                    </div>
                                </div>
                                <div class="form-group m-form__group pt-0 ">
                                    <label class="control-label ">Member</label>
                                    <div class="">
                                    <?php
                                        echo form_dropdown('member_id[]',array()+$this->group_member_options,$this->input->get('member_id'),'id="member_id" class="form-control select2" multiple="multiple"');
                                    ?>
                                    </div>
                                </div>

                                <div class="form-group m-form__group pt-0 ">

                                    <label class="control-label ">Select Contribution Accounts</label>
                                    <div class="">
                                        <?php echo form_dropdown('contributions[]',array()+translate($contribution_options),$this->input->get('contributions')?$this->input->get('contributions'):'','class="form-control select2" multiple="multiple"'); ?>
                                    </div>
                                </div>

                                <div class="form-group m-form__group pt-0 fine_categories_search pt-0" style="display: none;">
                                    <div class="input-group col-md-12 col-sm-12 col-xs-12 ">
                                        <label class="control-label ">
                                            <?php echo translate('Select Fine Categories');?>
                                        </label>
                                        <?php echo form_dropdown('fine_categories[]',array()+$fine_category_options,$this->input->get('fine_categories')?$this->input->get('fine_categories'):'','class="form-control select2" multiple="multiple" '); ?>
                                    </div>
                                </div>

                                <div class="m-form__actions m-form__actions pt-0">
                                    <button name="filter" value="filter" type="submit"  class="btn btn-sm btn-primary"><i class="fa fa-filter"></i>&nbsp;<?php echo translate('Search');?>
                                    </button>
                                    <button  type="button"  readonly="readonly" class="btn btn-sm btn-info processing"><i class="fa fa-spinner fa-spin"></i> </button>
                                    <button class="btn btn-sm btn-danger close-filter" type="button"><i class="fa fa-close"></i>&nbsp;<?php echo translate('Close');?></button>
                                </div>
                            <?php echo form_close();?>                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
            echo '
            <div class="btn-group margin-bottom-20 search-button">
                <a class="btn btn-sm btn-primary generate_excel_document_button" type="button" href="'.site_url('group/deposits/contribution_transfers').$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Deposits">
                    Export To Excel <i class="fa fa-file-excel-o"></i>
                </a>
            </div>';
        ?>
    </div>
</div>

<div class="m-section">
    <div class="m-section__content">
        <div id="fines_listing">
        </div>
    </div>
</div>


<script>

    $(document).ready(function(){

        $('#type').change(function(){
            $('.fine_categories_search,.contributions_search').slideUp();
            $('select[name="fine_categories[]"],select[name="contributions[]"]').val("").trigger('change');
            if($(this).val()==2){
                $('.contributions_search').slideDown();
            }else if($(this).val()==3){
                $('.fine_categories_search').slideDown();
            }
        });
        <?php if($this->input->get('type')=="2"){ ?>
            $('.contributions_search').slideDown();
        <?php }else if($this->input->get('type')=="3"){ ?>
            $('.fine_categories_search').slideDown();
        <?php } ?>

        $(document).on('click','.confirmation_link',function(){
            var element = $(this);
            bootbox.confirm({
                message: "Are you sure you want to void this fine?",
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
        load_fines_listing();
    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_fines_listing(){
        mApp.block('#fines_listing', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Processing...'
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/fines/get_fines_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#fines_listing').html(response);
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#fines_listing');
                }
            }
        );
    }

</script>