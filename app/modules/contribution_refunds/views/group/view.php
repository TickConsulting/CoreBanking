<div class="invoice-content-2 bordered document-border">
    <div class="row invoice-head">
        <div class="col-md-7 col-xs-6">
            <div class="invoice-logo">
                <img src="<?php echo is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url($this->application_settings->paper_header_logo); ?>" alt="" class='group-logo image-responsive' /> 
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
        <div class="col-md-7 col-xs-6">
            <span class="bold">
                <?php
                    $default_message='Date';
                    $this->languages_m->translate('date',$default_message);
                ?>
            :</span> <?php echo timestamp_to_date($post->created_on); ?><br/>
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
            <span class="bold">
                <?php
                    $default_message='Email Address';
                    $this->languages_m->translate('enail_address',$default_message);
                ?>
                :</span> <?php echo $member->email; ?>
        </div>
        <div class="col-md-5 col-xs-6">
            <span class="bold">Refund Date : </span> <?php echo timestamp_to_date($post->refund_date); ?><br/>
            <span class="bold">
                <?php
                    $default_message='Refund Method';
                    $this->languages_m->translate('refund_method',$default_message);
                ?>
            : </span> <?php echo $withdrawal_methods[$post->refund_method]; ?><br/>
            <span class="bold">Created by : </span> <?php echo $user->first_name.' '.$user->last_name;?><br/>
        </div>
    </div>
    <hr/>
    <div class="row invoice-body">
            <div class="col-xs-12 table-responsive ">
                <table class="table table-hover table-striped table-condensed table-statement">
                    <thead>
                        <tr>
                            <th class="invoice-title ">#</th>
                            <th class="invoice-title ">
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
                            <th class="invoice-title ">Contribution Refunded</th>
                            <th class="invoice-title ">Account Refunded From</th>
                            <th class="invoice-title  text-right">
                                <?php
                                    $default_message='Amoun Refunded';
                                    $this->languages_m->translate('amount_refunded',$default_message);
                                ?>
                                (<?php echo $this->group_currency; ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr>
                                <td><?php echo '1.';?></td>
                                <td><?php echo timestamp_to_date($post->refund_date); ?></td>
                                <td><?php echo $post->description; ?></td>
                                <td><?php echo $contribution_options[$post->contribution_id]; ?></td>
                                <td><?php echo group_account($post->account_id,$accounts); ?></td>
                                <td class='text-right'><?php echo number_to_currency($post->amount);?></td>
                            </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Totals</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="clearfix"></div> 


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
