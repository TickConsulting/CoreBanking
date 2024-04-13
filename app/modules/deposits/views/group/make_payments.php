<style type="text/css">
    .modal .modal-content{
        border-radius: 2%;
    }
</style>
<div class="row widget-row">
    <div class="col-md-3 col-xs-6">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-5 bordered">
            <h4 class="widget-thumb-heading">Total Payment To Group</h4>
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
    <?php $contribution_arrears = number_to_currency($total_group_member_contributions-$total_member_contribution_transfers_from_contribution_to_fine_category-$total_member_contribution_refunds-$total_member_contribution_transfers_to_loan+$total_member_contribution_transfers_from_loan_to_contribution); ?>
    <div class="col-md-3 col-xs-6">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-5 bordered">
            <h4 class="widget-thumb-heading">Contribution Arrears</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-green icon-layers"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"><?php echo $this->group_currency;?></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="<?php echo $contribution_arrears;?>"><?php echo $contribution_arrears;?></span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <?php $fine_arrears =  number_to_currency($total_group_member_total_fines+$total_member_contribution_transfers_from_contribution_to_fine_category); ?>
    <div class="col-md-3 col-xs-6">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-5 bordered">
            <h4 class="widget-thumb-heading">Fine Arrears</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon  bg-red fa fa-thumbs-down"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"><?php echo $this->group_currency;?></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="<?php echo $fine_arrears;?>"><?php echo $fine_arrears;?></span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
    <?php $loan_balances =number_to_currency($total_loan_balances); ?>
    <div class="col-md-3 col-xs-6">
        <!-- BEGIN WIDGET THUMB -->
        <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-5 bordered">
            <h4 class="widget-thumb-heading">Loan Balances</h4>
            <div class="widget-thumb-wrap">
                <i class="widget-thumb-icon bg-blue icon-bar-chart"></i>
                <div class="widget-thumb-body">
                    <span class="widget-thumb-subtitle"><?php echo $this->group_currency;?></span>
                    <span class="widget-thumb-body-stat" data-counter="counterup" data-value="<?php echo $loan_balances;?>"><?php echo $loan_balances;?></span>
                </div>
            </div>
        </div>
        <!-- END WIDGET THUMB -->
    </div>
</div>
<div class="clearfix"></div>
<hr/>
<div class="row">
    <div class="col-md-12">
        <h5>Pay To Group</h5>
        <div class="alert alert-danger alert_danger" style="display: none;">
            <button class="close" data-dismiss="alert"></button>
            <h4 class="block">Error! Something went wrong.</h4>
            <p class="data_error">
                
            </p>
        </div>

        <hr/>
        <?php echo form_open($this->uri->uri_string(),'class="form_submit" role="form" method="POST"'); ?>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered table-hover table-checkable table-multiple-items" id="datatable_orders make_payments">
                        <thead>
                            <tr role="row" class="heading">
                                <th width="5%" class="text-right"> # </th>
                                <th width="2%"> # </th>
                                <th width="35%"> Make Payment&nbsp;for <span class="required">*</span> </th>
                                <th width="35%"> Payment&nbsp;Particulars <span class="required">*</span></th>
                                <th width="20%" class="text-right"> Amount&nbsp;(<?php echo $this->group_currency;?>) <span class="required">*</span></th>
                            </tr>
                        </thead>
                        <tbody id='append-place-holder'> 
                            <tr role="row" class="filter">
                                <td class="text-left">
                                    <a data-original-title="Remove line" href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                                <td class='count'>1</td>
                                <td>
                                    <?php echo form_dropdown("payment_fors[0]",array(''=>'Select...')+$payment_for_options,$this->input->post('payment_fors'),'class="form-control form-filter input-sm select2 payment_for"');?>
                                </td>
                                <td class="particulars_place_holder">
                                    <?php echo form_dropdown("payments[0]",array(''=>'Select...'),'','class="form-control form-filter input-sm select2" disabled readonly');?>
                                </td>
                                <td>
                                    <?php echo form_input("amounts[0]",$this->input->post('amount'),'class="form-control form-filter input-sm currency amount text-right " placeholder="Enter amount"');?>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-right" colspan="4">Total Amount</td>
                                <td class="text-right total-amount"><?php echo number_to_currency();?></td>
                                <?php echo form_hidden('total_amount','');?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 margin-bottom-10 text-left">
                    <a href="javascript:;" class="btn margin-right-10 btn-default btn-xs" id="add-new-line">
                        <i class="fa fa-plus"></i>
                        <span class="hidden-380">
                            Add new payment line
                        </span>
                    </a>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="submit" value="1" class="btn btn-sm blue submit_form_button">
                    Pay now          
                </button>
                <button type="button" class="btn btn-sm blue processing_form_button disabled" name="processing" value="Processing">
                    <i class="fa fa-spinner fa-spin"></i> 
                    Processing            
                </button> 
                &nbsp;&nbsp;&nbsp;
                <button type="button" class="btn-sm btn btn-default">
                    Cancel            
                </button>
            </div>
        <?php echo form_close();?>
    </div>
</div>
<div style="display: none;" class="contribution-payments">
    <div class="row">
        <?php echo form_dropdown('contribution_ids[0]',array(''=>'Select contribution...')+$contribution_options,'','class="form-control form-filter input-sm append-select2 contribution"');?>
    </div>
</div>

<div style="display: none;" class="fine-payments">
    <div class="row">
        <?php echo form_dropdown('fine_categories[0]',array(''=>'Select Fine Category...')+$fine_category_options,'','class="form-control form-filter input-sm append-select2 fine_category"');?>
    </div>
</div>

<div style="display: none;" class="loan-payments">
    <div class="row">

        <?php 
            if($member_active_loans){
                echo form_dropdown('loan_ids[0]',array(''=>'Select Loan...')+$member_active_loans,'','class="loan form-control form-filter input-sm append-select2"');
            }else{
                echo form_dropdown('loan_ids[0]',array(''=>'No active loans'),'','class="loan form-control form-filter input-sm append-select2"');
            }
        ?>
    </div>
</div>

<div style="display: none;" class="default-payments">
    <div class="row">
       <?php echo form_dropdown("default",array(''=>'Select...'),'','class="form-control form-filter input-sm select2" disabled readonly');?>
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
                'class'=>'form-control miscellaneous_deposit_description',
                'placeholder'=>''
            ); 
            echo form_textarea($textarea);

        ?>
    </div>
</div>

<div id='append-new-line'>
    <table>
        <tbody>
            <tr>
                <td width='4%'>
                    <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                        <i class="fa fa-times"></i>
                    </a>
                </td>
                <td width='2%' class='count'>1</td>
                <td class="deposit_for_cell" width='25%'>
                    <?php echo form_dropdown("payment_fors[0]",array(''=>'Select...')+$payment_for_options,$this->input->post('payment_fors'),'class="form-control form-filter input-sm append-select2 payment_for"');?>
                </td>
                <td class="particulars_place_holder">
                    <?php echo form_dropdown("payment[0]",array(''=>'Select...'),'','class="form-control form-filter input-sm append-select2" disabled readonly');?>
                </td>
                <td>
                    <?php echo form_input("amounts[]",$this->input->post('amount'),'class="form-control form-filter input-sm currency amount text-right " placeholder="Enter amount"');?>
                </td>
            </tr>
        </tbody>
    </table>
</div>


<button id="add_payment_options" class="btn btn-square btn-sm red todo-bold inline hidden" data-toggle="modal" href="#todo-task-modal" data-content="#select_payment_option_form_holder" data-title="Confirm contribution and select payment option"><i class='fa fa-plus'></i> Confirm Payment</button>

<div id="select_payment_option_form_holder" class="hidden">
    <div id="contribution_form" class="form-body form_holder">
        <div class="alert alert-danger alert_danger" style="display: none;">
            <button class="close" data-dismiss="alert"></button>
            <h4 class="block">Error! Something went wrong.</h4>
            <p class="data_error">
                
            </p>
        </div>
        <div class="form-group">
           <h4>Confirm payment to <?php echo $this->group->name;?> account of <?php echo $this->group_currency;?> <strong><span class="total_pay_amount">0.00</span></strong>.</h4>
            <div class="todo-container margin-top-15">
                <div class="row">
                    <div class="col-md-5">
                        <ul class="todo-projects-container">
                            <li class="todo-padding-b-0">
                                <div class="todo-head">
                                    <h5>Select payment option</h5>
                                </div>
                            </li>
                            <li class="todo-projects-item equitel_option">
                                <h3>
                                    <a href="javascript:;">
                                        <img src="<?php echo base_url();?>/templates/admin_themes/groups/img/Equitel_Kenya_Logo.png" class="image-logos">                     
                                    </a>
                                </h3>
                            </li>
                            <div class="todo-projects-divider"></div>
                            <li class="todo-projects-item m-pesa_option">
                                <h3>
                                    <a href="javascript:;" class="">
                                        <img src="<?php echo base_url();?>/templates/admin_themes/groups/img/mpesa-logo.png" class="image-logos"> 
                                    </a>                    
                                </h3>
                            </li>
                            <div class="todo-projects-divider"></div>
                            <li class="todo-projects-item equity-bank_option">
                                <h3>
                                    <a href="javascript:;" class="">
                                        <img src="<?php echo base_url();?>/templates/admin_themes/groups/img/equity-bank.png" class="image-logos">                
                                    </a>
                                </h3>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-7 payment_options_holder">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="equitel_payment_option_holder">
    <div class="todo-tasks-container">
        <div class="todo-head">
            <h5 class="payment_header">
                Equitel Push Payment                  
            </h5>
        </div>
        <div id="contribution_refunds_listing" style="min-height: 100px; position: relative; zoom: 1;">
            <h6>Enter Equitel Number to pay from</h6>
            <div class="form-group">
                <label>Enter your phone number <span class='required'>*</span></label>
                <div class="input-group">
                    <span class="input-group-btn">
                        <?php 
                            echo form_hidden('calling_code',$default_calling_code);
                            echo form_hidden('deposit_method',5);
                        ?>
                        <button class="btn blue" type="button"><?php echo $default_calling_code;?></button>
                    </span>
                    <?php echo form_input('original_phone',$this->input->post('original_phone'),'class="form-control phone" placeholder="763000000" autocomplete="false"'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mpesa_payment_option_holder">
    <div class="todo-tasks-container">
        <div class="todo-head">
            <h5 class="payment_header">
                Lipa na M-PESA                  
            </h5>
        </div>
        <div id="contribution_refunds_listing" style="min-height: 100px; position: relative; zoom: 1;">
            <h6>Enter Safaricom Number to pay from</h6>
            <div class="form-group">
                <label>Enter your phone number <span class='required'>*</span></label>
                <div class="input-group">
                    <span class="input-group-btn">
                        <?php 
                            echo form_hidden('calling_code',$default_calling_code);
                            echo form_hidden('deposit_method',1);
                        ?>
                        <button class="btn blue" type="button"><?php echo $default_calling_code;?></button>
                    </span>
                    <?php echo form_input('original_phone',$this->input->post('original_phone'),'class="form-control phone" placeholder="722000000" autocomplete="false"'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="equity_bank_payment_option_holder">
    <div class="todo-tasks-container">
        <div class="todo-head">
            <h5 class="payment_header">
                Pay with Equity Bank (EazzyCheckout)                
            </h5>
        </div>
        <div id="contribution_refunds_listing" style="position: relative; zoom: 1; margin: 30px;">
            <div class="form-actions">
                <button type="submit" name="submit" value="1" class="btn blue pay_eazzycheckout">Proceed to checkout</button>
            </div>
        </div>
    </div>
</div>


<?php echo form_open(current_url(),"id='dummyform'")?>
        <input type="hidden" id="token" name="token" value="">
        <input type="hidden" id="amount" name="amount" value="">
        <input type="hidden" id="currency" name="currency" value="<?php echo $this->group_currency;?>">
        <input type="hidden" id="orderReference" name="orderReference" value="">
        <input type="hidden" id="popupLogo" name="popupLogo">
        <input type="hidden" id="merchantCode" name="merchantCode" value="0598105584">
        <input type="hidden" id="merchantNames" name="merchantNames" value="Digital Vision">
        <input type="hidden" id="outletCode" name="outletCode" value="0000000000">
        <input type="hidden" id="merchant" name="merchant">
        <input type="hidden" id="custName" name="custName" value="DevTest">
        <input type="hidden" id="expiry" name="expiry" value="2030-02-17T19:00:00">
        <input type="hidden" id="ez1_callbackurl" name="ez1_callbackurl" value="<?php echo base_url();?>group/deposits/payment_status">
        <input type="hidden" id="ez2_callbackurl" name="ez2_callbackurl"  value="<?php echo base_url();?>group/deposits/payment_status">
        <button type="submit" name="submit" value="1" class="btn blue hidden" id="checkout-btn">Proceed to checkout</button>
<?php echo form_close();?>


<script type="text/javascript">
    $(document).ready(function(){
        $('.m-select2').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        $(document).on('click','#add_payment_options',function(){
            $('.modal').on('show.bs.modal', function (e) {
                $('.alert_danger').slideUp();
                $('.modal-header').hide();
                $('.modal_submit_form_button').val('Proceed to Payment');
                FormInputMask.init();
                $('li.equitel_option').trigger('click');
            });
        });


        $(document).on('click','.modal button.pay_eazzycheckout',function(e){
            var content = $('.modal-content');
            App.blockUI({
                target: content,
                overlayColor: 'grey',
                animate: true
            });
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("ajax/deposits/generate_eazzycheckout_token"); ?>',
                data: '',
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == '200'){
                            $('#dummyform input[name="token"]').val(data.token);
                            $('#modal').modal('toggle');
                            if($('input[name="token"]').val()){
                                /*$(function() {
                                   $('#dummyform').submit();
                                });*/
                                $('#checkout-btn').trigger('click');
                                $('#checkout-btn').trigger('click');
                            }
                        }else if(data.status == '1'){
                            $('.data_error').html(data.message).show();
                            $('.alert_danger').slideDown();
                        }else{
                            $('.data_error').html('Error occured').show();
                            $('.alert_danger').slideDown();
                        }
                    }else{
                        alert(response);
                    }
                    //form.find('.submit_form_button').show();
                    //form.find('.processing_form_button').hide(); 
                    App.unblockUI(content);
                }
            });
            e.preventDefault();
        });

        
        $(document).on('click','li',function(){
            var li_click = $(this);
            var payment_options_holder = $('.payment_options_holder');
            var equitel_payment_option_holder = $('.equitel_payment_option_holder').html();
            var mpesa_payment_option_holder = $('.mpesa_payment_option_holder').html();
            var equity_bank_payment_option_holder = $('.equity_bank_payment_option_holder').html();
            if(li_click.hasClass('equitel_option')){
                html = equitel_payment_option_holder.replace_all('checker','');
                payment_options_holder.html(html);
                $('.m-pesa_option').removeClass('todo-active');
                $('.equity-bank_option').removeClass('todo-active');
                li_click.addClass('todo-active');
                $('.modal_processing_form_button').hide();
                $('.modal_submit_form_button').slideDown();
            }else if(li_click.hasClass('m-pesa_option')){
                html = mpesa_payment_option_holder.replace_all('checker','');
                payment_options_holder.html(html);
                $('.equitel_option').removeClass('todo-active');
                $('.equity-bank_option').removeClass('todo-active');
                li_click.addClass('todo-active');
                $('.modal_processing_form_button').hide();
                $('.modal_submit_form_button').slideDown();
            }else if(li_click.hasClass('equity-bank_option')){
                html = equity_bank_payment_option_holder.replace_all('checker','');
                payment_options_holder.html(html);
                $('.equitel_option').removeClass('todo-active');
                $('.m-pesa_option').removeClass('todo-active');
                li_click.addClass('todo-active');
                $('.modal_processing_form_button').hide();
                $('.modal_submit_form_button').slideUp();
            }else{
                html = equitel_payment_option_holder.replace_all('checker','');
                payment_options_holder.html(html);
                $('.m-pesa_option').removeClass('todo-active');
                $('.equity-bank_option').removeClass('todo-active');
                $('.equitel_option').addClass('todo-active');
                $('.modal_processing_form_button').hide();
                $('.modal_submit_form_button').slideDown();
            }
            FormInputMask.init();
        });

        $('select[name="payment_fors"]').select2({width:'200px'});
        $(document).on('change','.payment_for',function(){
            $('.table-multiple-items .append-select2').each(function(){
                if($(this).data('select2')){
                    $(this).select2('destroy');
                }
            });
            var payment_for = $(this).val();
            var parent = $(this).parent().parent().find('td.particulars_place_holder');
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
            $('input[type=checkbox]').uniform();
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

        $(document).on('click','.submit_form_button',function(e){
            var total_amount = $('.total-amount').text();
            var converted_total_amount = (parseFloat(total_amount.replace(/,/g, ""))*100);
            $('#dummyform input[name="amount"]').val(converted_total_amount);
            var form = $('.form_submit');
            $('.submit_form_button').hide();
            $('.processing_form_button').show();
            $('.alert_danger').slideUp();
            $('.total_pay_amount').html(total_amount);
            var content = $('.page-content-wrapper');
            var form_data = form.serialize();
            App.blockUI({
                target: content,
                overlayColor: 'white',
                boxed: true,
                message: 'Processing...'
            });
            $('td').removeClass('has-error');
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("ajax/deposits/make_payment_validation"); ?>',
                data: form_data,
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == '200'){
                            createPaymentRequest(form_data);
                            $('#add_payment_options').trigger('click');
                        }else if(data.status == '1'){
                            $(data.form_type+'[name="'+data.error_value+'"]').parent().addClass('has-error');
                            $('.data_error').html(data.message).show();
                            $('.alert_danger').slideDown();
                        }else{
                            $('.data_error').html(data.message).show();
                            $('.alert_danger').slideDown();
                        }
                    }else{
                        alert(response);
                    }
                    form.find('.submit_form_button').show();
                    form.find('.processing_form_button').hide(); 
                    App.unblockUI(content);
                }
            });
            e.preventDefault();
        });

        function createPaymentRequest(form_data){
            var merchantCode = $('input[name="merchantCode"]').val();
            var currency = $('input[name="currency"]').val();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("ajax/deposits/generate_payment_request"); ?>',
                data: {'merchantCode':merchantCode,'form_data':form_data,'currency':currency},
                success: function(response){
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == '200'){
                            $('#dummyform input[name="orderReference"]').val(data.requestId);
                        }else{
                            alert(data.message);
                        }
                    }else{
                        alert(response);
                    }
                }
            });
        }   

        $('.modal #submit_form').submit(function(){
            var form = $(this);
            App.blockUI({
                target: form,
                overlayColor: 'grey',
                animate: true
            });
            var calling_code = form.find('input[name="calling_code"]').val();
            var original_phone = form.find('input[name="original_phone"]').val();
            var deposit_method = form.find('input[name="deposit_method"]').val();
            var alertTxt = form.find('.alert_danger');
            alertTxt.html('').slideUp();
            if(calling_code){
                if(original_phone){
                    if(deposit_method){
                        var form_data = $('.form_submit').serialize();
                        $.ajax({
                            type: "POST",
                            url: '<?php echo base_url("ajax/deposits/process_push_payment_requests"); ?>',
                            data: {"calling_code":calling_code,"original_phone":original_phone,"deposit_method":deposit_method,'form_data':form_data},
                            success:function(result){
                                if(isJson(result)){
                                    var response = $.parseJSON(result);
                                    if(response.status=='200'){
                                        $('#modal').modal('toggle');
                                        toastr['success'](response.message);
                                        window.location.replace("<?php echo site_url('group/deposits/listing');?>");
                                    }else{
                                        alertTxt.html(response.message).slideDown();
                                    }
                                }else{
                                    alertTxt.html(result).slideDown();
                                }
                                $('.modal_processing_form_button').hide();
                                $('.modal_submit_form_button').show();
                                App.unblockUI(form);
                            },
                            error:function (xhr, ajaxOptions, thrownError){
                                if(xhr.status==404) {
                                    alertTxt.html('Method not found. Try again later').slideDown();
                                }
                                $('.modal_processing_form_button').hide();
                                $('.modal_submit_form_button').show();
                                App.unblockUI(form);
                            }
                        });
                    }else{
                        $('.modal_processing_form_button').hide();
                        $('.modal_submit_form_button').show();
                        alertTxt.html('Missing deposit method channel').slideDown();
                        App.unblockUI(form);
                    }
                }else{
                    $('.modal_processing_form_button').hide();
                    $('.modal_submit_form_button').show();
                    alertTxt.html('Enter phone number to pay from').slideDown();
                    App.unblockUI(form);
                }
            }else{
                $('.modal_processing_form_button').hide();
                $('.modal_submit_form_button').show();
                alertTxt.html('Missing phone calling code').slideDown();
                App.unblockUI(form);
            }
        });
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

</script>
