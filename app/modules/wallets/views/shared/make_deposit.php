<style type="text/css">
    .table-payments{

    }

    .table.table-payments  thead tr th {
        font-size: 12px;
        font-weight: 600;
    }

    .table.table-payments  td, .table.table-payments th {
        font-size: 12px;
    }
    .table.table-payments>tfoot>tr>td, .table.table-payments>tfoot>tr>th {
        padding: 10px 6px;
    }
    .total_payments_entry{
        background-color: rgba(0,255,0,0.05);
        border: 1px solid rgba(0,255,0,0.1);
        border-radius: 5px;
        transition: 0.2s;
        padding: 9px 3px;
    }
    .modal-body{
        padding: 0px 15px;
    }
    
    a.colpse-title{
        text-decoration:none;
    }
    .colpse-title:hover, .colpse-title:active, .colpse-title:visited{
        text-decoration:none;
    }
    .colpse-title .colpse-icon{
        font-size:24px;
        font-weight:400;
        color:#26A1AB;
    }
    .colpse-title h4{
        font-weight:400;
        color:#26A1AB;
    }
    .colpse-body{
        padding-top:5px;
        padding-left:20px;
    }
    .colpse-hide{
        display:none;
    }
    .c-ul{
        padding:0px!important;
        padding-left:18px!important;
    }
    .p_amnt{
        padding-top: 2%;
    }

    .modal .m-alert--air.alert {
        margin: 0%;
    }

    .overpayment{
        font-size: 55%;
        color: #008000;
    }
    .overpayment_text{
        color: #008000 !important;
    }
    .balance_text{
        color: #ff0000 !important;
    }
    .img_logo{
        width: 65px;
        height: 23px;
        margin-left: 10px;
    }
    .inline_class{
        padding: 5px 5px 5px 10px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -khtml-border-radius: 5px;
        border-radius: 5px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        border-bottom-left-radius: 5px;
        border: 1px solid #d7d7d7;
        margin: 0 5px 10px 0!important;
        display: inline-block;
        cursor: pointer;
    }
</style>
<?php 
    if($this->group->group_offer_loans){
        $per_item = "3";
    }else{
        $per_item = "4";
    }
?>
<div class="row widget-row member_deposit">
    <div class="col-md-<?php echo $per_item?> col-xs-6">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-5 bordered">
            <h4 class="widget-thumb-heading"><?php echo translate('Total Payment To Group')?></h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-purple fa fa-thumbs-up"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"><?php echo $this->group_currency;?></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="<?php echo  number_to_currency($total_member_payments);?>"><?php echo  number_to_currency($total_member_payments);?></span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>

    <div class="col-md-<?php echo $per_item?> col-xs-6">
        <!-- BEGIN WIDGET THUMB -->
        <?php 
            $overpayment = FALSE;
            if($total_group_member_contribution_arrears>=0){
                $class_name = 'balance_text';
            }else{
                $total_group_member_contribution_arrears = 0;
                $overpayment = FALSE;
                $class_name = 'overpayment_text';
            }
        ?>
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-5 bordered">
            <h4 class="widget-thumb-heading"><?php echo translate('Contribution Arrears')?></h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-green la la-hand-o-down"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"><?php echo $this->group_currency;?></span>
                    <span class="widget-thumb-body-stat <?=$class_name;?>" data-counter="counterup" data-value="<?php echo number_to_currency($total_group_member_contribution_arrears);?>"><?php echo number_to_currency($total_group_member_contribution_arrears);?>
                        <?php echo $overpayment?'<span class="overpayment">(Overpayment)</span>':'';?>
                    </span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>

    <div class="col-md-<?php echo $per_item?> col-xs-6">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-5 bordered">
            <h4 class="widget-thumb-heading"><?php echo translate('Fine Arrears')?></h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon  bg-red fa fa-thumbs-down"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"><?php echo $this->group_currency;?></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="<?php echo number_to_currency($total_group_member_fine_arrears);?>"><?php echo number_to_currency($total_group_member_fine_arrears);?></span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <?php 
    if($this->group->group_offer_loans){
        $loan_balances =number_to_currency($total_loan_balances); ?>
        <div class="col-md-<?php echo $per_item?> col-xs-6">
            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-5 bordered">
                <h4 class="widget-thumb-heading"><?php echo translate('Loan Balances')?></h4>
                <div class="widget-thumb-wrap">
                    <i class="widget-thumb-icon bg-blue mdi mdi-scale-balance"></i>
                    <div class="widget-thumb-body">
                        <span class="widget-thumb-subtitle"><?php echo $this->group_currency;?></span>
                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="<?php echo $loan_balances;?>"><?php echo $loan_balances;?></span>
                    </div>
                </div>
            </div>
            <!-- END WIDGET THUMB -->
        </div>
    <?php }?>
</div>
<div class="clearfix"></div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger alert_danger" style="display: none;">
            <button class="close" data-dismiss="alert"></button>
            <h4 class="block">Error! Something went wrong.</h4>
            <p class="data_error">
                
            </p>
        </div>
        <hr/>
        <?php echo form_open($this->uri->uri_string(),'class="form_submit m-form m-form--state" role="form" method="POST" id="make_payment_form"'); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-condensed table-multiple-items" id="datatable_orders make_payments">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="2%"> # </th>
                                    <th width="35%"> <?php echo translate('Make Payment')?>&nbsp;<?php echo translate('for')?> <span class="required">*</span> </th>
                                    <th width="35%"> <?php echo translate('Payment')?>&nbsp;<?php echo translate('Particulars')?> <span class="required">*</span></th>
                                    <th width="20%" class="text-right"> <?php echo translate('Amount')?>&nbsp;(<?php echo $this->group_currency;?>) <span class="required">*</span></th>
                                    <th width="5%" class="text-right">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id='append-place-holder'> 
                                <tr role="row" class="filter">
                                    <td class='count'>1</td>
                                    <td>
                                        <span class="m-select2-sm m-input--air">
                                            <?php echo form_dropdown("payment_fors[0]",array(''=>'Select...')+$payment_for_options,$this->input->post('payment_fors'),'class="form-control form-filter input-sm m-select2 payment_for"');?>
                                        </span>
                                    </td>
                                    <td class="particulars_place_holder">
                                        <span class="m-select2-sm m-input--air">
                                            <?php echo form_dropdown("payments[0]",array(''=>'Select...'),'','class="form-control form-filter input-sm m-select2" disabled readonly');?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="">
                                            <?php echo form_input("amounts[0]",$this->input->post('amount'),'class="form-control form-filter m-input--air input-sm currency amount text-right " placeholder="Enter amount"');?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href='javascript:;' class="remove-line">
                                            <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right" colspan="3"><?php echo translate('Total Amount')?></td>
                                    <td class="text-right total-amount"><?php echo number_to_currency();?></td>
                                    <td></td>
                                    <?php echo form_hidden('total_amount','');?>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-default btn-sm add-new-line" id="add-new-line">
                        <i class="la la-plus"></i><?php echo translate('Add new payment line');?>
                    </a>
                </div>
            </div>


            <div class="m-form__actions m-form__actions p-0 pt-5 m--margin-top-10">                            
                <div class="row">
                    <div class="col-md-12">
                        <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm submit_form_button" id="make_payment_form_button" type="submit">
                               <?php echo translate('Pay now');?>                              
                            </button>
                            &nbsp;&nbsp;
                            <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="">
                                <?php echo translate('Cancel')?>                              
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        <?php echo form_close();?>
    </div>
</div>
<div style="display: none;" class="contribution-payments">
    <div class="row">
        <span class="m-select2-sm m-input--air">
            <?php echo form_dropdown('contribution_ids[0]',array(''=>'Select contribution...')+$contribution_options,'','class="form-control form-filter input-sm append-select2 contribution"');?>
        </span>
    </div>
</div>

<div style="display: none;" class="fine-payments">
    <div class="row">
        <span class="m-select2-sm m-input--air">
            <?php echo form_dropdown('fine_categories[0]',array(''=>'Select Fine Category...')+$fine_category_options,'','class="form-control form-filter input-sm append-select2 fine_category"');?>
        </span>
    </div>
</div>

<div style="display: none;" class="loan-payments">
    <div class="row">
        <span class="m-select2-sm m-input--air">

            <?php 
                if($member_active_loans){
                    echo form_dropdown('loan_ids[0]',array(''=>'Select Loan...')+$member_active_loans,'','class="loan form-control form-filter input-sm append-select2"');
                }else{
                    echo form_dropdown('loan_ids[0]',array(''=>'No active loans'),'','class="loan form-control form-filter input-sm append-select2"');
                }
            ?>
        </span>
    </div>
</div>

<div style="display: none;" class="default-payments">
    <div class="row">
        <span class="m-select2-sm m-input--air">
            <?php echo form_dropdown("default",array(''=>'Select...'),'','class="form-control form-filter input-sm select2" disabled readonly');?>
        </span>
    </div>
</div>

<div style="display: none;" class="miscellaneous-payments">
    <div class="row">
        <?php 
            $textarea = array(
                'name'=>'descriptions[0]',
                'id'=>'',
                'value'=> '',
                'cols'=>25,
                'rows'=>5,
                'maxlength'=>'',
                'class'=>'form-control miscellaneous_deposit_description m-input--air>',
                'placeholder'=>''
            ); 
            echo form_textarea($textarea);

        ?>
    </div>
</div>

<div id='append-new-line' class="d-none">
    <table>
        <tbody>
            <tr>
                <td width='2%' class='count'>1</td>
                <td class="deposit_for_cell" width='25%'>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown("payment_fors[0]",array(''=>'Select...')+$payment_for_options,$this->input->post('payment_fors'),'class="form-control form-filter input-sm append-select2 payment_for"');?>
                    </span>
                </td>
                <td class="particulars_place_holder">
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown("payment[0]",array(''=>'Select...'),'','class="form-control form-filter input-sm append-select2" disabled readonly');?>
                    </span>
                </td>
                <td>
                    <span class="">
                        <?php echo form_input("amounts[0]",$this->input->post('amount'),'class="form-control form-filter m-input--air input-sm currency amount text-right " placeholder="Enter amount"');?>
                    </span>
                </td>
                <td class="text-right" width='4%'>
                    <a href='javascript:;' class="remove-line">
                        <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>


<a href="#gSubscrModal" style="margin-left:5px;margin-bottom:10px;" data-toggle="modal" role="button" class="btn btn-circle hidden btn-sm green d-none" data-backdrop="static" id="add_payment_options" data-keyboard="false">Pay Now here <i class="fa fa-chevron-circle-right"></i></a>

<div class="modal fade" id="gSubscrModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div id="options_holder">
                    <h5>
                        <?php echo translate("Contributions").', '.translate("Fines").' '.translate("and")." ".translate("Loans").' '.translate("Payment");?>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span>
                        </button>
                    </h5>
                    <?php echo form_open($this->uri->uri_string(),'class="form_submit m-form m-form--state" role="form" method="POST" id="mpesa_payment"'); ?>
                        <div class="row pt-2">
                           <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-12" style="">
                                        <div class="form-group">
                                            <h5><strong>Confirm payments to group</strong></h5>
                                            <h6>Confirm group payments and click make payment to enter you pin to complete transaction.</h6>
                                        </div>
                                        <div class="form-group selPMode" style="min-height: 50px;">
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p><?php echo translate('Select Payment Channel')?></p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="controls">
                                            <?php
                                                $c = 0;
                                                foreach ($wallets as $wallet) {
                                                    $c++;
                                                    ?>
                                                    <label class="radio inline inline_class mt-radio <?php if($c==1){ echo 'rChkd'; } ?>">
                                                        <input type="radio" value="<?=$wallet->channel?>" class="billerradio" name="billerid" id="biller16" checked="checked">
                                                        <img src="<?=base_url().$path.$wallet->logo;?>" alt="<?=$wallet->name;?>" title="<?=$wallet->name;?>" class="img-rounded img_logo"> </label>
                                            <?php 
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="channel_1">
                                    <a class="colpse-title xpress-lnk" href="javascript:;"><h4><i class="colpse-icon mdi mdi-chevron-down"></i> M-Pesa Xpress Billing</h4></a>
                                    <div class="colpse-body mpesa-xpress-info">
                                        <div class="form-group">
                                            <h6><strong>Confirm phone number</strong></h6>
                                            <h6>Confirm the total amount to pay and the phone number to pay via <img height="20px;" src="<?php echo base_url();?>templates/admin_themes/groups/img/mpesalogo_small.png" alt="M-Pesa"></h6>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6 p_amnt">
                                                <div class="total_payments_entry">
                                                    <h6>&nbsp;&nbsp;<strong>Total Amount : KES <span class="total_amount_to_pay">25,600.00</span></strong></h6>
                                                </div>
                                            </div>
                                            <div class="form-group m-form__group col-md-6 p_phone">
                                                <label class="sr-only" for="phoneNumber">Phone Number</label>
                                                <div class="input-group m-input-group m-input-group--air">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1">
                                                            +<?php echo $default_calling_code;?>
                                                            <?php echo form_hidden('calling_code',$default_calling_code);?>
                                                        </span></div>
                                                    <input type="text"  value="<?php echo raw_phone_number(valid_phone($this->user->phone)); ?>" class="form-control m-input" placeholder="Quantity" aria-describedby="basic-addon1" name="phone_pay" id="phone_pay" placeholder="Phone number">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                &nbsp;&nbsp;&nbsp;
                                                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm payBtn" style="float:right;margin-bottom:10px;" type="submit" id="mpesa_payment_button"><i class="fa fa-check"></i>
                                                   <?php echo translate('Make Payment');?>                              
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <a class="colpse-title paybill-lnk" href="javascript:;"><h4><i class="colpse-icon mdi mdi-chevron-right"></i> M-Pesa Paybill Instructions</h4></a>
                                    <div class="colpse-body mpesa-paybill-info" style="display:none;">
                                        <ul class="c-ul"> 
                                            <li>
                                                <?php
                                                    echo translate("Go To Paybill Option");
                                                ?>
                                            </li>
                                            <li>
                                                <?php
                                                    echo translate("Enter Pay bill number - <strong>546448</strong> and confirm.");
                                                ?>
                                            </li>
                                            <li>
                                                <?php
                                                    echo translate("Enter your E-Wallet account number ")."<strong>".$group_default_bank_account."</strong>";
                                                ?>
                                            </li>
                                            <li>
                                                <?php
                                                    echo translate("Enter the Amount to pay")." - (".translate("Amount entered above").") ".translate("and")." ".translate("confirm");
                                                ?>
                                            </li>
                                            <li>
                                                <?php
                                                    echo translate("Make sure the details entered are correct, then proceed to confirm the payment").". (".translate("You will receive an SMS once the payment is received, confirming the receipt of the payment").")";
                                                ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    <?php echo form_close();?>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function(){
        $('.m-select2').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        $('select[name="payment_fors"]').select2({width:'200px'});
        $(document).on('click','.xpress-lnk',function(){
            if($(this).hasClass('show_')){
                $('.mpesa-xpress-info').slideDown();
                $(this).find('.colpse-icon').addClass('mdi-chevron-down').removeClass('mdi-chevron-right');
                $(this).removeClass('show_');
                $('#gSubscrModal .modal-footer .payBtn').slideDown();
                if($('.paybill-lnk').hasClass('show_')){
                    $('.paybill-lnk').trigger('click');
                }
            }
            else{
                $('.mpesa-xpress-info').slideUp();
                $(this).find('.colpse-icon').addClass('mdi-chevron-right').removeClass('mdi-chevron-down');
                $(this).addClass('show_');
                $('#gSubscrModal .modal-footer .payBtn').slideUp();
                if($('.paybill-lnk').hasClass('show_')){
                }else{
                    $('.paybill-lnk').trigger('click');
                }
            }
        });
        $(document).on('click','.paybill-lnk',function(){
            if($(this).hasClass('show_')){
                $('.mpesa-paybill-info').slideUp();
                $(this).find('.colpse-icon').addClass('mdi-chevron-right').removeClass('mdi-chevron-down');
                $(this).removeClass('show_');
                if($('.xpress-lnk').hasClass('show_')){
                    $('.xpress-lnk').trigger('click');
                }else{
                }
            }else{
                $('.mpesa-paybill-info').slideDown();
                $(this).find('.colpse-icon').addClass('mdi-chevron-down').removeClass('mdi-chevron-right');
                $(this).addClass('show_');
                if($('.xpress-lnk').hasClass('show_')){
                }else{
                    $('.xpress-lnk').trigger('click');
                }
            }
        });


        $(document).on('change','.payment_for',function(){
            $('.table-multiple-items .append-select2').each(function(){
                if($(this).data('select2')){
                    $(this).select2('destroy');
                }
            });
            var payment_for = $(this).val();
            var parent = $(this).parent().parent().parent().find('td.particulars_place_holder');
            var contribution_row = $('.contribution-payments .row').html();
            var fine_row = $('.fine-payments .row').html();
            var miscellaneous_row = $('.miscellaneous-payments .row').html();
            var loans_row = $('.loan-payments .row').html();
            var default_row = $('.default-payments .row').html();
            if(payment_for == 1){
                parent.html(contribution_row);
            }else if(payment_for ==2){
                parent.html(fine_row);
            }else if(payment_for == 4){
                parent.html(miscellaneous_row);
            }else if(payment_for ==3){
                parent.html(loans_row);
            }else{
                parent.html(default_row);
            }
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                $(this).parent().find('.payment_for').attr('name','payment_fors['+(number-1)+']');
                $(this).parent().find('.contribution').attr('name','contribution_ids['+(number-1)+']');
                $(this).parent().find('.amount').attr('name','amounts['+(number-1)+']');
                $(this).parent().find('.miscellaneous_deposit_description').attr('name','descriptions['+(number-1)+']');
                $(this).parent().find('.loan').attr('name','loan_ids['+(number-1)+']');
                $(this).parent().find('.fine_category').attr('name','fine_categories['+(number-1)+']');
                number++;
            });
            $('.table-multiple-items .append-select2').select2();
        });

        $('#add-new-line').on('click',function(){
            var html = $('#append-new-line tbody').html();
            $('table-multiple-items .append-select2').each(function(){
                if($(this).data('select2')){
                    $(this).select2('destroy');
                }
            });
            html = html.replace_all('checker','');
            $('#append-place-holder').append(html);
            $('.tooltips').tooltip();
            $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                $(this).parent().find('.payment_for').attr('name','payment_fors['+(number-1)+']');
                $(this).parent().find('.contribution').attr('name','contribution_ids['+(number-1)+']');
                $(this).parent().find('.amount').attr('name','amounts['+(number-1)+']');
                $(this).parent().find('.miscellaneous_deposit_description').attr('name','descriptions['+(number-1)+']');
                $(this).parent().find('.loan').attr('name','loan_ids['+(number-1)+']');
                $(this).parent().find('.fine_category').attr('name','fine_categories['+(number-1)+']');
                number++;
            });
            $('.table-multiple-items .append-select2').select2();
            FormInputMask.init();
        });

        $('.table-multiple-items').on('click','a.remove-line',function(event){
            $(this).parent().parent().remove();
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                number++;
            });
            TotalAmount.init();
        });
        function paymentAlerts(mde, msg){
            $('.pAlert').addClass('hidden');
            $('.pAlert').html('<div class="alert alert-'+mde+' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>'+msg+'</div>');
            $('.pAlert').removeClass('hidden');
        }

        SnippetPaymentValidation.init();
        SnippetCompletePayment.init();
    });

    function isNumeric(num){
        return !isNaN(num)
    }

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }


    String.prototype.replace_all = function(search,replacement) {
        var target = this;
        return target.split(search).join(replacement);
    };

    function calculateConvenienceCharge(form_data,content,message=''){
        $.ajax({
            type: "POST",
            url: '<?php echo base_url("ajax/wallets/calculate_convenience_charge"); ?>',
            data: form_data,
            success: function(response){
                if(isJson(response)){
                    var data = $.parseJSON(response);
                    if(data.status == '200'){
                        $('.selPMode').html(data.data_fields).slideDown();
                        $('.total_amount_to_pay').html(data.total_amount_to_pay);
                        $('#add_payment_options').trigger('click');
                    }else{
                        alert(data.message);
                    }
                }else{
                    alert(response);
                }
                Toastr.show("Success",message,'success');
                $('.form_submit').find('.submit_form_button').show();
                $('.form_submit').find('.processing_form_button').hide(); 
                mApp.unblock(content);
            }
        });
    }

    var SnippetPaymentValidation = function(){
        $("#make_payment_form");
        var t = function (redirect,modal) {
            $(document).on('click',".btn#make_payment_form_button",function (t) {
                t.preventDefault();
                var e = $(this),
                a = $("#make_payment_form");               
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                RemoveDangerClass();
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+"/ajax/wallets/make_payment_validation",
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        form_data = a.serialize();
                                        content = a;
                                        calculateConvenienceCharge(form_data,content,response.message);
                                        
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('textarea[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('textarea[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                        }, 2e3)
                    }
                }));
            })
        };
        return {
            init: function (redirect = true,modal = false) {
                t(redirect,modal)
            }
        }
    }();

    var SnippetCompletePayment = function(){
        $("#mpesa_payment");
        var t = function (redirect,modal) {
            $(document).on('click',".btn#mpesa_payment_button",function (t) {
                t.preventDefault();
                var e = $(this),
                a = $("#mpesa_payment");   
                var content = $('#gSubscrModal');            
                mApp.block(content, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Complete Payment...'
                });
                RemoveDangerClass();var phone = $('#mpesa_payment #phone_pay').val();
                var default_calling_code = $('#mpesa_payment input[name="calling_code"]').val();
                var phone_to_pay = default_calling_code+''+phone;
                var form_data = $("#make_payment_form").serialize();
                form_data+= '&phone='+phone_to_pay;
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),$.post({
                    type: "POST",
                    url: base_url+"/ajax/wallets/initiate_member_payments",
                    data: form_data,
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        $('#gSubscrModal').modal('hide');
                                        $('.pAlert').addClass('hidden');
                                        Toastr.show("Payment in progress",response.message,'success');   
                                        if(response.hasOwnProperty('refer')) {
                                            window.location.href = response.refer;
                                        }                                    
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(content);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                mApp.unblock(content);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            mApp.unblock(content);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            mApp.unblock(content);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                        }, 2e3)
                    }
                }));
            })
        };
        return {
            init: function (redirect = true,modal = false) {
                t(redirect,modal)
            }
        }
    }();


</script>
