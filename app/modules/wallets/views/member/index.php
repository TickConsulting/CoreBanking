<style type="text/css">
    .m-portlet.m-portlet--metal.m-portlet--head-solid-bg .m-portlet__head {
        background-color: inherit !important;
        border-color: inherit !important;
    }
</style>

<div class="row">
    <div class="col-md-6">
        <div class="m-cont__portlet m-portlet--full-height" id="bank_balances_chart">
            <div class="m-portlet__head cust_portlet_head px-3">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title mr-4">
                        <p class="m-portlet__head-text_descr">
                            <?php echo translate('Bank Balance');?><br>
                            <span><?php echo $this->group_currency.' '.number_to_currency($total_cash_at_bank);?></span>
                        </p>
                    </div>
                    <div class="m-portlet__head-title">
                        <p class="m-portlet__head-text_descr">
                            <?php echo translate('Cash at Hand');?><br>
                            <span><?php echo $this->group_currency.' '.number_to_currency($total_cash_at_hand);?></span>
                        </p>
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
                                                <a href="#" class="m-nav__link" id="last_7">
                                                    <i class=""></i>
                                                    <span class="m-nav__link-text ">
                                                        <?php echo translate('Last 7 days');?>
                                                    </span>
                                                </a>
                                            </li>
                                            <li class="m-nav__item">
                                                <a href="#" class="m-nav__link" id="last_1">
                                                    <i class=""></i>
                                                    <span class="m-nav__link-text">
                                                        <?php echo translate('Last 1 month');?>
                                                    </span>
                                                </a>
                                            </li>
                                            <li class="m-nav__item">
                                                <a href="#" class="m-nav__link" id="last_3">
                                                    <i class=""></i>
                                                    <span class="m-nav__link-text">
                                                        <?php echo translate('Last 3 months');?>
                                                    </span>
                                                </a>
                                            </li>
                                            <li class="m-nav__item">
                                                <a href="#" class="m-nav__link" id="last_6">
                                                    <i class=""></i>
                                                    <span class="m-nav__link-text">
                                                        <?php echo translate('Last 6 months');?>
                                                    </span>
                                                </a>
                                            </li>
                                            <li class="m-nav__item">
                                                <a href="#" class="m-nav__link" id="last_12">
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
                    <div class="col-md-12" id="bank_balances_data">
                        <canvas id="balances_chart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="m-cont__portlet expenses_loading">
            <div class="m-portlet__head cust_portlet_head px-3 expen_hdr" style="display:none;">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <p class="m-portlet__head-text_descr">
                            <span><?php echo translate('Group Expenses');?></span><br>
                            <?php echo translate('Group expenses summary by category');?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="m-portlet__body chart_portlet_body" style="min-height:180px;">
                <div class="row pb-4 pl-3">
                    <div class="col-md-12 text-center mt-5 expen_disp" style="display:none;opacity:0.7;">
                        <img src="<?php echo site_url('templates/admin_themes/groups/img/') ?>nodat_eazzykikundi.png" alt="no data">
                        <p><small>There are no expenses here, yet.</small></p>
                    </div>
                    <div class="col-md-12">
                        <!-- expenses table -->
                        <div class="m-scrollable expen_data" data-scrollable="true" data-max-height="160" style="max-height:170px;min-height:40px" id="expenses_summary">
                            <!-- <table class="table table-sm m-table m-table--head-bg-metal table-bordered">
                                <thead class="thead-inverse">
                                    <tr>
                                        <th width="8px">
                                            #
                                        </th>
                                        <th>
                                           <?php echo translate('Expense Category');?>
                                        </th>
                                        <th class="text-right">
                                            <?php echo translate('Paid');?> (<?php echo $this->group_currency;?>)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table> -->
                        </div>
                    </div>
                    <div class="col-md-12 expen_chart mt-10">
                        <canvas id="expenses_chart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>
<script>
    // start: Income chart
    $(document).ready(function(){
        mApp.block('.member_contributions_summary',{
            overlayColor: 'white',
            animate: true
        });
        mApp.block('.member_fines_summary',{
            overlayColor: 'white',
            animate: true
        });
        mApp.block('.expenses_loading',{
            overlayColor: 'white',
            animate: true
        });
        mApp.block('#bank_balances_chart',{
            overlayColor: 'white',
            animate: true
        });

        mApp.block('#income_summary_chart',{
            overlayColor: 'white',
            animate: true
        });
        mApp.block('#loans_summary_chart',{
            overlayColor: 'white',
            animate: true
        });

        
        $(document).on('click','.bank_balance_link a',function(){
            var last_days = $(this).attr('id');
            if(last_days){
                //$('.m-dropdown__wrapper.filter_bank_balance_days').toggle();
                $('body').trigger('click');
                $('.filter_days').html($(this).text());
                mApp.block('#bank_balances_chart',{
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
        load_expenses_summary();
        load_expenses_chart();
        load_dashboard_bank_chart();
    });

    function load_dashboard_bank_chart(last_days = ''){
        $('#balances_chart').remove(); 
        $('#bank_balances_data').append('<canvas id="balances_chart" height="200"></canvas>');
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_account_summary_graph/"); ?>'+last_days,
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
                            var financial_summary_chart_ctx = document.getElementById("balances_chart").getContext('2d');
                            var bg_fill = '', cs_gradient = financial_summary_chart_ctx.createLinearGradient(0, 0, 0, 200), border_color='', point_border_color='', point_background_color='';
                            var bg_fill_ = '', cs_gradient_ = financial_summary_chart_ctx.createLinearGradient(0, 0, 0, 100), border_color_='', point_border_color_='', point_background_color_='';
                            // start: Balances chart
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
                            
                            //cs_gradient.addColorStop(1, 'rgba(255,255,255,0)');
                            var contribution_chart_series = new Chart(financial_summary_chart_ctx, {
                                type: 'line',
                                data: {
                                    labels: res.months,
                                    datasets: [
                                        {
                                            label: '<?php echo translate("Bank Balances");?>',
                                            data: res.bank_values,
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
                                            label: '<?php echo translate("Cash at Hand");?>',
                                            data: res.cash_values,
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
                                        text: 'Bank Balances'
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
                        mApp.unblock('#bank_balances_chart');
                    }
                }
            }
        );


        
        //end: Balances chart
    }

    function load_expenses_summary(){
        mApp.block('.expenses_loading',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_expenses_summary"); ?>',
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

</script>