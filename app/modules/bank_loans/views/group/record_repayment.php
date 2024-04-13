<?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state" role="form" id="bank_loan_repayment_form"'); ?>
    <div class="table-responsive">
        <table class="table table-condensed  bank_loan-table multiple_payment_entries">
            <thead>
                <tr>
                    <th width="2%">
                        #
                    </th>
                    <th width="20%">
                        <?php echo translate('Repayment Date');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="20%">
                        <?php echo translate('Account Withdrawn');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="20%">
                        <?php echo translate('Repayment Method');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="15%">
                        <?php echo translate('Description');?>
                        <span class='required'>*</span>                        
                    </th>
                    <th width="15%">
                        <?php echo translate('Amount');?>
                         (<?php echo $this->group_currency; ?>) 
                        <span class='required'>*</span>
                    </th>
                    <th width="5%">
                        &nbsp;
                    </th>
                    <th width="3%">
                       &nbsp;
                    </th>
                </tr>
            </thead>
            <tbody id='append-place-holder'>
                <tr>
                    <th scope="row" class="count">
                        1
                    </th>
                    <td>
                        <?php echo form_input('repayment_date[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input fine_dates date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" ');?>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                             <?php echo form_dropdown('account_id[0]',$accounts,'',' class="form-control m-input m-select2 account_id" ');?>
                         </span>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('repayment_method[0]',array(''=>'Select repayment methody')+translate($withdrawal_method_options),'',' class="form-control  m-input m-select2 repayment_method" ');?>
                        </span>
                    </td>  
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php 
                                $textarea = array(
                                    'name'=>'repayment_descriptions[0]',
                                    'id'=>'',
                                    'value'=>'',
                                    'cols'=>25,
                                    'rows'=>5,
                                    'maxlength'=>'',
                                    'class'=>'form-control repayment_descriptions',
                                    'placeholder'=>''
                                ); 
                                echo form_textarea($textarea);
                            ?>
                        </span>
                    </td>                                  
                    <td>
                        <?php echo form_input('amounts[0]','',' class="form-control m-input--air input-sm amount currency text-right" ');?>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-lg-6 col-sm-6">
                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand" data-toggle="m-tooltip" title="" data-original-title="Is an interest payment">
                                    <?php echo form_checkbox('is_bank_loan_interest[0]',1,FALSE,' class = "is_bank_loan_interest" '); ?>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </td>
                    <td class="text-right">
                        <a href='javascript:;' class="remove-line">
                            <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                        </a>
                    </td>
                </tr>                                
            </tbody>
            <?php echo form_hidden('id',$id);?>
            <tfoot>
                <tr>
                    <td class="text-right" colspan=6>
                        <?php echo translate('Totals');?>
                    </td>
                    <td class="text-right total-amount"><?php echo number_to_currency();?></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a class="btn btn-default btn-sm add-new-line" id="add-new-line">
                <i class="la la-plus"></i><?php echo translate('Add New Repayment Line');?>
            </a>
        </div>
    </div>
    <div class="m-form__actions m-form__actions p-0 pt-5 m--margin-top-30">                            
        <div class="row">
            <div class="col-md-12">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button name="submit" value="1" class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm loan_repayment_form_button" id="loan_repayment_form_button" type="button">
                        <?php echo translate('Save changes');?>                            
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button"  >
                        Cancel                              
                    </button>
                </span>
            </div>
        </div>
    </div>
<?php echo form_close() ?>
<div id='append-new-line' class="d-none">
    <table>
        <tbody>
            <tr>
                <th scope="row" class="count">
                    1
                </th>
                <td>
                    <?php echo form_input('repayment_date[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input fine_dates date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" ');?>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                         <?php echo form_dropdown('account_id[0]',$accounts,'',' class="form-control m-input m-select2-append account_id" ');?>
                     </span>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('repayment_method[0]',array(''=>'Select repayment methody')+translate($withdrawal_method_options),'',' class="form-control  m-input m-select2-append repayment_method" ');?>
                    </span>
                </td>  
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php 
                            $textarea = array(
                                'name'=>'repayment_descriptions[0]',
                                'id'=>'',
                                'value'=>'',
                                'cols'=>25,
                                'rows'=>5,
                                'maxlength'=>'',
                                'class'=>'form-control repayment_descriptions',
                                'placeholder'=>''
                            );  
                            echo form_textarea($textarea);
                        ?>
                    </span>
                </td>                                  
                <td>
                    <?php echo form_input('amounts[0]','',' class="form-control m-input--air input-sm amount currency text-right" ');?>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-6 col-sm-6">
                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand" data-toggle="m-tooltip" title="" data-original-title="Is an interest payment">
                                <?php echo form_checkbox('is_bank_loan_interest[0]',1,FALSE,' class = "is_bank_loan_interest" '); ?>
                                <span></span>
                            </label>
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    <a href='javascript:;' class="remove-line">
                        <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div><script>
    $(document).ready(function(){
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });

        $(document).on('click','.remove-line',function(event){
            $(this).parent().parent().remove();
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                number++;
            });
            TotalAmount.init();
        });

        $('.add-new-line').on('click',function(){
            var html = $('#append-new-line tbody').html();
            html = html.replace_all('checker','');
            $('#append-place-holder').append(html);
            $('.tooltips').tooltip();
            $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                $(this).parent().find('.repayment_date').attr('name','repayment_date['+(number-1)+']');
                $(this).parent().find('.account_id').attr('name','account_id['+(number-1)+']');
                $(this).parent().find('.repayment_method').attr('name','repayment_method['+(number-1)+']');
                $(this).parent().find('.repayment_descriptions').attr('name','repayment_descriptions['+(number-1)+']');
                $(this).parent().find('.amounts').attr('name','amounts['+(number-1)+']');
                $(this).parent().find('.is_bank_loan_interest').attr('name','is_bank_loan_interest['+(number-1)+']');
                number++;
            });

            $('.bank_loan-table .m-select2-append').select2({
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }, 
            });
        });

        SnippetBankLoanRepayment.init();

    });

</script>