<?php if($member_only):

else:?>
<div class="row">
    <div class="col-md-12">
        <div class="btn-group margin-bottom-20 search-button">
            <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle="">
                <?php echo translate('Search');?>
                <i class="fa fa-angle-down"></i>
            </button>
            <div class="dropdown-menu dropdown-content input-large hold-on-click" role="menu">
                <?php echo form_open('group/deposits/listing','method="GET" class="filter"'); ?>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label">
                                <?php echo translate('Deposit Date Range');?>
                            </label>
                            <div class="">
                                <div class="input-group date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy" data-date-end-date="0d">';
                                    <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control" '); ?>
                                    <span class="input-group-addon"> to </span>
                                    <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control" '); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">
                                <?php echo translate('Deposit Types');?>
                            </label>
                            <div class="">
                                <?php echo form_dropdown('deposit_for',array(''=>'All')+translate($deposit_type_options),$this->input->get('deposit_for'),'class="form-control m-select2" id="deposit_for" '); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">
                                <?php echo translate('Accounts');?>
                            </label>
                            <div class="">
                                <?php echo form_dropdown('accounts[]',array()+$account_options,$this->input->get('accounts'),'class="form-control m-select2" multiple="multiple"'); ?>
                            </div>
                        </div>
                        <div class="form-group member_id_search">
                            <label class="control-label ">
                                <?php echo translate('Member');?>
                            </label>
                            <div class="">
                                <?php echo form_dropdown('member_id[]',array()+$this->group_member_options,$this->input->get('member_id'),'class="form-control m-select2" multiple="multiple"'); ?>
                            </div>
                        </div>
                        <div class="form-group contributions_search">
                            <label for="">
                                <?php echo translate('Select Contribution Accounts');?>
                            </label>
                            <div class="input-group col-md-12 col-sm-12 col-xs-12 ">
                                <?php echo form_dropdown('contributions[]',array()+$contribution_options,$this->input->get('contributions')?$this->input->get('contributions'):'','class="form-control m-select2" multiple="multiple" id = "type"  '); ?>
                            echo '
                            </div>
                        </div>
                        <div class="form-group fine_categories_search">
                            <label for="">
                                <?php echo translate('Select Fine Categories');?>
                            </label>
                            <div class="input-group col-md-12 col-sm-12 col-xs-12 ">
                                <?php echo form_dropdown('fine_categories[]',array()+translate($fine_category_options),$this->input->get('fine_categories')?$this->input->get('fine_categories'):'','class="form-control m-select2" multiple="multiple" id = "type"  '); ?>
                            </div>
                        </div>
                        <div class="form-group income_categories_search">
                            <label for="">
                                <?php echo translate('Select Income Categories');?>
                            </label>
                            <div class="input-group col-md-12 col-sm-12 col-xs-12 ">
                                <?php echo form_dropdown('income_categories[]',array()+translate($income_category_options),$this->input->get('income_categories')?$this->input->get('income_categories'):'','class="form-control m-select2" multiple="multiple" id = "type"  '); ?>
                            </div>
                        </div>
                        <div class="form-group stocks_search">
                            <label for="">
                                <?php echo translate('Select Stocks');?>
                            </label>
                            <div class="input-group col-md-12 col-sm-12 col-xs-12 ">
                                <?php echo form_dropdown('stocks[]',array()+translate($stock_options),$this->input->get('stocks')?$this->input->get('stocks'):'','class="form-control m-select2" multiple="multiple" id = "type"  '); ?>
                            </div>
                        </div>
                        <div class="form-group money_market_investments_search">
                            <label for="">
                                <?php echo translate('Select Money Market Investments');?>
                            </label>
                            <div class="input-group col-md-12 col-sm-12 col-xs-12 ">
                                <?php echo form_dropdown('money_market_investments[]',array()+translate($money_market_investment_options),$this->input->get('money_market_investments')?$this->input->get('money_market_investments'):'','class="form-control m-select2" multiple="multiple" id = "type"  '); ?>
                            </div>
                        </div>
                        <div class="form-group assets_search">
                            <label for="">
                                <?php echo translate('Select Assets');?>
                            </label>
                            <div class="input-group col-md-12 col-sm-12 col-xs-12 ">
                                <?php echo form_dropdown('assets[]',array()+translate($asset_options),$this->input->get('assets')?$this->input->get('assets'):'','class="form-control m-select2" multiple="multiple" id = "type"  '); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions m--align-right">
                        <button class="btn btn-xs btn-danger close-filter" type="button"><i class="fa fa-close"></i></button>
                        <button name="filter" value="filter" type="submit"  class="btn blue submit_form_button btn-sm"><i class="fa fa-filter"></i>
                            <?php echo translate('Filter');?>
                        </button>
                        <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <?php $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1'; ?>
        <div class="btn-group margin-bottom-20 search-button">
            <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="<?php echo site_url('group/deposits/listing').$query; ?>" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Deposits">
                <?php echo translate('Export To Excel');?>
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>
</div>
<?php endif;?>

<div id="deposits_listing">

</div>

<script>
    $(document).ready(function(){
       $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
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

    });

    $(window).on('load',function(){
        load_deposits_listing();

    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    <?php if($member_only){?>
        get_string +='&formember=1';
    <?php }?>

    function load_deposits_listing(){
        App.blockUI({
            target: '#deposits_listing',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/deposits/get_deposits_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#deposits_listing').html(response);
                    $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    App.unblockUI('#deposits_listing');
                }
            }
        );
    }

    $("pdf").click(function(){
        alert("Value: " + $("#action_to").val());
        // alert("The paragraph was clicked.");
    });
</script>