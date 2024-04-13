<div class="invoice-content-2 bordered document-border">
    <div class="row invoice-head">
        <div class="col-md-7 col-xs-6">
            <div><?php if($this->group->avatar && file_exists('uploads/groups/'.$this->group->avatar))
                {
                    echo '<img src="'.FCPATH.'uploads/groups/'.$this->group->avatar.'" alt="" class="group-logo image-responsive" />';
                }else{
                    echo '<img src="'.site_url('uploads/logos/'.$this->application_settings->paper_header_logo).'" alt="" class="group-logo image-responsive" /> ';
                }
            ?>
                <h3 class="">Loan Repayment</h3>
            </div>
        </div>
        <div class="col-md-5 col-xs-6">
            <div class="company-address">
                <span class="bold uppercase"><?php echo $this->group->name; ?></span><br/>
                <?php echo nl2br($this->group->address); ?><br/>
                <span class="bold"><?php
                                        if($this->lang->line('telephone')){
                                        echo $this->lang->line('telephone');
                                        }else{
                                        echo "Telephone";
                                        }
                                    ?>: </span> <?php echo $this->group->phone; ?>
                <br/>
                <span class="bold"><?php
                                        if($this->lang->line('email_address')){
                                        echo $this->lang->line('email_address');
                                        }else{
                                        echo "Email Address";
                                        }
                                    ?>: </span> <?php echo $this->group->email; ?>
                <br/>
            </div>
        </div>
    </div>
    <div class="row invoice-cust-add margin-bottom-20">
        <div class="col-md-7 col-xs-6">
            <h4 class="invoice-title">Payment Particulars</h4>
            <span class="bold"><?php
                                        if($this->lang->line('payment_date')){
                                        echo $this->lang->line('payment_date');
                                        }else{
                                        echo "Payment Date";
                                        }
                                    ?>:</span> <?php echo timestamp_to_date($post->receipt_date) ?><br/>
            <span class="bold">Payment Account:</span> <?php echo $accounts[$post->account_id];?><br/>
            <span class="bold">Payment Method:</span> <?php echo $withdrawal_method_options[$post->payment_method] ?><br/>
        </div>
    </div>
    <div class="row invoice-body">
        <div class="col-xs-12 table-responsive table-condensed">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="55%" class="invoice-title uppercase"><?php
                                        if($this->lang->line('description')){
                                        echo $this->lang->line('description');
                                        }else{
                                        echo "Description";
                                        }
                                    ?></th>
                        <th class="invoice-title uppercase text-right"><?php
                                        if($this->lang->line('amount_payable')){
                                        echo $this->lang->line('amount_payable');
                                        }else{
                                        echo "Amount Payable";
                                        }
                                    ?>(<?php echo $this->group_currency; ?>)</th>
                        <th class="invoice-title uppercase text-right">Total(<?php echo $this->group_currency; ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <p> <?php echo 'Bank Loan Repayment - '.$post->description; ?> </p>
                        </td>
                        <td class="text-right sbold"><?php echo number_to_currency($post->amount); ?></td>
                        <td class="text-right sbold"><?php echo number_to_currency($post->amount); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> <?php
                                        if($this->lang->line('print')){
                                        echo $this->lang->line('print');
                                        }else{
                                        echo "Print";
                                        }
                                    ?></a>
        </div>
    </div>
</div>