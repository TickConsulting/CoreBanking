
<div class="row">
    <div class="col-sm-6">
        <div class="mt-element-ribbon bg-grey-steel">
            <div class="ribbon ribbon-clip ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-info uppercase">
                <div class="ribbon-sub ribbon-clip "></div> 
                    <?php
                        $default_message='Fine Categories';
                        $this->languages_m->translate('fine_categories',$default_message);
                    ?>
                 </div>
            <p class="ribbon-content"><strong>Banking with a Banking Institution?</strong> <br/> Add a back account to keep track of transactions within your bank account.</p>
            <span class="pull-right">
                <a href="<?php echo site_url('group/fine_categories/listing')?>" class="btn btn-xs btn-success action-link">
                    <?php
                        $default_message='List Fine Categories';
                        $this->languages_m->translate('list_fine_categories',$default_message);
                    ?>
                 <i class="fa fa-list-ul"></i></a> &nbsp;
                <a href="<?php echo site_url('group/fine_categories/create')?>" class="btn btn-xs btn-primary action-link">Create <i class="fa fa-angle-double-right"></i></a>
            </span>
        </div>
    </div>
</div>