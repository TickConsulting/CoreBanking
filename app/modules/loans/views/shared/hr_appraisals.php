<?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
    <div class="form-body">
        <div class="loan_application_form_holder" id="loan_application_form_holder">
            
        </div>
        <div class="supervisory_form_holder" id="supervisory_form_holder" style="display: none;">
            <fieldset>
                <div class="form-group">
                    <label> Terms of Employment</label>
                    <div class="margin-top-10">
                        <label class="radio-inline radio-padding-0">
                            <div class="radio" id="">
                                <span class="">
                                <?php echo form_radio('contract_type_id',1,$this->input->post('contract_type_id')?$post->contract_type_id:''," id=''"); ?>
                                </span>
                            </div> Permanent
                        </label>
                       
                        <label class="radio-inline radio-padding-0">
                            <div class="radio" id="">
                                <span class="">
                                    <?php echo form_radio('contract_type_id',2,$this->input->post('contract_type_id')?$post->contract_type_id:''," id=''"); ?>
                                </span>
                            </div> Contract
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Contract End Date<span class="required">*</span></label>
                            <div class="input-group  date <?php
                                echo " date-picker ";
                             ?> " data-date="<?php echo $this->input->post('contract_end_date')?timestamp_to_datepicker(strtotime($this->input->post('contract_end_date'))):timestamp_to_datepicker(time());?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <?php echo form_input('contract_end_date',$this->input->post('contract_end_date')?timestamp_to_datepicker(strtotime($this->input->post('contract_end_date'))):timestamp_to_datepicker($post->contract_end_date)?:timestamp_to_datepicker(time()),'class="form-control" readonly');?> 
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>  
                        </div>
                    </div>
                </div>

                 <div class="form-group">
                    <label>Do the member have an existing loan ? <span class="required">*</span></label>
                    <div class="margin-top-10">
                        <label class="radio-inline radio-padding-0">
                            <div class="radio" id="">
                                <span class="">
                                <?php echo form_radio('is_loan_exisiting',1,$this->input->post('is_loan_exisiting')?$post->is_loan_exisiting:''," id=''"); ?>
                                </span>
                            </div> Yes
                        </label>
                       
                        <label class="radio-inline radio-padding-0">
                            <div class="radio" id="">
                                <span class="">
                                    <?php echo form_radio('is_loan_exisiting',2,$this->input->post('is_loan_exisiting')?$post->is_loan_exisiting:''," id=''"); ?>
                                </span>
                            </div> No
                        </label>
                    </div>
                </div>

                <div class="existing_loan_container" id="existing_loan_container" style="display: none;">

                    <div class="col-md-12">
                        <table class="table table-striped table-bordered table-hover table-checkable table-multiple-items" id="datatable_orders make_payments">
                            <thead>
                                <tr role="row" class="heading">
                                    <th  class="text-right">&nbsp;</th>
                                    <th > # </th>
                                    <th > Loan Amount <span class="required">*</span> </th>
                                    <th> Installments&nbsp;(<?php echo $this->group_currency;?>) <span class="required">*</span></th>
                                    <th> Term&nbsp; <span class="required">*</span>
                                    </th>
                                    <th> Balance&nbsp;(<?php echo $this->group_currency;?>) <span class="required">*</span></th>
                                </tr>
                            </thead>
                            <tbody id='append-place-holder'>
                            <?php
                           // print_r($_POST);
                                if(isset($_POST['loan_amount'])){ 
                                $loan_amounts = $this->input->post('loan_amount');
                                $loan_amount_installments = $this->input->post('loan_amount_installments');
                                $repayment_period = $this->input->post('repayment_period');
                                $loan_balance = $this->input->post('loan_balance');
                                $count = 1;
                                foreach ($loan_amounts as $key => $loan_amount):
                                    
                                        ?>                              
                                        <tr role="row" class="filter">
                                            <td class="text-left">
                                                <a data-original-title="Remove line" href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </td>
                                            <td class='count'><?php echo $count++;?></td>
                                            <td  width='25%'>
                                                <?php echo form_input('loan_amount[]',$loan_amount,'  class="form-control currency" placeholder="Loan  Amount" id="loan_amount"'); ?>
                                            </td>
                                            <td >
                                                <?php echo form_input('loan_amount_installments[]',$loan_amount_installments[$key],'  class="form-control currency" placeholder="Loan Amount Installments" id="loan_amount_installments"'); ?>
                                            </td>
                                            <td>
                                                <?php echo form_input('repayment_period[]',$repayment_period[$key],'  class="form-control currency" placeholder="Loan Term" id="repayment_period"'); ?>
                                            </td>
                                            <td>
                                                <?php echo form_input('loan_balance[]',$loan_balance[$key],'  class="form-control currency" placeholder="Loan Balance" id="loan_balance"'); ?>
                                            </td>
                                        </tr>
                                    <?php
                                endforeach;
                            }else{ ?>
                                <tr role="row" class="filter">
                                    <td class="text-left">
                                        <a data-original-title="Remove line" href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                    <td class='count'>1</td>
                                    <td  width='25%'>
                                        <?php echo form_input('loan_amount[]','','  class="form-control currency" placeholder="Loan  Amount" id="loan_amount"'); ?>
                                    </td>
                                    <td >
                                        <?php echo form_input('loan_amount_installments[]','','  class="form-control currency" placeholder="Loan Amount Installments" id="loan_amount_installments"'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_input('repayment_period[]','','  class="form-control currency" placeholder="Loan Term" id="repayment_period"'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_input('loan_balance[]',
                                        '','  class="form-control currency" placeholder="Loan Balance" id="loan_balance"'); ?>
                                    </td>
                                </tr>
                          <?php  } ?>
                            </tbody>
                            
                        </table>
                    </div>               

                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Net Pay <span class="required">*</span></label>
                                <div class="input-group input-group-md">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                    <?php echo form_input('net_pay',$this->input->post('net_pay')?$this->input->post('net_pay'):$post->net_pay,'  class="form-control currency" placeholder="Net Pay" id="net_pay"'); ?>
                                </div> 
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Percentage Net Pay <span class="required">*</span></label>
                                <div class="input-group input-group-md">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                    <?php echo form_input('percentage_net_pay',$this->input->post('percentage_net_pay')?$this->input->post('percentage_net_pay'):$post->percentage_net_pay,'  class="form-control currency" placeholder="Percentage Net Pay" id="percentage_net_pay"'); ?>
                                </div> 
                            </div> 
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 margin-bottom-10 text-left">
                            <a href="javascript:;" class="btn margin-right-10 btn-default btn-xs" id="add-new-line">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-380">
                                    Add new Loan
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

            </fieldset>
        </div>
        <div class="form-actions" id="form_action_holder" style="display: none;">
            <input type="submit" class="btn blue submit_form_button" name="approve" value=" <?php
                    $default_message='Approve  Request';
                    $this->languages_m->translate('Approve  Request',$default_message);
                ?>">                   
            </input>
            <input type="submit" class="btn red submit_form_button" name="decline" value="<?php
                    $default_message='Decline  Request';
                    $this->languages_m->translate('Decline  Request',$default_message);
                ?>">                 
            </input>
            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                <?php
                    $default_message='Processing';
                    $this->languages_m->translate('processing',$default_message);
                ?>
            </button> 
            <button type="button" class="btn default">
                <?php
                    $default_message='Cancel';
                    $this->languages_m->translate('cancel',$default_message);
                ?>
            </button>
        </div>
    </div>    
<?php echo form_close(); ?>

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
                    <?php echo form_input('loan_amount[]','','  class="form-control currency" placeholder="Loan  Amount" id="loan_amount"'); ?>
                </td>
                <td class="particulars_place_holder">
                    <?php echo form_input('loan_amount_installments[]','','  class="form-control currency" placeholder="Loan Amount Installments" id="loan_amount_installments"'); ?>
                </td>
                <td>
                    <?php echo form_input('repayment_period[]','','  class="form-control currency" placeholder="Loan Term" id="repayment_period"'); ?>
                </td>
                <td>
                    <?php echo form_input('loan_balance[]','','  class="form-control currency" placeholder="Loan Balance" id="loan_balance"'); ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    $(window).on('load',function(){
        $('#supervisory_form_holder').slideDown();
        $('#form_action_holder').slideDown();
        update_loan_counts();
        if($('input[name="loan_amount"]').val()){
            $('#loan_installments_form_holder').slideDown();
        }else{
           $('#loan_installments_form_holder').slideUp();
        }

        var is_loan_exisiting = '<?php echo $post->is_loan_exisiting ?>' 
        if(is_loan_exisiting == 1){
            $('.existing_loan_container').slideDown();
        }else if(is_loan_exisiting == 2){
           $('.existing_loan_container').slideUp();
        }else{
            $('.existing_loan_container').slideUp(); 
        }

        if($('input[name="loan_amount_installments"]').val()){
            $('#loan_term_form_holder').slideDown();
        }else{
           $('#loan_term_form_holder').slideUp();
        }

        if($('input[name="repayment_period"]').val()){
            $('#loan_balance_form_holder').slideDown();
        }else{
           $('#loan_balance_form_holder').slideUp();
        }

        if($('input[name="loan_balance"]').val()){
            $('#loan_net_pay_form_holder').slideDown();
        }else{
           $('#loan_net_pay_form_holder').slideUp();
        }

        if($('input[name="net_pay"]').val()){
            $('#percentage_net_pay_form_holder').slideDown();
        }else{
           $('#percentage_net_pay_form_holder').slideUp();
        }
        loan_application_details();
    });

    
    $(document).on('click','input[name="is_loan_exisiting"]',function(){
        var element = $(this).val();
        if(element == 1){
            $('.existing_loan_container').slideDown();
        }else if(element == 2){
            $('.existing_loan_container').slideUp();
        }else{
            $('.existing_loan_container').slideUp(); 
        }

    });

    function update_loan_counts(){
        var count = 0;
        $('#loan_amount').each(function(){
            $(this).attr('name','loan_amount['+(count)+']');
            count++
            //console.log($(this).attr('name','guarantor_id['+(count)+']'));
        });
        
    }

    //start semaless
    $(document).on('keyup keydown','input[name="loan_amount"]',function(){
        var element = $(this);            
        if(element.val()){
            $('#loan_installments_form_holder').slideDown();
        }else{
           $('#loan_installments_form_holder').slideUp();
        }
    });

    $(document).on('keyup keydown','input[name="loan_amount_installments"]',function(){
        var element = $(this);            
        if(element.val()){
            $('#loan_term_form_holder').slideDown();
        }else{
           $('#loan_term_form_holder').slideUp();
        }
    });

    $(document).on('keyup keydown','input[name="repayment_period"]',function(){
        var element = $(this);            
        if(element.val()){
            $('#loan_balance_form_holder').slideDown();
        }else{
           $('#loan_balance_form_holder').slideUp();
        }
    });

    $(document).on('keyup keydown','input[name="loan_balance"]',function(){
        var element = $(this);            
        if(element.val()){
            $('#loan_net_pay_form_holder').slideDown();
        }else{
           $('#loan_net_pay_form_holder').slideUp();
        }
    });

    $(document).on('keyup keydown','input[name="net_pay"]',function(){
        var element = $(this);            
        if(element.val()){
            $('#percentage_net_pay_form_holder').slideDown();
        }else{
           $('#percentage_net_pay_form_holder').slideUp();
        }
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


    function loan_application_details(){
        var loan_application_id = '<?php echo $this->uri->segment(4) ?>';
        App.blockUI({
            target: '#loan_application_form_holder',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "POST",
            data:{loan_application_id: loan_application_id},
            url: '<?php echo base_url("ajax/loans/ajax_loan_details"); ?>',
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response);                       
                        if(result.status == '200'){
                            $('#loan_application_form_holder').html(result.html); 
                            $('#supervisory_form_holder').slideDown();
                            $('#form_action_holder').slideDown();
                        }else if(result.status == '0'){
                           // $('#loan_type_details').html(result.message);  
                        }
                    }else{
                        alert(response);
                    }                          
                    App.unblockUI('#invoice_body');
                }
            }
        );
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