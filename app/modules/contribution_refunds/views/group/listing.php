<?php if(!empty($posts)){ ?>
<?php echo form_open('group/contribution_refunds/action', ' id="form"  class="form-horizontal"'); ?> 

<?php if (!empty($pagination['links'])): ?>
    <div class="row col-md-12">
        <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Contributions</p>
    <?php 
        echo '<div class ="top-bar-pagination">';
        echo $pagination['links']; 
        echo '</div></div>';
        endif; 
    ?>  
     <table class="table m-table m-table--head-separator-primary">
        <thead>
            <tr>
                <th width='2%'>
                    <label class="m-checkbox">
                        <input type="checkbox" name="check" value="all" class="check_all">
                        <span></span>
                    </label>
                </th>
                <th width='2%'>
                    #
                </th>
                <th>
                    <?php
                        $default_message='Member Name';
                        $this->languages_m->translate('member_name',$default_message);
                    ?>

                </th>
                <th>
                    <?php
                        $default_message='Refund Date';
                        $this->languages_m->translate('refund_date',$default_message);
                    ?>
                </th>
                <th>
                    <?php
                        $default_message='Contribution';
                        $this->languages_m->translate('contribution',$default_message);
                    ?>
                </th>
                <th>
                    <?php
                        $default_message='Account';
                        $this->languages_m->translate('account',$default_message);
                    ?>
                </th>
                <th class='text-right'>
                    <?php
                        $default_message='Amount';
                        $this->languages_m->translate('amount',$default_message);
                    ?>
                     (<?php echo $this->group_currency; ?>)
                </th>
                <th>
                    <?php
                        $default_message='Actions';
                        $this->languages_m->translate('actions',$default_message);
                    ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php $i = $this->uri->segment(5,1); foreach($posts as $post): ?>
                    <tr>
                        <td>
                            <label class="m-checkbox">
                                <input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" />
                                <span></span>
                            </label></td>
                        <td><?php echo $i++;?></td>
                        <td>
                            <?php echo $this->group_member_options[$post->member_id]; ?>
                        </td>
                        <td><?php echo timestamp_to_date($post->refund_date);?></td>
                        <td><?php echo $contribution_options[$post->contribution_id];?></td>
                        <td><?php echo group_account($post->account_id,$accounts);?></td>
                        <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>
                        <td>
                           <?php if($post->active):?>
                                <a href="<?php echo site_url('group/contribution_refunds/view/'.$post->id);?>" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> 
                                    <?php
                                        $default_message='View';
                                        $this->languages_m->translate('view',$default_message);
                                    ?>
                                </a>
                                <a href="<?php echo site_url('group/contribution_refunds/void/'.$post->id);?>" class="btn red btn-sm btn-danger confirmation_link"><i class="fa fa-trash"></i>
                                    <?php
                                        $default_message='Void';
                                        $this->languages_m->translate('void',$default_message);
                                    ?>
                                </a>
                            <?php else:?>
                                <button class="btn btn-danger btn-xs" disabled="disabled">Voided</button>
                            <?php endif;?>
                        </td>
                    </tr>
                <?php endforeach; ?>
        </tbody>
    </table>

    <div class="clearfix"></div>
    <div class="row col-md-12">
    <?php 
        if( ! empty($pagination['links'])): 
        echo $pagination['links']; 
        endif; 
    ?>  
    </div>
    <div class="clearfix"></div>

    <div class="clearfix"></div>
    <?php if($posts):?>
        <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_void' data-toggle="confirmation" data-placement="top"> <i class='fa fa-trash'></i>
                <?php
                        $default_message='Bulk Void';
                        $this->languages_m->translate('bulk_void',$default_message);
                ?>
        </button>
    <?php endif;?>
<?php echo form_close(); ?>
<?php }else{ ?>
    <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        <strong>
            Info! &nbsp;
            </strong>
        No contribution refunds to display.                        
    </div>
<?php } ?>

<script type="text/javascript">
    $(document).readyfunction(){

        $(document).on('click','.confirmation_link',function(){
            var element = $(this);
            bootbox.confirm({
                message: "Are you sure you want to do this ?",
                // title: "Before you proceed",
                callback: function(result) {
                    if(result==true){
                        if (result === null) {
                            return true;
                        }else{
                            var href = element.attr('href');
                            window.location = href;
                        }
                    }else{
                        return true;
                    }
                }
            });
            return false;
        });
    });
</script>