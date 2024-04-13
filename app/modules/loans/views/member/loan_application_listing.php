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
                    Loan Details
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
                        $default_message='Status';
                        $this->languages_m->translate('status',$default_message);
                    ?>
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
                        <strong><?php echo $loan_types[$post->loan_type_id];?></strong>
                        <br/>
                       <strong>Applied on:</strong> <?php echo timestamp_to_date($post->created_on);?> 
                       <br/>
                        <strong>Repayment Period: </strong> <?php echo $post->repayment_period;?> months 
                    </td>
                    <td class='text-right'><?php echo number_to_currency($post->loan_amount); ?></td>
                    <td>
                        <?php if($post->status ==1){?>
                            <span class="label label-primary label-xs"><?php echo $this->loan->loan_application_stages[$post->status];?></span>
                        <?php }else if($post->status==2 || $post->status==3){?>
                            <span class="label label-success label-xs"><?php echo $this->loan->loan_application_stages[$post->status];?></span>
                        <?php }else{?>
                            <span class="label label-danger label-xs"><?php echo $this->loan->loan_application_stages[$post->status];?></span>
                            <br/>
                            <br/>
                            <p>
                            <strong>Reason: </strong> <?php echo $post->decline_message;?>
                            </p>
                        <?php }?> 
                    </td>
                    <td>
                        <?php if($post->status ==1){?>
                            <a href="<?php echo site_url('member/loan_types/edit_application/'.$post->id);?>" class="btn btn-xs default">
                            <i class="icon-eye"></i> Edit &nbsp;&nbsp; 
                            </a>
                            <a href="<?php echo site_url('member/loan_applications/delete/'.$post->id);?>" class="btn btn-xs btn-danger confirmation_link">
                                <i class="icon-trash"></i> Delete  &nbsp;&nbsp; 
                            </a>
                        <?php }?> 

                        <?php if($post->status==3){
                            if($post->is_loan_disbursed==1){
                            ?>
                            <button type="button" class="btn btn-xs blue disabled" name="processing" value="Disburse in Progress"><i class="fa fa-spinner fa-spin"></i> Disburse in Progress</button> 
                        <?php }else{?>
                            <a href="#" class="btn btn-xs btn-primary">
                                <?php
                                    $default_message='View Loan';
                                    $this->languages_m->translate('actions',$default_message);
                                ?>
                            </a>
                        <?php } }?>
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
            You currently have no loans to display.
        </p>
    </div>
<?php } ?>