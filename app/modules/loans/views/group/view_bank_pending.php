<div class="invoice-content-2 bordered document-border">
    
    <div class="row invoice-cust-add margin-bottom-20">
        <div class="col-md-7 col-xs-6">
            <h4>Group Details:</h4>
            <span class="bold">Group Name : </span> <?php echo ucfirst($this->group->name) ?><br/>
            <span class="bold">Telephone: </span> <?php echo $this->group->phone;?><br/>
            <span class="bold">Email Address: </span>
            <?php 
                echo $this->group->email
            ?>
        </div>
        <div class="col-md-5 col-xs-6">
            <h4>Member Details: </h4>
            <span class="bold">Disbursed To:</span> <?php echo $loan_applicant->first_name.' '.$loan_applicant->last_name; ?><br/>
            <span class="bold">
                        <?php
                            $default_message='Phone';
                            $this->languages_m->translate('phone',$default_message);
                        ?>
            :</span> <?php echo $loan_applicant->phone; ?><br/>
            <span class="bold">
                        <?php
                            $default_message='Email Address';
                            $this->languages_m->translate('email_address',$default_message);
                        ?>
                :</span> <?php echo $loan_applicant->email; ?>
        </div>
    </div>
    <hr/>
    <div class="row invoice-body">
        <?php if(!empty($posts)){ ?>
            <div class="col-xs-12 table-responsive ">
                <table class="table table-hover table-striped table-condensed table-statement">
                    <thead>
                        <tr>
                            <th class="invoice-title" >
                                <?php
                                    $default_message='Loan Name';
                                    $this->languages_m->translate('loan_name',$default_message);
                                ?>

                            </th>
                            <th class="invoice-title ">
                               <?php
                                    $default_message='Loan Details';
                                    $this->languages_m->translate('loan_details',$default_message);
                                ?>
                            </th>
                            <th class="invoice-title ">
                                <?php
                                    $default_message='Amount';
                                    $this->languages_m->translate('amount',$default_message);
                                ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                               <?php echo ucfirst($loan_type->name); ?> 
                            </td>
                            <td>
                                <span class="bold">Amount Borrowed: </span> <?php echo $this->group_currency.' '.number_to_currency($posts->loan_amount); ?><br/>
                                <span class="bold">Loan Interest Rate: </span> <?php echo $loan_type->interest_rate;?>% <?php echo $loan_type->loan_interest_rate_per?$loan_interest_rate_per[$loan_type->loan_interest_rate_per]:$loan_type->loan_interest_rate_per[4]?> on <?php echo $interest_types[$loan_type->interest_type];?><br/>
                                <span class="bold">Loan Duration: </span>
                                <?php if($loan_type->loan_repayment_period_type == 1){
                                    echo $loan_type->fixed_repayment_period;?> Months <br/>
                                <?php }else if($loan_type->loan_repayment_period_type == 2){
                                     echo $loan_type->minimum_repayment_period .'-'.  $loan_type->maximum_repayment_period ?> Months <br/><?php
                                }?>
                                <span class="bold">Loan Status: </span> 
                                    <?php if($loan_type->active==1):?>
                                        <span class="label label-xs label-success"> Active</span>
                                    <?php else:?>
                                        <span class="label label-xs label-danger">Inactive</span>
                                    <?php endif;?>
                                    <br/>
                                    <?php if($loan_type->enable_loan_processing_fee):?>
                                        <span class="bold">Loan Processing fee: </span> 
                                            <?php if($loan_type->loan_processing_fee_type==1){
                                                echo 'Fixed Amount of '.$this->group_currency.' '.number_to_currency($loan_type->loan_processing_fee_fixed_amount).'</br>';
                                            }else{
                                                echo $loan_type->loan_processing_fee_percentage_rate.'% of '.$loan_processing_fee_percentage_charged_on[$loan->loan_processing_fee_percentage_charged_on].'<br/>';
                                                }?>
                                    <?php endif;?>
                                    <?php if($loan_type->enable_loan_fines):?>
                                        <span class="bold">Late Payment Fine: </span>
                                            <?php if($loan_type->loan_fine_type==1){
                                                echo $this->group_currency.' '.number_to_currency($loan_type->fixed_fine_amount).' fine '.$late_payments_fine_frequency[$loan_type->fixed_amount_fine_frequency].' on ';
                                                echo isset($fixed_amount_fine_frequency_on[$loan_type->fixed_amount_fine_frequency_on])?$fixed_amount_fine_frequency_on[$loan_type->fixed_amount_fine_frequency_on]:'';
                                                echo '<br/>';
                                            }else if($loan_type->loan_fine_type==2){
                                                echo $loan_type->percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan_type->percentage_fine_frequency].' on '.$percentage_fine_on[$loan_type->percentage_fine_on].'<br/>';
                                            }else if($loan_type->loan_fine_type==3){
                                                if($loan_type->one_off_fine_type==1){
                                                    echo 'One Off Amount of '. $this->group_currency.' '.number_to_currency($loan_type->one_off_fixed_amount).' per Installment<br/>';
                                                }else if($loan_type->one_off_fine_type==2){
                                                    echo $loan_type->one_off_percentage_rate.'% One of Fine on '.$percentage_fine_on[$loan_type->one_off_percentage_rate_on].' per Installment<br/>';                            }
                                            }
                                            ?>
                                    <?php endif;?>
                                    <?php if($loan_type->enable_outstanding_loan_balance_fines):?>
                                        <span class="bold">Outstanding Loan Balance Fines: </span>
                                        <?php if($loan_type->outstanding_loan_balance_fine_type==1){
                                            echo $this->group_currency.' '.number_to_currency($loan_type->outstanding_loan_balance_fine_fixed_amount).' '.$late_payments_fine_frequency[$loan->outstanding_loan_balance_fixed_fine_frequency].'<br/>';
                                        }else if($loan->outstanding_loan_balance_fine_type==2){
                                            echo $loan->outstanding_loan_balance_percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan_type->outstanding_loan_balance_percentage_fine_frequency].' on '.$percentage_fine_on[$loan->outstanding_loan_balance_percentage_fine_on].'<br/>';
                                        }else{
                                            echo 'One Off Amount '.$this->group_currency.' '.number_to_currency($loan->outstanding_loan_balance_fine_one_off_amount).'<br/>';
                                        }?>
                                    <?php endif;?>
                                    <span class="bold">Loan Fine Deferment: </span><?php if($loan_type->enable_loan_fine_deferment){
                                            echo '<span class="label label-xs label-primary">Active</span>'.'<br/>';
                                        }else{
                                            echo '<span class="label label-xs label-default">Inactive</span>'.'<br/>';
                                        } ?>
                            </td>
                            <td>
                                <?php echo $this->group_currency.' '.number_to_currency($posts->loan_amount); ?>
                            </td>
                        </tr>                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Totals</td>
                            <td></td>
                            <td><?php echo $this->group_currency.' '.number_to_currency($posts->loan_amount); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if ($loan_type->enable_loan_guarantors):?>
                <div class="clearfix"></div>
                <div class="col-xs-12 table-responsive ">
                    <h5>Loan Guarantor Details:</h5>
                    <table class="table table-hover table-striped table-condensed table-statement">
                        <thead>
                            <tr>
                                <th class="invoice-title" width="2%">#</th>
                                <th class="invoice-title ">
                                        <?php
                                            $default_message='Member Name';
                                            $this->languages_m->translate('member_name',$default_message);
                                        ?>
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
                    <table class="table table-hover table-striped table-condensed table-statement">
                        <thead>
                            <tr>
                                <th class="invoice-title ">
                                        <?php
                                            $default_message='Bank Approval :';
                                            $this->languages_m->translate('bank_approval',$default_message);
                                        ?>
                                </th>
                                <th class="invoice-title ">
                                        ____________________________________________________________________
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="row">
                    <?php echo form_open(current_url(),'class="form_submit" role="form"');?>   
                    <?php echo form_hidden('loan_application_id',$posts->id,'');?>
                    <div class="col-xs-12">
                        <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> Print</a>
                        &nbsp;&nbsp;&nbsp;
                        <input type="submit" name="approve"  class="btn blue submit_form_button">Bank Approve Loan</input>
                        <input type="submit" name="decline"  class="btn red submit_form_button">Bank Decline Loan</input>
                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                    </div>
                    <?php form_close() ?>

                </div>
            <?php endif;?>
        <?php }else{ ?>
            <div class="col-xs-12 margin-bottom-10 ">
                <div class="alert alert-info">
                    <h4 class="block">
                        <?php
                            $default_message='Information! No records to display';
                            $this->languages_m->translate('no_records_to_display',$default_message);
                        ?>
                        
                    </h4>
                    <p>
                        No loan invoices to display.
                    </p>
                </div>
            </div>
        <?php } ?> 
    </div>

</div>
