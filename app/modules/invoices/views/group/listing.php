<div class="row">
    <div class="col-md-12">
        <div class="row mb-3 filter_header">
            <div class="">
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
                                            <?php echo form_dropdown('member_ids[]',array()+$this->group_member_options,$this->input->get('member_ids')?$this->input->get('member_ids'):'','class="form-control select2" multiple="multiple"'); ?>
                                        </div>
                                        <div class="form-group m-form__group pt-0">
                                            <label>
                                                <?php echo translate('Select Contribution Accounts');?>
                                            </label>
                                            <?php echo form_dropdown('contributions[]',array()+translate($contribution_options),$this->input->get('contributions')?$this->input->get('contributions'):'','class="form-control select2" multiple="multiple"'); ?>
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
            &nbsp;&nbsp;&nbsp;
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
</div>
<div class="table-responsive" id="invoices_listing" style="min-height:300px">
</div>


<div class="modal fade" id="invoice_receipt" role="dialog" aria-labelledby="exampleModalLongTitle" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"  style="display: none;">
                <h5 class="modal-title invoice_for" id="exampleModalLongTitle">
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="min-height: 150px;">
                <div class="invoice_details" style="display: none;">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td nowrap>
                                    <strong>
                                        <?php echo translate('Member');?>
                                    </strong>
                                </td>
                                <td class="member">
                                </td>
                            </tr>
                            <tr>
                                <td nowrap>
                                    <strong>
                                        <?php echo translate('Invoice Date');?>
                                    </strong>
                                </td>
                                <td nowrap>
                                    <span class="invoice_date"></span> - <strong><?php echo translate('Due on');?> </strong> <span class="due_date"></span>
                                </td>
                            </tr>

                            <tr>
                                <td nowrap>
                                    <strong>
                                        <?php echo translate('Sent on');?>
                                    </strong>
                                </td>
                                <td nowrap>
                                   <span class="sent_on"></span>
                                </td>
                            </tr>
                            
                            <tr>
                                <td nowrap>
                                    <strong>
                                        <?php echo translate('Invoice Details');?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="description"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pattern" style="display: none;"></div>
            <div class="modal-footer bg-light" style="display: none;">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td nowrap>
                                <strong>
                                    <?php echo translate('Amount Payable');?>
                                </strong>
                            </td>
                            <td class="amount_payable" colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#type').on('change',function(){
            $('#contributions_select,#fine_categories_select').val("").trigger('change');
            if($(this).val()==1){
                $('.fine_categories_search').slideUp();
                $('.contributions_search').slideDown();
            }else if($(this).val()==2){
                $('.fine_categories_search').slideUp();
                $('.contributions_search').slideDown();
            }else if($(this).val()==3){
                $('.fine_categories_search').slideDown();
                $('.contributions_search').slideUp();
            }else if($(this).val()==4){
                $('.contributions_search,.fine_categories_search').slideUp();
            }else{
                $('.contributions_search,.fine_categories_search').slideUp();
            }
        });
        <?php if($this->input->get('type')==1){ ?>
            $('.fine_categories_search').slideUp();
            $('.contributions_search').slideDown();
        <?php }else if($this->input->get('type')==2){ ?>
            $('.fine_categories_search').slideUp();
            $('.contributions_search').slideDown();
        <?php }else if($this->input->get('type')==3){ ?>
            $('.fine_categories_search').slideDown();
            $('.contributions_search').slideUp();
        <?php }else if($this->input->get('type')==4){ ?>
            $('.contributions_search,.fine_categories_search').slideUp();
        <?php }else{ ?>
            $('.contributions_search,.fine_categories_search').slideUp();
        <?php } ?>
    });

    $(document).on('click','.confirmation_link',function(){
        var element = $(this);
        bootbox.confirm({
            message: "Are you sure you want to this?",
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

    $(document).on('click','.prompt_confirmation_message_link',function(){
        var id = $(this).attr('id');
        swal({
            title: "Are you sure?", text: "You won't be able to revert this!", type: "warning", showCancelButton: !0, confirmButtonText: "Yes, Void it!", cancelButtonText: "No, cancel!", reverseButtons: !0
        }).then(function(e) {
            if(e.value == true){
                mApp.block('.'+id+'_active_row', {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'processing..'
                });
                $.ajax({
                    type:'POST',
                    url:'<?php echo site_url('ajax/invoices/void') ?>',
                    data:{'id':id},
                    success: function(response){
                        if(isJson(response)){
                            var data = $.parseJSON(response)
                            if(data.status == '1'){
                                mApp.unblock('.'+id+'_active_row');
                                $('.'+id+'_active_row').hide();
                                swal("success",data.message, "success")
                            }else{
                                mApp.unblock('.'+id+'_active_row');
                                swal("Cancelled",data.message, "error")
                            }
                        }else{
                            mApp.unblock('.'+id+'_active_row');
                            swal("Cancelled", "Could not delete your invoice :)", "error")   
                        }
                    },
                    error: function(){
                        mApp.unblock('.'+id+'_active_row');
                        swal("Cancelled", "Could not delete your invoice :)", "error")
                    },
                });
            }else{
                swal("Cancelled", "Your invoice is safe :)", "error")
            }
        })
    });

    $(window).on('load',function(){
        load_invoices_listing();
    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_invoices_listing(){
        mApp.block('#invoices_listing', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Processing...'
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/invoices/ajax_get_invoices_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#invoices_listing').html(response);
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#invoices_listing');
                }
            }
        );


        $(document).on('click','.view_invoice',function(){
            var id = $(this).attr('id');
            $.ajax({
                type: "GET",
                url: '<?php echo base_url("ajax/invoices/get_invoice/"); ?>'+id,
                dataType : "html",
                    success: function(response) {
                        if(isJson(response)){
                            var data = $.parseJSON(response);
                            $('#invoice_receipt .invoice_for').html(data.invoice_for);
                            $('#invoice_receipt .member').html(data.member);
                            $('#invoice_receipt .amount_payable').html(data.amount_payable);
                            $('#invoice_receipt .invoice_date').html(data.invoice_date);
                            $('#invoice_receipt .due_date').html(data.due_date);
                            $('#invoice_receipt .sent_on').html(data.created_on);
                            $('#invoice_receipt .description').html(data.description);
                            mApp.unblock('#invoice_receipt .modal-body');
                            $('#invoice_receipt .modal-footer,#invoice_receipt .modal-header, #invoice_receipt .invoice_details, #invoice_receipt .pattern').slideDown();
                        }else{
                            $('.error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry:</strong>'+response+'</div>');
                            $('#invoice_receipt .close').trigger('click');
                            mApp.unblock('#invoice_receipt .modal-body');
                        }
                    }
                }
            );
        });

        $('#invoice_receipt').on('shown.bs.modal',function(){
            mApp.block('#invoice_receipt .modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Fetching details...'
            });
        });

        $('#invoice_receipt').on('hidden.bs.modal',function(){
            $('#invoice_receipt .modal-footer,#invoice_receipt .modal-header, #invoice_receipt .invoice_details, #invoice_receipt .pattern').slideUp('fast');
        });

        $('.m-select2-append').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
    }

</script>   