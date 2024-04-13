
<?php if(!empty($posts)){ ?>
<?php echo form_open('group/checkoffs/action', ' id="form"  class="form-horizontal"'); ?> 

<?php
    if($pagination){
        echo '
        <div class="search-pagination">';
            if ( ! empty($pagination['links'])):
                echo '
                <div class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Checkoffs
                </div>
                <div class ="pagination">'.$pagination['links'].'</div>';
            endif; 
        echo '
        </div>';                    
    }
?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>
                        <label class="m-checkbox">
                            <input type="checkbox" name="check" value="all" class="check_all">
                            <span></span>
                        </label>
                    </th>
                    <th>#</th>
                    <th>
                        <?php echo translate('Checkoff Date'); ?>
                    </th>
                    <th>
                        <?php echo translate('Recorded On'); ?>
                    </th>
                    <th class='text-right'>
                        <?php echo translate('Amount'); ?>
                        (<?php echo $this->group_currency; ?>)
                    </th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = $this->uri->segment(5, 0); $i++; foreach($posts as $post): ?>
                    <tr>
                        <td scope="row">
                            <label class="m-checkbox">
                                <input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>"/>
                                <span></span>
                            </label>
                        </th>
                        <th scope="row"><?php echo $i++; ?></th>
                        <td>
                            <?php echo timestamp_to_date($post->checkoff_date); ?><br/>
                        </td>
                        <td>
                            <?php echo timestamp_to_date_and_time($post->created_on); ?>
                        </td>
                        <td  class='text-right'>
                            <?php echo number_to_currency($post->amount); ?>
                        </td>
                        <td>
                            <a href="<?php echo site_url('group/checkoffs/view/'.$post->id); ?>" class="btn p-1 btn-primary btn-sm m-btn  m-btn m-btn--icon">
                                <span>
                                    <i class="fa fa-eye"></i>
                                    <span><?php echo translate('View'); ?></span>
                                </span>
                            </a>

                            <a href="<?php echo site_url('group/checkoffs/void/'.$post->id); ?>" class="btn p-1 btn-danger btn-sm m-btn  m-btn m-btn--icon confirmation_link">
                                <span>
                                    <i class="fa fa-trash"></i>
                                    <span><?php echo translate('Void'); ?></span>
                                </span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="row col-md-12">
    <?php 
        if($pagination){
            echo '
            <div class="search-pagination">';
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class ="pagination">'.$pagination['links'].'</div>';
                endif; 
            echo '
            </div>';                    
        }
    ?>  
    </div>
    <div class="clearfix"></div>
    <?php if($posts):?>
        <button class="btn p-1 btn-danger btn-sm m-btn  m-btn m-btn--icon" name='btnAction' value='bulk_void_checkoff' data-toggle="confirmation" data-placement="top">
            <i class='fa fa-trash'></i>
            <?php echo translate('Bulk Void'); ?>
        </button>
    <?php endif;?>
<?php echo form_close(); ?>
<?php }else{ ?>
    <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
        <strong>Ooops!</strong> <?php echo translate('Looks like you have not done any checkoffs yet'); ?>.
    </div>
<?php } ?>