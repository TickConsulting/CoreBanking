<?php if(!empty($posts)){ ?>
<?php echo form_open('group/loans/action', ' id="form"  class="form-horizontal"'); ?> 

<?php if ( ! empty($pagination['links'])): ?>
    <div class="row col-md-12">
        <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Loans</p>
    <?php 
        echo '<div class ="top-bar-pagination">';
        echo $pagination['links']; 
        echo '</div></div>';
        endif; 
    ?>
    <div class="table-responsive">
        <table class="table m-table m-table--head-separator-primary">
            <thead>
                <tr>
                    <th width='2%' nowrap>
                        <label class="m-checkbox">
                            <input type="checkbox" name="check" value="all" class="check_all">
                            <span></span>
                        </label>
                    </th>
                    <th width='2%' nowrap>
                        #
                    </th>
                    <th nowrap>
                        <?php echo translate('Loan Type');?>
                    </th>
                    <th nowrap>
                        <?php echo translate('Loan Dates');?>
                    </th>
                    <th class='text-right' nowrap>
                        <?php echo translate('Amount');?>
                         (<?php echo $this->group_currency; ?>)
                    </th>  
                    <th nowrap>
                        <?php echo translate('Status');?>
                    </th>
                    <th nowrap>
                        &nbsp;
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                    <tr>
                        <td>
                            <label class="m-checkbox">
                                <input name='action_to[]' type="checkbox" class="checkboxes" value="'.$post->id.'"/>
                                <span></span>
                            </label>
                        </td>
                        <td><?php echo $i+1;?></td>
                        <td>
                          <?php echo isset($loan_type_options[$post->loan_type_id])?$loan_type_options[$post->loan_type_id]:'';?>  
                        </td>
                        <td>
                          <?php echo timestamp_to_date($post->disbursement_date).' - '.timestamp_to_date($post->loan_end_date);?>  
                        </td>
                        <td class='text-right'><?php echo number_to_currency($post->loan_amount); ?></td>
                        <td>
                            <?php 
                                if($post->is_fully_paid){
                                    echo '<span class="m-badge m-badge--primary m-badge--wide">Fully paid</span>';
                                }else{
                                    if($post->active){
                                        echo '<span class="m-badge m-badge--primary m-badge--wide">Active</span>';
                                    }else{
                                        echo '<span class="m-badge m-badge--metal m-badge--wide">Inactive</span>';
                                    }
                                }
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo site_url('member/loans/view_installments/'.$post->id); ?>" class="btn btn-sm btn-info m-btn m-btn--icon action_button">
                                <span>
                                    <i class="la la-eye"></i>
                                    <span>
                                        <?php echo translate('View');?>&nbsp;&nbsp; 
                                    </span>
                                </span>
                            </a>

                            <a href="<?php echo site_url('member/loans/loan_statement/'.$post->id); ?>" class="btn btn-sm btn-primary m-btn m-btn--icon action_button">
                                <span>
                                    <i class="la la-folder"></i>
                                    <span>
                                        <?php echo translate('Statement');?>&nbsp;&nbsp; 
                                    </span>
                                </span>
                            </a>
                        </td>
                    </tr>
                    <?php $i++;
                    endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="clearfix"></div>
    <div class="row col-md-12">
    <?php 
        if( ! empty($pagination['links'])): 
        echo $pagination['links']; 
        endif; 
    ?>  
    </div>

    <div class="clearfix"></div>
<?php echo form_close(); ?>
<?php }else{ ?>
    <div class="m-alert m-alert--outline alert alert-accent alert-dismissible fade show" role="alert">
        <strong><?php echo translate('Oooops')?></strong> <?php echo translate('Seems you have no loan history')?>.
    </div>
<?php } ?>