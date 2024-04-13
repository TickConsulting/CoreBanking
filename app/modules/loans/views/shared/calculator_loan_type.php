<style type="text/css">
    .loan_details_calc{
        line-height: 2.0;
    }
    .datepicker.datepicker-dropdown.dropdown-menu.datepicker-orient-left.datepicker-orient-top
    {
        left:calc(35.5% - 3em) !important;
    }
    .datepicker.datepicker-dropdown.dropdown-menu.datepicker-orient-left.datepicker-orient-bottom
    {
        left:calc(35.5% - 3em) !important;
    }
</style>
<?php echo form_open(current_url(),'class="form_submit" id="loan_calculator"');?>
    <div class="form-body loan">
        <div class="form-group">
            <label>Loan Type<span class="required">*</span></label>            
             <div class="input-group col-xs-12">
                <?php echo form_dropdown('loan_type_id',array(''=>'--Select Loan Type--')+$group_loan_types_options,$this->input->post('loan_type_id')?$this->input->post('loan_type_id'):$post->loan_type_id,'class="form-control select2 loan_type_id" id ="loan_type_id"  ') ?>
            </div>
        </div>
        <div class="loan_type_details" id="loan_type_details"> </div>
        <?php echo form_hidden('maximum_loan_amount_from_savings','');?>
        <?php echo form_hidden('maximum_guarantors_from_settings','');?> 
        <?php echo form_hidden('loan_guarantor_type',''); ?>
        <?php echo form_hidden('loan_repayment_type',''); ?>
        <div class="row " id='repayment_periods' style="display: none;">
            <div class="col-md-12">
                 <div class="form-group ">
                    <label> Repayment Period<span class="required">*</span></label>
                        <div class="input-group col-xs-12  ">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                             <?php echo form_input('repayment_period',$this->input->post('repayment_period')?$this->input->post('repayment_period'):'','  class="form-control numeric" placeholder=" Repayment Period" id="repayment_period"'); ?>
                        </div>                   
                        <span class="help-block"> Value in months eg.2 </span>
                 </div>
            </div> 
        </div>
        <div class="form-group" id="loan_amount_form_holder" style="display: none;">
            <label>Loan Amount<span class="required">*</span></label>
             <div class="input-group input-group-md">
                <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                </span>
                 <?php echo form_input('loan_application_amount',$this->input->post('loan_application_amount')?$this->input->post('loan_application_amount'):'','  class="form-control currency" placeholder="Loan Application Amount" id="loan_application_amount"'); ?>
            </div>
        </div>

        


    </div>

    <div class="form-actions form_action_form_holder" style="display: none;">
        <button type="submit"  class="btn blue submit_form_button">Calculate</button>
        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                <?php
                    $default_message='Processing';
                    $this->languages_m->translate('processing',$default_message);
                ?>
        </button> 
        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">
                <?php
                    $default_message='Cancel';
                    $this->languages_m->translate('cancel',$default_message);
                ?>      
                </button></a>
    </div>
<?php echo form_close();?>
<div class="amortized_schedule">
<div class="clearfix table_details"></div>
    <?php if($loan_values):?>
    <h4>Amortization Schedule</h4>
    <div class="row">
        <div class="col-md-6">
            <div id="loan_interest_principle" class="loan_interest_principle" style="height: 200px;"> </div>        
        </div>       
         <div class="col-md-6 loan_details_calc">       
            <h4>Loan Details</h4>
            <strong>Total Loan Payable:</strong> <?php echo $this->group_currency.' '.number_to_currency($total_amount_payable);?><br/>
            <strong>Total Interest :</strong> <?php echo $this->group_currency.' '.number_to_currency($total_interest);?><br/>
            <strong>Repayment Period:</strong> <?php echo $this->input->post('repayment_period');?> Months<br/>
            <strong>Monthly Payments :</strong> <?php echo $this->group_currency.' '.number_to_currency($monthly_payment);?><br/>
            <strong>Interest Rate :</strong> <?php echo $get_loan_type_options->interest_rate?>% <?php echo $loan_interest_rate_per[$get_loan_type_options->loan_interest_rate_per]?>
                <?php if($this->input->post('loan_interest_rate_per')!=3){
                        if($this->input->post('loan_interest_rate_per')==1){
                            echo 'at '.number_format($this->input->post('interest_rate')*30,1).' % Monthly rate';
                        }
                        else if($this->input->post('loan_interest_rate_per')==2){
                            echo 'at '.number_format($this->input->post('interest_rate')*4,1).' % Monthly rate';
                        }
                        else if($this->input->post('loan_interest_rate_per')==4){
                            echo 'at '.number_format($this->input->post('interest_rate')/12,1).' % Monthly rate';
                        }else if($this->input->post('loan_interest_rate_per')==5){
                            $interest_rate = $this->input->post('interest_rate');
                            $repayment_period = $this->input->post('repayment_period');
                            echo 'at '.number_format($interest_rate/$repayment_period,1).' % Monthly rate';
                        }
                    }?>

                <br/>
            <strong>Interest Type :</strong> <?php echo $interest_types[$get_loan_type_options->interest_type];?><br/>
        </div>

    </div>
    <div class="col-xs-12 table-responsive">
        <table class="table table-hover table-striped table-condensed table-statement">
            <thead>
                <tr>
                    <th class="invoice-title" width="2%">#</th>
                    <th class="invoice-title" >Date Payment</th>
                    <th class="invoice-title text-right">Monthly Payments</th>
                    <th class="invoice-title text-right">Principal Payable</th>
                    <th class="invoice-title text-right">Interest Payable</th>
                    <th class="invoice-title text-right">Total Interest</th>
                    <th class="invoice-title  text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_payable =0; $total_principle=0;$balance=$total_amount_payable;$i=0;$total_interest=0; foreach($loan_values as $key=>$value):  $value = (object)$value;
                        $total_payable+=$value->amount_payable;
                    ?>
                        <tr>
                            <td><?php echo ++$i?></td>
                            <td><?php echo timestamp_to_date($value->due_date);?></td>
                            <td class="text-right"><?php echo number_to_currency($value->amount_payable);?></td>
                            <td class="text-right"><?php echo number_to_currency($principle=$value->principle_amount_payable);?></td>
                            <td class="text-right"><?php echo number_to_currency($value->interest_amount_payable);?></td>
                            <td class="text-right"><?php echo number_to_currency($total_interest+=$value->interest_amount_payable);?></td>
                            <td class="text-right"><?php echo number_to_currency($balance-$total_payable);?></td>
                        </tr>
                <?php $total_principle+=$principle; endforeach;?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Totals</th>
                    <th class="text-right"><?php echo number_to_currency($total_payable);?></th>
                    <th class="text-right"><?php echo number_to_currency($total_principle);?></th>
                    <th class="text-right"><?php echo number_to_currency($total_interest);?></th>
                    <th class="text-right"></th>
                    <th class="text-right"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> 

                <?php
                    $default_message='Print';
                    $this->languages_m->translate('print',$default_message);
                ?>
            </a>
        </div>
    </div>
<?php endif;?>
</div>

<script type="text/javascript">

    $(window).on('load',function(){
      $("html, body").animate({ scrollTop: $('.table_details').offset().top }, 800);
    });

    $(document).ready(function(){
        $('.loan_type_id').on('change',function(){
            var loan_type_id = $('#loan_type_id').val();
            if(loan_type_id){
                $('.loan_guarantor_settings_values_templates').slideDown(); 
                $('.guarantors').slideUp();
                var loan_temp =  $('#guarantor_numbers_form_holder').html();
                $("div #guarantor_numbers_form_holder").html("");
                $("div #guarantor_numbers_form_holder").empty("");
                $('#loan_amount_form_holder').slideUp();
                $('#btnloan_amortization').slideUp(); 
                $('#loan_type_details').slideDown();
                $('#repayment_periods').slideUp();
                $('.agree_to_rules_holder').slideUp();
                $('.form_action_form_holder').slideUp(); 
                $('input[name="if_guarantors_enabled"]').val('');
                $('#append-place-holder').each(function(){
                    $('#append-place-holder').html(''); 
                });
                load_loan_type_option_details($(this).val());                
            }else{
                $('#loan_type_details').slideUp();
                $('#loan_amount_form_holder').slideUp();
                $('#btnloan_amortization').slideUp(); 
                $('#repayment_periods').slideUp();
                $('.agree_to_rules_holder').slideUp();
                $('.form_action_form_holder').slideUp(); 
                $('input[name="if_guarantors_enabled"]').val('');
                $('#append-place-holder').each(function(){
                    $('#append-place-holder').html(''); 
                });
                $('input[name="if_guarantors_enabled"]').val('');
                $("div #guarantor_numbers_form_holder").html("");
                $("div #guarantor_numbers_form_holder").empty("");
                $('.loan_guarantor_settings_values_templates').slideUp(); 
                $('.guarantors').slideUp();
            }
                        
        });
    });

    $(window).on('load',function(){
        var loan_type_id = $('select[name="loan_type_id"]').val();
        load_loan_type_option_details(loan_type_id);
    });

    function load_loan_type_option_details(loan_type_id){
        if(loan_type_id){
            $('#loan_type_details').html("");
            $('#loan_type_details').css("min-height","70px");
            App.blockUI({
                target: '#loan_type_details',
                overlayColor: 'grey',
                animate: true
            });
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('ajax/loan_types/get_loan_type_information'); ?>",
                data: {'loan_type_id':loan_type_id},
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response);
                        if(result.status == '200'){
                            $('input[name="maximum_loan_amount_from_savings"]').val(result.member_savings);
                            $('input[name="loan_guarantor_type"]').val(result.loan_guarantor_type);
                            $('input[name="maximum_guarantors_from_settings"]').val(result.maximum_guarantors);
                            $('input[name="loan_repayment_type"]').val(result.repayment_period_type);
                            $('#loan_type_details').html(result.html);  
                            add_repayment_type();
                        }else if(result.status == '0'){
                            $('#loan_type_details').html(result.message);  
                        }
                    }else{
                        alert(response);
                    }           
                    $('.guarantors').slideDown();
                    $('#loan_amount_form_holder').slideDown();
                    $('#btnloan_amortization').show(); 
                    var loan_amount = $('#loan_application_amount').val();
                    $("input[name=loan_application_amount]").trigger('keyup');
                    $('.agree_to_rules_holder').slideDown();
                    $('.form_action_form_holder').slideDown();               
                    App.unblockUI('#loan_type_details');
                }
            });  
        }
    }

    function add_repayment_type(){
        var repayment_type = $('input[name="loan_repayment_type"]').val();
        if(repayment_type == '2'){
            $('#repayment_periods').slideDown();
        }else{
           $('#repayment_periods').slideUp(); 
        }
    } 

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    function toCurrency(num){
        num.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        return num.toFixed(2);
       
    }  

</script>

<script type="text/javascript">
    var ChartsAmcharts = function() {
        var loan_calculator = function() {
            var chart = AmCharts.makeChart("loan_interest_principle", {
                "type": "pie",
                "theme": "light",

                "fontFamily": 'Open Sans',
                
                "color":    '#888',

                "dataProvider": [{
                    "Loan_title": "Principle",
                    "value": <?php echo $total_principle_amount;?>
                }, {
                    "Loan_title": "Interest",
                    "value": <?php echo $total_interest;?>
                }],
                "valueField": "value",
                "titleField": "Loan_title",
                "outlineAlpha": 0.4,
                "depth3D": 15,
                "balloonText": "[[title]]<br><span style='font-size:14px'><b><?php echo $this->group_currency;?> [[value]]</b> ([[percents]]%)</span>",
                "angle": 30,
                "exportConfig": {
                    menuItems: [{
                        icon: '/lib/3/images/export.png',
                        format: 'png'
                    }]
                }
            });

            jQuery('.cloan_interest_principle_chart_input').off().on('input change', function() {
                var property = jQuery(this).data('property');
                var target = chart;
                var value = Number(this.value);
                chart.startDuration = 0;

                if (property == 'innerRadius') {
                    value += "%";
                }

                target[property] = value;
                chart.validateNow();
            });

            $('#loan_interest_principle').closest('.portlet').find('.fullscreen').click(function() {
                chart.invalidateSize();
            });
        }
        return {
            //main function to initiate the module

            init: function() {
                loan_calculator();
            }

        };

    }();

</script>
<script type="text/javascript">
    jQuery(document).ready(function() {   
       ChartsAmcharts.init(); 
    });
</script>


