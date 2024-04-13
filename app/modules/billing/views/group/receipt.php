<div class="invoice-content-2 bordered document-border">
    <div class="row invoice-head">
        <div class="col-md-7 col-xs-6">
            <div class="invoice-logo">
                 <?php echo '<img src="'.site_url('uploads/logos/'.$this->application_settings->paper_header_logo).'" alt="" class="group-logo image-responsive" /> ';?>
            </div>
        </div>
        <div class="col-md-5 col-xs-6 text-right">
            <div class="company-address ">
                <span class="bold uppercase invoice-number"><?php echo $post->billing_receipt_number; ?></span><br/>
            </div>
        </div>
    </div>
    <div class="row invoice-cust-add margin-bottom-20">
        <div class="col-md-7 col-xs-6">
            <h4 class="invoice-title"><?php
                                            $default_message='Group Details';
                                            $this->languages_m->translate('group_details',$default_message);
                                    ?></h4>
            <span class="bold">
                        <?php
                                            $default_message='Name';
                                            $this->languages_m->translate('name',$default_message);
                        ?>
            :</span> <?php echo $group->name; ?><br/>
            <span class="bold">
                        <?php
                                            $default_message='Amount';
                                            $this->languages_m->translate('amount',$default_message);
                        ?>
            :</span> <u><span class="amount-words"><?php echo number_to_words($post->amount).' only';?></span></u><br/>
        </div>
        <div class="col-md-5 col-xs-6 text-right">
            <h4 class="invoice-title">Date Particulars</h4>
            <span class="bold">
                        <?php
                                            $default_message='Paid On';
                                            $this->languages_m->translate('paid_on',$default_message);
                        ?>
            :</span> <?php echo timestamp_to_receipt($post->receipt_date) ?><br/>
            <span class="bold">
                        <?php
                                            $default_message='Received On';
                                            $this->languages_m->translate('received_on',$default_message);
                        ?>
            :</span> <?php echo timestamp_to_receipt($post->created_on) ?><br/>
        </div>
    </div>
    <div class="row invoice-body">
        <div class="col-xs-12 table-responsive table-condensed">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="55%" class="invoice-title uppercase">
                        <?php
                                            $default_message='Description';
                                            $this->languages_m->translate('description',$default_message);
                        ?>
                        </th>
                        <th class="invoice-title uppercase text-right">
                        <?php
                                            $default_message='Subscription';
                                            $this->languages_m->translate('subscription',$default_message);
                        ?>
                            (<?php echo $this->group_currency; ?>)</th>
                        <th class="invoice-title uppercase text-right">Tax(<?php echo $this->group_currency; ?>)</th>
                        <th class="invoice-title uppercase text-right">
                            <?php
                                            $default_message='Total';
                                            $this->languages_m->translate('total',$default_message);
                        ?>
                            (<?php echo $this->group_currency; ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span class=""><?php echo $payment_methods[$post->payment_method].' payment<br/>'.$post->description; if($post->ipn_transaction_code){echo ' - '.$post->ipn_transaction_code;} ?></span>
                        </td>
                        <td class="text-right"><?php echo number_to_currency($post->amount - $post->tax); ?></td>
                        <td class="text-right"><?php echo number_to_currency($post->tax); ?></td>
                        <td class="text-right sbold"><?php echo number_to_currency($post->amount); ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="note">
                <span class="sbold ">Note :</span> Receipt automatically generated by the system without any alternations. It is therefore a valid receipt.
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> Print</a>

            &nbsp;&nbsp;&nbsp;
            <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().'/'.TRUE?>" target="_blank"><i class='fa fa-file'></i>
                        <?php
                                            $default_message='Generate PDF';
                                            $this->languages_m->translate('generate_pdf',$default_message);
                        ?>
            </a>
        </div>
    </div>
</div>

<!--<div class="note voided-note">
    voided
</div>-->