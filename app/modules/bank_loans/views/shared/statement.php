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
                <span class="bold">Telephone: </span> <?php echo $this->group->phone; ?>
                <br/>
                <span class="bold">E-mail Address: </span> <?php echo $this->group->email; ?>
                <br/>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row invoice-cust-add margin-bottom-20">
        <div class="col-md-8 col-xs-12">
            <h4>Loan Details:</h4>
            <span class="bold">Description : </span> <?php echo $loan->description;?><br/>
            <span class="bold">Amount Received : </span> <?php echo $this->group_currency.' '.number_to_currency($loan->amount_loaned); ?><br/>
            <span class="bold">Total Payable : </span> <?php echo $this->group_currency.' '.number_to_currency($loan->total_loan_amount_payable);?><br/>
            <span class="bold">Loan Balance : </span> <?php echo $this->group_currency.' '.number_to_currency($loan->loan_balance);?><br/>
            <span class="bold">Disbursement Date : </span> <?php echo timestamp_to_date($loan->loan_start_date); ?><br/>
            <span class="bold">Loan End Date : </span> <?php echo timestamp_to_date($loan->loan_end_date); ?><br/>
        </div>
        <div class="col-md-4 col-xs-12">
        </div>
    </div>
    <hr/>
    <div class="row invoice-body">
        <div class="col-xs-12 table-responsive ">
            <table class="table m-table m-table--head-separator-primary">
                <thead>
                    <tr>
                        <th class="invoice-title" width="15%">Type</th>
                        <th class="invoice-title" >Date</th>
                        <th class="invoice-title ">Description</th>
                        <th class="invoice-title  text-right">Amount Paid (<?php echo $this->group_currency; ?>)</th>
                        <th class="invoice-title  text-right">Balance(<?php echo $this->group_currency; ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan=3><strong>Total Balance Amount Payable</strong></td>
                        <td></td>
                        <td class="text-right"><strong><?php echo number_to_currency($payable = $loan->balance);?></strong></td>
                    </tr>
                    <?php $amount = 0;$total_amount=0; foreach($posts as $post): $total_amount+=$post->amount;?>
                        <tr>
                            <td>Payment</td>
                            <td><?php echo timestamp_to_date($post->receipt_date);?></td>
                            <td><?php echo $post->description;?></td>
                            <td class="text-right"><?php echo number_to_currency($amount = $post->amount);?></td>
                            <td class="text-right"><strong><?php echo number_to_currency($payable-$total_amount);?></strong></td>
                        </tr>
                    <?php endforeach;?>
                    <tfoot>
                        <tr>
                            <td colspan="3">Totals</td>
                            <td class='text-right'><?php echo number_to_currency($total_amount); ?></td>
                            <td class='text-right'><?php echo number_to_currency($loan->loan_balance); ?></td>
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
            <a class="btn btn-sm btn-info hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> Print</a>
        </div>
    </div>
</div>
