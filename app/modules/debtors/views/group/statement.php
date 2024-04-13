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
                        <?php
                            $default_message='Telephone';
                            $this->languages_m->translate('telephone',$default_message);
                        ?>
                    : </span> <?php echo $this->group->phone; ?>
                <br/>
                <span class="bold">
                        <?php
                            $default_message='Email Address';
                            $this->languages_m->translate('email_address',$default_message);
                        ?>
                    : </span> <?php echo $this->group->email; ?>
                <br/>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row invoice-cust-add margin-bottom-20">
        <div class="col-md-7 col-xs-6">
            <h4>Loan Details:</h4>
            <span class="bold">Amount Borrowed : </span> <?php echo $this->group_currency.' '.number_to_currency($loan->loan_amount); ?><br/>
            <span class="bold">Total Payable : </span> <?php echo $this->group_currency.' '.number_to_currency($payable =$total_installment_payable+$total_fines);?><br/>
            <span class="bold">Total Fine Amount : </span> <?php echo $this->group_currency.' '.number_to_currency($total_fines);?><br/>
            <span class="bold">Lump sum remaining : </span> <?php if($lump_sum_remaining>0) {echo $this->group_currency.' '.number_to_currency($lump_sum_remaining);} else {echo $this->group_currency.' '.number_to_currency(0);}?><br/>
            <span class="bold">Total Amount Paid : </span> <?php echo $this->group_currency.' '.number_to_currency($total_paid);?><br/>
            <span class="bold">Disbursement Date : </span> <?php echo timestamp_to_date($loan->disbursement_date); ?><br/>
            <span class="bold">Loan End Date : </span> <?php echo timestamp_to_date($loan->loan_end_date); ?><br/>
            <span class="bold">Loan Interest Rate: </span> <?php echo $loan->interest_rate;?>% <?php echo $loan->loan_interest_rate_per?$loan_interest_rate_per[$loan->loan_interest_rate_per]:$loan->loan_interest_rate_per[4];?> on <?php echo $interest_types[$loan->interest_type];?><br/>
                <?php if($loan->enable_loan_processing_fee):?>
                    <span class="bold">Loan Processing fee: </span> 
                        <?php if($loan->loan_processing_fee_type==1){
                            echo 'Fixed Amount of '.$this->group_currency.' '.number_to_currency($loan->loan_processing_fee_fixed_amount).'</br>';
                        }else{
                            echo $loan->loan_processing_fee_percentage_rate.'% of '.$loan_processing_fee_percentage_charged_on[$loan->loan_processing_fee_percentage_charged_on].'<br/>';
                            }?>
                <?php endif;?>
                <?php if($loan->enable_loan_fines):?>
                    <span class="bold">Late Installment Payment Fine: </span>
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
                    <span class="bold">Outstanding Loan Balance Fine: </span>
                    <?php if($loan->outstanding_loan_balance_fine_type==1){
                        echo $this->group_currency.' '.number_to_currency($loan->outstanding_loan_balance_fine_fixed_amount).' '.$late_payments_fine_frequency[$loan->outstanding_loan_balance_fixed_fine_frequency].'<br/>';
                    }else if($loan->outstanding_loan_balance_fine_type==2){
                            echo $loan->outstanding_loan_balance_percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan->outstanding_loan_balance_percentage_fine_frequency].' on '.$percentage_fine_on[$loan->outstanding_loan_balance_percentage_fine_on].'<br/>';
                        }else{
                            echo 'One Off Amount '.$this->group_currency.' '.number_to_currency($loan->outstanding_loan_balance_fine_one_off_amount).'<br/>';
                        }?>
                <?php endif;?>
                 <span class="bold">Loan Duration: </span><?php echo $loan->repayment_period;?> Months <br/>
                 <span class="bold">Loan Grace Period: </span><?php echo $loan->grace_period;?> Month(s) <br/>
                <span class="bold">Loan Fine Deferment: </span><?php if($loan->enable_loan_fine_deferment){
                        echo '<span class="label label-xs label-default">Deferment Active</span>'.'<br/>';
                    }else{
                        echo '<span class="label label-xs label-primary">Deferment Inactive</span>'.'<br/>';
                    }?>
                <span class="bold">Loan Status: </span><?php if($loan->is_fully_paid){
                        echo '<span class="label label-xs label-primary">Fully Paid</span>'.'<br/>';
                    }else{
                        echo '<span class="label label-xs label-default">Payment in progress</span>'.'<br/>';
                    }?>
        </div>
        <div class="col-md-5 col-xs-6">
            <h4>Debtor Details: </h4>
            <span class="bold">Disbursed To:</span> <?php echo $loan->name; ?><br/>
            <span class="bold">
                    <?php
                            $default_message='Phone';
                            $this->languages_m->translate('phone',$default_message);
                        ?>
            :</span> <?php echo $loan->phone; ?><br/>
            <span class="bold">
                    <?php
                            $default_message='Email Address';
                            $this->languages_m->translate('email_address',$default_message);
                        ?>
                :</span> <?php echo $loan->email; ?>
        </div>
    </div>
    <hr/>
    <div class="row invoice-body">
        <div class="col-xs-12 table-responsive ">
            <table class="table table-hover table-striped table-condensed table-statement">
                <thead>
                    <tr>
                        <th class="invoice-title" width="15%">
                            
                        <?php
                            $default_message='Type';
                            $this->languages_m->translate('type',$default_message);
                        ?>
                        </th>
                        <th class="invoice-title" >
                            
                        <?php
                            $default_message='Date';
                            $this->languages_m->translate('date',$default_message);
                        ?>
                        </th>
                        <th class="invoice-title ">
                            
                        <?php
                            $default_message='Description';
                            $this->languages_m->translate('description',$default_message);
                        ?>
                        </th>
                        <th class="invoice-title  text-right">

                        <?php
                            $default_message='Amount Paid';
                            $this->languages_m->translate('amount_paid',$default_message);
                        ?>
                         (<?php echo $this->group_currency; ?>)</th>
                        <th class="invoice-title  text-right">
                        <?php
                            $default_message='balance';
                            $this->languages_m->translate('balance',$default_message);
                        ?>
                            (<?php echo $this->group_currency; ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Amount Payable</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><strong><?php echo number_to_currency($payable);?></strong></td>
                    </tr>
                    <?php $amount = 0;$total_amount=0;$remaining_payable=0; foreach($posts as $post): 
                        if($post->transaction_type!=5){$total_amount+=$post->amount;}?>
                        <tr>
                            <td><?php if($post->transaction_type==5){
                                echo 'Contibution Transfer';
                                }else{echo 'Payment';}?></td>
                            <td><?php echo timestamp_to_date($post->transaction_date);?></td>
                            <td><?php if($post->transaction_type==5){
                                     echo 'Transfer to '.$transfer_options[$post->transfer_to];
                                 }
                                else{
                                    if($post->transfer_from){
                                        echo 'Transfer from ';
                                        if($post->transfer_from=='loan'){
                                            echo 'another loan';
                                        }else{
                                            echo ' Contributions';
                                        }
                                    }
                                if($post->payment_method){
                                            echo $deposit_options[$post->payment_method].' payment to ';
                                        } 
                                        if($post->account_id):
                                            echo $accounts[$post->account_id];
                                        endif;
                                }?></td>
                            <td class="text-right"><?php if($post->transaction_type==5){echo '('.number_to_currency($post->amount).')';}else{echo number_to_currency($amount = $post->amount);}?></td>
                            <td class="text-right"><strong><?php if($post->transaction_type==5){}else{echo number_to_currency(($payable-$total_amount));}?></strong></td>
                        </tr>
                    <?php endforeach;?>
                    <tfoot>
                        <tr>
                            <td colspan="3">Totals</td>
                            <td class='text-right'><?php echo number_to_currency($total_paid); ?></td>
                            <td class='text-right'><?php if($payable-$total_paid==0){echo number_to_currency(0);}else{echo number_to_currency(($payable-$total_paid));} ?></td>
                        </tr>
                    </tfoot>
                </tbody>
            </table>
        </div>

        <div class="col-md-8 col-md-offset-2 text-center">
                © 2013 - <?php echo date('Y');?>. This statement was issued with no alteration <br/><br/>

                <strong>Powered by :</strong><br/>

            <img src="<?php echo site_url('uploads/logos/'.$this->application_settings->paper_footer_logo);?>" alt="" class='group-logo-footer image-responsive' /> 
        </div> 
    </div>
    <div class="row">
        <div class="col-xs-12">
            <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> Print</a>
            &nbsp;&nbsp;&nbsp;
            <?php $search_string = substr(basename($_SERVER['REQUEST_URI']),strpos(basename($_SERVER['REQUEST_URI']), "?"));?>
            <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().'/'.TRUE.$search_string;?>" target="_blank"><i class='fa fa-file'></i> 
                        <?php
                            $default_message='Generate PDF';
                            $this->languages_m->translate('generate_pdf',$default_message);
                        ?>
            </a>
        </div>
    </div>
</div>
