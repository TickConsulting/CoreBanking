<style type="text/css">
    .dash_cont .fin_summ .fin_summ_entity .fin_summ_title {
        font-size: 16px;
    }
    .dash_cont .fin_summ .fin_summ_entity .fin_summ_amount {
        font-size: 19px;
    }
</style>
<div class="dash_cont wallet_summary">
    <h4><?php echo translate('Loans Summary');?></h4>
    <p><?php echo translate('Group loans overview summary');?></p>
    <div class="row fin_summ">
        <div class="col-md-12 mt-2 mb-2">
            <div class="row">
                <div class="col-md-4">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Loaned Amount');?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?> </span><?php echo number_to_currency($loaned_amount);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-information-outline"></i></span> <?php echo translate('Total loaned amount');?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Total Repayments');?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?></span> <?php echo number_to_currency($total_repayments);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-cash-multiple"></i></span> <?php echo translate('Total loan repayments');?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Outstanding Balances');?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?></span> <?php echo number_to_currency($loan_balances);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-cash-refund"></i></span> <?php echo translate('Total loan arrears');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="m-portlet m-portlet--rounded m-cont__portlet expenses_loading">
                <div class="m-portlet__head cust_portlet_head px-3 expen_hdr">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <p class="m-portlet__head-text_descr">
                                <span><?php echo translate('Top member loans');?></span><br>
                                <?php echo translate('Members with highest total loan amount');?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body chart_portlet_body" style="min-height:200px;">
                    <div class="row pb-4 pl-3">
                        <div class="col-md-6 d-none">
                            <div class="m-scrollable expen_data1" data-scrollable="true" data-max-height="200" style="max-height:190px;min-height:40px" id="expenses_summary">
                            </div>
                        </div>
                        <div class="col-md-12 expen_chart" id="member_loans_data">
                            <canvas id="expenses_chart" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="m-portlet m-portlet--rounded m-cont__portlet ">
                <div class="m-portlet__head cust_portlet_head px-3">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <p class="m-portlet__head-text_descr">
                                <span><?php echo translate('Loan Type Summary');?></span><br>
                                <?php echo translate('Loan types disbursement summary');?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body chart_portlet_body" style="min-height:160px;">
                    <div class="col-md-12" id="fin_summ_chart_summary_canvas">
                        <div id="fin_summ_chart_summary"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet m-portlet--rounded m-cont__portlet m-portlet--full-height">
                <div class="m-portlet__head cust_portlet_head px-3">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <p class="m-portlet__head-text_descr">
                                <span><?php echo translate('Loans Summary');?></span><br>
                                <?php echo translate('Monthly Disbursements against Repayments');?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body chart_portlet_body" id="income_summary_chart">
                    <div class="row">
                        <div class="col-md-12">
                            <canvas id="income_chart" height="100"></canvas>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="m-widget4 m-widget4--progress">
                                        <div class="m-widget4__item py-3">                  
                                            <div class="m-widget4__info">
                                                <span class="m-widget4__title">
                                                    <?php echo translate('Loan Types');?>
                                                </span>
                                                <br>
                                                <span class="m-widget4__sub">
                                                    <?php echo translate('Loan types created');?>
                                                </span>
                                            </div>
                                            <div class="m-widget4__progress">
                                                <div class="m-widget4__progress-wrapper text-right">
                                                    <span class="m-widget17__progress-number">
                                                        <?php echo $loan_types_count;?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="m-widget4__ext pr-3">
                                                <a href="<?php echo site_url('bank/loan_types/listing');?>" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
                                                    <?php echo translate('View');?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="m-widget4__item py-3">                  
                                            <div class="m-widget4__info">
                                                <span class="m-widget4__title">
                                                    <?php echo translate('Average Applications');?>
                                                </span>
                                                <br>
                                                <span class="m-widget4__sub">
                                                   <?php echo translate('Applications made monthly');?>
                                                </span>
                                            </div>
                                            <div class="m-widget4__progress">
                                                <div class="m-widget4__progress-wrapper text-right">
                                                    <span class="m-widget17__progress-number">
                                                        <?php echo $average_loan_applications_per_month;?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="m-widget4__ext pr-3">
                                                <a href="<?php echo site_url('group/loan_applications')?>" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
                                                   <?php echo translate('View');?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="m-widget4__item py-3">                  
                                            <div class="m-widget4__info">
                                                <span class="m-widget4__title">
                                                    <?php echo translate('Average Disbursements');?>
                                                </span>
                                                <br>
                                                <span class="m-widget4__sub">
                                                    <?php echo translate('Loans disbursed monthly');?>
                                                </span>
                                            </div>
                                            <div class="m-widget4__progress">
                                                <div class="m-widget4__progress-wrapper text-right">
                                                    <span class="m-widget17__progress-number">
                                                        <?php echo $average_loan_disbursements_per_month_count;?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="m-widget4__ext pr-3">
                                                <a href="<?php echo site_url('group/loans/listing')?>" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
                                                    <?php echo translate('View');?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="m-widget4 m-widget4--progress">
                                        <div class="m-widget4__item py-3 d-none">                  
                                            <div class="m-widget4__info">
                                                <span class="m-widget4__title">
                                                    <?php echo translate('Projected Profits');?>
                                                </span>
                                                <br>
                                                <span class="m-widget4__sub">
                                                    <?php echo translate('Profit projected from loans');?>
                                                </span>
                                            </div>
                                            <div class="m-widget4__progress">
                                                <div class="m-widget4__progress-wrapper text-right">
                                                    <span class="m-widget17__progress-number" id="projected_profits">
                                                        <?php echo $this->group_currency.' '.number_to_currency();?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="m-widget4__ext pr-3">
                                                <a href="<?php echo site_url('bank/loan_types/listing');?>" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
                                                    <?php echo translate('View');?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="m-widget4__item py-3">                 
                                            <div class="m-widget4__info">
                                                <span class="m-widget4__title">
                                                    <?php echo translate('Defaulted Loans');?>
                                                </span>
                                                <br>
                                                <span class="m-widget4__sub">
                                                    <?php echo translate('Loans marked as bad loans');?>
                                                </span>
                                            </div>
                                            <div class="m-widget4__progress">
                                                <div class="m-widget4__progress-wrapper text-right">
                                                    <span class="m-widget17__progress-number" id="defaulted_loan_amount">
                                                        <?php echo $this->group_currency.' '.number_to_currency($total_defaulted_loan_amount);?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="m-widget4__ext pr-3">
                                                <a href="#" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
                                                    <?php echo translate('View');?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="m-widget4__item py-3">                  
                                            <div class="m-widget4__info">
                                                <span class="m-widget4__title">
                                                    <?php echo translate('Average Amount');?>
                                                </span>
                                                <br>
                                                <span class="m-widget4__sub">
                                                   <?php echo translate('Amount applied monthly');?>
                                                </span>
                                            </div>
                                            <div class="m-widget4__progress">
                                                <div class="m-widget4__progress-wrapper text-right">
                                                    <span class="m-widget17__progress-number">
                                                        <?php echo $this->group_currency.' '.number_to_currency($average_loan_application_amounts);?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="m-widget4__ext pr-3">
                                                <a href="<?php echo site_url('group/loan_applications')?>" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
                                                   <?php echo translate('View');?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="m-widget4__item py-3">                  
                                            <div class="m-widget4__info">
                                                <span class="m-widget4__title">
                                                    <?php echo translate('Average Disbursement');?>
                                                </span>
                                                <br>
                                                <span class="m-widget4__sub">
                                                   <?php echo translate('Amount disbursed monthly');?>
                                                </span>
                                            </div>
                                            <div class="m-widget4__progress">
                                                <div class="m-widget4__progress-wrapper text-right">
                                                    <span class="m-widget17__progress-number">
                                                        <?php echo $this->group_currency.' '.number_to_currency($average_loan_disbursement_amounts_per_month_count);?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="m-widget4__ext pr-3">
                                                <a href="<?php echo site_url('group/loans/listing')?>" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
                                                    <?php echo translate('View');?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="no_data_space" style="display: none;">
    <?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ ?>
        <img src="<?php echo site_url('templates/admin_themes/groups/img/') ?>nodat_eazzykikundi.png" alt="no data">
    <?php } else if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
        <img src="<?php echo site_url('templates/admin_themes/groups/img/') ?>nodat_websacco.png" alt="no data">
    <?php } else{ ?>
        <img src="<?php echo site_url('templates/admin_themes/groups/img/') ?>nodat_default.png" alt="no data">
    <?php } ?>
    <p class="no_data_space_message"></p>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>
<script>
    // start: Income chart
    var no_data_space = $('.no_data_space').html();
    $(document).ready(function(){
        mApp.block('#income_summary_chart',{
            overlayColor: 'white',
            animate: true
        });
        mApp.block('#fin_summ_chart_summary_canvas',{
            overlayColor: 'white',
            animate: true
        });
        mApp.block('.expenses_loading',{
            overlayColor: 'white',
            animate: true
        });
        load_loan_types_summary();
        load_payments_vs_disbursements();
        //load_expenses_summary();
        load_member_loans_distribution();
    });

    function load_payments_vs_disbursements(){
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_monthly_loan_deposits_vs_withdrawals/"); ?>',
            dataType : "html",
                success: function(response) {
                    if(isJson(response)){
                        res = $.parseJSON(response);
                        if(res.hasOwnProperty('status')){
                            if(res.status == '202'){
                                Toastr.show("Session Expired",res.message,'error');
                                window.location.href = res.refer;
                            }
                        }else{
                            var income_summary_chart_ctx = document.getElementById("income_chart").getContext('2d');
                            var bg_fill = '',cs_gradient = income_summary_chart_ctx.createLinearGradient(0, 0, 0, 160), border_color='', point_border_color='', point_background_color='';
                            var bg_fill_ = '', cs_gradient_ = income_summary_chart_ctx.createLinearGradient(0, 0, 0, 100), border_color_='', point_border_color_='', point_background_color_='';
                            <?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ ?>
                                cs_gradient.addColorStop(0, '#d8511f');
                                border_color = '#d8511f';
                                point_border_color = 'rgba(255,255,255,1)';
                                point_background_color = '#d8511f';
                                bg_fill = 'rgba(216,81,31,0.2)';
                                //second set of colors
                                bg_fill_ = 'rgba(237,158,0,0.2)';
                                cs_gradient_.addColorStop(0, '#ed9e00');
                                border_color_ = '#ed9e00';
                                point_border_color_ = 'rgba(255,255,255,1)';
                                point_background_color_ = '#ed9e00';
                            <?php } else if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
                                cs_gradient.addColorStop(0, '#33911a');
                                border_color = '#33911a';
                                point_border_color = 'rgba(255,255,255,1)';
                                point_background_color = '#33911a';
                                bg_fill = 'rgba(51, 145, 26, 0.2)';
                                //second set of colors
                                bg_fill_ = 'rgba(129, 145, 26, 0.2)';
                                cs_gradient_.addColorStop(0, '#81911a');
                                border_color_ = '#81911a';
                                point_border_color_ = 'rgba(255,255,255,1)';
                                point_background_color_ = '#81911a';
                            <?php } else{ ?>
                                cs_gradient.addColorStop(0, '#00abf2');
                                border_color = '#00abf2';
                                point_border_color = 'rgba(255,255,255,1)';
                                point_background_color = '#00abf2';
                                //second set of colors
                                cs_gradient_.addColorStop(0, '#00ffa9');
                                border_color_ = '#00ffa9';
                                point_border_color_ = 'rgba(255,255,255,1)';
                                point_background_color_ = '#00ffa9';
                            <?php } ?>
                            cs_gradient.addColorStop(1, 'rgba(255,255,255,0)');

                            var contribution_chart_series = new Chart(income_summary_chart_ctx, {
                                type: 'line',
                                data: {
                                    labels: res.months,
                                    datasets: [
                                        {
                                            label: "<?php echo translate('Disbursements') ?>",
                                            data: res.disbursement,
                                            fill: true,
                                            borderColor: border_color,
                                            backgroundColor: bg_fill,
                                            pointBorderColor: point_border_color,
                                            pointBackgroundColor: point_background_color,
                                            pointRadius: 5,
                                            pointHoverRadius: 8,
                                            pointBorderWidth: 3,
                                            pointStyle: 'rectRounded'
                                        },
                                        {
                                            label: "<?php echo translate('Payments') ?>",
                                            data: res.repayment,
                                            fill: true,
                                            borderColor: border_color_,
                                            backgroundColor: bg_fill_,
                                            pointBorderColor: point_border_color_,
                                            pointBackgroundColor: point_background_color_,
                                            pointRadius: 5,
                                            pointHoverRadius: 8,
                                            pointBorderWidth: 3,
                                            pointStyle: 'rectRounded'
                                        }
                                    ]
                                },
                                options: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                    },
                                    title: {
                                        display: false,
                                        position: 'bottom',
                                        text: 'Disbursements'
                                    },
                                    tooltips: {
                                        enabled: true,
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                return "<?php echo $this->group_currency; ?> " + Number(tooltipItem.yLabel).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                            }
                                        }
                                    },
                                    scales: {
                                        yAxes: [{
                                            display: true,
                                            ticks: {
                                                beginAtZero:true,
                                                display: true
                                            }
                                        }],
                                        xAxes: [{
                                            display: true,
                                            gridLines: {
                                                display: true
                                            }  
                                        }]
                                    }
                                }
                            });
                            var this_month = res.monthly_collections;
                            var this_month_income = res.this_month_income;
                            var last_month = res.previous_month;
                            var last_month_income = res.last_month_income;
                            var last_last_month_income = res.last_last_month_income;
                            var percentage =  parseFloat(res.percentage);
                            var income_percentage = parseFloat(res.income_percentage);
                            var profit_margin_percentage = parseFloat(res.profit_margin_percentage);
                            $('#monthly_collections').html('<?php echo $this->group_currency;?>'+' '+this_month);
                            $('#net_income').html('<?php echo $this->group_currency;?>'+' '+this_month_income);
                            $('#profit_margin').html('<?php echo $this->group_currency;?>'+' '+res.income_difference);
                            if(percentage>0){
                                $('#text_success_danger_collection').addClass('text-success').html('<strong>'+Math.abs(res.percentage)+'% <i class="fa fa-arrow-up"></i></strong> Increase');
                            }else if(percentage < 0){
                                $('#text_success_danger_collection').addClass('text-danger').html('<strong>'+Math.abs(res.percentage)+'% <i class="fa fa-arrow-down"></i></strong> Decrese');
                            }else{
                                $('#text_success_danger_collection').addClass('text-info').html('<strong>'+Math.abs(res.percentage)+'% <i class="fa fa-arrow-up"></i></strong> Increase');
                            }

                            if(income_percentage>0){
                                $('#text_success_danger_income').addClass('text-success').html('<strong>'+Math.abs(res.income_percentage)+'% <i class="fa fa-arrow-up"></i></strong> Increase');
                            }else if(income_percentage < 0){
                                $('#text_success_danger_income').addClass('text-danger').html('<strong>'+Math.abs(res.income_percentage)+'% <i class="fa fa-arrow-down"></i></strong> Decrese');
                            }else{
                                $('#text_success_danger_income').addClass('text-info').html('<strong>'+Math.abs(res.income_percentage)+'% <i class="fa fa-arrow-up"></i></strong> Increase');
                            }

                            if(profit_margin_percentage>0){
                                $('#text_success_danger_profit_margin').addClass('text-success').html('<strong>'+Math.abs(res.profit_margin_percentage)+'% <i class="fa fa-arrow-up"></i></strong> Increase');
                            }else if(profit_margin_percentage < 0){
                                $('#text_success_danger_profit_margin').addClass('text-danger').html('<strong>'+Math.abs(res.profit_margin_percentage)+'% <i class="fa fa-arrow-down"></i></strong> Decrese');
                            }else{
                                $('#text_success_danger_profit_margin').addClass('text-info').html('<strong>'+Math.abs(res.profit_margin_percentage)+'% </strong> Increase');
                            }
                        }
                        mApp.unblock('#income_summary_chart');
                    }
                }
            }
        );
    }

    function load_loan_types_summary(){
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_group_loan_types_summary"); ?>',
            success: function(response) {
                if(isJson(response)){
                    res = $.parseJSON(response);
                    if(res.hasOwnProperty('status')){
                        if(res.status == '202'){
                            Toastr.show("Session Expired",res.message,'error');
                            window.location.href = res.refer;
                        }else{
                            Toastr.show("Error occurred",res.message,'error');
                            if(res.hasOwnProperty('refer')){
                                window.location.href = res.refer;
                            }
                        }
                    }else{
                        categories = res.categories;
                        amounts =  res.amounts;
                        dataset = [];
                        if(categories.length >= 1){
                            $.each(categories,function(index, value){
                                dataset.push({
                                    "label" : value,
                                    "value" : amounts[index]
                                });
                            });
                            $('#fin_summ_chart_summary').css("height","210px");
                            Morris.Donut({
                                element: "fin_summ_chart_summary",
                                data: dataset,
                                colors: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#a2d246","#dcf8c6"]
                            });
                        }else{
                            $('#fin_summ_chart_summary').css("height","230px");
                            $('#fin_summ_chart_summary_canvas').html('<div class="text-center mt-5">'+no_data_space+"</div>");
                            $('#fin_summ_chart_summary_canvas .no_data_space_message').html('<small><?php echo translate('There are no loans disbursed thus no loan type to match') ?></small>');
                        }
                        
                    }
                }
                mApp.unblock('#fin_summ_chart_summary_canvas');
            },
            error: function(){
                mApp.unblock('#fin_summ_chart_summary_canvas');
                Toastr.show("Error occurred","There is an error that has occurred. Kindly try again later",'error');
                window.location.href = res.refer;
            },
            always: function(){
                mApp.unblock('#fin_summ_chart_summary_canvas');
                Toastr.show("Error occurred","There is an error that has occurred. Kindly try again later",'error');
                window.location.href = res.refer;
            }
        });
        //end: Balances chart
    }

    function load_expenses_summary(){
        mApp.block('.expenses_loading',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_per_member_total_loan_amount"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#expenses_summary').html(response);
                    //alert($('.expen_available').val());
                    if($('.expen_available').val() == 'false'){
                        $('.expen_hdr').show();
                        $('.expen_disp').show();
                        $('.expen_data').hide();
                        $('.expen_chart').hide();
                    }else if($('.expen_available').val() == 'true'){
                        $('.expen_hdr').show();
                        $('.expen_disp').hide();
                        $('.expen_data').show();
                        $('.expen_chart').show();
                    }
                    mApp.unblock('.expenses_loading');
                }
            }
        );
    }

    function load_member_loans_distribution(){
        //start: expenses chart
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_members_loan_amount_summary"); ?>',
            dataType : "json",
                success: function(response) {
                    if(response.hasOwnProperty('status')){
                        if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }
                    }else{
                        categories = response.categories;
                        amounts = response.amount;
                        if(categories){
                        }else{
                            categories = "Expenses Total";
                        }
                        if(amounts){
                        }else{
                            amounts = 0;
                        }
                        if(categories.length >= 1){
                            var expenses_summary_chart_ctx = document.getElementById("expenses_chart").getContext('2d');
                            document.getElementById("expenses_chart").height = 100;
                            var expenses_summary_chart = new Chart(expenses_summary_chart_ctx, {
                                type: 'pie',
                                data: {
                                    labels: categories,
                                    datasets: [{
                                        label: "Members",
                                        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#a2d246","#dcf8c6"],
                                        data: amounts
                                    }]
                                },
                                options: {
                                    legend: {
                                        display: true,
                                        position: 'right',
                                    },
                                    title: {
                                        display: false,
                                        text: 'Member loan distrubution'
                                    }
                                }
                            });
                        }else{
                            $('#member_loans_data').html('<div class="text-center mt-5">'+no_data_space+"</div>");
                            $('#member_loans_data .no_data_space_message').html('<small><?php echo translate('There are no loans disbursed yet') ?></small>');
                        }
                    }
                    mApp.unblock('.expenses_loading');
                }

            }
        );
        // end: expenses chart
    }

</script>