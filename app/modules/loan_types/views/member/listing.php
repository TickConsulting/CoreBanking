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
                    #
                </th>
                <th>
                  Name
                </th>
                <th>
                    Loan Details
                </th>
                <th >
                    Other Details
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
                    <td><?php echo $i+1;?></td>
                    <td>
                        <?php echo $post->name; ?>
                    </td>
                    <td>
                        <strong>
                            <?php
                                $default_message='Amount';
                                $this->languages_m->translate('amount',$default_message);
                            ?>
                         :</strong> <?php echo number_to_currency($post->minimum_loan_amount).' - '.number_to_currency($post->maximum_loan_amount);?>  
                        <br/>
                        <br/>
                        <strong>Repayment :</strong> <?php echo $post->minimum_repayment_period.' - '.$post->maximum_repayment_period.' Months';?>
                        <br/>
                        <br/>
                        <strong>Interest Rate :</strong><?php echo $post->interest_rate.'% per '.$loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$interest_types[$post->interest_type]; ?>
                        <br/>
                    </td>
                    <td>
                        <strong>Late Payment Fine: </strong>
                        <?php if($post->enable_loan_fines):?>
                            <?php 
                                echo $late_loan_payment_fine_types[$post->loan_fine_type].' of ';
                                if($post->loan_fine_type==1){
                                    echo $this->group_currency.' '.number_to_currency($post->fixed_fine_amount).' fine '.$late_payments_fine_frequency[$post->fixed_amount_fine_frequency].' on ';
                                    echo isset($fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on])?$fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on]:'';
                                    echo '<br/>';
                                }else if($post->loan_fine_type==2){
                                    echo $post->percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$post->percentage_fine_frequency].' on '.$percentage_fine_on[$post->percentage_fine_on].'<br/>';
                                }else if($post->loan_fine_type==3){
                                    if($post->one_off_fine_type==1){
                                        echo 'One Off Amount of '. $this->group_currency.' '.number_to_currency($post->one_off_fixed_amount).' per Installment<br/>';
                                    }else if($post->one_off_fine_type==2){
                                        echo $post->one_off_percentage_rate.'% One of Fine on '.$percentage_fine_on[$post->one_off_percentage_rate_on].' per Installment<br/>';
                                    }
                                }
                            ?>
                        <?php else:?>
                            <span class="label label-warning label-xs">Fines Disabled</span><br/>
                        <?php endif;?>
                        <br/>
                        <strong>Outstanding Loan Payment Fine: </strong>
                        <?php if($post->enable_outstanding_loan_balance_fines):
                            if($post->outstanding_loan_balance_fine_type==1){
                                echo $this->group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_fixed_amount).' '.$late_payments_fine_frequency[$post->outstanding_loan_balance_fixed_fine_frequency].'<br/>';
                            }else if($post->outstanding_loan_balance_fine_type==2){
                                echo $post->outstanding_loan_balance_percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$post->outstanding_loan_balance_percentage_fine_frequency].' on '.$percentage_fine_on[$post->outstanding_loan_balance_percentage_fine_on].'<br/>';
                            }else{
                                echo 'One Off Amount '.$this->group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_one_off_amount).'<br/>';
                            }
                        ?>
                        <?php else:?>
                            <span class="label label-warning label-xs">Outstanding Balance Fines Disabled</span><br/>
                        <?php endif;?>
                        <br/>
                        <strong>Processing Fee Charges: </strong>
                        <?php if($post->enable_loan_processing_fee):?>
                            <?php if($post->loan_processing_fee_type==1){
                                echo 'Fixed Amount of '.$this->group_currency.' '.number_to_currency($post->loan_processing_fee_fixed_amount).'</br>';
                            }else{
                                echo $post->loan_processing_fee_percentage_rate.'% of '.$loan_processing_fee_percentage_charged_on[$post->loan_processing_fee_percentage_charged_on].'<br/>';
                                }?>
                        <?php else:?>
                            <span class="label label-warning label-xs">Inactive</span>
                            <br/>
                        <?php endif;?>
                        <br/>
                        <strong>Guarantors: </strong>
                        <?php if($post->enable_loan_guarantors):?>
                            Between <?php echo 'Minimum of '.$post->minimum_guarantors.' and a Maximum of '.$post->maximum_guarantors.' guarantors required'; ?>
                        <?php else:?>
                            <span class="label label-warning label-xs">Not Required</span>
                        <?php endif;?>

                    </td>
                    <td>
                        <a href="<?php echo site_url('member/loan_types/apply/'.$post->id); ?>" class="btn btn-xs btn-primary">
                            <i class="fa fa-pencil"></i> Apply Now &nbsp;&nbsp; 
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
            No Loan Types to display.
        </p>
    </div>
<?php } ?>