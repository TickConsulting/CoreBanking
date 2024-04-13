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
                                            <?php echo translate('Deposit Date Range');?>
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
                                            <?php echo translate('Deposit Types');?>
                                        </label>
                                        <?php echo form_dropdown('deposit_for',array(''=>'All')+translate($deposit_type_options),$this->input->get('deposit_for'),'class="form-control m-select2" id="deposit_for" '); ?>
                                    </div>
                                    <div class="form-group m-form__group pt-0">
                                        <label>
                                            <?php echo translate('Accounts');?>
                                        </label>
                                        <?php echo form_dropdown('accounts[]',array()+translate($account_options),$this->input->get('accounts'),'class="form-control m-select2" multiple="multiple"'); ?>
                                    </div>

                                    <div class="form-group m-form__group pt-0">
                                        <label>
                                            <?php echo translate('Member');?>
                                        </label>
                                        <?php echo form_dropdown('member_id[]',array()+$this->group_member_options,$this->input->get('member_id'),'class="form-control m-select2" multiple="multiple"'); ?>
                                    </div>

                                    <div class="form-group m-form__group pt-0">
                                        <label>
                                            <?php echo translate('Select Contribution Accounts');?>
                                        </label>
                                        <?php echo form_dropdown('contributions[]',array()+translate($contribution_options),$this->input->get('contributions')?$this->input->get('contributions'):'','class="form-control m-select2" multiple="multiple"'); ?>
                                    </div>

                                    <div class="form-group m-form__group fine_categories_search pt-0" style="display: none;">
                                        <label for="">
                                            <?php echo translate('Select Fine Categories');?>
                                        </label>
                                        <?php echo form_dropdown('fine_categories[]',array()+$fine_category_options,$this->input->get('fine_categories')?$this->input->get('fine_categories'):'','class="form-control m-select2" multiple="multiple" '); ?>
                                    </div>

                                    <div class="form-group  m-form__group income_categories_search pt-0" style="display: none;">
                                        <label for="">
                                            <?php echo translate('Select Income Categories');?>
                                        </label>
                                        <?php echo form_dropdown('income_categories[]',array()+translate($income_category_options),$this->input->get('income_categories')?$this->input->get('income_categories'):'','class="form-control m-select2" multiple="multiple" '); ?>
                                    </div>

                                    <div class="form-group m-form__group stocks_search pt-0" style="display: none;">
                                        <label for="">Select Stocks</label>
                                        <?php echo form_dropdown('stocks[]',array()+$stock_options,$this->input->get('stocks')?$this->input->get('stocks'):'','class="form-control m-select2" multiple="multiple"'); ?>
                                    </div>

                                    <div class="form-group m-form__group money_market_investments_search pt-0" style="display: none;">
                                        <label for="">
                                            <?php echo translate('Select Money Market Investments');?>
                                        </label>
                                        <?php  echo form_dropdown('money_market_investments[]',array()+$money_market_investment_options,$this->input->get('money_market_investments')?$this->input->get('money_market_investments'):'','class="form-control m-select2" multiple="multiple" '); ?>
                                    </div>

                                    <div class="form-group m-form__group assets_search pt-0" style="display: none;">
                                        <label for="">
                                            <?php echo translate('Select Assets');?>
                                        </label>
                                        <?php echo form_dropdown('assets[]',array()+$asset_options,$this->input->get('assets')?$this->input->get('assets'):'','class="form-control m-select2" multiple="multiple"'); ?>
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
            <div class="btn-group margin-bottom-20 search-button d-none">
                <a href="<?php echo site_url('group/deposits/listing').$query; ?>" class="btn btn-sm btn-primary m-btn m-btn--icon">
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
            <div id="deposits_listing" class="table-responsive">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deposit_receipt" role="dialog" aria-labelledby="exampleModalLongTitle" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"  style="display: none;">
                <h5 class="modal-title deposit_transaction_name" id="exampleModalLongTitle">
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="min-height: 150px;">
                <div class="deposit_details" style="display: none;">
                    <table class="table table-borderless">
                        <tbody>
                            <tr class="depositor-row">
                                <td nowrap>
                                    <strong>
                                        <?php echo translate('Received From');?>
                                    </strong>
                                </td>
                                <td class="depositor">
                                </td>
                            </tr>
                            <tr>
                                <td nowrap>
                                    <strong>
                                        <?php echo translate('Received On');?>
                                    </strong>
                                </td>
                                <td nowrap>
                                    <span class="deposit_date"></span> - <strong><?php echo translate('Recorded On');?> :</strong> <span class="recorded_on"></span>
                                </td>
                            </tr>
                            
                            <tr>
                                <td nowrap>
                                    <strong>
                                        <?php echo translate('Payment Details');?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="description"></span>
                                    <span class="account"></span>
                                    <span class='reconciled_badge'>
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td nowrap>
                                    <strong>
                                        <?php echo translate('Payment Channel');?>
                                    </strong>
                                </td>
                                <td class="deposit_method">
                                    
                                </td>
                            </tr>
                            <tr class="receiver-row">
                                <td nowrap>
                                    <strong>
                                        <?php echo translate('Received By');?>
                                    </strong>
                                </td>
                                <td class="receiver">
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
                                    <?php echo translate('Amount Paid');?>
                                </strong>
                            </td>
                            <td class="amount"></td>
                            <td class="m--align-right">
                                <div class="btn-group">
                                    <a href="<?php echo current_url().'/'.TRUE?>" class="btn btn-sm btn-primary m-btn  m-btn m-btn--icon generate_pdf_link">
                                        <span>
                                            <i class="fa fa-file"></i>
                                            <span>
                                                <?php echo translate('GENERATE PDF RECEIPT');?>
                                            </span>
                                        </span>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split more_actions_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="display: none;">
                                        <span class="sr-only">More actions..</span>
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(120px, 31px, 0px);">
                                        <a class="dropdown-item view_transaction_alert_link" href="#" style="display: none;">
                                            <?php echo translate('View transaction alert');?>
                                        </a>
                                        <a class="dropdown-item view_loan_statement_link" href="#" style="display: none;">
                                            <?php echo translate('Vew loan statement');?>
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

    $(document).ready(function(){
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
            width: "100%"
        });
        $('#deposit_for').on('change',function(){
            $('.member_id_search,.contributions_search,.fine_categories_search,.income_categories_search,.stocks_search,.money_market_investments_search,.assets_search').slideUp().trigger('change');
            $('select[name="member_id[]"],select[name="contributions[]"],select[name="fine_categories[]"],select[name="income_categories[]"],select[name="stocks[]"],select[name="money_market_investments[]"],select[name="assets[]"]').val("").trigger('change');
            if($(this).val()=="1,2,3,7"){
                $('.member_id_search,.contributions_search').slideDown();
            }else if($(this).val()=="4,5,6,8"){
                $('.member_id_search,.fine_categories_search').slideDown();
            }else if($(this).val()=="9,10,11,12"){
                $('.member_id_search').slideDown();
            }else if($(this).val()=="13,14,15,16"){
                $('.income_categories_search').slideDown();
            }else if($(this).val()=="17,18,19,20"){
                $('.member_id_search').slideDown();
            }else if($(this).val()=="21,22,23,24"){
                
            }else if($(this).val()=="25,26,27,28"){
                $('.stocks_search').slideDown();
            }else if($(this).val()=="29,30,31,32"){
                $('.money_market_investments_search').slideDown();
            }else if($(this).val()=="33,34,35,36"){
                $('.assets_search').slideDown();
            }else if($(this).val()=="37,38,39,40"){
                
            }else if($(this).val()=="41,42,43,44"){
                $('.member_id_search').slideDown();
            }else{
                $('.member_id_search,.contributions_search').slideUp();
            }
        });

        <?php if($this->input->get('deposit_for')=="1,2,3,7"){ ?>
            $('.member_id_search,.contributions_search').slideDown();
        <?php }else if($this->input->get('deposit_for')=="4,5,6,8"){ ?>
            $('.member_id_search,.fine_categories_search').slideDown();
        <?php }else if($this->input->get('deposit_for')=="9,10,11,12"){ ?>
            $('.member_id_search').slideDown();
        <?php }else if($this->input->get('deposit_for')=="13,14,15,16"){ ?>
            $('.income_categories_search').slideDown();
        <?php }else if($this->input->get('deposit_for')=="17,18,19,20"){ ?>
            $('.member_id_search').slideDown();
        <?php }else if($this->input->get('deposit_for')=="21,22,23,24"){ ?>
        <?php }else if($this->input->get('deposit_for')=="25,26,27,28"){ ?>
            $('.stocks_search').slideDown();
        <?php }else if($this->input->get('deposit_for')=="29,30,31,32"){ ?>
            $('.money_market_investments_search').slideDown();
        <?php }else if($this->input->get('deposit_for')=="33,34,35,36"){ ?>
            $('.assets_search').slideDown();
        <?php }else if($this->input->get('deposit_for')=="37,38,39,40"){ ?>

        <?php }else if($this->input->get('deposit_for')=="41,42,43,44"){ ?>
            $('.member_id_search').slideDown();
        <?php } ?>


        $(document).on('click','.view_deposit',function(){
            var id = $(this).attr('id');
            $.ajax({
                type: "GET",
                url: '<?php echo base_url("ajax/deposits/get_deposit/"); ?>'+id,
                dataType : "html",
                    success: function(response) {
                        if(isJson(response)){
                            var data = $.parseJSON(response);
                            $('#deposit_receipt .deposit_transaction_name').html(data.deposit_transaction_name);
                            if(data.depositor){
                                $('.depositor-row').show();
                                $('#deposit_receipt .depositor').html(data.depositor);
                            }else{
                                $('.depositor-row').hide();
                            }
                            if(data.received_by){
                                $('.receiver-row').show();
                                $('#deposit_receipt .receiver').html(data.received_by);
                            }else{
                                $('.receiver-row').hide();
                            }
                            $('#deposit_receipt .amount').html(data.amount);
                            $('#deposit_receipt .deposit_date').html(data.deposit_date);
                            if(data.account){
                                $('#deposit_receipt .account').html('Deposited to '+data.account).slideDown();
                            }
                            $('#deposit_receipt .deposit_method').html(data.deposit_method);
                            $('#deposit_receipt .description').html(data.description);
                            $('#deposit_receipt .recorded_on').html(data.recorded_on);
                            if(data.is_reconciled == 1){
                                $('#deposit_receipt .reconciled_badge').html('<span class="m-badge m-badge--info m-badge--wide">Reconciled</span>');

                                $('#deposit_receipt .view_transaction_alert_link').attr('href',window.location.origin+'/group/transaction_alerts/reconciled_deposits?transaction_alert='+data.transaction_alert_id).slideDown();
                                 $('#deposit_receipt .more_actions_toggle').slideDown();
                            }else{
                                $('#deposit_receipt .reconciled_badge').html('');
                            }
                            $('#deposit_receipt .generate_pdf_link').attr('id',data.id);
                            $('#deposit_receipt .generate_pdf_link').attr('href',window.location.origin+'/group/deposits/generate_pdf_deposit_receipt/'+data.id);
                            if(data.type==17||data.type==18||data.type==19||data.type==20||data.type==41||data.type==42||data.type==43||data.type==44){
                                $('#deposit_receipt .view_loan_statement_link').attr('href',window.location.origin+'/group/loans/loan_statement/'+data.loan_id).slideDown();
                                 $('#deposit_receipt .more_actions_toggle').slideDown();
                            }

                            mApp.unblock('#deposit_receipt .modal-body');
                            $('#deposit_receipt .modal-footer,#deposit_receipt .modal-header, #deposit_receipt .deposit_details, #deposit_receipt .pattern').slideDown();
                        }else{
                            $('.error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry:</strong>'+response+'</div>');
                            $('#deposit_receipt .close').trigger('click');
                            mApp.unblock('#deposit_receipt .modal-body');
                        }
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
                message: 'Fetching details...'
            });
        });
        $('#deposit_receipt').on('hidden.bs.modal',function(){
            $('#deposit_receipt .modal-footer,#deposit_receipt .modal-header, #deposit_receipt .deposit_details, #deposit_receipt .pattern, #deposit_receipt .account, #deposit_receipt .view_loan_statement_link, #deposit_receipt .view_transaction_alert_link, #deposit_receipt .more_actions_toggle').slideUp('fast');
        });
    });

    $(window).on('load', function() {
        load_deposits_listing();
    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_deposits_listing(){
        mApp.block('#deposits_listing', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Loading...'
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/wallets/get_member_wallet_deposits/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#deposits_listing').html(response);
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#deposits_listing');
                }
            }
        );
    }
</script>