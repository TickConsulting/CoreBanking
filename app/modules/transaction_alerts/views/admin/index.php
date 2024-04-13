<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-green-sharp">
                        <span data-counter="counterup" data-value="<?php echo $account_number_count; ?>">0</span>
                        <small class="font-green-sharp"></small>
                    </h3>
                    <small>Bank Accounts</small>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
                        <!--<span class="sr-only">76% progress</span>-->
                    </span>
                </div>
                <div class="status">
                    <div class="status-title"> Bank Accounts  </div>
                    
                    <div class="status-number">  </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-red-haze">


                        <?php if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){ ?>
                            UGX
                        <?php }else if(preg_match('/(eazzykikundi)/',$_SERVER['HTTP_HOST'])){ ?>
                            TZS
                        <?php }else{?>
                            KSH
                        <?php } ?> 

                        <span data-counter="counterup" data-value="<?php echo number_to_currency($total_transactions_amount); ?>">0</span>
                    </h3>
                    <small>Total Transactions</small>
                </div>
                <div class="icon">
                    <i class="icon-money"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 100%;" class="progress-bar progress-bar-success red-haze">
                        <!--<span class="sr-only">85% change</span>-->
                    </span>
                </div>
                <div class="status">
                    <div class="status-title"> Total Transactions Value </div>
                    <div class="status-number"> </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-blue-sharp">
                        


                        <?php if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){ ?>
                            UGX
                        <?php }else if(preg_match('/(eazzykikundi)/',$_SERVER['HTTP_HOST'])){ ?>
                            TZS
                        <?php }else{?>
                            KSH
                        <?php } ?> 


                        <span data-counter="counterup" data-value="<?php echo number_to_currency($total_deposit_transactions_amount); ?>"></span>
                    </h3>
                    <small>Total Deposits</small>
                </div>
                <div class="icon">
                    <!--<i class="icon-money"></i>-->
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: <?php echo $deposit_percentage; ?>%;" class="progress-bar progress-bar-success blue-sharp">
                        <span class="sr-only"><?php echo $deposit_percentage; ?>% grow</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title">  </div>
                    <div class="status-number"> <?php echo $deposit_percentage; ?>% </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-purple-soft">
                        


                        <?php if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])){ ?>
                            UGX
                        <?php }else if(preg_match('/(eazzykikundi)/',$_SERVER['HTTP_HOST'])){ ?>
                            TZS
                        <?php }else{?>
                            KSH
                        <?php } ?> 


                        <span data-counter="counterup" data-value="<?php echo number_to_currency($total_withdrawal_transactions_amount); ?>"></span>
                    </h3>
                    <small>Total Withdrawals</small>
                </div>
                <div class="icon">
                    <!--<i class="icon-user"></i>-->
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: <?php echo $withdrawal_percentage; ?>%;" class="progress-bar progress-bar-success purple-soft">
                        <span class="sr-only"><?php echo $withdrawal_percentage; ?>% change</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title">  </div>
                    <div class="status-number"> <?php echo $withdrawal_percentage; ?>% </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green">
                    <span class="caption-subject bold uppercase">Transactions Breakdown</span>
                    <span class="caption-helper">Month by month</span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-7 col-sm-7">
                        <div id="deposits_vs_withdrawals" class="CSSAnimationChart"></div>
                    </div>
                    <div class="col-md-5 col-sm-5">
                        <div id="" class="table-responsive">
                            <table class="table table-bordered table-condensed table-striped table-hover table-header-fixed">
                                <thead>
                                    <tr>
                                        <!-- <th>#</th> -->
                                        <th>Month</th>
                                        <th class='text-right'>Deposits</th>
                                        <th class='text-right'>Withdrawals</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                        // $years = array(2025,2024,2023,2022,2021,2020,2019,2018,2017,2016,2015,2014);
                                        $years = array(2014,2015,2016,2017,2018,2019,2020,2021,2022,2023,2024,2025);
                                        foreach($years as $year):
                                            for($month = 1;$month <= 12;$month++){
                                                $deposit = isset($total_deposits_by_month_array[$year][$month])?$total_deposits_by_month_array[$year][$month]:0;
                                                $withdrawal = isset($total_withdrawals_by_month_array[$year][$month])?$total_withdrawals_by_month_array[$year][$month]:0;
                                                if($deposit||$withdrawal):
                                    ?>

                                            <tr>
                                                <!-- <td><?php echo $month; ?></td> -->
                                                <td><?php echo month_name_to_name($month).", ".$year; ?></td>
                                                <td class='text-right'><?php echo number_to_currency($deposit); ?></td>
                                                <td class='text-right'><?php echo number_to_currency($withdrawal); ?></td>
                                            </tr>
                                    <?php
                                                endif;
                                            }
                                        endforeach;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
<?php
    $data_provider_string = '';
    $year = date('Y');
    for($month = 1;$month <= 12;$month++){
        $deposit = isset($total_deposits_by_month_array[$year][$month])?$total_deposits_by_month_array[$year][$month]:0;
        $withdrawal = isset($total_withdrawals_by_month_array[$year][$month])?$total_withdrawals_by_month_array[$year][$month]:0;
        
        $data_provider_string.='
            {
                "date": "'.$year.'-01-'.$month.'",
                "total_deposits": '.$deposit.',
                "total_withdrawals": '.$withdrawal.',
            },
        ';
    }
?>
var Dashboard = function() {

    return {

        initMorisCharts: function() {
            if (Morris.EventEmitter && $('#sales_statistics').size() > 0) {
                // Use Morris.Area instead of Morris.Line
                dashboardMainChart = Morris.Area({
                    element: 'sales_statistics',
                    padding: 0,
                    behaveLikeLine: false,
                    gridEnabled: false,
                    gridLineColor: false,
                    axes: false,
                    fillOpacity: 1,
                    data: [{
                        period: '2011 Q1',
                        sales: 1400,
                        profit: 400
                    }, {
                        period: '2011 Q2',
                        sales: 1100,
                        profit: 600
                    }, {
                        period: '2011 Q3',
                        sales: 1600,
                        profit: 500
                    }, {
                        period: '2011 Q4',
                        sales: 1200,
                        profit: 400
                    }, {
                        period: '2012 Q1',
                        sales: 1550,
                        profit: 800
                    }],
                    lineColors: ['#399a8c', '#92e9dc'],
                    xkey: 'period',
                    ykeys: ['sales', 'profit'],
                    labels: ['Sales', 'Profit'],
                    pointSize: 0,
                    lineWidth: 0,
                    hideHover: 'auto',
                    resize: true
                });

            }
        },

        initAmChart1: function() {
            if (typeof(AmCharts) === 'undefined' || $('#deposits_vs_withdrawals').size() === 0) {
                return;
            }

            var chartData = [
            <?php echo $data_provider_string; ?>
            ];
            var chart = AmCharts.makeChart("deposits_vs_withdrawals", {
                type: "serial",
                fontSize: 12,
                fontFamily: "Open Sans",
                dataDateFormat: "YYYY-MM-DD",
                dataProvider: chartData,

                addClassNames: true,
                starttotal_withdrawals: 1,
                color: "#6c7b88",
                marginLeft: 0,

                categoryField: "date",
                categoryAxis: {
                    parseDates: true,
                    minPeriod: "DD",
                    autoGridCount: false,
                    gridCount: 50,
                    gridAlpha: 0.1,
                    gridColor: "#FFFFFF",
                    axisColor: "#555555",
                    dateFormats: [{
                        period: 'DD',
                        format: 'DD'
                    }, {
                        period: 'WW',
                        format: 'MMM DD'
                    }, {
                        period: 'MM',
                        format: 'MMM'
                    }, {
                        period: 'YYYY',
                        format: 'YYYY'
                    }]
                },

                valueAxes: [{
                    id: "a1",
                    title: "Total Deposits",
                    gridAlpha: 0,
                    axisAlpha: 0
                }, {
                    id: "a2",
                    position: "right",
                    gridAlpha: 0,
                    axisAlpha: 0,
                    labelsEnabled: false
                }, {
                    id: "a3",
                    title: "Total Withdrawals",
                    position: "right",
                    gridAlpha: 0,
                    axisAlpha: 0,
                    inside: false
                }],
                graphs: [{
                    id: "g1",
                    valueField: "total_deposits",
                    title: "Total Deposits",
                    type: "column",
                    fillAlphas: 0.7,
                    valueAxis: "a1",
                    balloonText: "KES [[value]]",
                    legendValueText: " KES [[value]]",
                    legendPeriodValueText: "Total: KES [[value.sum]]",
                    lineColor: "#08a3cc",
                    alphaField: "alpha",
                }, {
                    id: "g3",
                    title: "Total Withdrawals",
                    valueField: "total_withdrawals",
                    type: "line",
                    valueAxis: "a1",
                    lineAlpha: 0.8,
                    lineColor: "#e26a6a",
                    balloonText: "KES [[value]]",
                    lineThickness: 1,
                    legendValueText: "KES [[value]]",
                    bullet: "square",
                    bulletBorderColor: "#e26a6a",
                    bulletBorderThickness: 1,
                    bulletBorderAlpha: 0.8,
                    dashLengthField: "dashLength",
                    animationPlayed: true
                }],

                chartCursor: {
                    zoomable: false,
                    categoryBalloonDateFormat: "DD",
                    cursorAlpha: 0,
                    categoryBalloonColor: "#e26a6a",
                    categoryBalloonAlpha: 0.8,
                    valueBalloonsEnabled: false
                },
                legend: {
                    bulletType: "round",
                    equalWidths: false,
                    valueWidth: 120,
                    useGraphSettings: true,
                    color: "#6c7b88"
                }
            });
        },

        init: function() {
            this.initMorisCharts();
            this.initAmChart1();
        }
    };

}();


// if (App.isAngularJsApp() === false) {
    jQuery(document).ready(function() {
        Dashboard.init(); // init metronic core componets
    });
// }
</script>
