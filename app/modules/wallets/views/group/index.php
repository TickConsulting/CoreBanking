<style type="text/css">
    .dash_cont .fin_summ .fin_summ_entity .fin_summ_title {
        font-size: 14px;
        font-weight: 500;
    }
    .table th, .table td {
        font-size: 12px;
        line-height: 1.75rem;
    }
</style>
<div class="dash_cont wallet_summary">
    <div class="row fin_summ">
        <?php 
            $total_actual_balance = 0;
        if($wallet_accounts){
            $accounts = count($wallet_accounts);

            foreach ($wallet_accounts as $account) {
                $total_actual_balance+=$account->actual_balance;
        ?>
                <div class="col-md-<?php echo 12/$accounts;?>">
                    <div class="m-portlet m-portlet--rounded m-cont__portlet fin_summ_entity">
                        <div class="fin_summ_title"><?php echo $account->account_name;?></div>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>
                                        <?php echo translate('Account Name'); ?> : <strong><?php echo $account->account_name;?> </strong> <br/>
                                        <?php echo translate('Actual Account Balance'); ?> : <strong><?php echo (isset($currency_options[$account->account_currency_id])?$currency_options[$account->account_currency_id]:$this->group_currency).' '.number_to_currency($account->actual_balance);?> </strong><br/>
                                        <?php if($this->application_settings->show_system_account_balance){?>
                                            <?php echo translate('System Account Balance'); ?> : <strong><?php echo (isset($currency_options[$account->account_currency_id])?$currency_options[$account->account_currency_id]:$this->group_currency).' '.number_to_currency($account->initial_balance+$account->current_balance);?> </strong><br/>
                                        <?php }?>
                                        <?php echo translate('Bank Name'); ?> : <strong><?php echo $account->bank_name;?></strong><br/>
                                        <?php echo translate('Branch Name'); ?>: <strong><?php echo $account->bank_branch_name;?></strong><br/>
                                        <?php echo translate('Account Number'); ?>: <strong><?php echo $account->account_number;?></strong><br />
                                    <?php echo translate('Your Member Number'); ?>: <strong><?php echo $this->member->membership_number?:translate("--Not set--"); ?></strong>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        <?php }
        }?>
    </div>
    <h4><?php echo translate('Transactions Summary') ?></h4>
    <p><?php echo translate('Your group bank account summary') ?></p>
    <div class="row fin_summ">
        <div class="col-md-12 mt-2 mb-2">
            <div class="row">
                <div class="col-md-4">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Account Balance') ?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?> </span><?php echo number_to_currency($total_actual_balance);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-information-outline"></i></span> <?php echo translate('Total account balances') ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Total Deposits') ?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?></span> <?php echo number_to_currency($wallet_deposits);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-cash-multiple"></i></span> <?php echo translate('Total account deposits') ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Total Withdrawals') ?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?></span> <?php echo number_to_currency($wallet_withdrawals);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-cash-refund"></i></span> <?php echo translate('Total account withdrawals') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12 mt-2 mb-2">
            <div class="m-portlet m-portlet--rounded m-cont__portlet m-portlet--full-height" id="bank_balances_chart">
                <div class="m-portlet__head cust_portlet_head px-3">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title mr-4">
                            <p class="m-portlet__head-text_descr">
                                <?php echo translate('Account Balances Summary - As per System Account Balances');?><br>
                                <span><?php echo $this->group_currency.' '.number_to_currency($total_actual_balance);?></span>
                            </p>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="click" m-dropdown-persistent="1" aria-expanded="true">
                            <a href="javascript:;" class="m-dropdown__toggle btn btn-sm btn-outline-metal m-btn m-btn--outline-2x  dropdown-toggle filter_days"><?php echo translate('Last 12 months');?></a>
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
                                                    <a href="javascript:;" class="m-nav__link" id="last_7">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text ">
                                                            <?php echo translate('Last 7 days');?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="javascript:;" class="m-nav__link" id="last_1">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text">
                                                            <?php echo translate('Last 1 month');?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="javascript:;" class="m-nav__link" id="last_3">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text">
                                                            <?php echo translate('Last 3 months');?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="javascript:;" class="m-nav__link" id="last_6">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text">
                                                            <?php echo translate('Last 6 months');?>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="javascript:;" class="m-nav__link" id="last_12">
                                                        <i class=""></i>
                                                        <span class="m-nav__link-text">
                                                            <?php echo translate('Last 1 year');?>
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
                            <canvas id="balances_chart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6 deposits_big_chart">
            <div class="m-portlet m-portlet--rounded m-cont__portlet m-portlet--full-height" id="wallet_deposits_chart">
                <div class="m-portlet__head cust_portlet_head px-3">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <p class="m-portlet__head-text_descr">
                                <?php echo translate('Account Deposits Distribution');?><br>
                                <span><?php echo $this->group_currency.' '.number_to_currency($wallet_deposits);?></span>
                            </p>
                        </div>
                    </div>

                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body chart_portlet_body">
                    <div class="row">
                        <div class="col-md-12" id="deposits_balances_data">
                            <canvas id="deposits_chart" height="140">
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 withdrawal_big_chart">
            <div class="m-portlet m-portlet--rounded m-cont__portlet m-portlet--full-height" >
                <div class="m-portlet__head cust_portlet_head px-3">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <p class="m-portlet__head-text_descr">
                                <?php echo translate('Account Withdrawal Distribution');?><br>
                                <span><?php echo $this->group_currency.' '.number_to_currency($wallet_withdrawals);?></span>
                            </p>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body chart_portlet_body">
                    <div class="row">
                        <div class="col-md-12" id="withdrawal_balances_data">
                            <canvas id="withdrawal_chart" height="140">
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 expenses_big_chart">
            <div class="m-portlet m-portlet--rounded m-cont__portlet m-portlet--full-height">
                <div class="m-portlet__head cust_portlet_head px-3">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <p class="m-portlet__head-text_descr">
                                <?php echo translate('Account Expenses Summary');?><br>
                                <span><?php echo $this->group_currency.' '.number_to_currency($wallet_expenses);?></span>
                            </p>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                    </div>
                </div>
                <div class="m-portlet__body chart_portlet_body">
                    <div class="row">
                        <div class="col-md-12" id="expenses_data">
                            <canvas id="expenses_chart" height="70"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>
<div class="no_data_space" style="display: none;">
    <img src="<?php echo site_url('templates/admin_themes/groups/img/') ?>nodat_eazzykikundi.png" alt="no data">
    <p class="no_data_space_message"></p>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        mApp.block('#bank_balances_chart',{
            overlayColor: 'white',
            animate: true
        });
        mApp.block('.deposits_big_chart',{
            overlayColor: 'white',
            animate: true
        });
        mApp.block('.withdrawal_big_chart',{
            overlayColor: 'white',
            animate: true
        });
        mApp.block('.expenses_big_chart',{
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
        load_dashboard_bank_chart();
        load_deposits_chart();
        load_withdrawals_chart();
        load_expenses_chart();
    });
    function load_dashboard_bank_chart(last_days = ''){
        $('#balances_chart').remove(); 
        $('#bank_balances_data').append('<canvas id="balances_chart"></canvas>');
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/wallets/get_account_summary_graph/"); ?>'+last_days,
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
                            document.getElementById("balances_chart").height = 100;
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
                                            label: '<?php echo translate("Bank Account Balances over time");?>',
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
                },
                error: function(){
                    Toastr.show("Error occurred","There is an error that has occurred. Kindly try again later",'error');
                    window.location.href = res.refer;
                },
                always: function(){
                    Toastr.show("Error occurred","There is an error that has occurred. Kindly try again later",'error');
                    window.location.href = res.refer;
                }
            }
        );
        //end: Balances chart
    }

    function load_deposits_chart(){
        var no_data_space = $('.no_data_space').html();
        mApp.block('.deposits_big_chart',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/wallets/wallet_deposits_summary"); ?>',
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
                        if(amounts.length > 0){
                            if(categories){
                            }else{
                                categories = "Expenses Total";
                            }
                            if(amounts){
                            }else{
                                amounts = 0;
                            }
                            var expenses_summary_chart_ctx = document.getElementById("deposits_chart").getContext('2d');
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
                        }else{
                            $('#deposits_balances_data').html('<div class="text-center mt-5">'+no_data_space+"</div>");
                            $('#deposits_balances_data .no_data_space_message').html('<small> <?php echo translate('There are no deposits yet') ?></small>');
                        }
                        
                    }
                    mApp.unblock('.deposits_big_chart');
                },
                error: function(){
                    mApp.unblock('.deposits_big_chart');
                    Toastr.show("Error occurred","There is an error that has occurred. Kindly try again later",'error');
                    window.location.href = res.refer;
                },
                always: function(){
                    mApp.unblock('.deposits_big_chart');
                    Toastr.show("Error occurred","There is an error that has occurred. Kindly try again later",'error');
                    window.location.href = res.refer;
                }
            }
        );
        // end: expenses chart
    }

    function load_withdrawals_chart(){
        var no_data_space = $('.no_data_space').html();
        mApp.block('.withdrawal_big_chart',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/wallets/wallet_withdrawal_summary"); ?>',
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
                        if(amounts.length > 0){
                            if(categories){
                            }else{
                                categories = "Expenses Total";
                            }
                            if(amounts){
                            }else{
                                amounts = 0;
                            }
                            var expenses_summary_chart_ctx = document.getElementById("withdrawal_chart").getContext('2d');
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
                        }else{
                            $('#withdrawal_balances_data').html('<div class="text-center mt-5">'+no_data_space+"</div>");
                            $('#withdrawal_balances_data .no_data_space_message').html('<small> <?php echo translate('There are no withdrawals yet') ?></small>');
                        }
                        
                    }
                    mApp.unblock('.withdrawal_big_chart');
                },
                error: function(){
                    mApp.unblock('.withdrawal_big_chart');
                    Toastr.show("Error occurred","There is an error that has occurred. Kindly try again later",'error');
                    window.location.href = res.refer;
                },
                always: function(){
                    mApp.unblock('.withdrawal_big_chart');
                    Toastr.show("Error occurred","There is an error that has occurred. Kindly try again later",'error');
                    window.location.href = res.refer;
                }
            }
        );
        // end: expenses chart
    }

    function load_expenses_chart(){
        var no_data_space = $('.no_data_space').html();
        mApp.block('.expenses_big_chart',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/wallets/get_expenses_categories_summary"); ?>',
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
                        if(amounts.length > 0){
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
                        }else{
                            $('#expenses_data').html('<div class="text-center mt-5">'+no_data_space+"</div>");
                            $('#expenses_data .no_data_space_message').html('<small> <?php echo translate('There are no expenses yet') ?></small>');
                        }
                    }
                    mApp.unblock('.expenses_big_chart');
                },
                error: function(){
                    mApp.unblock('.expenses_big_chart');
                    Toastr.show("Error occurred","There is an error that has occurred. Kindly try again later",'error');
                    // window.location.href = "<?php echo site_url();?>";
                },
                always: function(){
                    mApp.unblock('.expenses_big_chart');
                    Toastr.show("Error occurred","There is an error that has occurred. Kindly try again later",'error');
                    // window.location.href = "<?php echo site_url();?>";
                }
            }
        );
        // end: expenses chart
    }

</script>