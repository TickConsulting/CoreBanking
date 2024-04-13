
<div class="row">
    <div class="col-sm-6">
        <div class="mt-element-ribbon bg-grey-steel">
            <div class="ribbon ribbon-clip ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-info uppercase">
                <div class="ribbon-sub ribbon-clip "></div> 
                    <?php
                        $default_message='Income Categories';
                        $this->languages_m->translate('income_categories',$default_message);
                    ?>
                 </div>
            <p class="ribbon-content"><strong>Do you generate income from different sources</strong> <br/> Add income categories to track your different streams of income.</p>
            <span class="pull-right">
                <a href="<?php echo site_url('group/income_categories/listing')?>" class="btn btn-xs btn-success action-link">
                    <?php
                        $default_message='List Income Categories';
                        $this->languages_m->translate('list_income_categories',$default_message);
                    ?>
                    <i class="fa fa-list-ul"></i></a> &nbsp;
                <a href="<?php echo site_url('group/income_categories/create')?>" class="btn btn-xs btn-primary action-link">Create <i class="fa fa-angle-double-right"></i></a>
            </span>
        </div>
    </div>
</div>