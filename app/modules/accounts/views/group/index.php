<div class="row page_menus">
    
    <div class="col-sm-6 menu_item p-3" style="cursor:default;">
        <div class="menu_cont pl-0">
            <div class="menu_cont_hdr uppercase"style="font-size:16px;font-weight:600;">
            <?php
                $default_message='Bank Account';
                $this->languages_m->translate('bank_account',$default_message);
            ?>
            </div>
            <p class="ribbon-content"><strong><?php echo translate('Banking with a Banking Institution') ?>?</strong> <br/> <?php echo translate('Add a bank account to keep track of transactions within your bank account') ?>.</p>
            <span class="pull-right">
                <a href="<?php echo site_url('group/bank_accounts/listing')?>" class="btn btn-sm btn-success action-link"><?php echo translate('List Accounts') ?> <i class="mdi mdi-playlist-check icon_md"></i></a> &nbsp;
                <a href="<?php echo site_url('group/bank_accounts/create')?>" class="btn btn-sm btn-primary action-link"><?php echo translate('Create Account') ?> <i class="mdi mdi-plus icon_md"></i></a>
            </span>
        </div>
    </div>


     <div class="col-sm-6 menu_item p-3" style="cursor:default;">
        <div class="menu_cont pl-0">
            <div class="menu_cont_hdr uppercase"style="font-size:16px;font-weight:600;">
                <?php echo translate('Sacco Account') ?>
            </div>
            <p class="ribbon-content">
                <strong><?php echo translate('Banking with a Sacco?') ?></strong>
                <br>
                <?php echo translate('Click create account to add a Sacco account to keep track of your group banking transactions') ?>.
            </p>
            <span class="pull-right">
                <a href="<?php echo site_url('group/sacco_accounts/listing')?>" class="btn btn-sm btn-success action-link"><?php echo translate('List Accounts') ?> <i class="mdi mdi-playlist-check icon_md"></i></a> &nbsp;
                <a href="<?php echo site_url('group/sacco_accounts/create')?>" class="btn btn-sm btn-primary action-link"><?php echo translate('Create Account') ?> <i class="mdi mdi-plus icon_md"></i></a>
            </span>
        </div>
    </div>
</div>

<div class="row page_menus">

    <div class="col-sm-6 menu_item p-3" style="cursor:default;">
        <div class="menu_cont pl-0">
            <div class="menu_cont_hdr uppercase"style="font-size:16px;font-weight:600;">
                <?php echo translate('Mobile Money Cash Account') ?>
            </div>
            <p class="ribbon-content">
                <strong><?php echo translate('Using a Till Number or Mobile Number Account to Bank') ?>?</strong>
                <br/>
                <?php echo translate('Proceed to create account to keep track of all transactions within your group mobile money accounts Indicate the mobile money provider') ?>.
            </p>

            <span class="pull-right">
                <a href="<?php echo site_url('group/mobile_money_accounts/listing')?>" class="btn btn-sm btn-success action-link"><?php echo translate('List Accounts') ?> <i class="mdi mdi-playlist-check icon_md"></i></a> &nbsp;
                <a href="<?php echo site_url('group/mobile_money_accounts/create')?>" class="btn btn-sm btn-primary action-link"><?php echo translate('Create Account') ?> <i class="mdi mdi-plus icon_md"></i></a>
            </span>

        </div>
    </div>

     <div class="col-sm-6 menu_item p-3" style="cursor:default;">
         <div class="menu_cont pl-0">
            <div class="menu_cont_hdr uppercase"style="font-size:16px;font-weight:600;">
                <?php
                    if($this->lang->line('petty_cash_account')){
                    echo $this->lang->line('petty_cash_account');
                    }else{
                    echo "Petty Cash Account";
                    }
                ?> </div>
            <p class="ribbon-content">
                <strong><?php echo translate('Does your group perform Cash at Hand Transactions') ?>?</strong>
                <br/>
                <?php echo translate('Create a Petty Cash Account to keep track of all the group transactions and generate transaction statements') ?>.
            </p>
             <span class="pull-right">
                <a href="<?php echo site_url('group/petty_cash_accounts/listing')?>" class="btn btn-sm btn-success action-link"><?php echo translate('List Accounts') ?> <i class="mdi mdi-playlist-check icon_md"></i></a> &nbsp;
                <a href="<?php echo site_url('group/petty_cash_accounts/create')?>" class="btn btn-sm btn-primary action-link"><?php echo translate('Create Account') ?> <i class="mdi mdi-plus icon_md"></i></a>
            </span>
        </div>
    </div>
</div>
