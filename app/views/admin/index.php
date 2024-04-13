<style>
    /* Define custom CSS variables */
    :root {
        --bg-blue-hoki: #012f6b !important;
        --bg-red-sunglo: #012f6b !important;
        /* Add more custom CSS variables as needed */
    }
    .btn.green:not(.btn-outline) {
    color: #FFF;
    background-color: #012f6b;
    border-color: #012f6b;
}

    /* Apply styles using custom CSS variables */
    .portlet.light.bordered .tile.double-down {
        background-color:!important ;
    }

    .portlet.light.bordered .tile.bg-red-sunglo {
        background-color:!important ;
    }
    .bg-blue-hoki {
        color:!important
    }
    .font-green-sharp {
    color: #012f6b !important;

}
.progress-bar.red-haze {
    background: #012f6b !important;
    color: #fff !important;
}
.progress-bar.green-sharp {
    background: #c3fcb4 !important;
    color: #FFF !important;
}
.font-green {
    color: #012f6b !important;
}
    /* Add styles for other elements using custom CSS variables */
</style>


<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-green-sharp">
                        <span data-counter="counterup" data-value="<?php echo $partners; ?>">0</span>
                        <small class="font-green-sharp"></small>
                    </h3>
                    <small>Users</small>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
                        <span class="sr-only">76% progress</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title">Active Users </div>
                    
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

                        <span data-counter="counterup" data-value="<?php echo number_to_currency($partners); ?>">0</span>
                    </h3>
                    <small>Our Partners</small>
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
                    <div class="status-title">our partners </div>
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
                        <?php }else{ ?>
                            KES
                        <?php } ?> 


                        <span data-counter="counterup" data-value="<?php echo number_to_currency($total_deposit_transactions_amount); ?>"></span>
                    </h3>
                    <small>Total Loan Repayments</small>
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
                        <?php }else{ ?>
                            KES
                        <?php } ?> 


                        <span data-counter="counterup" data-value="<?php echo number_to_currency($total_withdrawal_transactions_amount); ?>"></span>
                    </h3>
                    <small>Total Disbursement</small>
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
                                        <th>#</th>
                                        <th>Month</th>
                                        <th class='text-right'>LOAN REPAYMENTS</th>
                                        <th class='text-right'>DISBURSEMENTS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                        $years = array('2015','2016','2017','2018','2019','2020');
                                        foreach($years as $year):
                                            for($month = 1;$month <= 12;$month++){
                                                $deposit = isset($total_deposits_by_month_array[$year][$month])?$total_deposits_by_month_array[$year][$month]:0;
                                                $withdrawal = isset($total_withdrawals_by_month_array[$year][$month])?$total_withdrawals_by_month_array[$year][$month]:0;
                                                if($deposit||$withdrawal):
                                    ?>

                                            <tr>
                                                <td><?php echo $month; ?></td>
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
