<div class="invoice-content-2 bordered document-border"> 
    <div class="row invoice-cust-add margin-bottom-20">
         <div class="col-md-7 col-xs-6">
            <div class="invoice-logo">
                <img src="<?php echo is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo); ?>" alt="" height="50px" class='image-responsive' /> 
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
                            $default_message='E-Mail Address';
                            $this->languages_m->translate('email_address',$default_message);
                        ?>
                    : </span> <?php echo $this->group->email; ?>
                <br/>
            </div>       
        </div>
    </div>
    <!-- <hr/> -->
    <div class="row invoice-cust-add margin-bottom-20">
        <div class="col-md-7 col-xs-6">
            <h5 class="invoice-title">
                <?php
                    $default_message='Member';
                    $this->languages_m->translate('member',$default_message);
                ?>
            </h5>
            <span class="bold">
                <?php
                    $default_message='Name';
                    $this->languages_m->translate('name',$default_message);
                ?>
            :</span> <?php echo $member->first_name.' '.$member->last_name; ?><br/>
            <span class="bold">
                <?php
                    $default_message='Phone';
                    $this->languages_m->translate('phone',$default_message);
                ?>
            :</span> <?php echo $member->phone; ?><br/>
            <span class="bold">Email Address:</span> <?php echo $member->email; ?>
        </div>
        <div class="col-md-5 col-xs-6">
            <h5 class="invoice-title">Date Particulars</h5>
            <span class="bold">
                        <?php
                            $default_message='Invoice Date';
                            $this->languages_m->translate('invoice_date',$default_message);
                        ?>
            :</span> <?php echo timestamp_to_date($post->invoice_date) ?><br/>
            <span class="bold">Due Date:</span> <?php echo timestamp_to_date($post->due_date) ?><br/>
            <span class="bold">Sent On:</span> <?php echo timestamp_to_date($post->created_on) ?><br/>
        </div>
    </div>
    <hr>
    <div class="row invoice-body">
        <div class="col-xs-12 table-responsive p-3">
            <table class="table table-hover table-striped table-condensed table-statement">
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
                                $default_message='Amount Payable';
                                $this->languages_m->translate('amount_payable',$default_message);
                            ?>
                            (<?php echo $this->group_currency; ?>)</th>
                        <th class="invoice-title uppercase text-right">Total(<?php echo $this->group_currency; ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span class="bold"><?php echo $this->invoice_type_options[$post->type]; ?></span>
                            <p> <?php echo $post->description; ?> </p>
                        </td>
                        <td class="text-right sbold"><?php echo number_to_currency($post->amount_payable); ?></td>
                        <td class="text-right sbold"><?php echo number_to_currency($post->amount_payable); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
                                     
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button type="button" class="btn btn-sm btn-info hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class="fa fa-print"></i> 
                Print            
            </button>
            &nbsp;&nbsp;&nbsp;
        </div>
    </div>
</div>