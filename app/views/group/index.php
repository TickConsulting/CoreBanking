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
<div class="m-content m-cont__no-padding-top">

    <div class="m-subheader mb-5 pl-0">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h3 class="m-subheader__title cust_subheader_title">{group:template:title}</h3>
                <p class="cust_subheader_title_descr"><small><?php echo translate('Group transaction summary');?>.</small></p>
            </div>
            
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="m-portlet m-portlet--rounded m-cont__portlet m-portlet--full-height" id="bank_balances_chart">
                <div class="m-portlet__head cust_portlet_head px-3" style="height: 4.1rem;">
                <?php if($this->application_settings->show_system_account_balance){?>
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title mr-4">
                            <p class="m-portlet__head-text_descr">
                                <?php echo translate('Actual Bank Balance');?><br>
                                <span class="total_bank_balance"><?php echo $this->group_currency.' '.number_to_currency(0);?></span>
                            </p>
                        </div>
                    </div>
                <?php }?>
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
                <div class="m-portlet__head cust_portlet_head px-3">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title mr-4">
                            <?php if($this->application_settings->show_system_account_balance){?>
                                <p class="m-portlet__head-text_descr">
                                    <?php echo translate('System Bank Balance');?><br>
                                    <span class="system_bank_balance"><?php echo $this->group_currency.' '.number_to_currency($total_cash_at_bank);?></span>
                                </p>
                            <?php }else{ ?>
                                <p class="m-portlet__head-text_descr">
                                    <?php echo translate('Actual Bank Balance');?><br>
                                    <span class="total_bank_balance"><?php echo $this->group_currency.' '.number_to_currency(0);?></span>
                                </p>
                            <?php }?>
                        </div>
                        <div class="m-portlet__head-title">
                            <p class="m-portlet__head-text_descr">
                                <?php echo translate('Cash at Hand');?><br>
                                <span><?php echo $this->group_currency.' '.number_to_currency($total_cash_at_hand);?></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body chart_portlet_body">
                    <div class="row">
                        <div class="col-md-12" id="bank_balances_data">
                            <canvas id="balances_chart" height="180"></canvas>
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
                                <?php echo translate('Contributions');?> & <?php echo translate('Fines');?>
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--right m-tabs-line-danger" role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active show" data-toggle="tab" href="#m_portlet_tab_contributions" role="tab" aria-selected="true">
                                    <?php echo translate('Contributions');?>
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_portlet_tab_fines" role="tab" aria-selected="false">
                                    <?php echo translate('Fines');?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane active show member_contributions_summary" id="m_portlet_tab_contributions">
                            <!-- members contributions table -->
                            <div class="m-scrollable member_contributions_summary_position" data-scrollable="true" data-max-height="200" style="max-height:200px;">
                                <table class="table table-sm m-table m-table--head-bg-metal table-bordered d-none">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th>
                                                <?php echo translate('Member');?>
                                            </th>
                                            <th>
                                                <?php echo translate('Paid');?> (<?php echo $this->group_currency;?>)
                                            </th>
                                            <th>
                                                <?php echo translate('Arrears');?> (<?php echo $this->group_currency;?>)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php    
                                        $count = 1;
                                        foreach($this->active_group_member_options  as $member_id => $member_name):
                                            echo '
                                                <tr>
                                                    <td>'.$count.'</td>
                                                    <td><a href="'.site_url('group/members/view/'.$member_id).'">'.$member_name.'</a></td>
                                                    <td class="text-right">'.number_to_currency(0).'</td>
                                                    <td class=" font-red-mint  text-right">'.number_to_currency(0).'</td>
                                                </tr>
                                            ';
                                            ++$count;
                                        endforeach;
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane member_fines_summary" id="m_portlet_tab_fines">
                            <div class="m-scrollable member_fines_summary_position" data-scrollable="true" data-max-height="200" style="max-height:200px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet m-portlet--rounded m-cont__portlet expenses_loading">
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
                <div class="m-portlet__body chart_portlet_body" style="min-height:100px;">
                    <div class="row pb-4 pl-3">
                        <div class="col-md-12 text-center mt-5 expen_disp" style="display:none;opacity:0.7;">
                            <?php if(preg_match('/eazzy/i', $this->application_settings->application_name)){ ?>
                                <img src="<?php echo site_url('templates/admin_themes/groups/img/') ?>nodat_eazzykikundi.png" alt="no data">
                            <?php } else if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
                                <img src="<?php echo site_url('templates/admin_themes/groups/img/') ?>nodat_websacco.png" alt="no data">
                            <?php } else{ ?>
                                <img src="<?php echo site_url('templates/admin_themes/groups/img/') ?>nodat_default.png" alt="no data">
                            <?php } ?>
                            <p><small>There are no expenses here, yet.</small></p>
                        </div>
                        <div class="col-md-6">
                            <!-- expenses table -->
                            <div class="m-scrollable expen_data" data-scrollable="true" data-max-height="140" style="max-height:170px;min-height:40px" id="expenses_summary">
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
                        <div class="col-md-6 expen_chart">
                            <canvas id="expenses_chart" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-<?php if(!$this->group->group_offer_loans) echo '12'; else echo '6'; ?>">
            <div class="m-portlet m-portlet--rounded m-cont__portlet m-portlet--full-height">
                <div class="m-portlet__head cust_portlet_head px-3">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <p class="m-portlet__head-text_descr">
                                <span><?php echo translate('Group Income');?></span><br>
                                <?php echo translate('Monthly Deposits less withdrawals');?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body chart_portlet_body" id="income_summary_chart">
                    <div class="row">
                        <div class="col-md-12">
                            <canvas id="income_chart" height="<?php if(!$this->group->group_offer_loans) echo '50'; else echo '110'; ?>"></canvas>
                            <div class="m-widget4 m-widget4--progress">
                                <div class="m-widget4__item py-2">					
                                    <div class="m-widget4__info">
                                        <span class="m-widget4__title">
                                            <?php echo date('M',time()).' '.translate('Collections');?>
                                        </span>
                                        <br>
                                        <span class="m-widget4__sub" id="text_success_danger_collection">
                                            <strong>0% <i class="fa fa-arrow-up"></i></strong> Increase
                                        </span>
                                    </div>
                                    <div class="m-widget4__progress">
                                        <div class="m-widget4__progress-wrapper">
                                            <span class="m-widget17__progress-number" id="monthly_collections">
                                                <?php echo $this->group_currency.' '.number_to_currency();?> 
                                            </span>
                                        </div>
                                    </div>
                                    <div class="m-widget4__ext pr-3">
                                        <a href="#" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
                                           <?php echo translate('View');?>
                                        </a>
                                    </div>
                                </div>
                                <div class="m-widget4__item py-2">					
                                    <div class="m-widget4__info">
                                        <span class="m-widget4__title">
                                            <?php echo date('M',time()).' '.translate('Net Income');?>
                                        </span>
                                        <br>
                                        <span class="m-widget4__sub " id="text_success_danger_income">
                                            <strong>0% <i class="fa fa-arrow-down"></i></strong> decrease
                                        </span>
                                    </div>
                                    <div class="m-widget4__progress">
                                        <div class="m-widget4__progress-wrapper">
                                            <span class="m-widget17__progress-number" id="net_income">
                                                <?php echo $this->group_currency.' '.number_to_currency();?> 
                                            </span>
                                        </div>
                                    </div>
                                    <div class="m-widget4__ext pr-3">
                                        <a href="#" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
                                            <?php echo translate('View');?>
                                        </a>
                                    </div>
                                </div>
                                <div class="m-widget4__item py-2">					
                                    <div class="m-widget4__info">
                                        <span class="m-widget4__title">
                                            <?php echo date('M',time());?> <?php echo translate('Profit Margin');?>
                                        </span>
                                        <br>
                                        <span class="m-widget4__sub" id="text_success_danger_profit_margin">
                                            <strong>0% <i class="fa fa-arrow-down"></i></strong> decrease
                                        </span>
                                    </div>
                                    <div class="m-widget4__progress">
                                        <div class="m-widget4__progress-wrapper">
                                            <span class="m-widget17__progress-number" id="profit_margin">
                                                <?php echo $this->group_currency.' '.number_to_currency();?> 
                                            </span>
                                        </div>
                                    </div>
                                    <div class="m-widget4__ext pr-3">
                                        <a href="#" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
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
        <?php if($this->group->group_offer_loans):?>
        <div class="col-md-6">
            <div class="m-portlet m-portlet--rounded m-cont__portlet m-portlet--full-height">
                <div class="m-portlet__head cust_portlet_head px-3">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <p class="m-portlet__head-text_descr">
                                <span><?php echo translate('Group Loans');?></span><br>
                                <?php echo translate('Loans to group members and debtors summary');?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body chart_portlet_body" id="loans_summary_chart">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="m-widget4 m-widget4--progress">
                                <div class="m-widget4__item py-3">					
                                    <div class="m-widget4__info">
                                        <span class="m-widget4__title">
                                            <?php echo translate('Amount Loaned');?> in <?php echo date('M');?>
                                        </span>
                                        <br>
                                        <span class="m-widget4__sub" id="text_danger_amount_loaned_percentage">
                                            <strong>0% <i class="fa fa-arrow-up"></i></strong> increase
                                        </span>
                                    </div>
                                    <div class="m-widget4__progress">
                                        <div class="m-widget4__progress-wrapper">
                                            <span class="m-widget17__progress-number" id="amount_loaned_this_month">
                                                <?php echo $this->group_currency.' '.number_to_currency();?>
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
                                            <?php echo translate('Loan Arrears');?>
                                        </span>
                                        <br>
                                        <span class="m-widget4__sub">
                                           <?php echo translate('Unpaid loan');?>
                                        </span>
                                    </div>
                                    <div class="m-widget4__progress">
                                        <div class="m-widget4__progress-wrapper">
                                            <span class="m-widget17__progress-number" id="loan_unpaid_debt">
                                                <?php echo $this->group_currency.' '.number_to_currency();?>
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
                                            <?php echo translate('Defaulted Loans');?>
                                        </span>
                                        <br>
                                        <span class="m-widget4__sub">
                                            <?php echo translate('Loans marked as bad loans');?>
                                        </span>
                                    </div>
                                    <div class="m-widget4__progress">
                                        <div class="m-widget4__progress-wrapper">
                                            <span class="m-widget17__progress-number" id="defaulted_loan_amount">
                                                <?php echo $this->group_currency.' '.number_to_currency();?>
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
                                            <?php echo translate('Loan Repayments');?>
                                        </span>
                                        <br>
                                        <span class="m-widget4__sub" id="text_danger_loan_repayments">
                                            <strong>0% <i class="fa fa-arrow-down"></i></strong> of loaned amount
                                        </span>
                                    </div>
                                    <div class="m-widget4__progress">
                                        <div class="m-widget4__progress-wrapper">
                                            <span class="m-widget17__progress-number" id="loan_repayments">
                                                <?php echo $this->group_currency.' '.number_to_currency();?>
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
                                            <?php echo translate('Projected Profits');?>
                                        </span>
                                        <br>
                                        <span class="m-widget4__sub text-success">
                                            <!-- <strong>9% <i class="fa fa-arrow-up"></i></strong> increase -->
                                            <?php echo translate('Profit projected from loans');?>
                                        </span>
                                    </div>
                                    <div class="m-widget4__progress">
                                        <div class="m-widget4__progress-wrapper">
                                            <span class="m-widget17__progress-number" id="projected_profits">
                                                <?php echo $this->group_currency.' '.number_to_currency();?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="m-widget4__ext pr-3">
                                        <a href="#" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
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
    <?php endif;?>
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

        mApp.block('.total_bank_balance',{
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
        load_member_contributions_summary();
        load_member_fines_summary();
        load_expenses_summary();
        load_expenses_chart();
        load_income_chart();
        <?php if($this->group->group_offer_loans):?>
            load_loans_summary_chart();
        <?php endif;?>
        load_bank_balances();
    });

    function load_member_contributions_summary(){
        var member_contributions_summary_request = $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/load_member_contributions_summary"); ?>',
            dataType : "html",
                success: function(response) {
                    $('.member_contributions_summary_position').html(response);
                    mApp.unblock('.member_contributions_summary');
                }
            }
        );
    }

    function load_member_fines_summary(){
        var member_fines_summary_request = $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/load_member_fines_summary"); ?>',
            dataType : "html",
                success: function(response) {
                    $('.member_fines_summary_position').html(response);
                    mApp.unblock('.member_fines_summary');
                }
            }
        );
    }

    function load_bank_balances(){
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/bank_accounts/get_group_account_balances/"); ?>',
            dataType : "html",
                success: function(response) {
                    if(isJson(response)){
                        res = $.parseJSON(response);
                        if(res.hasOwnProperty('status')){
                            if(res.status == '202'){
                                Toastr.show("Session Expired",res.message,'error');
                                window.location.href = res.refer;
                            }
                            $('.total_bank_balance').html(res.currency+' '+res.amount);
                        }else{}
                        mApp.unblock('.total_bank_balance');
                    }
                    load_dashboard_bank_chart();
                },
                always:function(){
                    load_dashboard_bank_chart();
                    mApp.unblock('.total_bank_balance');
                },
                error: function(){
                    load_dashboard_bank_chart();
                    mApp.unblock('.total_bank_balance');
                }
            }
        );

    }

    function load_dashboard_bank_chart(last_days = ''){
        $('#balances_chart').remove(); 
        $('#bank_balances_data').append('<canvas id="balances_chart" height="180"></canvas>');
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
                            // bg_fill = 'rgba(216,81,31,0.2)';
                            // cs_gradient.addColorStop(0, '#d8511f');
                            // border_color = '#d8511f';
                            // point_border_color = 'rgba(255,255,255,1)';
                            // point_background_color = '#d8511f';
                            // //second set of colors
                            // bg_fill_ = 'rgba(237,158,0,0.2)';
                            // cs_gradient_.addColorStop(0, '#ed9e00');
                            // border_color_ = '#ed9e00';
                            // point_border_color_ = 'rgba(255,255,255,1)';
                            // point_background_color_ = '#ed9e00';
                            <?php if(preg_match('/eazzy/i', $this->application_settings->application_name)){ ?>
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
                            // cs_gradient.addColorStop(0, '#d8511f');
                            // border_color = '#d8511f';
                            // point_border_color = 'rgba(255,255,255,1)';
                            // point_background_color = '#d8511f';
                            <?php if(preg_match('/eazzy/i', $this->application_settings->application_name)){ ?>
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