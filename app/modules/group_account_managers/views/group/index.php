<div class="row">
    <div class="col-md-12 margin-bottom-20">
        <a href="<?php echo site_url('group/settings'); ?>" class="btn blue btn-sm" > 
            <i class="fa fa-cogs"></i>
            Settings Panel
        </a>
    </div>
</div>
<div class="row">

    <div class="col-sm-6">
        <div class="mt-element-ribbon bg-grey-steel">
            <div class="ribbon ribbon-clip ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-info uppercase">
                <div class="ribbon-sub ribbon-clip "></div> 
                    <?php
                        $default_message='Add Group Account Managers';
                        $this->languages_m->translate('add_group_account_managers',$default_message);
                    ?>
                 </div>
            <p class="ribbon-content"><strong>Add Group Account Managers one-by-one</strong> <br/> 
                    <?php
                        $default_message='While adding your group account managers, click \'Add New Line\' to add another entry.';
                        $this->languages_m->translate('new_line_group_account_managers',$default_message);
                    ?>
            </p>
            <span class="pull-right">
                <a href="<?php echo site_url('group/group_account_managers/add_group_account_managers')?>" class="btn btn-xs btn-primary action-link">Add Group Account Managers <i class="fa fa-angle-double-right"></i></a>
            </span>
        </div>
    </div>
    <div class="col-sm-6">
        
    </div>
</div>