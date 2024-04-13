<?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state" role="form" id="record_stock_purchase_form"'); ?>
    <div class="table-responsive">
        <table class="table table-condensed stock-purchase-table multiple_payment_entries">
            <thead>
                <tr>
                    <th width="2%">
                        #
                    </th>
                    <th width="13%">
                        <?php echo translate('Date');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="23%">
                        <?php echo translate('Stock Name');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="20%">
                        <?php echo translate('Number of Shares');?>
                        <span class='required'>*</span>
                    </th>
                    <th width='20%'><?php echo translate('Account');?><span class='required'>*</span></th>
                    <th width="23%">
                        <?php echo translate('Price per Share');?>
                        (<?php echo $this->group_currency; ?>) 
                        <span class='required'>*</span>
                    </th>
                    <th width="5%">
                        
                    </th>
                </tr>
            </thead>
            <tbody id='append-place-holder'>
                <tr>
                     <th scope="row" class="count">
                        1
                    </th>
                    <td>
                        <?php echo form_input('purchase_dates[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input purchase_date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" ');?>
                    </td>
                    <td>
                        <?php echo form_input('names[0]','',' class="form-control m-input--air input-sm name" ');?>
                    </td>
                    <td>
                        <?php echo form_input('number_of_shares[0]','',' class="form-control input-sm m-input numeric number_of_share" ');?>
                        
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                             <?php echo form_dropdown('accounts[0]',$account_options,'',' class="form-control m-input m-select2 account" ');?>
                        </span>
                    </td>                                   
                    <td>
                        <?php echo form_input('price_per_shares[0]','',' class="form-control m-input--air input-sm price_per_share currency text-right" ');?>
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
                    <td class="text-right" colspan="5">
                        <?php echo translate('Totals');?>
                    </td>
                    <td class="text-right total-amount"><?php echo number_to_currency();?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a class="btn btn-default btn-sm add-new-line" id="add-new-line">
                <i class="la la-plus"></i><?php echo translate('Add new stock purchase line');?>
            </a>
        </div>
    </div>
    <div class="m-form__actions m-form__actions p-0 pt-5 m--margin-top-30">                            
        <div class="row">
            <div class="col-md-12">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button name="submit" value="1" class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm submit_form_button" id="create_stock_purchase" type="button">
                        <?php echo translate('Save changes & Submit');?>                            
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
                    <?php echo form_input('purchase_dates[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input purchase_dates date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" ');?>
                </td>
                <td>
                    <?php echo form_input('names[0]','',' class="form-control m-input--air input-sm name " ');?>
                </td>
                <td>
                    <?php echo form_input('number_of_shares[0]','',' class="form-control input-sm m-input number_of_share numeric" ');?>
                    
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                         <?php echo form_dropdown('accounts[0]',$account_options,'',' class="form-control m-input m-select2-append account" ');?>
                    </span>
                </td>                                   
                <td>
                    <?php echo form_input('price_per_shares[0]','',' class="form-control m-input--air input-sm price_per_share currency text-right" ');?>
                </td>
                <td class="text-right">
                    <a href='javascript:;' class="remove-line">
                        <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function(){
        $('.m-select2').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        TotalAmount.init();
        $(document).on('change','.member',function(){
            if($(this).val()=='0'){
                $('#add_new_member').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('change','.fine_category',function(){
            if($(this).val()=='0'){
                $('#add_new_fine_category').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('changeDate','.input.purchase_date',function(){
            if($(this).val()==''){
                $(this).parent().addClass('has-danger');
            }else{
                $(this).parent().removeClass('has-danger');
            }
        });

        $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true}).on('changeDate', function(e) {
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });
      
        $(document).on('click','#add_new_member',function(){
            $(".member").select2({
                language: 
                    {
                    noResults: function() {
                        return '<a class="inline" data-toggle="modal" data-content="#create_new_member_pop_up" data-title="Add Member" data-id="create_member" id="add_new_member"  >Add Member</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).trigger("select2:close");
            TotalAmount.init();
        });


        $(document).on('click','#add_new_fine_category',function(){
            $(".fine_category").select2({
                language: 
                    {
                    noResults: function() {
                        return '<a class="inline" data-toggle="modal" data-content="#create_new_fine_category_pop_up" data-title="Add Fine Category" data-id="create_fine_category" id="add_new_fine_category"  >Add Fine Category</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).trigger("select2:close");
        })

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
                $(this).parent().find('.purchase_dates').attr('name','purchase_dates['+(number-1)+']');
                $(this).parent().find('.name').attr('name','names['+(number-1)+']');
                $(this).parent().find('.number_of_share').attr('name','number_of_shares['+(number-1)+']');
                $(this).parent().find('.account').attr('name','accounts['+(number-1)+']'); 
                $(this).parent().find('.price_per_share').attr('name','price_per_shares['+(number-1)+']');
                number++;
            });
            $('.stock-purchase-table .m-select2-append').select2({
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }, 
            });
            TotalAmount.init();
        });
        SnippetRecordStockPurchase.init();

    });

</script>