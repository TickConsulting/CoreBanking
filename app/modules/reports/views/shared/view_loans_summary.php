<html>
    <head>
        <link href="<?php echo site_url();?>assets/styling/style.css" rel="stylesheet" type="text/css" />
        <style>
            th {padding: 15px 0px 15px 0px;}
            td{padding: 7px 0px 7px 0px;}
        </style>
    </head>
    <body>
        <div class="col-xs-12">
            <div class="container">
                <div class="row">
                    <div class="invoice-content-2 bordered document-border">
                        <div class="row invoice-head">
                            <div class="col-xs-4">
                                <div class="invoice-logo">
                                    <img src="<?php echo $group_logo;?>" alt="" class='group-logo image-responsive' /> 
                                </div>
                            </div>
                            <div class="col-xs-7 text-right" style="font-size: 12px;">
                                <div class="company-address">
                                    <span class="bold uppercase group-name"><?php echo $group->name; ?></span><br/>
                                    <?php echo nl2br($group->address); ?>
                                    <span class="bold">Telephone: </span> <?php echo $group->phone; ?>
                                    <br/>
                                    <span class="bold">E-mail Address: </span> <?php echo $group->email; ?>
                                    <br/>
                                </div>
                            </div>
                        </div>
                        <hr style="width:100%"/>
                        <div class="row" style="padding: 15px 0;">
                            <div class="col-md-6 summary-details" style="font-size: 12px;">
                                <h4 class="bold text-primary">Loan Details</h4>
                                <span class="bold">Total Loaned Amount : </span> <?php echo $group_currency;?> <?php echo number_to_currency($total_loan_out)?><br/>
                                <span class="bold">Total Repaid Amount : </span> <?php echo $group_currency;?> <?php echo number_to_currency($total_loan_paid)?><br/>
                                <span class="bold">Total Loan Arrears : </span> <?php echo $group_currency;?> <?php echo number_to_currency($total_loan_out-$total_loan_paid)?><br/>
                            </div>
                        </div>
                        <div class="row invoice-body">
                            <?php if(!empty($posts)){ ?>
                                <div class="col-xs-12 table-responsive ">
                                    <table class="table bpmTopnTailC table-hover table-striped table-condensed table-statement table-header-fixed">
                                        <thead>
                                            <tr>
                                                <th class="invoice-title" width="2%">#</th>
                                                <th class="invoice-title  text-left" width="15%">Member</th>
                                                <th class="invoice-title  text-left" width="13%">Loan Duration</th>
                                                <th class="invoice-title  text-right" width="10%">Amount Loaned</th>
                                                <th class="invoice-title  text-right" width="10%">Interest</th>
                                                <th class="invoice-title  text-right" width="10%">Amount Paid</th>
                                                <th class="invoice-title  text-right" width="10%">Arrears</th>
                                                <th class="invoice-title  text-right" width="10%">Profits</th>
                                                <th class="invoice-title  text-right" width="13%">Outstanding Profits</th>
                                                <th class="invoice-title  text-right" width="8%">Projected Profits</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php   $i=0;
                                                    $total_loan=0;
                                                    $total_interest=0;
                                                    $total_paid=0;
                                                    $total_balance=0;
                                                    $total_projected=0;
                                                    $total_outstanding_profit=0;
                                                    $total_profits=0;
                                                    foreach ($posts as $posts_key => $posts_value) {
                                                        $posts_array[$posts_key] = $posts_value;
                                                    }
                                                    foreach ($amount_payable_to_date as $amount_payable_to_date_key => $amount_payable_to_date_value) {
                                                        $amount_payable_to_date_array[$amount_payable_to_date_key] = $amount_payable_to_date_value;
                                                    }
                                                    foreach ($amount_paid as $amount_paid_key => $amount_paid_value) {
                                                        $amount_paid_array[$amount_paid_key] = $amount_paid_value;
                                                    }
                                                    foreach ($projected_profit as $projected_profit_key => $projected_profit_value) {
                                                       $projected_profit_array[$projected_profit_key] = $projected_profit_value;
                                                    }
                                                    foreach ($members as $members_key => $members_value) {
                                                        $members_array[$members_key] = $members_value;
                                                    }
                                                    foreach ($posts_array as $post):
                                                        if(isset($members_array[$post->member_id]) && $members_array[$post->member_id]):

                     
                                                    $total_amount_payable_to_date=$amount_payable_to_date_array[$post->id]->todate_amount_payable?:0;
                                                    $principle_payable_todate = $amount_payable_to_date_array[$post->id]->todate_principle_payable?:0;
                                                    if((round($total_amount_payable_to_date-$amount_paid_array[$post->id])) <= 0)
                                                    {
                                                        $intere = $total_amount_payable_to_date - $principle_payable_todate;
                                                        $overpayments = $amount_paid_array[$post->id] - $total_amount_payable_to_date;
                                                        if($overpayments<0)
                                                        {
                                                            $overpayments = '';
                                                        }
                                                        $due_inter = '';
                                                        $pen = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                                                        if($pen>0)
                                                        {
                                                            $penalty = $pen;
                                                        }
                                                        else
                                                        {
                                                            $penalty = 0;
                                                        }
                                                    }  
                                                    else
                                                    {
                                                        //$intere = $post->total_amount_paid-$payable_to_date;
                                                        $intere = '';
                                                        //$due_inter = $post->total_amount_paid-$prin_payable;
                                                        $overpayments = '';
                                                        $penalty = ($post->total_amount_payable) - ($post->total_interest_payable+$post->total_principle_payable);
                                                    } ?>
                                                <tr>
                                                    <td width="2%"><?php echo ++$i;?>
                                                    </td>
                                                    <td class="text-left" width="20%"><?php echo $members_array[$post->member_id];?></td>
                                                    <td class="text-left" width="12%"><?php echo timestamp_to_date($post->disbursement_date).' - '.timestamp_to_date($post->loan_end_date);?></td>
                                                    <td class="text-right"><?php echo number_to_currency($loan = $post->loan_amount);?></td>
                                                    <td class="text-right"><?php echo number_to_currency($interest = $post->total_interest_payable);?></td>
                                                    <td class="text-right"><?php echo number_to_currency($paid = $amount_paid_array[$post->id]);?></td>
                                                    <td class="text-right "><span class="tooltips" data-original-title="Interest Breakdown" data-content="Overpayment : <?php echo number_to_currency($overpayments); ?> , Penalties : <?php echo number_to_currency($penalty); ?>"><?php echo number_to_currency($balance = $post->total_amount_payable - $paid);?></span></td>
                                                    <td class="text-right"><?php echo number_to_currency($profit = $projected_profit_array[$post->id]);?></td>
                                                    <td class="text-right"><?php 
                                                                $outstanding_profit = round(($post->total_interest_payable+$penalty)-$profit);
                                                                echo number_to_currency($outstanding_profit); 
                                                    ?>
                                                    </td>
                                                    <td class="text-right">
                                                         <?php 
                                                            $projected_profits = $post->total_interest_payable+$penalty;
                                                            echo number_to_currency($projected_profits);  
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php   $total_loan+=$loan; 
                                                    $total_interest+=$interest;
                                                    $total_paid+=$paid;
                                                    $total_balance+=$balance; 
                                                    $total_profits+=$profit; 
                                                    $total_projected+=$projected_profits; 
                                                    $total_outstanding_profit+=$outstanding_profit;
                                                    endif;
                                                    endforeach ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3">Totals</td>
                                                <td class="text-right"><?php echo number_to_currency($total_loan);?></td>
                                                <td class="text-right"><?php echo number_to_currency($total_interest);?></td>
                                                <td class="text-right"><?php echo number_to_currency($total_paid);?></td>
                                                <td class="text-right"><?php echo number_to_currency($total_balance);?></td>
                                                <td class="text-right"><?php echo number_to_currency($total_profits);?></td>
                                                <td class="text-right"><?php echo number_to_currency($total_outstanding_profit);?></td>
                                                <td class="text-right"><?php echo number_to_currency($total_projected);?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div> 
                            <?php }else{ ?>
                                <div class="col-xs-12 margin-bottom-10 ">
                                    <div class="alert alert-info">
                                        <h4 class="block">Information! No records to display</h4>
                                        <p>
                                            No loan records to display.
                                        </p>
                                    </div>
                                </div>
                            <?php } ?> 
                        </div>
                        <div class="row">
                            <div class="col-md-12 margin-top-60 text-center">
                                <h6 class="powered-by">Powered by</h6><br/>
                                 <div class="invoice-logo-footer">
                                    <img src="<?php echo $chamasoft_settings->protocol.$chamasoft_settings->url;?>/uploads/logos/<?php echo $chamasoft_settings->paper_footer_logo; ?>" alt="" class='report-group-logo-footer image-responsive' /> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
