<?php if(!empty($posts)){ ?>
    <?php echo form_open('group/bank_loans/action_payment', ' id="form"  class="form-horizontal"'); ?> 

    <?php if ( ! empty($pagination['links'])): ?>
        <div class="row col-md-12">
            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Saccos</p>
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
                    <th>
                        #
                    </th>
                    <th>
                        Dates
                    </th>
                    <th>
                       <?php
                            if($this->lang->line('description')){
                            echo $this->lang->line('description');
                            }else{
                            echo "Description";
                            }
                        ?>
                    </th>
                    <th>
                        <?php
                            if($this->lang->line('account')){
                            echo $this->lang->line('account');
                            }else{
                            echo "Account";
                            }
                        ?> 
                    </th>
        
                    <th class="text-right">
                        <?php
                            if($this->lang->line('amount')){
                            echo $this->lang->line('amount');
                            }else{
                            echo "Amount";
                            }
                        ?>
                    </th>
                    <th>
                        <?php
                            if($this->lang->line('actions')){
                            echo $this->lang->line('actions');
                            }else{
                            echo "Actions";
                            }
                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                    <tr>
                        <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                        <td><?php echo $i+1;?></td>
                        <td><?php echo timestamp_to_date($post->receipt_date); ?></td>
                        <td><?php echo $post->description; ?></td>
                        <td>
                            <?php echo $accounts[$post->account_id]; ?>
                        </td>
                        <td class="text-right">
                            <?php echo number_to_currency($post->amount);?>
                        </td>
                        <td class="actions">
                            <?php if($post->active){?>
                                <!--<a data-original-title="Edit Bank Loan" href="<?php echo site_url('group/bank_loans/edit/'.$post->id); ?>" class="tooltips btn btn-xs default">
                              <i class="fa fa-edit"></i> Edit &nbsp;&nbsp; -->
                               <a data-original-title="View Bank Loan Repayment" href="<?php echo site_url('group/bank_loans/view_repayment/'.$post->id); ?>" class="tooltips btn btn-xs default">
                              <i class="fa fa-eye"></i> <?php
                                    if($this->lang->line('view')){
                                    echo $this->lang->line('view');
                                    }else{
                                    echo "View";
                                    }
                                ?>  &nbsp;&nbsp;
                              <a data-original-title="Void Bank Loan Repayment" href="<?php echo site_url('group/bank_loans/void_repayment/'.$post->id); ?>" class="tooltips btn btn-xs btn-danger confirmation_link">
                              <i class="fa fa-edit"></i> <?php
                                                            if($this->lang->line('void')){
                                                            echo $this->lang->line('void');
                                                            }else{
                                                            echo "Void";
                                                            }
                                                        ?> &nbsp;&nbsp; 
                            </a>
                            <?php }else{?>

                            <?php }?>
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
        <div class="clearfix"></div>
        <?php if($posts):?>
            <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_void' data-toggle="confirmation" data-placement="top"> <i class='fa fa-trash-o'></i><?php
                                                            if($this->lang->line('bulk_void')){
                                                            echo $this->lang->line('bulk_void');
                                                            }else{
                                                            echo "Bulk Void";
                                                            }
                                                        ?></button>
        <?php endif;?>

        <div class="clearfix"></div>
        
    <?php echo form_close(); ?>
<?php }else{ ?>
    <div class="alert alert-info">
        <h4 class="block">
            <?php
                if($this->lang->line('no_records_to_display')){
                echo $this->lang->line('no_records_to_display');
                }else{
                echo "Information! No records to display";
                }
            ?>
        </h4>
        <p>
            There are no  Bank loan repayments to display.
        </p>
    </div>
<?php } ?>