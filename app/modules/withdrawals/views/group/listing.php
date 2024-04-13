<div class="row">
    <div class="col-md-12">
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
                                                <?php echo translate('Withdrawal Date Range');?>
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
                                                <?php echo translate('Withdrawal Category');?>
                                            </label>
                                            <?php echo form_dropdown('type',array(''=>'All')+translate($withdrawal_type_options),$this->input->get('type'),'class="form-control m-select2" id="type" '); ?>
                                        </div>
                                        <div class="form-group m-form__group pt-0">
                                            <label>
                                                <?php echo translate('Accounts');?>
                                            </label>
                                            <?php echo form_dropdown('accounts[]',array()+translate($account_options),$this->input->get('accounts'),'class="form-control m-select2" multiple="multiple"'); ?>
                                        </div>

                                        <div class="form-group m-form__group member_id_search pt-0">
                                            <label>
                                                <?php echo translate('Member');?>
                                            </label>
                                            <?php echo form_dropdown('member_id[]',array()+$this->group_member_options,$this->input->get('member_id'),'class="form-control m-select2" multiple="multiple"'); ?>
                                        </div>

                                        <div class="form-group m-form__group expense_categories_search pt-0">
                                            <label>
                                                <?php echo translate('Expense Category');?>
                                            </label>
                                            <?php echo form_dropdown('expense_categories[]',array()+translate($expense_category_options),$this->input->get('expense_categories')?$this->input->get('expense_categories'):'','class="form-control m-select2" multiple="multiple"'); ?>
                                        </div>

                                        <div class="form-group m-form__group assets_search pt-0" style="display: none;">
                                            <label for="">
                                                <?php echo translate('Assets');?>
                                            </label>
                                            <?php echo form_dropdown('Assets[]',array()+$asset_options,$this->input->get('Assets')?$this->input->get('Assets'):'','class="form-control m-select2" multiple="multiple" '); ?>
                                        </div>

                                        <div class="form-group m-form__group contributions_search pt-0">
                                            <label>
                                                <?php echo translate('Select Contribution Accounts');?>
                                            </label>
                                            <?php echo form_dropdown('contributions[]',array()+translate($contribution_options),$this->input->get('contributions')?$this->input->get('contributions'):'','class="form-control m-select2" multiple="multiple"'); ?>
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
                    <a href="<?php echo site_url('group/withdrawals/listing').$query; ?>" class="btn btn-sm btn-primary m-btn m-btn--icon">
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
                <div id="withdrawals_listing">

                </div>
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
        $('#type').on('change',function(){
            $('.member_id_search,.contributions_search,.expense_categories_search,.stocks_search,.money_market_investments_search,.assets_search').slideUp().trigger('change');
            $('select[name="member_id[]"],select[name="contributions[]"],select[name="expense_categories[]"],select[name="stocks[]"],select[name="money_market_investments[]"],select[name="assets[]"]').val("").trigger('change');
            if($(this).val()=="1,2,3,4"){
                $('.expense_categories_search').slideDown();
            }else if($(this).val()=="5,6,7,8"){
                $('.assets_search').slideDown();
            }else if($(this).val()=="9,10,11,12"){
                $('.member_id_search').slideDown();
            }else if($(this).val()=="13,14,15,16"){
                $('.stocks_search').slideDown();
            }else if($(this).val()=="17,18,19,20"){
                $('.money_market_investments_search').slideDown();
            }else if($(this).val()=="21,22,23,24"){
                $('.contributions_search').slideDown();
            }else if($(this).val()=="25,26,27,28"){

            }else if($(this).val()=="29,30,31,32"){

            }else if($(this).val()=="33,34,35,36"){

            }
        });

        <?php if($this->input->get('type')=="1,2,3,4"){ ?>
            $('.expense_categories_search').slideDown();
        <?php }else if($this->input->get('type')=="5,6,7,8"){ ?>
            $('.assets_search').slideDown();
        <?php }else if($this->input->get('type')=="9,10,11,12"){ ?>
            $('.member_id_search').slideDown();
        <?php }else if($this->input->get('type')=="13,14,15,16"){ ?>
            $('.stocks_search').slideDown();
        <?php }else if($this->input->get('type')=="17,18,19,20"){ ?>
            $('.money_market_investments_search').slideDown();
        <?php }else if($this->input->get('type')=="21,22,23,24"){ ?>
            $('.contributions_search').slideDown();
        <?php }else if($this->input->get('type')=="25,26,27,28"){ ?>
            
        <?php }else if($this->input->get('type')=="29,30,31,32"){ ?>

        <?php }else if($this->input->get('type')=="33,34,35,36"){ ?>

        <?php } ?>

        $(document).on('click','.confirmation_link',function(){
            var element = $(this);
            bootbox.confirm({
                message: "Are you sure you want to void this withdrawal?",
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
        load_withdrawals_listing();
    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_withdrawals_listing(){
        mApp.block('#deposits_listing', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Processing...'
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/withdrawals/get_withdrawals_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#withdrawals_listing').html(response);
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#withdrawals_listing');
                }
            }
        );
    }

</script>