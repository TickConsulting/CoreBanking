<style type="text/css">
    .balance{
        color:#FF0000;
    }
    .nill{
        color:#000000;
    }
    /*.voided-note{
        -ms-transform: rotate(20deg); /* IE 9 */
        -webkit-transform: rotate(-20deg); /* Safari */
        transform: rotate(-20deg);
    }*/
</style>
<div class="invoice-content-2 bordered document-border">
    <div class="row invoice-head">
        <div class="col-md-7 col-xs-6">
            <div class="invoice-logo">
                 <?php echo '<img src="'.site_url('uploads/logos/'.$this->application_settings->paper_header_logo).'" alt="" class="group-logo image-responsive" /> ';?>
            </div>
        </div>
        <div class="col-md-5 col-xs-6 text-right">
            <div class="company-address">
                <span class="bold uppercase invoice-number"><?php echo $post->billing_invoice_number; ?></span><br/>
            </div>
        </div>
    </div>
    <div class="row invoice-cust-add margin-bottom-20">
        <div class="col-md-7 col-xs-6">
            <h4 class="invoice-title">Group Details</h4>
            <span class="bold">
                <?php
                    $default_message='Name';
                    $this->languages_m->translate('name',$default_message);
                ?>
            :</span> <?php echo $group->name; ?><br/>
            <span class="bold">Sent to:</span> <?php echo $group->first_name.' '.$group->last_name; ?><br/>
            <span class="bold">
                <?php
                    $default_message='Phone';
                    $this->languages_m->translate('phone',$default_message);
                ?>
            :</span> <?php echo $group->phone; ?><br/>
            <span class="bold">
                <?php
                    $default_message='Email';
                    $this->languages_m->translate('email',$default_message);
                ?>
            :</span> <?php echo $group->email; ?><br/>
        </div>
        <div class="col-md-5 col-xs-6 text-right">
            <h4 class="invoice-title">Date Particulars</h4>
            <span class="bold">Invoice Date:</span> <?php echo timestamp_to_receipt($post->billing_date) ?><br/>
            <span class="bold">
                <?php
                    $default_message='Due Date';
                    $this->languages_m->translate('due_date',$default_message);
                ?>
            :</span> <?php echo timestamp_to_receipt($post->due_date) ?><br/>
            <span class="bold">
                <?php
                    $default_message='Sent On';
                    $this->languages_m->translate('sent_on',$default_message);
                ?>
            :</span> <?php echo timestamp_to_receipt($post->created_on) ?><br/>
        </div>
    </div>
    <div class="row invoice-body">
        <div class="col-xs-12 table-responsive table-condensed">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="30%" class="invoice-title uppercase">
                <?php
                    $default_message='Description';
                    $this->languages_m->translate('description',$default_message);
                ?>
                        </th>
                        <th width="30%" class="invoice-title uppercase text-right">
                <?php
                    $default_message='Subscription';
                    $this->languages_m->translate('subscription',$default_message);
                ?>
                            (<?php echo $this->group_currency; ?>)</th>
                        <th class="invoice-title uppercase text-right">Tax(<?php echo $this->group_currency; ?>)</th>
                        <th class="invoice-title uppercase text-right">Total(<?php echo $this->group_currency; ?>)</th>
                        <th class="invoice-title uppercase text-right">
                <?php
                    $default_message='Amount Paid';
                    $this->languages_m->translate('Amount Paid',$default_message);
                ?>
                            (<?php echo $this->group_currency; ?>)</th>
                        <th class="invoice-title uppercase text-right">

                <?php
                    $default_message='Balance';
                    $this->languages_m->translate('balance',$default_message);
                ?>
                            (<?php echo $this->group_currency; ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span class=""><?php echo 'Group '.$billing_cycles[$post->billing_cycle].' Subscription invoice'; ?></span>
                        </td>
                        <td class="text-right"><?php
                            echo $billing_cycles[$post->billing_cycle].' amount: '.number_to_currency($post->amount - $post->tax-$post->prorated_amount).'<br/>';
                            echo 'Prorated amount: '.number_to_currency($post->prorated_amount);
                        ?></td>
                        <td class="text-right"><?php echo number_to_currency($post->tax); ?></td>
                        <td class="text-right"><?php echo number_to_currency($post->amount); ?></td>
                        <td class="text-right"><?php echo number_to_currency($post->amount_paid); ?></td>
                        <td class="text-right sbold"><?php echo number_to_currency($balance = $post->amount - $post->amount_paid); ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="note">
                <span class="sbold ">Note :</span> <?php if($balance>0){?>
                    Outstanding balance is <span class="balance"><?php echo $this->group_currency.'. '.number_to_currency($balance);?></span><br/>
                    Kindly make timely payment.
                <?php }else{?>
                    There are no arrears. 
                <?php }?>
            </div>
            <?php if(preg_match('/(eazzy)/',$_SERVER['HTTP_HOST'])||preg_match('/(sandbox)/',$_SERVER['HTTP_HOST'])){?>
                    <div class="col-md-12">
                        <ul class="media-list">
                            <li class="media">
                                <div class="media-body">
                                    <h4 class="media-heading">Your <span class="font-red">"<?php echo $this->application_settings->application_name; ?> Group Number"</span></h4>
                                    <p> <span  style="font-size:20px"><?php echo $this->group->account_number; ?></span><br/>You will use this when paying for your <?php echo $this->application_settings->application_name; ?> subscriptions or when communicating with <?php echo $this->application_settings->application_name; ?> support to identify your group</p>
                                    <!-- Nested media object -->
                                    <h4 class="media-heading">How to pay for your group subscription</h4>
                                    <div class="media">
                                        <div class="media-body">
                                            <h5 class="media-heading">Equitel SIM Card</h5>
                                            <ul> 
                                                <li>
                                    <?php
                                        $default_message='Go to Equitel toolkit on your phone';
                                        $this->languages_m->translate('equitel_sim_toolkit',$default_message);
                                    ?>
                                                </li>
                                                <li>
                                    <?php
                                            $default_message='Then go to My Money';
                                            $this->languages_m->translate('go_to_my_money',$default_message);
                                    ?>
                                                </li>
                                                <li>
                                    <?php
                                            $default_message='Then go to Send/Pay';
                                            $this->languages_m->translate('go_to_send',$default_message);
                                    ?>
                                                </li>
                                                <li>

                                    <?php
                                            $default_message='Then go to pay bill and enter '. $this->application_settings->application_name.' Pay bill Number - 967600 and confirm';
                                            $this->languages_m->translate('pay_bill_and_confirm',$default_message);
                                    ?>
                                                    </li>
                                                <li>
                                    <?php
                                            $default_message='Enter your '.$this->application_settings->application_name.' group number - <strong>'.$this->group->account_number.'</strong> and confirm';
                                            $this->languages_m->translate('enter_chamasoft_group_number',$default_message);
                                    ?>
                                                </li>
                                                <li>
                                    <?php
                                            $default_message='Enter the Amount to pay - (Amount Quoted on invoice ) and confirm';
                                            $this->languages_m->translate('enter_amount_to_pay',$default_message);
                                    ?>
                                                </li>
                                                <li>

                                    <?php
                                            $default_message='Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment)';
                                            $this->languages_m->translate('confirm_details_entered',$default_message);
                                    ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="media">
                                        <div class="media-body">
                                            <h5 class="media-heading">
                                    <?php
                                            $default_message='Eazzy Banking App';
                                            $this->languages_m->translate('eazzy_banking_app',$default_message);
                                    ?>
                                            </h5>
                                            <ul> 
                                                <li>
                                    <?php
                                            $default_message='Go To Paybill Option';
                                            $this->languages_m->translate('go_to_pay_bill_option',$default_message);
                                    ?>
                                                </li>
                                                <li>
                                    <?php
                                            $default_message='Enter Eazzychama Pay bill number - 967600 and confirm.';
                                            $this->languages_m->translate('chamasoft_paybill_number',$default_message);
                                    ?>
                                                </li>
                                                <li>
                                    <?php
                                            $default_message='Enter your '.$this->application_settings->application_name.' group number - <strong>'.$this->group->account_number.'</strong>';
                                            $this->languages_m->translate('enter_chamasoft_group_number',$default_message);
                                    ?>
                                                    </li>
                                                <li>
                                    <?php
                                            $default_message='Enter the Amount to pay - (Amount Quoted on invoice) and confirm.';
                                            $this->languages_m->translate('enter_amount_to_pay',$default_message);
                                    ?>
                                                </li>
                                                <li>
                                    <?php
                                            $default_message='Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment) ';
                                            $this->languages_m->translate('confirm_details_entered',$default_message);
                                    ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="media">
                                        <div class="media-body">
                                            <h5 class="media-heading">Over the counter payment</h5>
                                            <ul> 
                                                <li>Visit branch deposit to account number 0006500504435 (Amount Quoted on invoice) and confirm.</li>
                                                <li>Indicate your <?php echo $this->application_settings->application_name; ?> group number <strong><?php echo $this->group->account_number; ?></strong></li>
                                                <li>You will be issued with a receipt</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
            <?php }else{ ?>
                <div class="col-md-12">
                    <ul class="media-list">
                        <li class="media">
                            <div class="media-body">
                                <h4 class="media-heading">Your <?php echo $this->application_settings->application_name; ?> Group Number</h4>
                                <p> <span  style="font-size:20px"><?php echo $this->group->account_number; ?></span><br/>

                                    <?php
                                        $default_message='You will use this when paying for your '.$this->application_settings->application_name.' subscriptions or when communicating with '.$this->application_settings->application_name.' support to identify your group';
                                        $this->languages_m->translate('identify_your_group',$default_message);
                                    ?>

                                    </p>
                                <!-- Nested media object -->
                                <h4 class="media-heading">
                                <?php
                                    $default_message='How to pay for your group subscription';
                                    $this->languages_m->translate('how_to_pay_for_group_subscription',$default_message);
                                ?>
                                </h4>
                                <div class="media">
                                    <div class="media-body">
                                        <h5 class="media-heading">
                                            
                                <?php
                                    $default_message='MPesa';
                                    $this->languages_m->translate('mpesa',$default_message);
                                ?>
                                        </h5>
                                        <ul> 
                                            <li>
                                <?php
                                    $default_message='Go to MPesa on your phone toolkit';
                                    $this->languages_m->translate('go_to_mpesa',$default_message);
                                ?>
                                            </li>
                                            <li>
                                <?php
                                    $default_message='Then go to Lipa na MPesa';
                                    $this->languages_m->translate('go_to_lipa_na_mpesa',$default_message);
                                ?>
                                            </li>
                                            <li>
                                <?php
                                    $default_message='Then go to Paybill';
                                    $this->languages_m->translate('go_to_paybill',$default_message);
                                ?>

                                            </li>
                                            <li>
                                <?php
                                    $default_message='Then go to pay bill and enter '.$this->application_settings->application_name.' Pay bill Number - 967600 and confirm';
                                    $this->languages_m->translate('confirm_paybill',$default_message);
                                ?>
                                                </li>
                                            <li>
                                <?php
                                    $default_message='Enter group account number - <strong>'.$this->group->account_number.'</strong> and confirm';
                                    $this->languages_m->translate('enter_group_account_number',$default_message);
                                ?>
                                                </li>
                                            <li>

                                <?php
                                    $default_message='Enter the Amount to pay - (Amount Quoted on invoice ) and confirm';
                                    $this->languages_m->translate('enter_amount_to_pay',$default_message);
                                ?>
                                    
                                            </li>
                                            <li>
                                <?php
                                    $default_message='Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment) ';
                                    $this->languages_m->translate('confirm_details',$default_message);
                                ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="media">
                                    <div class="media-body">
                                        <h5 class="media-heading">
                                            
                                <?php
                                    $default_message='Equitel Sim Card';
                                    $this->languages_m->translate('equitel_sim_card',$default_message);
                                ?>
                                        </h5>
                                        <ul> 
                                            <li>
                                <?php
                                    $default_message='Go to Equitel Sim toolkit on your phone';
                                    $this->languages_m->translate('go_to_equitel_toolkit',$default_message);
                                ?>
                                            </li>
                                            <li>
                                <?php
                                    $default_message='Then go to My Money';
                                    $this->languages_m->translate('go_to_my_money',$default_message);
                                ?>
                                            </li>
                                            <li>
                                <?php
                                    $default_message=' Then go to Send/Pay';
                                    $this->languages_m->translate('go_to_send',$default_message);
                                ?>
                                          </li>
                                            <li>
                                <?php
                                    $default_message='Then go to pay bill and enter '.$this->application_settings->application_name.' Pay bill Number - 967600 and confirm';
                                    $this->languages_m->translate('pay_bill_and_confirm',$default_message);
                                ?>
                                            </li>
                                            <li>
                                <?php
                                    $default_message='Enter Chama Account Number - <strong>'.$this->group->account_number.' </strong> and confirm';
                                    $this->languages_m->translate('account_number_and_confirm',$default_message);
                                ?>
                                            </li>
                                            <li>
                                <?php
                                    $default_message='Enter the Amount to pay - (Amount Quoted on invoice ) and confirm';
                                    $this->languages_m->translate('amount_to_pay_and_confirm',$default_message);
                                ?>
                                            </li>
                                            <li>
                                <?php
                                    $default_message='Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment)';
                                    $this->languages_m->translate('confirm_details',$default_message);
                                ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="media">
                                    <div class="media-body">
                                        <h5 class="media-heading">
                                 <?php
                                            $default_message='Eazzy Banking App';
                                            $this->languages_m->translate('eazzy_banking_app',$default_message);
                                    ?>
                                        </h5>
                                        <ul> 
                                            <li>
                                <?php
                                            $default_message='Go To Paybill Option';
                                            $this->languages_m->translate('go_to_pay_bill_option',$default_message);
                                ?>
                                            </li>
                                            <li>                
                                <?php
                                            $default_message='Enter Eazzychama Pay bill number - 967600 and confirm.';
                                            $this->languages_m->translate('chamasoft_paybill_number',$default_message);
                                ?>
                                            </li>
                                            <li>
                                <?php
                                            $default_message='Enter your '.$this->application_settings->application_name.' group number - <strong>'.$this->group->account_number.'</strong>';
                                            $this->languages_m->translate('enter_chamasoft_group_number',$default_message);
                                ?>

                                            </li>
                                            <li>
                                                
                                <?php
                                            $default_message='Enter the Amount to pay - (Amount Quoted on invoice) and confirm.';
                                            $this->languages_m->translate('enter_amount_to_pay',$default_message);
                                ?>
                                            </li>
                                            <li>
                                <?php
                                            $default_message='Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment) ';
                                            $this->languages_m->translate('confirm_details_entered',$default_message);
                                ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="media">
                                    <div class="media-body">
                                        <h5 class="media-heading">
                                            
                                <?php
                                            $default_message='Cheque';
                                            $this->languages_m->translate('cheque',$default_message);
                                ?>
                                        </h5>
                                        <ul> 
                                            <li>
                                <?php
                                            $default_message='Write a cheque Risk Tick Credit Limited, kindly note the amount is on the proforma invoice sent your email address';
                                            $this->languages_m->translate('write_cheque',$default_message);
                                ?>

                                            </li>
                                            <li>
                                <?php
                                            $default_message='Deliver the cheque to our offices at Upper Hill, Elgon Court Block D1 along Ralph Bunche Road off Ngong Road';
                                            $this->languages_m->translate('deliver_cheque',$default_message);
                                ?>
                                            </li>
                                            <li>
                                <?php
                                            $default_message='Upon receipt our sales team will activate your account for the period subscribed for';
                                            $this->languages_m->translate('sales_team_will_activate_account',$default_message);
                                ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            <?php } ?>
            <p/>
            <p/>
            <p/>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i>
                    <?php
                                            $default_message='Print';
                                            $this->languages_m->translate('print',$default_message);
                                ?>
            </a>
        </div>
    </div>
</div>

<!--<div class="note voided-note">
    voided
</div>-->