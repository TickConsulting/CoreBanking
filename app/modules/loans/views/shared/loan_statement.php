<?php if(isset($pdf_true) && $pdf_true){?>
    <link href="<?php echo site_url();?>assets/styling/style.css" rel="stylesheet" type="text/css" /> 
    <link href="<?php echo site_url();?>templates/admin_themes/groups/css/custom.css" rel="stylesheet" type="text/css" /> 
    <style type="text/css">
        .filter_header,.print_layout{
            display: none;
        }
        #statement_paper .table td {
            font-size: 9px;
            padding: .25rem;
        }
        .statement-header-content {
            font-size: 9px;
        }
        #statement_header,.header_paper,.contribution_name,#statement_paper .table th,#statement_footer{
            font-size: 10px;
        }
        #statement_paper {
            padding: none;
            box-shadow: none;
        }
        .pdf_layout{
            display: none;  
        }
    </style>
<?php } ?>

<div class="">
    <div class="container">
        <div class="row">
            <div class="col-md-12 pt-4">
                <div id="statement_paper">
                    <div class="row" id="statement_header">
                        <div class="col-xs-6 col-sm-6">
                            <div class="invoice-logo">
                                <img src="<?php echo is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo); ?>" alt="" class='group-logo image-responsive' /> 
                            </div>
                        </div> 
                        <div class="col-sm-6 text-right" >
                            <div class="company-address">
                                <span class="bold uppercase"><?php echo $this->group->name; ?></span>
                                <?php echo nl2br($this->group->address); ?><br/>
                                <span class="bold">Telephone: </span> <?php echo $this->group->phone; ?>
                                <br/>
                                <span class="bold">E-mail Address: </span> <?php echo $this->group->email; ?>
                                <br/>
                            </div>
                        </div>
                    </div>
                    <div class="row header_paper">
                        <div class="col-xs-7 col-sm-7">
                            <h4>Loan Details:</h4>
                            <div>
                                <span class="bold">Amount Borrowed : </span> <?php echo $this->group_currency.' '.number_to_currency($loan->loan_amount); ?><br/>
                                <span class="bold">Total Payable : </span> <?php echo $this->group_currency.' '.number_to_currency($payable =$total_installment_payable+$total_fines+$total_transfers_out);?><br/>
                                <span class="bold">Total Fine Amount : </span> <?php echo $this->group_currency.' '.number_to_currency($total_fines);?><br/>
                                <span class="bold">Total Transfers Out : </span> <?php echo $this->group_currency.' '.number_to_currency($total_transfers_out);?><br/>
                                <span class="bold">Lump sum remaining : </span> <?php if($lump_sum_remaining>0) {echo $this->group_currency.' '.number_to_currency($lump_sum_remaining);} else {echo $this->group_currency.' '.number_to_currency(0);}?><br/>
                                <span class="bold">Total Amount Paid : </span> <?php echo $this->group_currency.' '.number_to_currency($total_paid);?><br/>
                                <span class="bold">Disbursement Date : </span> <?php echo timestamp_to_date($loan->disbursement_date); ?><br/>
                                <span class="bold">Loan End Date : </span> <?php echo timestamp_to_date($loan->loan_end_date); ?><br/>
                                <span class="bold">Loan Interest Rate: </span> <?php echo $loan->interest_rate;?>% 
                                <?php echo $loan->loan_interest_rate_per?$loan_interest_rate_per[$loan->loan_interest_rate_per]:
                                $loan_interest_rate_per[4];?> on 

                                <?php echo $interest_types[$loan->interest_type];?><br/>
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
                                     <span class="bold">
                                            <?php
                                                $default_message='Loan Duration';
                                                $this->languages_m->translate('loan_duration',$default_message);
                                            ?>
                                     : </span><?php echo $loan->repayment_period;?> Months <br/>
                                    <?php
                                        if($loan->grace_period == 'date'){
                                            echo '<span class="bold">Loan Grace Period End Date: </span>'.timestamp_to_date($loan->grace_period_end_date);
                                        }else{
                                            echo '<span class="bold">Loan Grace Period: </span>'.$loan_grace_periods[$loan->grace_period];
                                        }
                                    ?>
                                    <br>
                                    <span class="bold">Loan Fine Deferment: </span>
                                        <?php if($loan->enable_loan_fine_deferment){
                                            echo '<span class="label label-xs label-default">Deferment Active</span>'.'<br/>';
                                        }else{
                                            echo '<span class="label label-xs label-primary">Deferment Inactive</span>'.'<br/>';
                                        }?>
                                    <span class="bold">Loan Status: </span><?php if($loan->is_fully_paid){
                                            echo '<span class="label label-xs label-primary">Fully Paid</span>'.'<br/>';
                                        }else{
                                            echo '<span class="label label-xs label-default">Payment in progress</span>'.'<br/>';
                                        }?>
        
                                <br>
                            </div>
                        </div>
                        <div class="col-md-5 text-right">
                            <div>
                                <strong>Disbursed To: </strong> <?php echo $loan->first_name.' '.$loan->last_name; ?>
                                <br>
                                <strong>Phone: </strong> <?php echo $loan->phone; ?>
                                <br>
                                <strong>Email Address: </strong> <?php echo $loan->email; ?>
                                <br>
                                <strong>Member Number: </strong> <?php echo $loan->email;?>
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if(empty($posts)){ ?>
                                <div class="col-md-12 margin-bottom-10 ">
                                    <div class="m-alert m-alert--outline alert alert-info fade show mr-3" role="alert">
                                        <strong><?php echo translate('No Records');?>!</strong>
                                        <br/><br/>
                                        <?php echo translate('No transaction records to display')?>
                                    </div>
                                </div>
                            <?php }else{ ?>
                                <div class="col-xs-12 table-responsive p-3">
                                    <table class="table table-hover table-striped table-condensed table-statement">
                                        <thead>
                                            <tr>
                                                <th nowrap width="20%" class="text-left">Type</th>
                                                <th nowrap width="15%" class="text-left">Date</th>
                                                <th nowrap width="40%" class="text-left">Description</th>
                                                <th nowrap width="10%" class="text-right">Amount Paid (<?php echo $this->group_currency; ?>)</th>
                                                <th nowrap width="15%" class="text-right">Balance (<?php echo $this->group_currency; ?>)</th>
                                                <!-- <th class="invoice-title nowrap">

                                                    <?php echo translate('Type'); ?>
                                                </th>
                                                <th class="invoice-title nowrap ">
                                                    <?php echo translate('Date'); ?>
                                                </th>
                                                <th class="invoice-title nowrap  ">
                                                    <?php echo translate('Description'); ?>
                                                </th>
                                                <th class="invoice-title nowrap ">
                                                    <?php echo translate('Amount Paid'); ?>
                                                    (<?php echo $this->group_currency; ?>)</th>
                                                <th class="invoice-title nowrap  ">
                                                    <?php echo translate('Balance'); ?>
                                                    (<?php echo $this->group_currency; ?>)</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td nowrap><?php echo translate('Total Amount Payable'); ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-right"><?php echo number_to_currency($payable-$total_transfers_out);?></td>
                                            </tr>
                                            <?php $amount = 0;$total_amount=0;$remaining_payable=0; foreach($posts as $post): 
                                                if($post->transaction_type!=5){$total_amount+=$post->amount;}?>
                                                <tr>
                                                    <td nowrap ><?php if($post->transaction_type==5){
                                                        echo 'Contibution Transfer';
                                                        }else{echo 'Payment';}?></td>
                                                    <td nowrap ><?php echo timestamp_to_date($post->transaction_date);?></td>
                                                    <td nowrap ><?php if($post->transaction_type==5){
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
                                                                //echo $post->transaction_type;
                                                            }
                                                        if($post->payment_method){
                                                            echo $deposit_options[$post->payment_method].' payment to ';
                                                        } 
                                                        if($post->account_id):
                                                            $account = isset($accounts[$post->account_id])?$accounts[$post->account_id]:'';
                                                            echo $account;
                                                        endif;
                                                        }?></td>
                                                    <td nowrap class="text-right"><?php if($post->transaction_type==5){echo '('.number_to_currency($post->amount).')';}else{echo number_to_currency($amount = $post->amount);}?></td>
                                                    <td nowrap class="text-right"><strong><?php if($post->transaction_type==5){}else{echo number_to_currency(($payable-$total_amount));}?></strong></td>
                                                </tr>
                                            <?php endforeach;?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3">Totals</td>
                                                <td class="text-right" ><?php echo number_to_currency($total_paid-$total_transfers_out); ?></td>
                                                <td class="text-right"><?php if($payable-$total_paid==0){echo number_to_currency(0);}else{echo number_to_currency(($payable-$total_paid));} ?>
                                                    
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div> 
                            <?php } ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div id="statement_footer" >
                        <p style="text-align:center;">© <?php echo date('Y')?> . This statement was issued with no alteration </p>
                        <p style="text-align:center;">
                            <strong>Powered by:</strong>
                            <br>
                            <img width="150px" src="<?php echo site_url('uploads/logos/'.$this->application_settings->paper_header_logo);?>" alt="<?php echo $this->application_settings->application_name;?> Logo" ?="">
                        </p>
                    </div>

                </div>
            </div>
            <div class="clearfix"></div>
            <hr>
        </div>
    </div>    
    <div class="row print_layout mt-3">
        <div class="col-xs-12">
            <button class="btn btn-sm btn-info hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> 
                <?php echo translate('Print'); ?>
            </button>
            &nbsp;&nbsp;&nbsp;
            <?php $search_string = substr(basename($_SERVER['REQUEST_URI']),strpos(basename($_SERVER['REQUEST_URI']), "?"));
            ?>
            <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().'/'.TRUE;?>" target="_blank"><i class='fa fa-file'></i>
                <?php echo translate('Generate PDF'); ?>
            </a>
        </div>
    </div>
</div>

<div class="row pdf_layout">
    
</div>
<script type="text/javascript">
    $(document).ready( function(){
        $(".m-select2-search").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
            width: "100%"
        });
        $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
    });
</script>