
<div class="row">
    <div class="col-sm-6">
        <div class="mt-element-ribbon bg-grey-steel">
            <div class="ribbon ribbon-clip ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-info uppercase">
                <div class="ribbon-sub ribbon-clip "></div> Asset Categories </div>
            <p class="ribbon-content"><strong>Banking with a Banking Institution?</strong> <br/> Add a back account to keep track of transactions within your bank account.</p>
            <span class="pull-right">
                <a href="<?php echo site_url('group/asset_categories/listing')?>" class="btn btn-xs btn-success action-link"><?php
                    if($this->lang->line('list_assets_categories')){
                    echo $this->lang->line('list_assets_categories');
                    }else{
                    echo "List Asset Categories";
                    }
                ?><i class="fa fa-list-ul"></i></a> &nbsp;
                <a href="<?php echo site_url('group/asset_categories/create')?>" class="btn btn-xs btn-primary action-link">Create <i class="fa fa-angle-double-right"></i></a>
            </span>
        </div>
    </div>
</div>