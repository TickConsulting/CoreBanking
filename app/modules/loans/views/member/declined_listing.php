<?php if(!empty($posts)){ ?>
<?php echo form_open('member/loans/action', ' id="form"  class="form-horizontal"'); ?> 
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
                    Name
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
                <!-- <th>
                    <?php
                        $default_message='Actions';
                        $this->languages_m->translate('actions',$default_message);
                    ?>
                </th> -->
            </tr>
        </thead>
        <tbody>
            <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                <tr>
                    <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                    <td><?php echo $i+1;?></td>                   
                    <td>
                      <?php echo $loan_type_details->name;?>  
                    </td>
                    <td>
                        
                        <strong>
                                <?php
                                    $default_message='Loan Amount Type';
                                    $this->languages_m->translate('loan_amount_type',$default_message);
                                ?>
                        :</strong> 
                        <?php if($loan_type_details->loan_amount_type == 1){
                              echo 'Based On Amount Range'; 
                         }else if($loan_type_details->loan_amount_type == 2){
                            echo 'Based On Memeber Savings';                            
                         }else if($loan_type_details->loan_amount_type == ''){
                             echo 'Based On Amount Range';
                         }
                            ?>
                            <br>
                            <br> 
                            <strong>
                                    <?php
                                        $default_message='Amount';
                                        $this->languages_m->translate('amount',$default_message);
                                    ?>
                            :</strong>
                             <?php  if($loan_type_details->loan_amount_type == 1  ){
                                echo number_to_currency($loan_type_details->minimum_loan_amount).' - '.number_to_currency($loan_type_details->maximum_loan_amount);
                            }else if($loan_type_details->loan_amount_type == 2){
                                echo  $loan_type_details->loan_times_number.'  * (amount) of member savings'; 
                            }else if($loan_type_details->loan_amount_type == ''){
                                echo number_to_currency($loan_type_details->minimum_loan_amount).' - '.number_to_currency($loan_type_details->maximum_loan_amount);
                            } ?> 
                              
                        <br/>
                        <br/>
                        <strong>Repayment Type :</strong> <?php echo $loan_repayment_period_type[$loan_type_details->loan_repayment_period_type]?><br/><br/>
                        <strong>Repayment :</strong> 
                        <?php if($loan_type_details->loan_repayment_period_type == 1){
                             echo $loan_type_details->fixed_repayment_period.' Months';
                        }else if ($loan_type_details->loan_repayment_period_type == 2) {
                             echo $loan_type_details->minimum_repayment_period.' - '.$loan_type_details->maximum_repayment_period.' Months';
                        }
                        ?>
                        <br/>
                        <br/>
                        <strong>Interest Rate :</strong><?php echo $loan_type_details->interest_rate.'% per '.$loan_interest_rate_per[$loan_type_details->loan_interest_rate_per].' on '.$interest_types[$loan_type_details->interest_type]; ?>
                        <br/>
                    </td>
                    <td class='text-right'><?php echo number_to_currency($post->loan_amount); ?></td>
                    <td>
                        <?php 
                            if($post->status == 4){
                                echo "<span class='label label-danger'>Loan Declined</span>";
                            }else if($post->status == 2 || $post->status ==1 || $post->status == 3){
                                echo "<span class='label label-success'>In Progress</span>";
                            }
                        ?>
                        <?php 
                            if($post->active == 1){
                                echo "<span class='label label-success'>Active</span>";
                            }else{
                                echo "<span class='label label-default'>Inactive</span>";
                            }
                        ?>
                    </td>
                   <!--  <td>
                        <a href="<?php echo site_url('member/loans/view_installments/'.$post->id); ?>" class="btn btn-xs default">
                            <i class="icon-eye"></i> 
                                <?php
                                    $default_message='View';
                                    $this->languages_m->translate('view',$default_message);
                                ?>
                             &nbsp;&nbsp; 
                        </a>
                        <a href="<?php echo site_url('member/loans/loan_statement/'.$post->id); ?>" class="btn btn-xs btn-primary">
                            <i class="icon-book"></i> Statement &nbsp;&nbsp; 
                        </a>
                    </td> -->
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