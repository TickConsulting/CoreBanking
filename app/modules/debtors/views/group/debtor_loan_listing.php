<div>
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
                                <?php echo form_open(current_url(),'method="GET" class="m-form m-form--fit m-form--label-align-right"'); ?>
                                    <div class="form-group m-form__group">
                                        <label>
                                            <?php echo translate('Disbursement Date Range');?>
                                        </label>
                                        <div class="input-daterange input-group" id="m_datepicker_5">
                                            <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" '); ?>
                                            <div class="input-group-append">
                                                <span class="input-group-text">to</i></span>
                                            </div>
                                            <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control input m-input date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" '); ?>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group pt-0">
                                        <label>
                                            <?php echo translate('Debtors');?>
                                        </label>
                                        <?php echo form_dropdown('debtor_id[]',array(''=>'All')+translate($this->group_debtor_options),$this->input->get('debtor_id'),'class="form-control m-select2" id="debtor_id" '); ?>
                                    </div>
                                    <div class="m-form__actions m-form__actions pt-0 m--align-right">
                                        <button class="btn btn-sm btn-danger">
                                            <i class="la la-close"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary">
                                            <i class="la la-filter"></i>
                                            <?php echo translate('Search');?>
                                        </button>
                                    </div>

                                <?php  echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1'; ?>
            <div class="btn-group margin-bottom-20 search-button">
                <a href="<?php echo current_url().$query ?>" class="btn btn-sm btn-primary m-btn m-btn--icon">
                    <span>
                        <i class="la la-file-excel-o"></i>
                        <span>
                            <?php echo translate('Export To Excel'); ?>
                        </span>
                    </span>
                </a>
            </div>
        </div>
    </div>
    <div class="row pt-2">
        <div class="col-md-12">
            <span class="error"></span>
            <div id="deposits_listing" class="table-responsive" style="min-height: 200px;">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deposit_receipt" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header  m--padding-15">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="min-height: 150px;">
                <div class="loan_details_content" style="display: none;">
                    <!--begin::Section-->                                            
                    <div class="m-accordion m-accordion--bordered" id="m_accordion_2" role="tablist">   
                        <!--begin::Item-->              
                        <div class="m-accordion__item">
                            <div class="m-accordion__item-head collapsed"  role="tab" id="m_accordion_2_item_1_head" data-toggle="collapse" href="#m_accordion_2_item_1_body" aria-expanded="    false">
                                <span class="m-accordion__item-icon"><i class="fa flaticon-user-ok"></i></span>
                                <span class="m-accordion__item-title deposit_transaction_name"></span>
                                   
                                <span class="m-accordion__item-mode"></span>     
                            </div>

                            <div class="m-accordion__item-body collapse" id="m_accordion_2_item_1_body" class=" " role="tabpanel" aria-labelledby="m_accordion_2_item_1_head" data-parent="#m_accordion_2"> 
                                <div class="m-accordion__item-content">
                                    <div class="row invoice-cust-add margin-bottom-20">
                                        <div class="col-md-7 col-xs-6" style="line-height: 2;font-size: 11px;">
                                            <h5>Loan Details:</h5>
                                            <span class="bold">Amount Borrowed : </span> <span class="amount_borrowed"></span><br/>
                                            <span class="bold">Total Payable : </span> <span class="total_payable"></span><br/>
                                            <span class="bold">Total Fine Amount : </span> <span class="total_fine"></span><br/>
                                            <span class="bold">Total Transfers Out : </span> <span class="total_transfer_out"></span><br/>
                                            <span class="bold">Lump sum remaining : </span> <span class="lump_sum_remaining"></span><br/>
                                            <span class="bold">Total Amount Paid : </span><span class="amount_paid"></span><br/>
                                            <span class="bold">Disbursement Date : </span> <span class="disbursed_date"></span><br/>
                                            <span class="bold">Loan End Date : </span> <span class="loan_end_date"></span><br/>
                                            <span class="bold">Loan Interest Rate: </span> <span class="interest_rate"></span><br/>
                                            <span class="fines_column">
                                                <span class="bold">Late Payment Fines: </span> <span class="payment_fines"></span><br/></span></span>
                                            <span class="outstanding_loan_balance_column">
                                                <span class="bold">Outstanding Balance Fines: </span> <span class="outstanding_payment_fines"></span><br/></span>
                                            <span class="bold">
                                                    <?php
                                                        $default_message='Loan Duration';
                                                        $this->languages_m->translate('loan_duration',$default_message);
                                                    ?>
                                             : </span><span class="loan_duration"></span><br/>
                                             <span class="bold">Loan Grace Period: </span><span class="grace_period"></span><br/>
                                            <span class="bold">Loan Fine Deferment: </span><span class="loan_deferment"></span><br/>
                                            <span class="bold">Loan Status: </span><span class="loan_status"></span><br/>
                                            <span class="processing_fee_column">
                                                <span class="bold">Loan Processing Fee: </span> <span class="processing_fee"></span><br/></span>
                                        </div>
                                        <div class="col-md-5 col-xs-6" style="line-height: 2;font-size: 11px;">
                                            <h5>Applicant Details
: </h5>
                                           
                                            <span class="bold">Disbursed To:</span><span class="member_name"></span><br/>
                                            <span class="bold">
                                                    <?php
                                                        $default_message='Phone';
                                                        $this->languages_m->translate('Phone',$default_message);
                                                    ?>
                                            :</span> <span class="member_phone"></span><br/>
                                            <span class="bold">
                                                    <?php
                                                        $default_message='Email Address';
                                                        $this->languages_m->translate('email_address',$default_message);
                                                    ?>
                                                :</span> <span class="member_email"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Item--> 
                        <!--begin::Item--> 
                        <div class="m-accordion__item">
                            <div class="m-accordion__item-head" role="tab" id="m_accordion_2_item_2_head" data-toggle="collapse" href="#m_accordion_2_item_2_body" aria-expanded="true">
                                <span class="m-accordion__item-icon"><i class="mdi mdi-format-list-bulleted"></i></span>
                                <span class="m-accordion__item-title">Payment Breakdown</span>
                                <span class="m-accordion__item-mode"></span>     
                            </div>

                            <div class="m-accordion__item-body collapse show" id="m_accordion_2_item_2_body" class=" " role="tabpanel" aria-labelledby="m_accordion_2_item_2_head" data-parent="#m_accordion_2"> 
                                <div class="m-accordion__item-content">
                                    <div class="row invoice-body">
                                        <div class="col-xs-12 table-responsive ">
                                            <table class="table table-hover table-striped table-condensed table-statement" style="font-size: 11px;">
                                                <thead>
                                                    <tr>
                                                        <th class="invoice-title" width="15%">
                                                            <?php
                                                                $default_message='Type';
                                                                $this->languages_m->translate('type',$default_message);
                                                            ?>

                                                        </th>
                                                        <th class="invoice-title" >
                                                            <?php
                                                                $default_message='Date';
                                                                $this->languages_m->translate('date',$default_message);
                                                            ?>

                                                        </th>
                                                        <th class="invoice-title ">
                                                            <?php
                                                                $default_message='Description';
                                                                $this->languages_m->translate('description',$default_message);
                                                            ?>

                                                        </th>
                                                        <th class="invoice-title  text-right">
                                                            <?php
                                                                $default_message='Amount Paid';
                                                                $this->languages_m->translate('amount_paid',$default_message);
                                                            ?>
                                                         (<?php echo $this->group_currency; ?>)</th>
                                                        <th class="invoice-title  text-right">Balance(<?php echo $this->group_currency; ?>)</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table_body_data">
                                                </tbody>
                                                <tfoot class="table_footer_data">
                                                </tfoot>
                                            </table>
                                        </div>

                                        <div class="col-md-12 text-center">
                                                © 2013 - <?php echo date('Y');?>. This statement was issued with no alteration <br/><br/>

                                                <strong>Powered by :</strong><br/>

                                            <img src="<?php echo site_url('uploads/logos/'.$this->application_settings->paper_footer_logo);?>" alt="" class='group-logo-footer image-responsive' /> 
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>   
                        <!--end::Item-->                   
                    </div>
                </div>
            </div>
            <div class="pattern" style="display: none;"></div>
            <div class="modal-footer bg-light" style="display: none;">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td nowrap>
                            </td>
                            <td class="amount">
                                
                            </td>
                            <td class="m--align-right">
                                <a href="javascript:;" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon void_loan" data-message="Are you sure you want to void debtor loan?">
                                    <span>
                                        <i class="la la-trash"></i>
                                        <span>
                                            Void &nbsp;&nbsp;
                                        </span>
                                    </span>
                                </a>
                                &nbsp;&nbsp;
                                <div class="btn-group">
                                    <a href="javascript:;" class="btn btn-sm btn-primary m-btn  m-btn m-btn--icon generate_pdf_link">
                                        <span>
                                            <i class="fa fa-file"></i>
                                            <span>
                                                <?php echo translate('GENERATE PDF RECEIPT');?>
                                            </span>
                                        </span>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split more_actions_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="sr-only">More actions..</span>
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(120px, 31px, 0px);">
                                        <a class="dropdown-item edit_loan_link" href="#">
                                            <?php echo translate('Edit Loan');?>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>

$(window).on('load',function(){
    load_debtor_loan_listing();
});

$(document).ready(function(){
    $(document).on('click','.view_loan_statement',function(){
        var id = $(this).attr('id');
        $('.table_body_data').html('');
        $('#deposit_receipt .modal-footer,#deposit_receipt .modal-header, #deposit_receipt .loan_details_content, #deposit_receipt .pattern').hide();
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/debtors/get_loan_statement/"); ?>'+id,
            dataType : "html",
                success: function(response) {
                    if(isJson(response)){
                        var url = '<?php echo base_url("group/debtors/loan_statement/"); ?>'+id+'/1';
                        var edit_url = '<?php echo base_url("group/debtors/edit_loan/"); ?>'+id;
                        $('.void_loan').attr('href','<?php echo site_url("group/debtors/void_loan/");?>'+id);
                        $('.generate_pdf_link').attr('href',url);
                        $('.edit_loan_link').attr('href',edit_url);
                        var data = $.parseJSON(response);
                        var loan_data = data.data;
                        var loan = loan_data.loan;
                        if(loan.is_a_bad_loan==1){
                            mark_as_bard_loan = 'Unmark as bad Loan';
                            var bad_loan_url = '<?php echo base_url("group/loans/unmark_as_a_bad_loan/"); ?>'+id;
                        }else{
                            mark_as_bard_loan = 'Mark as bad Loan';
                            var bad_loan_url = '<?php echo base_url("group/loans/mark_as_a_bad_loan/"); ?>'+id;
                        }
                        if(loan.is_fully_paid==1){
                            $('.mark_as_bard_loan').text(mark_as_bard_loan).attr('href',bad_loan_url).hide();
                        }else{
                            $('.mark_as_bard_loan').text(mark_as_bard_loan).attr('href',bad_loan_url).show();
                        }
                        var loan_type_options = loan_data.loan_type_options;
                        var total_installment_payable = parseFloat(loan_data.total_installment_payable);
                        var total_fines = parseFloat(loan_data.total_fines);
                        var total_transfers_out = parseFloat(loan_data.total_transfers_out);
                        $('.amount_borrowed').html('<?php echo $this->group_currency;?>'+' '+number_to_currency(loan.loan_amount));
                        var payable =total_installment_payable+total_fines+total_transfers_out;
                        $('.total_payable').html('<?php echo $this->group_currency;?>'+' '+number_to_currency(payable));
                        $('.total_fine').html('<?php echo $this->group_currency;?>'+' '+number_to_currency(loan_data.total_fines));
                        //$('.total_transfer_out').html('<?php echo $this->group_currency;?>'+' '+number_to_currency(loan_data.total_transfers_out));
                        if(loan_data.lump_sum_remaining > 0){
                            lump_sum_remaining = loan_data.lump_sum_remaining;
                        }else{
                            lump_sum_remaining = 0;
                        }
                        $('.lump_sum_remaining').html('<?php echo $this->group_currency;?>'+' '+number_to_currency(lump_sum_remaining));
                        $('.amount_paid').html('<?php echo $this->group_currency;?>'+' '+number_to_currency(loan_data.total_paid));
                        var disbursement_date = new Date(parseInt(loan.disbursement_date+'000'));
                        $('.disbursed_date').html(disbursement_date.toDateString());
                        var loan_end_date = new Date(parseInt(loan.loan_end_date+'000'));
                        $('.loan_end_date').html(loan_end_date.toDateString());
                        if(loan.enable_loan_fines==1){
                            if(loan.loan_fine_type==1){
                                fine = '<?php echo $this->group_currency;?>'+' '+number_to_currency(loan.fixed_fine_amount)+' fine '+loan_data.late_payments_fine_frequency[loan.fixed_amount_fine_frequency];
                            }else if(loan.loan_fine_type==2){
                                fine = loan.percentage_fine_rate+'% fine '+loan_data.late_payments_fine_frequency[loan.percentage_fine_frequency]+' on '+loan_data.percentage_fine_on[loan.percentage_fine_on];
                            }else if(loan.loan_fine_type==3){
                                if(loan.one_off_fine_type==1){
                                    fine = 'One Off Amount of <?php echo $this->group_currency;?> '.number_to_currency(loan.one_off_fixed_amount);
                                }else if(loan.one_off_fine_type==2){
                                    fine = loan.one_off_percentage_rate+'% One of Fine on '+loan_data.percentage_fine_on[loan.one_off_percentage_rate_on];
                                }
                            }
                            $('.payment_fines').html(fine);
                            $('.fines_column').show();
                        }else{
                            $('.fines_column').hide();
                        }
                        if(loan.enable_outstanding_loan_balance_fines==1){
                            outstanding_payment_fines = '';
                            if(loan.outstanding_loan_balance_fine_type==1){
                                outstanding_payment_fines= '<?php echo $this->group_currency;?>'+' '+number_to_currency(loan.outstanding_loan_balance_fine_fixed_amount)+' '+loan_data.late_payments_fine_frequency[loan.outstanding_loan_balance_fixed_fine_frequency];
                            }else if(loan.outstanding_loan_balance_fine_type==2){
                                outstanding_payment_fines= loan.outstanding_loan_balance_percentage_fine_rate+'% fine '+loan_data.late_payments_fine_frequency[loan.outstanding_loan_balance_percentage_fine_frequency]+' on '+loan_data.percentage_fine_on[loan.outstanding_loan_balance_percentage_fine_on];
                            }else{
                                outstanding_payment_fines = 'One Off Amount <?php echo $this->group_currency;?> '+number_to_currency(loan.outstanding_loan_balance_fine_one_off_amount);
                            }
                            $('.outstanding_payment_fines').html(outstanding_payment_fines);
                            $('.outstanding_loan_balance_column').show();
                        }else{
                            $('.outstanding_loan_balance_column').hide();
                        }
                        if(loan.enable_loan_processing_fee==1){
                            if(loan.enable_loan_processing_fee){
                               if(loan.loan_processing_fee_type==1){
                                    processing_fee = 'Fixed Amount of <?php echo $this->group_currency;?>'+number_to_currency(loan.loan_processing_fee_fixed_amount);
                                }else{
                                    processing_fee = loan.loan_processing_fee_percentage_rate+'% of '+loan_data.loan_processing_fee_percentage_rate[loan.loan_processing_fee_percentage_charged_on];
                                }
                            }
                            $('.processing_fee').html(processing_fee);
                            $('.processing_fee_column').show();
                        }else{
                            $('.processing_fee_column').hide();
                        }
                        interest_type = loan_data.interest_types[loan.interest_type];
                        period = loan_data.loan_interest_rate_per[loan.loan_interest_rate_per];
                        $('.interest_rate').html(loan.interest_rate+'% per '+period+' on '+interest_type);
                        $('.loan_duration').html(loan.repayment_period+" Months");
                        $('.grace_period').html(loan.grace_period+" Months");
                        if(loan.enable_loan_fine_deferment == 1){
                            $('.loan_deferment').html('<span class="m-badge m-badge--info m-badge--wide"><small>Enabled</small></span>');
                        }else{
                            $('.loan_deferment').html('<span class="m-badge m-badge--danger m-badge--wide"><small>Disabled</small></span>');
                        }
                        if(loan.is_fully_paid == 1){
                            $('.loan_status').html('<span class="m-badge m-badge--primary m-badge--wide"><small>Fully Paid</small></span>');
                        }else{
                            $('.loan_status').html('<span class="m-badge m-badge--info m-badge--wide"><small>In progress</small></span>');
                        }
                        if(loan_type_options.hasOwnProperty(loan.loan_type_id)){
                            loan_type_id = (loan.loan_type_id);
                            loan_type = loan_type_options[loan_type_id];
                            $('.loan_type').html(loan_type);
                        }else{
                            loan_type = 'Loan';
                            $('.loan_type').html(loan_type);
                        }
                        $('.member_name').html(loan.name+' ');
                        $('.member_phone').html(loan.phone);
                        $('.member_email').html(loan.email);

                        $('.deposit_transaction_name').html(loan.name+'  '+loan_type+' of <?php echo $this->group_currency;?>'+' '+number_to_currency(loan.loan_amount));
                        var tbody = 
                        '<tr>'+
                            '<td colspan="4">Total Amount Payable</td>'+
                            '<td class="text-right"><strong>'+number_to_currency(parseFloat(payable)-parseFloat(loan_data.total_transfers_out))+'</strong></td>'+
                        '</tr>';
                        posts = loan_data.posts;
                        var total_amount = 0;
                        $.each(posts,function(key,value){
                            if(value.transaction_type!=5){
                                total_amount+=parseFloat(value.amount);
                            }
                            type = '';
                            if(value.transaction_type==5){
                                type = 'Contibution Transfer'
                            }else{
                                type = 'Payment';
                            }
                            date = (new Date(parseInt(value.transaction_date+'000'))).toDateString();
                            description = '';
                            if(value.transaction_type==5){
                                description ='Transfer to '+loan_data.transfer_options[value.transfer_to];
                            }else{
                                if(value.transfer_from){
                                    description = 'Transfer from ';
                                    if(value.transfer_from =='loan'){
                                        description+='another loan';
                                    }else{
                                        description+=' Contributions';
                                    }
                                }
                                if(value.payment_method){
                                    description+= loan_data.deposit_options[value.payment_method]+' payment to ';
                                } 
                                if(value.account_id){
                                    description+=loan_data.accounts[value.account_id];
                                }
                            }
                            amount = 0;
                            if(value.transaction_type==5){
                                amount='('+number_to_currency(value.amount)+')';
                            }else{
                                amount= number_to_currency(value.amount);
                            }
                            if(value.transaction_type==5){
                                print_amount = number_to_currency(0);
                            }else{
                                this_amount = (parseFloat(payable)-total_amount);
                                print_amount=number_to_currency(this_amount);
                            }
                            tbody+=''+
                            '<tr>'+
                                '<td>'+type+'</td>'+
                                '<td>'+date+'</td>'+
                                '<td>'+description+'</td>'+
                                '<td class="text-right">'+amount+'</td>'+
                                '<td class="text-right"><strong>'+print_amount+'</strong></td>'+
                            '</tr>';
                        });
                        total_paid_amount = parseFloat(loan_data.total_paid)-parseFloat(total_transfers_out);
                        if(payable-parseFloat(loan_data.total_paid)==0){ 
                            total_balance = 0;
                        }else{
                            total_balance =  (payable-parseFloat(loan_data.total_paid));
                        }
                        tfoot = ''+
                        '<tr>'+
                            '<td colspan="3"><strong>Totals</strong></td>'+
                            "<td class='text-right'><strong>"+number_to_currency(total_paid_amount)+"<strong></td>"+
                            "<td class='text-right'><strong>"+number_to_currency(total_balance)+"<strong></td>"+
                        '</tr>';
                        $('.table_footer_data').html(tfoot);
                        $('.table_body_data').append(tbody);
                        mApp.unblock('#deposit_receipt .modal-body');
                        $('#deposit_receipt .modal-footer,#deposit_receipt .modal-header, #deposit_receipt .loan_details_content, #deposit_receipt .pattern').slideDown();
                    }else{
                        $('#deposit_receipt .close').trigger('click');
                        mApp.unblock('#deposit_receipt .modal-body');
                        Toastr.show("Error occured",'Could not complete getting member loan details. Try again later','error');
                    }
                },
                error: function(){
                    mApp.unblock('#deposit_receipt .modal-body');
                    Toastr.show("Error occured",'Could not complete getting member loan details. Try again later','error');
                },
                always: function(){
                    mApp.unblock('#deposit_receipt .modal-body');
                    Toastr.show("Error occured",'Could not complete getting member loan details. Try again later','error');
                }
            }
        );
    });

    $('#deposit_receipt').on('shown.bs.modal',function(){
        mApp.block('#deposit_receipt .modal-body', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Fetching loan details...'
        });
    });
    $('#deposit_receipt').on('hidden.bs.modal',function(){
        $('#deposit_receipt .modal-footer,#deposit_receipt .modal-header, #deposit_receipt .deposit_details, #deposit_receipt .pattern, #deposit_receipt .account, #deposit_receipt .view_loan_statement_link, #deposit_receipt .view_transaction_alert_link').slideUp('fast');
    });
});

var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

function load_debtor_loan_listing(){
    mApp.block('#deposits_listing', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Loading...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/debtors/ajax_get_debtor_loans_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
        dataType : "html",
            success: function(response) {
                $('#deposits_listing').html(response);
                $('input[type=checkbox]').uniform();
                $('.select2').select2({width:"100%"});
                $('.date-picker').datepicker({autoclose:true});
                mApp.unblock('#deposits_listing');
            }
        }
    );
}
</script>