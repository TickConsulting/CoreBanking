<div class="invoice-content-2 bordered document-border">
    <div class="row invoice-head">
        <div class="col-md-7 col-xs-6">
            <div class="invoice-logo">
                <?php if($this->group->avatar && file_exists('uploads/groups/'.$this->group->avatar))
                    {
                        echo '<img src="'.FCPATH.'uploads/groups/'.$this->group->avatar.'" alt="" class="group-logo image-responsive" />';
                    }else{
                        echo '<img src="'.site_url('uploads/logos/'.$this->application_settings->paper_header_logo).'" alt="" class="group-logo image-responsive" /> ';
                    }
                ?>
            </div>
        </div>
        <div class="col-md-5 col-xs-6">
            <div class="company-address">
                <span class="bold uppercase"><?php echo $this->group->name; ?></span><br/>
                <?php echo nl2br($this->group->address); ?><br/>
                <span class="bold">
                    <?php echo translate('Telephone'); ?>
                </span> <?php echo $this->group->phone; ?>
                <br/>
                <span class="bold">
                    <?php echo translate('Email Address'); ?>
                    : </span> <?php echo $this->group->email; ?>
                <br/>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row invoice-cust-add margin-bottom-20">
        <div class="col-md-7 col-xs-6">
            <h4><?php echo translate('Loan Details'); ?>:</h4>
            <span class="bold">Amount Borrowed: </span> <?php echo $this->group_currency.' '.number_to_currency($loan->loan_amount); ?><br/>
            <span class="bold">Loan Date: </span> <?php echo timestamp_to_date($loan->disbursement_date); ?> - <?php echo timestamp_to_date($loan->loan_end_date); ?><br/>
            <span class="bold">Loan Interest Rate: </span> <?php echo $loan->interest_rate;?>% <?php echo $loan->loan_interest_rate_per?$loan_interest_rate_per[$loan->loan_interest_rate_per]:$loan->loan_interest_rate_per[4]?> on <?php echo $interest_types[$loan->interest_type];?><br/>
            <span class="bold">
                <?php echo translate('Loan Duration'); ?>
            : </span><?php echo $loan->repayment_period;?> Months <br/>
            <span class="bold">Loan Status: </span> 
                <?php if($loan->is_fully_paid==1):?>
                    <span class="label label-xs label-success"> Fully Paid</span>
                <?php else:?>
                    <span class="label label-xs label-success">
                        <?php echo translate('Unpaid'); ?>
                    </span>
                <?php endif;?>
                <br/>
                <?php if($loan->enable_loan_processing_fee):?>
                    <span class="bold">Loan Processing fee: </span> 
                        <?php if($loan->loan_processing_fee_type==1){
                            echo 'Fixed Amount of '.$this->group_currency.' '.number_to_currency($loan->loan_processing_fee_fixed_amount).'</br>';
                        }else{
                            echo $loan->loan_processing_fee_percentage_rate.'% of '.$loan_processing_fee_percentage_charged_on[$loan->loan_processing_fee_percentage_charged_on].'<br/>';
                            }?>
                <?php endif;?>
                <?php if($loan->enable_loan_fines):?>
                    <span class="bold">Late Payment Fine: </span>
                        <?php if($loan->loan_fine_type==1){
                            echo $this->group_currency.' '.number_to_currency($loan->fixed_fine_amount).' fine '.$late_payments_fine_frequency[$loan->fixed_amount_fine_frequency].'<br/>';
                        }else if($loan->loan_fine_type==2){
                            echo $loan->percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan->percentage_fine_frequency].' on '.$percentage_fine_on[$loan->percentage_fine_on].'<br/>';
                        }else if($loan->loan_fine_type==3){
                            if($loan->one_off_fine_type==1){
                                echo 'One Off Amount of '. $this->group_currency.' '.number_to_currency($loan->one_off_fixed_amount).'<br/>';
                            }else if($loan->one_off_fine_type==2){
                                echo $loan->one_off_percentage_rate.'% One of Fine on '.$percentage_fine_on[$loan->one_off_percentage_rate_on].'<br/>';
                            }
                        }
                        ?>
                <?php endif;?>
                <?php if($loan->enable_outstanding_loan_balance_fines):?>
                    <span class="bold">Outstanding Loan Balance Fines: </span>
                    <?php if($loan->outstanding_loan_balance_fine_type==1){
                        echo $this->group_currency.' '.number_to_currency($loan->outstanding_loan_balance_fine_fixed_amount).' '.$late_payments_fine_frequency[$loan->outstanding_loan_balance_fixed_fine_frequency].'<br/>';
                    }else if($loan->outstanding_loan_balance_fine_type==2){
                            echo $loan->outstanding_loan_balance_percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan->outstanding_loan_balance_percentage_fine_frequency].' on '.$percentage_fine_on[$loan->outstanding_loan_balance_percentage_fine_on].'<br/>';
                        }else{
                            echo 'One Off Amount '.$this->group_currency.' '.number_to_currency($loan->outstanding_loan_balance_fine_one_off_amount).'<br/>';
                        }?>
                <?php endif;?>
                <span class="bold">Loan Fine Deferment: </span><?php if($loan->enable_loan_fine_deferment){
                        echo '<span class="label label-xs label-primary">Active</span>'.'<br/>';
                    }else{
                        echo '<span class="label label-xs label-default">Inactive</span>'.'<br/>';
                    }?>
        </div>
        <div class="col-md-5 col-xs-6">
            <h4>Member Details: </h4>
            <span class="bold">Disbursed To:</span> <?php echo $loan->first_name.' '.$loan->last_name; ?><br/>
            <span class="bold">
                <?php echo translate('Phone'); ?>
            :</span> <?php echo $loan->phone; ?><br/>
            <span class="bold">
                <?php echo translate('Email Address'); ?>
                :</span> <?php echo $loan->email; ?>
        </div>
    </div>
    <hr/>
    <div class="row invoice-body">
        <?php if(!empty($posts)){ ?>
            <div class="col-xs-12 table-responsive ">
                <h5>Loan Installment Breakdown as at <?php echo timestamp_to_date(time());?>:</h5>
                <table class="table table-hover table-striped table-condensed table-statement">
                    <thead>
                        <tr>
                            <th class="invoice-title" width="2%">#</th>
                            <th class="invoice-title" >
                                <?php echo translate('Description'); ?>
                            </th>
                            <th class="invoice-title ">
                                <?php echo translate('Invoice Date'); ?>
                            </th>
                            <th class="invoice-title ">
                                <?php echo translate('Due Date'); ?>
                            </th>
                            <th class="invoice-title  text-right">Installment(<?php echo $this->group_currency; ?>)</th>
                            <th class="invoice-title  text-right">
                                <?php echo translate('Paid'); ?>
                                (<?php echo $this->group_currency; ?>)</th>
                            <th class="invoice-title  text-right">Balance(<?php echo $this->group_currency; ?>)</th>
                            <th class="invoice-title">
                                <?php echo translate('Status'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=0;$total_balance=0;$total_installment=0;$total_paid=0; foreach($posts as $post):$i++;?>
                        <tr>
                            <td>
                                <?php echo $i;?>
                            </td>
                            <td>
                                <?php if($post->type==1){
                                    echo 'Installment invoice #'.$post->id;
                                }else if($post->type==2){
                                    echo 'late payment fine #'.$post->fine_parent_loan_invoice_id.'  $'.$post->id;
                                }else if($post->type==3){
                                    echo 'Outstandiing balance fine #'.$post->id;
                                }?>
                            </td>
                            <td>
                                <?php echo timestamp_to_date($post->invoice_date);?>
                            </td>
                            <td>
                                <?php echo timestamp_to_date($post->due_date);?>
                            </td>
                            <td class="text-right">
                                <?php echo number_to_currency($installment=$post->amount_payable); ?>
                            </td>
                            <td class="text-right">
                                <?php echo number_to_currency($paid =$post->amount_paid); ?>
                            </td>
                            <td class="text-right">
                                <?php 
                                    $balance = $post->amount_payable - $post->amount_paid;
                                    echo number_to_currency($balance); ?>
                            </td>
                            <td>
                                <?php if($post->status==1 || $post->status==''){?>
                                    <span class="label label-default label-xs">Unpaid</span>
                                <?php }else if($post->status==2){?>
                                    <span class="label label-primary label-xs">Paid</span>
                                <?php }?>
                            </td>
                        </tr>
                        <?php $total_balance+=$balance; $total_installment+=$installment; $total_paid+=$paid;  endforeach;?>                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Totals</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class='text-right'><?php echo number_to_currency($total_installment); ?></td>
                            <td class='text-right'><?php echo number_to_currency($total_paid); ?></td>
                            <td class='text-right'><?php echo number_to_currency($total_balance); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if ($loan->enable_loan_guarantors):?>
                <div class="clearfix"></div>
                <div class="col-xs-12 table-responsive ">
                    <h5>Loan Guarantor Details:</h5>
                    <table class="table table-hover table-striped table-condensed table-statement">
                        <thead>
                            <tr>
                                <th class="invoice-title" width="2%">#</th>
                                <th class="invoice-title ">
                                    <?php echo translate('Member Name'); ?>
                                </th>
                                <th class="invoice-title text-right">Guaranteed Amount(<?php echo $this->group_currency;?>)</th>
                                <th class="invoice-title">Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i=0; foreach($loan_guarantors as $guarantor){$i++?>
                            <tr>
                                <td><?php echo $i;?></td>
                                <td><?php echo $guarantor->guarantor_first_name.' '.$guarantor->guarantor_last_name;?></td>
                                <td class="text-right"><?php echo number_to_currency($guarantor->guaranteed_amount);?></td>
                                <td><?php echo $guarantor->guarantor_comment;?></td>
                            </tr>
                        <?php }?>                                
                        </tbody>
                    </table>
                </div>
            <?php endif;?>


        <?php }else{ ?>
            <div class="col-xs-12 margin-bottom-10 ">
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong><?php echo translate('Sorry'); ?>!</strong> <?php echo translate('No records to display'); ?>
                </div>
            </div>
        <?php } ?> 
    </div>
    <div class="row">
        <div class="col-xs-12">
            <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> 
                <?php echo translate('Print'); ?>
            </a>
        </div>
    </div>
</div>
