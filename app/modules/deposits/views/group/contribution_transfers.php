<div class="m-1">
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
                                        <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" data-date-start-date="-20y" autocomplete="off" '); ?>
                                        <div class="input-group-append">
                                            <span class="input-group-text">to</i></span>
                                        </div>
                                        <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control input m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" data-date-start-date="-20y" autocomplete="off" '); ?>
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
                                <div class="form-group m-form__group pt-0">
                                    <label class="control-label ">Transfers To</label>
                                    <div class="">
                                    <?php
                                        echo form_dropdown('transfer_to',array(""=>"Select transfer to")+$transfer_to_options,$this->input->get('transfer_to'),' class="form-control select2" id="transfer_to" ');
                                    ?>
                                    </div>
                                </div>
                                <div class="form-group m-form__group pt-0 ">
                                    <label class="control-label ">Transfers From</label>
                                    <div class="">
                                    <?php
                                        echo form_dropdown('contribution_from_id[]',array()+$contribution_options+array('loan'=>'Loan Payments'),$this->input->get('contribution_from_id'),' class="form-control select2" multiple="multiple" ');
                                    ?>
                                    </div>
                                </div>
                                <div class="form-group m-form__group pt-0 member_id_search">
                                    <label class="control-label ">Member To</label>
                                    <div class="">
                                    <?php
                                        echo form_dropdown('member_to_id[]',array()+$this->group_member_options,$this->input->get('member_to_id'),'class="form-control select2" id="member_to_id" multiple="multiple"');
                                    ?>
                                    </div>
                                </div>

                                <div class="form-group member_transfer_to_search">
                                    <label class="control-label ">Transfer To</label>
                                    <div class="">
                                    <?php
                                        echo form_dropdown('member_transfer_to',array(""=>"Select transfer to")+$member_transfer_to_options,$this->input->get('member_transfer_to'),' class="form-control select2" id="member_transfer_to" ');
                                    ?>
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
                    '.translate('Export To Excel').' <i class="fa fa-file-excel-o"></i>
                </a>
            </div>';
        ?>
    </div>
</div>

<div id="contribution_transfers_listing">
    
</div>

<script>
    $(document).ready(function(){
        $('#transfer_to').change(function(){
            if($(this).val()==4){
                $('.member_id_search,.member_transfer_to_search').slideDown();
            }else{
                $('#member_to_id,#member_transfer_to').val('').trigger('change');
                $('.member_id_search,.member_transfer_to_search').slideUp();
            }
        });
        <?php if($this->input->get('transfer_to')==4){ ?>
            $('.member_id_search,.member_transfer_to_search').slideDown();
        <?php }else{ ?>
            $('.member_id_search,.member_transfer_to_search').slideUp();
        <?php } ?>
    });
    $(window).on('load',function(){
        load_contribution_transfers_listing();
    });

var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

function load_contribution_transfers_listing(){
    mApp.block('#contribution_transfers_listing',{
        overlayColor: 'white',
        animate: true
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/deposits/get_contribution_transfers_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
        dataType : "html",
            success: function(response) {
                $('#contribution_transfers_listing').html(response);
                $('.select2').select2({width:"100%"});
                $('.date-picker').datepicker({autoclose:true});
                mApp.unblock('#contribution_transfers_listing');
            }
        }
    );
}

</script>