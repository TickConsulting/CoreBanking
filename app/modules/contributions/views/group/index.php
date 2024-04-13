
<div class="row">
    <div class="col-sm-6">
        <div class="mt-element-ribbon bg-grey-steel">
            <div class="ribbon ribbon-clip ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-info uppercase">
                <div class="ribbon-sub ribbon-clip "></div>
                <?php
                    $default_message='Regular Contributions';
                    $this->languages_m->translate('regular_contribution',$default_message);
                ?>
            </div>
            <p class="ribbon-content"><strong>Do you do Regular Contributions?</strong> <br/> Click create to add a regular contribution.<br/> The system will do automatic reminders to your members.</p>
            <span class="pull-right">
                <a href="<?php echo site_url('group/contributions/listing')?>" class="btn btn-xs btn-success action-link">List Regular Contributions <i class="fa fa-list-ul"></i></a> &nbsp;
                <a href="<?php echo site_url('group/contributions/create')?>" class="btn btn-xs btn-primary action-link">Create <i class="fa fa-angle-double-right"></i></a>
            </span>
        </div>
    </div>


     <div class="col-sm-6">
        <div class="mt-element-ribbon bg-grey-steel">
            <div class="ribbon ribbon-border-hor ribbon-clip ribbon-color-danger uppercase">
                <div class="ribbon-sub ribbon-clip"></div> One Time Contributions </div>
            <p class="ribbon-content">
                <strong>Want to create a one-time Contribution?</strong>
                <br>
                Click create to add a one-time contribution.<br/>
                The system will keep track of all contributions recorded towards the one-time contribution.
            </p>
            <span class="pull-right">
                <a href="<?php echo site_url('group/contributions/listing')?>" class="btn btn-xs btn-success action-link">List One Time Contributions <i class="fa fa-list-ul"></i></a> &nbsp;
                <a href="<?php echo site_url('group/contributions/create')?>" class="btn btn-xs btn-primary action-link">Create <i class="fa fa-angle-double-right"></i></a>
            </span>
        </div>
    </div>
</div>