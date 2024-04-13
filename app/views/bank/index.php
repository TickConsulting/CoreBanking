<style type="text/css">
    .fin_summ_entity{
        background-color: #fff;
    }
    .dash_cont .fin_summ .fin_summ_entity .fin_summ_amount {
        font-size: 16px;
    }

    .dash_cont .fin_summ .fin_summ_entity .fin_summ_title {
        font-size: 13px;
    }
    .cust_portlet_head .m-portlet__head-caption .m-portlet__head-title p.m-portlet__head-text_descr span {
        font-size: 14px!important;
    }
</style>
<div class="m-content m-cont__no-padding-top dash_cont">
    <div class="m-subheader mb-5 pl-0">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h3 class="m-subheader__title cust_subheader_title">{group:template:title} </h3>
                <p class="cust_subheader_title_descr"><small><?php echo translate('Panel summary');?>.</small></p>
            </div>
            
        </div>
    </div>
    <?php
        $card_count = 3;
    ?>
    <div class="row fin_summ">
        <div class="col-md-<?=$card_count?>">
            <div class="m-portlet m-portlet--rounded m-cont__portlet fin_summ_entity">
                <div class="fin_summ_title"><?php echo translate('Active Loan Applications');?></div>
                <div class="fin_summ_amount"><span></span><?php echo $groups_count;?></div>
                <div class="fin_summ_descr text-success">
                    <span><i class="mdi mdi-information-outline"></i></span>Ongoing Loans
                </div>
            </div>
        </div>
        <div class="col-md-<?=$card_count?>">
            <div class="m-portlet m-portlet--rounded m-cont__portlet fin_summ_entity">
                <div class="fin_summ_title"><?php echo translate('Active Users');?></div>
                <div class="fin_summ_amount"><span></span> <?php echo $total_members;?></div>
                <div class="fin_summ_descr text-success">
                    <span><i class="fa fa-users"></i></span> &nbsp;<?php echo translate('Active Users')?>
                </div>
            </div>
        </div>
        <div class="col-md-<?=$card_count?>">
            <div class="m-portlet m-portlet--rounded m-cont__portlet fin_summ_entity">
                <div class="fin_summ_title"><?php echo translate('Total Deposits');?></div>
                <div class="fin_summ_amount"><span><?php echo $this->current_country->currency_code; ?> </span><?php echo number_to_currency($total_deposits);?></div>
                <div class="fin_summ_descr text-success">
                    <span><i class="mdi mdi-cash"></i></span><?php echo translate('Total Loans Repayments')?>
                </div>
            </div>
        </div>
        <div class="col-md-<?=$card_count?>">
            <div class="m-portlet m-portlet--rounded m-cont__portlet fin_summ_entity">
                <div class="fin_summ_title"><?php echo  translate('Total Disbursements');?></div>
                <div class="fin_summ_amount"><span><?php echo $this->current_country->currency_code; ?></span> <?php echo number_to_currency($total_withdrawals);?></div>
                <div class="fin_summ_descr text-success">
                    <span><i class="mdi mdi-cash-refund"></i></span><?php echo translate('Loan Disbursements')?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="m-content m-cont__no-padding-top">
	<div class="row">
	    <div class="col-md-6">
	        <div class="m-portlet m-portlet--rounded m-cont__portlet m-portlet--full-height" id="bank_balances_chart">
	            <div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo translate('Disbursement  Trends');?>
							</h3>
						</div>
					</div>

					<div class="m-portlet__head-tools">
                        <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="click" m-dropdown-persistent="1" aria-expanded="true">
                            <a href="#" class="m-dropdown__toggle btn btn-sm btn-outline-metal m-btn m-btn--outline-2x  dropdown-toggle filter_days"><?php echo translate('Last 12 months');?></a>
                            <div class="m-dropdown__wrapper filter_bank_balance_days" style="z-index: 101;">
                                <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                                <div class="m-dropdown__inner">
                                    <div class="m-dropdown__body">
                                        <div class="m-dropdown__content">
                                            <ul class="m-nav bank_balance_link">
                                                <li class="m-nav__section m-nav__section--first">
                                                    <span class="m-nav__section-text">
                                                        <?php echo translate('Filter');?>
                                                    </span>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="#" class="m-nav__link" id="1">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text ">
                                                            <?php echo translate('Last 7 days');?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="#" class="m-nav__link" id="2">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text">
                                                            <?php echo translate('Last 1 month');?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="#" class="m-nav__link" id="3">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text">
                                                            <?php echo translate('Last 3 months');?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="#" class="m-nav__link" id="4">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text">
                                                            <?php echo translate('Last 6 months');?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="#" class="m-nav__link" id="5">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text">
                                                            <?php echo translate('Last 1 year');?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__separator m-nav__separator--fit"></li>
                                                <li class="m-nav__item">
                                                    <a href="javascript:;">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text">
                                                           <?php echo translate('View More');?>
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
	            <div class="m-portlet__body chart_portlet_body">
	                <div class="row">
	                    <div class="col-md-12" id="onboarded_groups_data">
	                        <canvas id="onboarded_groups_chart" height="180"></canvas>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="col-md-6">
	        <div class="m-portlet m-portlet--rounded m-cont__portlet m-portlet--tabs m-portlet--full-height">
	           <div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo translate('Active Loan Applicants');?>
							</h3>
						</div>
					</div>
				</div>
	            <div class="m-portlet__body">
	            	<div class="row">
                        <div class="col-md-12" id="latest_signups">

                        </div>
                    </div>

	                <!-- <div class="tab-content">
	                    <div class="tab-pane active show member_contributions_summary" id="m_portlet_tab_contributions">
	                        <div class="m-scrollable member_contributions_summary_position" data-scrollable="true" data-max-height="200" style="max-height:200px;">
	                           <div id="search-placeholder">
									<div class="alert m-alert--outline alert-metal">
								        <h4 class="block">Groups List.</h4>
								        <p>
								            Group List will show here.
								        </p>
								    </div>
								</div>
	                        </div>
	                    </div>
	                </div> -->

	            </div>
	        </div>
	    </div>
	</div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>
<script>
    // start: Income chart
    $(document).ready(function(){
        /*mApp.block('.member_contributions_summary',{
            overlayColor: 'white',
            animate: true
        });*/
        mApp.block('#onboarded_groups_data',{
            overlayColor: 'white',
            animate: true
        });

        
        $(document).on('click','.bank_balance_link a',function(){
            var last_days = $(this).attr('id');
            if(last_days){
                //$('.m-dropdown__wrapper.filter_bank_balance_days').toggle();
                $('body').trigger('click');
                $('.filter_days').html($(this).text());
                mApp.block('#onboarded_groups_data',{
                    overlayColor: 'white',
                    animate: true
                });
                load_dashboard_bank_chart(last_days);
            }else{
                console.log('no id');
            }
        });
    });
    
    $(window).on('load',function() {
       // load_member_contributions_summary();
       // load_member_fines_summary();
       // load_expenses_summary();
       // load_expenses_chart();
        load_dashboard_bank_chart(1);
        load_latest_signups();
    });

    function load_dashboard_bank_chart(last_days = ''){
        $('#onboarded_groups_chart').remove(); 
        $('#onboarded_groups_data').append('<canvas id="onboarded_groups_chart" height="200"></canvas>');
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("bank/groups/onboarded_group_trends/"); ?>'+last_days,
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
                            var financial_summary_chart_ctx = document.getElementById("onboarded_groups_chart").getContext('2d');
                            var bg_fill = '', cs_gradient = financial_summary_chart_ctx.createLinearGradient(0, 0, 0, 200), border_color='', point_border_color='', point_background_color='';
                            var bg_fill_ = '', cs_gradient_ = financial_summary_chart_ctx.createLinearGradient(0, 0, 0, 100), border_color_='', point_border_color_='', point_background_color_='';
                            // start: Balances chart
                            <?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ ?>
                                //first set of colors
                                bg_fill = 'rgba(216,81,31,0.2)';
                                cs_gradient.addColorStop(0, '#d8511f');
                                border_color = '#d8511f';
                                point_border_color = 'rgba(255,255,255,1)';
                                point_background_color = '#d8511f';
                                //second set of colors
                                bg_fill_ = 'rgba(237,158,0,0.2)';
                                cs_gradient_.addColorStop(0, '#ed9e00');
                                border_color_ = '#ed9e00';
                                point_border_color_ = 'rgba(255,255,255,1)';
                                point_background_color_ = '#ed9e00';
                            <?php } else if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
                                //first set of colors
                                bg_fill = 'rgba(51, 145, 26, 0.2)';
                                cs_gradient.addColorStop(0, '#33911a');
                                border_color = '#33911a';
                                point_border_color = 'rgba(255,255,255,1)';
                                point_background_color = '#33911a';
                                //second set of colors
                                bg_fill_ = 'rgba(129, 145, 26, 0.2)';
                                cs_gradient_.addColorStop(0, '#81911a');
                                border_color_ = '#81911a';
                                point_border_color_ = 'rgba(255,255,255,1)';
                                point_background_color_ = '#81911a';
                            <?php } else{ ?>
                                //first set of colors
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
                            //cs_gradient.addColorStop(1, 'rgba(255,255,255,0)');
                            var contribution_chart_series = new Chart(financial_summary_chart_ctx, {
                                type: 'line',
                                data: {
                                    labels: res.months,
                                    datasets: [
                                        {
                                            label: '<?php echo translate("Loan Repayments Vs Disbursement");?>',
                                            data: res.group_sign_ups,
                                            fill: true,
                                            borderColor: border_color,
                                            backgroundColor: bg_fill,
                                            pointBorderColor: point_border_color,
                                            pointBackgroundColor: point_background_color,
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
                                        text: 'Loan Repayments Vs Disbursements'
                                    },
                                    tooltips: {
                                        enabled: true,
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                return Number(tooltipItem.yLabel).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                            }
                                        }
                                    },
                                    scales: {
                                        yAxes: [{
                                            display: true,
                                            ticks: {
                                                beginAtZero:true,
                                                display: true,
                                                callback: function(value, index, values) {
                                                    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                                }
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
                        }
                        mApp.unblock('#onboarded_groups_data');
                    }
                }
            }
        );        
        //end: Balances chart
    }

    function load_latest_signups(){
        mApp.block('#latest_signups',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("bank/groups/get_latest_signups"); ?>',
            dataType : "html",
                success: function(response) {
                	res = $.parseJSON(response);
                    $('#latest_signups').html(res.html);
                    //alert($('.expen_available').val());
                    /*if($('.expen_available').val() == 'false'){
                        $('.expen_hdr').show();
                        $('.expen_disp').show();
                        $('.expen_data').hide();
                        $('.expen_chart').hide();
                    }else if($('.expen_available').val() == 'true'){
                        $('.expen_hdr').show();
                        $('.expen_disp').hide();
                        $('.expen_data').show();
                        $('.expen_chart').show();
                    }*/
                    mApp.unblock('#latest_signups');
                }
            }
        );
    }

    function load_expenses_chart(){
        //start: expenses chart
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_expenses_categories_summary"); ?>',
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
                        var expenses_summary_chart_ctx = document.getElementById("expenses_chart").getContext('2d');
                        var expenses_summary_chart = new Chart(expenses_summary_chart_ctx, {
                            type: 'pie',
                            data: {
                                labels: categories,
                                datasets: [{
                                    label: "Expenses categories",
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
                                    text: 'Group Expenses'
                                }
                            }
                        });
                    }
                }
            }
        );
        // end: expenses chart
    }

    function load_income_chart(){
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_deposits_less_withdrawals_summary_graph/"); ?>',
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
                            var cs_gradient = income_summary_chart_ctx.createLinearGradient(0, 0, 0, 160), border_color='', point_border_color='', point_background_color='';
                            <?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ ?>
                                cs_gradient.addColorStop(0, '#d8511f');
                                border_color = '#d8511f';
                                point_border_color = 'rgba(255,255,255,1)';
                                point_background_color = '#d8511f';
                            <?php } else if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
                                cs_gradient.addColorStop(0, '#33911a');
                                border_color = '#33911a';
                                point_border_color = 'rgba(255,255,255,1)';
                                point_background_color = '#33911a';
                            <?php } else{ ?>
                                cs_gradient.addColorStop(0, '#00abf2');
                                border_color = '#00abf2';
                                point_border_color = 'rgba(255,255,255,1)';
                                point_background_color = '#00abf2';
                            <?php } ?>
                            cs_gradient.addColorStop(1, 'rgba(255,255,255,0)');

                            var contribution_chart_series = new Chart(income_summary_chart_ctx, {
                                type: 'line',
                                data: {
                                    labels: res.months,
                                    datasets: [
                                        {
                                            label: "Bank Balances",
                                            data: res.income,
                                            fill: true,
                                            borderColor: border_color,
                                            backgroundColor: cs_gradient,
                                            pointBorderColor: point_border_color,
                                            pointBackgroundColor: point_background_color,
                                            pointRadius: 5,
                                            pointHoverRadius: 8,
                                            pointBorderWidth: 3,
                                            pointStyle: 'rectRounded'
                                        }
                                    ]
                                },
                                options: {
                                    legend: {
                                        display: false,
                                        position: 'bottom',
                                    },
                                    title: {
                                        display: false,
                                        position: 'bottom',
                                        text: 'Group Income'
                                    },
                                    tooltips: {
                                        enabled: true,
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                return "Group Income: <?php echo $this->default_country->currency_code; ?> " + Number(tooltipItem.yLabel).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + 'K';
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
    // end: Income chart

    function load_loans_summary_chart(){
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/group_loans_summary_chart_data/"); ?>',
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
                            $('#amount_loaned_this_month').html('<?php echo $this->group_currency;?>'+' '+res.amount_loaned);
                            $('#defaulted_loan_amount').html('<?php echo $this->group_currency;?>'+' '+res.defaulted_loan);
                            $('#projected_profits').html('<?php echo $this->group_currency;?>'+' '+res.projected_profit);
                            $('#loan_unpaid_debt').html('<?php echo $this->group_currency;?>'+' '+res.total_arrears);
                            $('#loan_repayments').html('<?php echo $this->group_currency;?>'+' '+res.total_amount_paid);
                            percentage = parseFloat(res.percentage_increase);
                            percentage_payments = parseFloat(res.percentage_repayments);
                            if(percentage>=0){
                                $('#text_danger_amount_loaned_percentage').addClass('text-success').html('<strong>'+Math.abs(res.percentage_increase)+'% <i class="fa fa-arrow-up"></i></strong> increase');
                            }else if(percentage<0){
                                $('#text_danger_amount_loaned_percentage').addClass('text-danger').html('<strong>'+Math.abs(res.percentage_increase)+'% <i class="fa fa-arrow-down"></i></strong> decrese');
                            }
                            if(percentage_payments>=0){
                                $('#text_danger_loan_repayments').addClass('text-success').html('<strong>'+Math.abs(res.percentage_repayments)+'% <i class="fa fa-arrow-up"></i></strong> of loaned amount');
                            }else if(percentage_payments<0){
                                $('#text_danger_loan_repayments').addClass('text-danger').html('<strong>'+Math.abs(res.percentage_repayments)+'% <i class="fa fa-arrow-down"></i></strong> of loaned amount');
                            }
                        }
                        mApp.unblock('#loans_summary_chart');
                    }
                }
            }
        );
    }

</script>