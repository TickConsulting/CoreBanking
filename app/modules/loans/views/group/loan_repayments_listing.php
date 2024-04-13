<?php if(!empty($posts)){ ?>
<?php echo form_open('group/loans/void_repayments', ' id="form"  class="form-horizontal"'); ?> 

<?php if ( ! empty($pagination['links'])): ?>
    <div class="row col-md-12">
        <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Contributions</p>
    <?php 
        echo '<div class ="top-bar-pagination">';
        echo $pagination['links']; 
        echo '</div></div>';
        endif; 
    ?>  
     <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed table-searchable">
        <thead>
            <tr>
                <th width='2%'>
                     <input type="checkbox" name="check" value="all" class="check_all">
                </th>
                <th width='2%'>
                    #
                </th>
                <th>
                    <?php
                        $default_message='Payment Date';
                        $this->languages_m->translate('payment_date',$default_message);
                    ?>
                </th>
                <th>
                    <?php
                        $default_message='Member Name';
                        $this->languages_m->translate('member_name',$default_message);
                    ?>
                </th>
                <th>
                    Payment Details
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
            <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                <tr>
                    <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                    <td><?php echo $i+1;?></td>
                    <td>
                        <?php echo timestamp_to_date($post->receipt_date);?>
                    </td>
                    <td>
                        <?php echo $members[$post->member_id]; ?>
                    </td>
                    <td>
                      <?php echo $deposit_method_options[$post->payment_method].' to '.$accounts[$post->account_id];?>  
                    </td>
                    <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>
                    <td>
                        <a href="<?php echo site_url('group/deposits/loan_repayment_receipt/'.$post->id); ?>" class="confirmation_link btn btn-xs default">
                            <i class="fa fa-book"></i> Receipt &nbsp;&nbsp; 
                        </a>
                        <a href="<?php echo site_url('group/loans/void_payment/'.$post->id); ?>" class="confirmation_link btn btn-xs red">
                            <i class="fa fa-trash"></i>
                                <?php
                                    $default_message='Void';
                                    $this->languages_m->translate('void',$default_message);
                                ?>
                             &nbsp;&nbsp; 
                        </a>
                    </td>
                </tr>
                <?php $i++;
                endforeach; ?>
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
    <?php if($posts):?>
        <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_void' data-toggle="confirmation" data-placement="top"> <i class='fa fa-trash-o'></i> Bulk Void</button>
    <?php endif;?>
    <div class="clearfix"></div>
<?php echo form_close(); ?>
<?php }else{ ?>
    <div class="alert alert-info">
        <h4 class="block">
            <?php
                $default_message='Information! No records to display';
                $this->languages_m->translate('no_records_to_display',$default_message);
            ?>
            
        </h4>
        <p>
            No Loan repayments to display.
        </p>
    </div>
<?php } ?>