<div class="dash_cont wallet_summary">
    <h4><?php echo translate('Investment Summary') ?></h4>
    <p><?php echo translate('Your investment portfolio summary') ?></p>
    <div class="row fin_summ">
        <div class="col-md-12 mt-2 mb-2">
            <div class="row">
                <div class="col-md-4">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Stocks Portfolio');?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?> </span><?php echo number_to_currency($stock_portfolio);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-information-outline"></i></span> <?php echo translate('Total stock portfolio value');?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Asset Portfolio');?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?></span> <?php echo number_to_currency($asset_portfolio);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-cash-multiple"></i></span> <?php echo translate('Total asset portfolio value');?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Money Market Portfolio');?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?></span> <?php echo number_to_currency($money_market_portfolio);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-cash-refund"></i></span> <?php echo translate('Total money market portfolio value');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>