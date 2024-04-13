<?php if(!empty($posts)){ ?>
    <?php echo form_open('admin/saccos/action', ' id="form"  class="form-horizontal"'); ?> 

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
                    <th>
                        #
                    </th>
                    <th>
                       Description
                    </th>
                    <th>
                        Dates
                    </th>
                    <th>
                        Deposited to
                    </th>
                    <th>
                        Status
                    </th>
                    <th class="text-right">
                        Loan Amount
                    </th>
                    <th class="text-right">
                       Payable
                    </th>
                    <th class="text-right">
                        Balances (<?php echo $this->group_currency;?>)
                    </th>
                    <th>
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                    <tr>
                        <td><?php echo $i+1;?></td>
                        <td><?php echo $post->description; ?></td>
                        <td><?php echo timestamp_to_date($post->loan_start_date).' to '.timestamp_to_date($post->loan_end_date); ?></td>
                        <td>
                            <?php echo $account_options[$post->account_id]; ?>
                        </td>
                        <td>
                            <?php if($post->active)
                            {
                                if($post->is_fully_paid)
                                {
                                    echo "<span class='label label-success'>Paid</span>";
                                }
                                else{
                                    echo "<span class='label label-primary'>In progress</span>";
                                }
                            }
                            else
                            {
                                echo "<span class='label label-danger'>Voided</span>";
                            }?>
                        </td>
                        <td class="text-right">
                            <?php echo number_to_currency($post->amount_loaned);?>
                        </td>
                        <td class="text-right">
                            <?php echo number_to_currency($post->total_loan_amount_payable);?>
                        </td>
                        <td class="text-right">
                            <?php echo number_to_currency($post->loan_balance);?>
                        </td>
                        <td class="actions">
                            <a data-original-title="View Bank Loan Statement" href="<?php echo site_url('member/bank_loans/statement/'.$post->id); ?>" class="tooltips btn btn-xs btn-primary">
                              <i class="fa fa-book"></i> Statement &nbsp;&nbsp; 
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
        
    <?php echo form_close(); ?>
<?php }else{ ?>
    <div class="alert alert-info">
        <h4 class="block">Information! No records to display</h4>
        <p>
            No Bank loans to display.
        </p>
    </div>
<?php } ?>    
       
