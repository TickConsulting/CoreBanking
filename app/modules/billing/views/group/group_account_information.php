<div class="row group-billing-information">
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        <div class="profile-sidebar">
            <!-- PORTLET MAIN -->
            <div class="portlet light profile-sidebar-portlet bordered">
                <!-- SIDEBAR USERPIC -->
                <div class="profile-userpic">

                    <?php if($this->group->avatar && is_file(FCPATH.'uploads/groups/'.$this->group->avatar)){ ?>
                        <img src="<?php echo site_url('uploads/groups/'.$this->group->avatar);?>" class="img-responsive" alt=""> </div>
                    <?php }else{ ?>
                        <img src="<?php echo site_url('templates/admin_themes/groups/img/default_group_avatar.png');?>" class="img-responsive" alt=""> </div>
                    <?php } ?>
                <!-- END SIDEBAR USERPIC -->
                <!-- SIDEBAR USER TITLE -->
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name"> <?php echo $this->group->name; ?> </div>
                </div>
                <!-- END SIDEBAR USER TITLE -->
                <!-- SIDEBAR BUTTONS -->
                <div class="profile-userbuttons">

                    <a href="<?php echo site_url('group/groups/edit_profile');?>" class="btn btn-circle green btn-sm"><i class="fa fa-edit"></i>

                        <?php
                        $default_message='Edit Profile';
                        $this->languages_m->translate('edit_profile',$default_message);
                        ?>
                         </a>

                </div>
                <!-- END SIDEBAR BUTTONS -->
                <!-- SIDEBAR MENU -->
                <div class="profile-usermenu">
                    <ul class="nav">
                        <li class="active">
                            <a href="<?php echo site_url('group/groups/edit_profile');?>">
                                <i class="icon-settings"></i> 

                        <?php
                            $default_message='Account Settings';
                            $this->languages_m->translate('account_settings',$default_message);
                        ?>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="<?php echo $this->application_settings->protocol."help.".$this->application_settings->url; ?>">
                                <i class="icon-info"></i> 

                        <?php
                            $default_message='Help';
                            $this->languages_m->translate('help',$default_message);
                        ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- END MENU -->
            </div>
            <!-- END PORTLET MAIN -->
        </div>
        <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-bar-chart theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold"><?php echo $this->group->name; ?>

                                        <?php
                                            $default_message='Profile';
                                            $this->languages_m->translate('profile',$default_message);
                                        ?>
                                </span>
                            </div>
                            <ul class="nav nav-tabs">
                                <?php if($this->group->status !=1){?>
                                <li class="active">
                                    <a href="#information" data-toggle="tab">

                                        <?php
                                                $default_message='Account';
                                                $this->languages_m->translate('account',$default_message);
                                        ?>

                                    <span class="hidden-sm hidden-xs">
                                        
                                        <?php
                                                $default_message='Information';
                                                $this->languages_m->translate('information',$default_message);
                                        ?>

                                    </span></a>
                                </li>
                                <?php }?>
                                <?php if($this->group->status !=2){?>
                                <li <?php if($this->group->status ==1){echo 'class="active"';}?>>
                                    <a href="#procedure" data-toggle="tab">Account Information & <span class="hidden-sm hidden-xs">Payment </span>Procedure</a>
                                </li>
                                <li>
                                    <a href="#smses" data-toggle="tab"><span class="hidden-sm hidden-xs">


                                        <?php
                                                $default_message='Top Up </span> SMSes';
                                                $this->languages_m->translate('top_up_smses',$default_message);
                                        ?>
                                    </a>
                                </li>
                                <?php }?>
                            </ul>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content" style="min-height:560px;">
                                <div class="tab-pane <?php if($this->group->status !=1){echo 'active';}?>" id="information">
                                    <div class="row">
                                        <?php if($this->group->status==1){?>
                                            
                                        <?php } else if($this->group->status==2){?>
                                            <div class="col-md-12">
                                                <div class="">
                                                    <h4 class="block"><span class="label label-md label-danger">Warning! Account suspended</span></h4>
                                                    <p>
                                                        Sorry, your account has been suspended.
                                                    </p>
                                                    <p>
                                                        To continue using <?php echo $this->application_settings->application_name;?>, kindly communicate with us by sending us an email or calling us through the contacts below.
                                                    </p>
                                                    <p>
                                                         <div class="margin-top-20 profile-desc-link">
                                                            <i class="fa fa-phone"></i>
                                        <?php
                                                $default_message='Phone';
                                                $this->languages_m->translate('phone',$default_message);
                                        ?>
                                                              - 
                                                            0733 366 240
                                                        </div>
                                                        <div class="margin-top-20 profile-desc-link">
                                                            <i class="fa fa-envelope"></i> 
                                        <?php
                                                $default_message='Email';
                                                $this->languages_m->translate('email',$default_message);
                                        ?>
                                                            - 
                                                            info@chamasoft.com
                                                        </div>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php } else{?>
                                            <div class="mt-element-step">
                                            
                                                <div class="col-md-12 knob-div">
                                                    <div class="font-dark bold uppercase">Trial days remaining</div>
                                                     <input class="knob" data-width="100" data-min="0" data-max="<?php echo $this->application_settings->trial_days;?>" data-displayprevious=true value="<?php echo $this->group->trial_days;?>"  data-thickness=".3" data-readOnly=true> 
                                                </div>
                                                <div class="col-md-12">

                                                    <div class="font-dark bold uppercase">Confirm subscription: </div>
                                                        <p>Kindly confirm if you are to use the system to manage your group operations, by clicking on the 'Confirm subscription' Button below.</p>
                                                        <?php if(isset($billing_payments) && $billing_payments):?>
                                                            <p> You will then receive an invoice of <strong><?php echo $this->default_country->currency_code; ?> <?php echo number_to_currency($billing_payments->annual_amount+$billing_payments->annual_tax); ?></strong> which will be due on <strong><?php echo timestamp_to_receipt(strtotime('+3 days',time()))?></strong>.</p>
                                                        <?php endif; ?>
                                                    <?php echo form_open_multipart($this->uri->uri_string(), ' role="form" class="form_submit" '); ?>
                                                        <div style="display:none;">
                                                            <div class="form-group">
                                                                <label>Billing Cycle<span class="required">*</span></label>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon">
                                                                    <i class="fa fa-clock-o"></i>
                                                                    </span>
                                                                    <?php echo form_dropdown('billing_cycle',array(''=>'Select Billing Cycle')+$billing_cycles,$this->input->post('billing_cycle')?:$group->billing_cycle?:'','class="form-control select2" placeholder="Billing Cycle"'); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group enable-tax-settings">
                                                            <label>Do you agree with <?php echo $this->application_settings->application_name;?> terms an conditions? </label>
                                                            <div class="input-group checkbox-list col-xs-12 ">
                                                                <label class="checkbox-inline">
                                                                    <?php echo form_checkbox('agree_terms',1,$this->input->post('agree_terms')?:'1',$this->input->post('agree_terms')?:'1',' id="enable_tax" class="agree_terms" required="required"'); ?> Yes, I agree.
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-actions">
                                                            <button type="submit" name="confirm_subscription" value="1" class="btn blue submit_form_button">Confirm Subscription <i class="fa fa-check"></i></button>
                                                            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i>
                                        <?php
                                                $default_message='Processing';
                                                $this->languages_m->translate('processing',$default_message);
                                        ?>                  </button>
                                                        </div>
                                                    <?php echo form_close(); ?>
                                                </div>
                                            </div>
                                        <?php }?>
                                    </div>
                                </div>

                                <!-- GENERAL QUESTION TAB -->
                                <div class="tab-pane <?php if($this->group->status ==1){echo 'active';}?>" id="procedure">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php echo $this->application_settings->subscription_payment_procedure; ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- END GENERAL QUESTION TAB -->

                                <!-- GENERAL QUESTION TAB -->
                                <div class="tab-pane" id="smses">
                                    <div class="row">
                                    <div class="col-md-12"><h4>

                                        <?php
                                                $default_message='How to Top up SMSes';
                                                $this->languages_m->translate('how_to_top_up_sms',$default_message);
                                        ?>

                                    </h4></div>
                                        <div class="col-md-12">
                                            <div class="font-dark bold uppercase">

                                        <?php
                                                $default_message='MPesa';
                                                $this->languages_m->translate('mpesa',$default_message);
                                        ?>   

                                            :</div>
                                            <ul>
                                            <li>
                                        <?php
                                                $default_message='Go to MPESA on your phone';
                                                $this->languages_m->translate('go_to_mpesa_on_your_phone',$default_message);
                                        ?>
                                                </li>
                                                <li>

                                        <?php
                                                $default_message='Then go to Lipa na MPESA';
                                                $this->languages_m->translate('then_go_to_lipa_na_mpesa',$default_message);
                                        ?>

                                                </li>
                                                <li>
                                        <?php
                                                $default_message='Then go to Paybill';
                                                $this->languages_m->translate('then_go_to_paybill',$default_message);
                                        ?>

                                                </li>
                                                <li>
                                        <?php
                                                $default_message='Enter the '.$this->application_settings->application_name.' Paybill Number - 967600 and confirm';
                                                $this->languages_m->translate('chamasoft_paybill_number',$default_message);
                                        ?>
                                                   </li>
                                                <li>
                                        <?php
                                                $default_message=' Enter the items Account Number - SMS '.$this->group->account_number.' and confirm';
                                                $this->languages_m->translate('',$default_message);
                                        ?>
                                                   </li>
                                                <li>

                                        <?php
                                                $default_message='Enter the Amount to pay - (Bundle Details Below) and confirm';
                                                $this->languages_m->translate('enter_amount_to_pay_bundle',$default_message);
                                        ?>

                                                </li>
                                                <li>

                                        <?php
                                                $default_message='Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment)';
                                                $this->languages_m->translate('proceed_to_confirm_payment',$default_message);
                                        ?>

                                                </li>
                                            </ul>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="font-dark bold uppercase">Equitel:</div>
                                            <ul>
                                                <li>

                                        <?php
                                                $default_message='Go to Equitel toolkit on your phone';
                                                $this->languages_m->translate('equitel_sim_toolkit',$default_message);
                                        ?>

                                                </li>
                                                <li>
                                        <?php
                                                $default_message='Then go to EazzyPay';
                                                $this->languages_m->translate('go_to_eazzy_pay',$default_message);
                                        ?>

                                                </li>
                                                <li>

                                        <?php
                                                $default_message='Enter the '.$this->application_settings->application_name.' Paybill Number - 967600 and confirm';
                                                $this->languages_m->translate('chamasoft_paybill_number',$default_message);
                                        ?>
                                                </li>
                                                <li>

                                        <?php
                                                $default_message='Enter Account Number - <strong> '.$this->group->account_number.' </strong> and confirm';
                                                $this->languages_m->translate('chamasoft_paybill_number',$default_message);
                                        ?>
                                                    </li>
                                                <li>Enter the items Account Number - SMS<?php echo $this->group->account_number;?> and confirm</li>
                                                <li>
                                        <?php
                                                $default_message='Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the paymgent is received, confirming the receipt of the payment)';
                                                $this->languages_m->translate('confirm_details',$default_message);
                                        ?>

                                                </li>
                                            </ul>
                                        </div>

                                        <div class="col-md-12">
                                        SMS Bundle
                                            <table class="table">
                                                <thead>
                                                    <th>
                                        <?php
                                                $default_message='Price';
                                                $this->languages_m->translate('price',$default_message);
                                        ?>
                                                    </th>
                                                    <th>Number of SMSes</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo $this->default_country->currency_code; ?> 2</td>
                                                        <td>1 SMS</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $this->default_country->currency_code; ?> 200</td>
                                                        <td>100 SMSes</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $this->default_country->currency_code; ?> 500</td>
                                                        <td>300 SMSes</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $this->default_country->currency_code; ?> 1000</td>
                                                        <td>700 SMSes</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- END GENERAL QUESTION TAB -->
                                <!-- MEMBERSHIP TAB -->
                                <div class="tab-pane" id="invoices">
                                    <?php if($invoices){?>
                                    <div class="table-responsive">
                                        <table class="table table-condensed">
                                            <thead>
                                                <th>#</th>
                                                <th>
                                                <?php
                                                $default_message='Due Date';
                                                $this->languages_m->translate('due_date',$default_message);
                                                ?></th>
                                                <th class="text-right">Subscription(<?php echo $this->default_country->currency_code; ?>)</th>
                                                <th class="text-right">Tax(<?php echo $this->default_country->currency_code; ?>)</th>
                                                <th class="text-right">Total(<?php echo $this->default_country->currency_code; ?>)</th>
                                                <th class="text-right">Balance</th>
                                                <th>
                                        <?php
                                                $default_message='Status';
                                                $this->languages_m->translate('status',$default_message);
                                        ?></th>
                                                <th>
                                        <?php
                                                $default_message='Actions';
                                                $this->languages_m->translate('actions',$default_message);
                                        ?>

                                                </th>
                                            </thead>
                                            <tbody>
                                                <?php $i=0; $total_tax=0;$total_subscription=0;$total_amount=0;$total_balance=0;foreach($invoices as $invoice):?>
                                                    <tr>
                                                        <td><?php echo ++$i;?></td>
                                                        <td><?php echo timestamp_to_date($invoice->due_date);?></td>
                                                        <td class="text-right"><?php echo number_to_currency($subscription=$invoice->amount-$invoice->tax);?></td>
                                                        <td class="text-right"><?php echo number_to_currency($tax=$invoice->tax);?></td>
                                                        <td class="text-right"><?php echo number_to_currency($amount=$invoice->amount);?></td>
                                                        <td class="text-right"><?php echo number_to_currency($balance=$invoice->amount - $invoice->amount_paid);?></td>
                                                        <td><?php if($invoice->status){echo '<span class="label label-sm label-success">Paid</span>';}else{echo '<span class="label label-sm label-danger">Unpaid</span>';}?></td>
                                                        <td><a href="<?php echo site_url('group/billing/invoice/'.$invoice->id);?>" class="btn btn-xs btn-default"><i class="fa fa-book"></i> View Invoice</a></td>
                                                    </tr>
                                                <?php $total_tax +=$tax; $total_subscription+=$subscription; $total_amount+=$amount; $total_balance+=$balance; endforeach;?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2">Total</td>
                                                    <td class="text-right"><?php echo number_to_currency($total_subscription);?></td>
                                                    <td class="text-right"><?php echo number_to_currency($total_tax);?></td>
                                                    <td class="text-right"><?php echo number_to_currency($total_amount);?></td>
                                                    <td class="text-right"><?php echo number_to_currency($total_balance);?></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <?php }else{?>
                                        <div class="alert alert-info">
                                            <h4 class="block">
                                    <?php
                                        $default_message='Information! No records to display';
                                        $this->languages_m->translate('no_records_to_display',$default_message);
                                    ?>
                                            </h4>
                                            <p>
                                    <?php
                                        $default_message='Sorry, there are no invoice records';
                                        $this->languages_m->translate('no_invoice_records',$default_message);
                                    ?>
                                            </p>
                                        </div>
                                    <?php }?>
                                </div>
                                <!-- END MEMBERSHIP TAB -->
                                <!-- TERMS OF USE TAB -->
                                <div class="tab-pane" id="payments">
                                   <?php if($payments){?>
                                   <div class="table-responsive">
                                        <table class="table table-condensed">
                                            <thead>
                                                <th>#</th>
                                                <th>
                                    <?php
                                        $default_message='Payment Date';
                                        $this->languages_m->translate('payment_date',$default_message);
                                    ?></th>
                                                <th>
                                    <?php
                                        $default_message='Payment Method';
                                        $this->languages_m->translate('payment_method',$default_message);
                                    ?>

                                                </th>
                                                <th>Tax(<?php echo $this->default_country->currency_code; ?>)</th>
                                                <th class="text-right">
                                    <?php
                                        $default_message='Total';
                                        $this->languages_m->translate('total',$default_message);
                                    ?>
                                                    (<?php echo $this->default_country->currency_code; ?>)</th>
                                                <th>
                                    <?php
                                        $default_message='Actions';
                                        $this->languages_m->translate('actions',$default_message);
                                    ?>


                                                </th>
                                            </thead>
                                            <tbody>
                                                <?php $i=0; foreach($payments as $payment):?>
                                                    <tr>
                                                        <td><?php echo ++$i;?></td>
                                                        <td><?php echo timestamp_to_date($payment->receipt_date);?></td>
                                                        <td><?php echo $this->billing_settings->payment_method[$payment->payment_method];?></td>
                                                        <td class="text-right"><?php echo number_to_currency($payment->tax);?></td>
                                                        <td class="text-right"><?php echo number_to_currency($payment->amount);?></td>
                                                        <td><a href="<?php echo site_url('group/billing/receipt/'.$payment->id)?>" class="btn btn-xs btn-default"><i class="fa fa-book"></i> View Receipt</a></td>
                                                    </tr>
                                                <?php endforeach;?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php }else{?>
                                        <div class="alert alert-info">
                                            <h4 class="block">
                                                
                                    <?php
                                        $default_message='Information! No records to display';
                                        $this->languages_m->translate('no_records_to_display',$default_message);
                                    ?>


                                            </h4>
                                            <p>
                                    <?php
                                        $default_message='Sorry, there are no invoice records';
                                        $this->languages_m->translate('no_invoice_records',$default_message);
                                    ?>
                                            </p>
                                        </div>
                                    <?php }?>
                                </div>
                                <!-- END TERMS OF USE TAB -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>