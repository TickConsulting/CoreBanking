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

                    <a href="<?php echo site_url('group/groups/edit_profile');?>" class="btn btn-circle green btn-sm"><i class="fa fa-edit"></i><?php
                                        if($this->lang->line('edit_profile')){
                                        echo $this->lang->line('edit_profile');
                                        }else{
                                        echo "Edit Profile";
                                        }
                                    ?></a>

                </div>
                <!-- END SIDEBAR BUTTONS -->
                <!-- SIDEBAR MENU -->
                <div class="profile-usermenu">
                    <ul class="nav">
                        <li class="active">
                            <a href="<?php echo site_url('group/groups/edit_profile');?>">
                                <i class="icon-settings"></i> <?php
                                        if($this->lang->line('account_settings')){
                                        echo $this->lang->line('account_settings');
                                        }else{
                                        echo "Account Settings";
                                        }
                                    ?> </a>
                        </li>
                        <li>
                            <a target="_blank" href="<?php echo $this->application_settings->protocol."help.".$this->application_settings->url; ?>">
                                <i class="icon-info"></i> <?php
                                        if($this->lang->line('help')){
                                        echo $this->lang->line('help');
                                        }else{
                                        echo "Help";
                                        }
                                    ?> </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-envelope-o"></i> <?php
                                        if($this->lang->line('sms_balance')){
                                        echo $this->lang->line('sms_balance');
                                        }else{
                                        echo "SMS Balance";
                                        }
                                    ?> - <?php echo $this->group->sms_balance; ?> SMSes </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-calendar"></i> 
                                <?php echo "Next Billing Date -". timestamp_to_receipt($this->group->billing_date);?>
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
                                <span class="caption-subject font-blue-madison bold"><?php echo $this->group->name; ?> <?php
                                        if($this->lang->line('billing_information')){
                                        echo $this->lang->line('billing_information');
                                        }else{
                                        echo "Billing Information";
                                        }
                                    ?></span>
                            </div>
                            <ul class="nav nav-tabs">
                                <?php if($this->group->status !=1){?>
                                <li class="active">
                                    <a href="#information" data-toggle="tab"><?php
                                        if($this->lang->line('account')){
                                        echo $this->lang->line('account');
                                        }else{
                                        echo "Account";
                                        }
                                    ?> <span class="hidden-sm hidden-xs">
                                        <?php
                                            $default_message='Information';
                                            $this->languages_m->translate('information',$default_message);
                                        ?>
                                    </span></a>
                                </li>
                                <?php }?>
                                <?php if($this->group->status !=2){?>
                                <li <?php if($this->group->status ==1){echo 'class="active"';}?>
                                    <a href="#procedure" data-toggle="tab"><span class="hidden-sm hidden-xs">How to Pay</span></a>
                                </li>
                                <li>
                                    <a href="#smses" data-toggle="tab"><span class="hidden-sm hidden-xs">
                                        <?php
                                            $default_message='Top Up </span> SMSes';
                                            $this->languages_m->translate('top_up_smses',$default_message);
                                        ?>
                                    </a>
                                </li>

                                <?php if($this->group->status == 1){?>
                                    <li>
                                        <a href="#invoices" data-toggle="tab"><span class="hidden-sm hidden-xs"></span><?php
                                        if($this->lang->line('billing_invoices')){
                                        echo $this->lang->line('billing_invoices');
                                        }else{
                                        echo "Billing Invoices";
                                        }
                                    ?></a>
                                    </li>
                                    <li>
                                        <a href="#payments" data-toggle="tab"><span class="hidden-sm hidden-xs"></span><?php
                                        if($this->lang->line('subscription_payments')){
                                        echo $this->lang->line('subscription_payments');
                                        }else{
                                        echo "Subscription Payments";
                                        }
                                    ?></a>
                                    </li>
                                <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content" style="min-height:400px;">
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
                                                            <i class="fa fa-phone"></i> <?php
                                                                if($this->lang->line('phone_number')){
                                                                echo $this->lang->line('phone_number');
                                                                }else{
                                                                echo "Phone";
                                                                }
                                                            ?> - 
                                                            0733 366 240
                                                        </div>
                                                        <div class="margin-top-20 profile-desc-link">
                                                            <i class="fa fa-envelope"></i> <?php
                                                                if($this->lang->line('email')){
                                                                echo $this->lang->line('email');
                                                                }else{
                                                                echo "email";
                                                                }
                                                            ?> - 
                                                            info@chamasoft.com
                                                        </div>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php }else{?>
                                            <div class="mt-element-step">
                                                <?php if(isset($billing_payments) && $billing_payments):?>
                                                    <div class="col-md-12"> 
                                                      <div class="font-dark bold uppercase">Pricing Schedule</div>  
                                                      <div class="table-responsive">
                                                        <table class="table table-condensed">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="2" class="text-right">Monthly Pay (<?php echo $this->default_country->currency_code; ?>)</th>
                                                                    <th colspan="2" class="text-right">Quarterly Pay (<?php echo $this->default_country->currency_code; ?>)</th>
                                                                    <th colspan="2" class="text-right">Annual Pay (<?php echo $this->default_country->currency_code; ?>)</th>
                                                                </tr>
                                                                <tr>
                                                                    <th class="text-right">Tax</th>
                                                                    <th class="text-right">Total</th>
                                                                    <th class="text-right">Tax</th>
                                                                    <th class="text-right">Total</th>
                                                                    <th class="text-right">Tax</th>
                                                                    <th class="text-right">Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-right">

                                                                    <?php echo number_to_currency($billing_payments->monthly_tax);?>
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <strong><?php echo number_to_currency($billing_payments->monthly_amount+$billing_payments->monthly_tax);?></strong>
                                                                            <br/>
                                                                            <br/>Awarded
                                                                        <?php echo $billing_payments->monthly_smses.' SMSes';?> 

                                                                        </td>
                                                                    <td class="text-right"><?php echo number_to_currency($billing_payments->quarterly_tax);?></td>
                                                                    <td class="text-right">
                                                                    <strong><?php echo number_to_currency($billing_payments->quarterly_amount+$billing_payments->quarterly_tax);?></strong>
                                                                         <br/>
                                                                         <br/>Awarded
                                                                        <?php echo $billing_payments->quarterly_smses.' SMSes';?> 
                                                                    </td>
                                                                    <td class="text-right"><?php echo number_to_currency($billing_payments->annual_tax);?></td>
                                                                    <td class="text-right">
                                                                    <strong><?php echo number_to_currency($billing_payments->annual_amount+$billing_payments->annual_tax);?>
                                                                    </strong>
                                                                    <br/>
                                                                    <br/>
                                                                        Awarded
                                                                        <?php echo $billing_payments->annual_smses.' SMSes';?> </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                      </div>
                                                    </div>
                                                <?php endif;?>
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
                                                        <div style="">
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
                                                            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> <?php
                                                                        if($this->lang->line('processing')){
                                                                        echo $this->lang->line('processing');
                                                                        }else{
                                                                        echo "Processing";
                                                                        }
                                                                        ?></button>
                                                        </div>
                                                    <?php echo form_close(); ?>
                                                </div>
                                            </div>
                                        <?php }?>
                                    </div>
                                </div>

                                <!-- HOW TO PAY TAB -->
                                <div class="tab-pane <?php if($this->group->status ==1){echo 'active';}?>" id="procedure">
                                    <div class="row">
                                
                                            <?php if(preg_match('/(eazzy)/',$_SERVER['HTTP_HOST'])||preg_match('/(sandbox)/',$_SERVER['HTTP_HOST'])){?>
                                                <div class="col-md-12">
                                                    <ul class="media-list">
                                                        <li class="media">
                                                            <div class="media-body">
                                                                <h4 class="media-heading">

                                                                    <?php
                                                                        $default_message='Your <span class="font-red">"'.$this->application_settings->application_name.' Group Number"';
                                                                        $this->languages_m->translate('identify_your_group',$default_message);
                                                                    ?>
                                                                </span></h4>
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
                                                                        <h5 class="media-heading"><?php
                                                                                if($this->lang->line('equitel_sim_card')){
                                                                                echo $this->lang->line('equitel_sim_card');
                                                                                }else{
                                                                                echo "Equitel Sim Card";
                                                                                }
                                                                            ?></h5>
                                                                        <ul> 
                                                                            <li><?php
                                                                                    if($this->lang->line('go_to_equitel_toolkit')){
                                                                                    echo $this->lang->line('go_to_equitel_toolkit');
                                                                                    }else{
                                                                                    echo "Go to Equitel Sim toolkit on your phone";
                                                                                    }
                                                                                ?></li>
                                                                            <li><?php
                                                                                    if($this->lang->line('go_to_my_money')){
                                                                                    echo $this->lang->line('go_to_my_money');
                                                                                    }else{
                                                                                    echo "Then go to My Money";
                                                                                    }
                                                                                ?>
                                                                            </li>
                                                                            <li><?php
                                                                                    if($this->lang->line('go_to_send')){
                                                                                    echo $this->lang->line('go_to_send');
                                                                                    }else{
                                                                                    echo "Then go to Send/Pay";
                                                                                    }
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
                                                                            <li><?php
                                                                                    if($this->lang->line('amount_to_pay_and_confirm')){
                                                                                    echo $this->lang->line('amount_to_pay_and_confirm');
                                                                                    }else{
                                                                                    echo "Enter the Amount to pay - (Amount Quoted on invoice ) and confirm";
                                                                                    }
                                                                                ?>
                                                                            </li>
                                                                            <li><?php
                                                                                    if($this->lang->line('confirm_details')){
                                                                                    echo $this->lang->line('confirm_details');
                                                                                    }else{
                                                                                    echo "Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment)";
                                                                                    }
                                                                                ?>
                                                                             </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="media">
                                                                    <div class="media-body">
                                                                        <h5 class="media-heading"><?php
                                                                                    if($this->lang->line('eazzy_banking_app')){
                                                                                    echo $this->lang->line('eazzy_banking_app');
                                                                                    }else{
                                                                                    echo "Eazzy Banking App";
                                                                                    }
                                                                                ?></h5>
                                                                        <ul> 
                                                                            <li><?php
                                                                                    if($this->lang->line('go_to_paybill_option')){
                                                                                    echo $this->lang->line('go_to_paybill_option');
                                                                                    }else{
                                                                                    echo "Go To Paybill Option";
                                                                                    }
                                                                                ?></li>
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
                                                                            <li><?php
                                                                                    if($this->lang->line('amount_to_pay_and_confirm')){
                                                                                    echo $this->lang->line('amount_to_pay_and_confirm');
                                                                                    }else{
                                                                                    echo "Enter the Amount to pay - (Amount Quoted on invoice ) and confirm";
                                                                                    }
                                                                                ?></li>
                                                                            <li>
                                                                                <?php
                                                                                    if($this->lang->line('confirm_details')){
                                                                                    echo $this->lang->line('confirm_details');
                                                                                    }else{
                                                                                    echo "Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment)";
                                                                                    }
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
                                                                <div class="note note-danger" style="border:1px solid #eee;border-radius:5px;">
                                                                    <h4 class="block">
                                                                    <?php
                                                                        $default = 'Your '.$this->application_settings->application_name.' Group Number:';
                                                                        $this->languages_m->translate('your_chamasoft_group_number',$default);
                                                                    ?>
                                                                    <strong><?php echo $this->group->account_number; ?></strong>
                                                                    </h4>
                                                                    <p> 
                                                                        <?php
                                                                            $default = 'You will use this when paying for your '.$this->application_settings->application_name.' subscriptions or when communicating with '.$this->application_settings->application_name.' support to identify your group';
                                                                            $this->languages_m->translate('identify_your_group',$default);
                                                                        ?>                
                                                                    </p>
                                                                </div>
                                                                
                                                                <!-- Nested media object -->
                                                                <h4 class="media-heading">
                                                                    <?php
                                                                        $default_message='How to pay for your group subscription';
                                                                        $this->languages_m->translate('how_to_pay_for_group_subscription',$default_message);
                                                                    ?>
                                                                </h4>
                                                                <!-- <p>
                                                                    Do you have a promotion coupon?
                                                                    <div class="form-group">
                                                                        <label>Enter coupon here</label>
                                                                        <div class="input-group">
                                                                           <?php echo form_input('coupon','','class="form-control" placeholder="Have coupon, enter here "');?>`
                                                                        </div>
                                                                        <a href="" class="apply_coupon" >Apply Now</a>
                                                                    </div>
                                                                </p> -->

                                                                <a href="#gSubscrModal" style="margin-left:5px;margin-top:20px;margin-bottom:10px;" data-toggle="modal" role="button" class="btn btn-circle btn-lg green" data-backdrop="static" data-keyboard="false">Pay Now <i class="fa fa-chevron-circle-right"></i></a>
                                                                <!--BEGIN ACCORDION--
                                                                <div class="panel-group accordion" id="howtoAccordion" style="margin-top:10px;">
                                                                    <style>.panel-default{border:1px solid #ddd;}.media-body ul li{margin-left:-20px!important;}</style>
                                                                    <div class="panel panel-default">
                                                                        
                                                                        <?php if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])||preg_match('/(sandbox)/',$_SERVER['HTTP_HOST'])){?>

                                                                        <?php }else{ ?>
                                                                        
                                                                        <div class="panel-heading">
                                                                            <h4 class="panel-title">
                                                                                <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#howtoAccordion" href="#collapse_3_1" aria-expanded="true">
                                                                                    <?php if($this->lang->line('mpesa')){ echo $this->lang->line('mpesa'); }else{ echo "M-Pesa"; } ?>
                                                                                </a>
                                                                            </h4>
                                                                        </div>
                                                                        <div id="collapse_3_1" class="panel-collapse collapse in" aria-expanded="true" style="">
                                                                            <div class="panel-body">
                                                                                <div class="media">
                                                                                    <div class="media-body">
                                                                                        
                                                                                        <a href="#gSubscrModal" style="margin-left:5px;margin-bottom:10px;" data-toggle="modal" role="button" class="btn btn-circle btn-sm green" data-backdrop="static" data-keyboard="false">Pay Now <i class="fa fa-chevron-circle-right"></i></a>
                                                                                        
                                                                                        <ul> 
                                                                                            <li><?php
                                                                                                    if($this->lang->line('go_to_mpesa')){
                                                                                                    echo $this->lang->line('go_to_mpesa');
                                                                                                    }else{
                                                                                                    echo "Go to MPesa on your phone toolkit";
                                                                                                    }
                                                                                                ?></li>
                                                                                            <li><?php
                                                                                                    if($this->lang->line('go_to_lipa_na_mpesa')){
                                                                                                    echo $this->lang->line('go_to_lipa_na_mpesa');
                                                                                                    }else{
                                                                                                    echo "Then go to Lipa na MPesa";
                                                                                                    }
                                                                                                ?></li>
                                                                                            <li>

                                                                                                <?php
                                                                                                    if($this->lang->line('go_to_paybill')){
                                                                                                    echo $this->lang->line('go_to_paybill');
                                                                                                    }else{
                                                                                                    echo "Then go to Paybill";
                                                                                                    }
                                                                                                ?>
                                                                                                    
                                                                                            </li>
                                                                                            <li>
                                                                                                <?php
                                                                                                $default_message = "Then go to pay bill and enter ".$this->application_settings->application_name." Pay bill Number - 967600 and confirm";
                                                                                                    $this->languages_m->translate('pay_bill_and_confirm',$default_message);
                                                                                                    
                                                                                                ?>

                                                                                            </li>
                                                                                            <li>
                                                                                                <?php
                                                                                                    $default_message='Enter Chama Account Number - <strong>'.$this->group->account_number.' </strong> and confirm';
                                                                                                    $this->languages_m->translate('account_number_and_confirm',$default_message);
                                                                                                ?>
                                                                                            </li>
                                                                                            <li><?php
                                                                                                    if($this->lang->line('amount_to_pay_and_confirm')){
                                                                                                    echo $this->lang->line('amount_to_pay_and_confirm');
                                                                                                    }else{
                                                                                                    echo "Enter the Amount to pay - (Amount Quoted on invoice ) and confirm";
                                                                                                    }
                                                                                                ?></li>
                                                                                            <li><?php
                                                                                                    if($this->lang->line('confirm_details')){
                                                                                                    echo $this->lang->line('confirm_details');
                                                                                                    }else{
                                                                                                    echo "Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment)";
                                                                                                    }
                                                                                                ?></li>
                                                                                        </ul>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php } ?>
                                                                    <div class="panel panel-default">
                                                                        <div class="panel-heading">
                                                                            <h4 class="panel-title">
                                                                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#howtoAccordion" href="#collapse_3_2" aria-expanded="false">
                                                                                    <?php $default_message='Equitel Sim Card'; $this->languages_m->translate('equitel_sim_card',$default_message); ?>
                                                                                </a>
                                                                            </h4>
                                                                        </div>
                                                                        <div id="collapse_3_2" class="panel-collapse collapse" aria-expanded="false" style="">
                                                                            <div class="panel-body">
                                                                                <div class="media">
                                                                                    <div class="media-body">
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
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="panel panel-default">
                                                                        <div class="panel-heading">
                                                                            <h4 class="panel-title">
                                                                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#howtoAccordion" href="#collapse_3_3" aria-expanded="false">
                                                                                    <?php $default_message='Eazzy Banking App'; $this->languages_m->translate('eazzy_banking_app',$default_message); ?>
                                                                                </a>
                                                                            </h4>
                                                                        </div>
                                                                        <div id="collapse_3_3" class="panel-collapse collapse" aria-expanded="false" style="">
                                                                            <div class="panel-body">
                                                                                <div class="media">
                                                                                    <div class="media-body">
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
                                                                                            </strong></li>
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
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="panel panel-default">
                                                                        <div class="panel-heading">
                                                                            <h4 class="panel-title">
                                                                                <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#howtoAccordion" href="#collapse_3_4" aria-expanded="false">
                                                                                    <?php $default_message='Cheque'; $this->languages_m->translate('cheque',$default_message); ?>
                                                                                </a>
                                                                            </h4>
                                                                        </div>
                                                                        <div id="collapse_3_4" class="panel-collapse collapse" aria-expanded="false" style="">
                                                                            <div class="panel-body">
                                                                                <div class="media">
                                                                                    <div class="media-body">
                                                                                        <ul> 
                                                                                            <li>
                                                                                                <?php
                                                                                                    $default_message='Write a cheque to Risk Tick Credit Limited, kindly note the amount is on the proforma invoice sent your email address';
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
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                --END ACCORDION-->
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php } ?>

                                    </div>
                                </div>
                                <!-- END HOW TO PAY TAB -->
                                <!-- TOP UP SMSES TAB -->
                                <div class="tab-pane" id="smses">
                                    <div class="row">
                                        <div class="col-md-12">

                                            <ul class="media-list">
                                                <li class="media">
                                                    <div class="media-body">
                                                        <div class="note note-danger" style="border:1px solid #eee;border-radius:5px;">
                                                            <h4 class="block">
                                                            <?php
                                                                $default_message='Your '.$this->application_settings->application_name.'Group SMS Number:';
                                                                $this->languages_m->translate('your_group_sms',$default_message);
                                                            ?>
                                                            <strong>SMS<?php echo $this->group->account_number; ?></strong>
                                                            </h4>
                                                            <p> 
                                                                <?php
                                                                    $default_message='You will use this when purchasing your '.$this->application_settings->application_name.' group SMSes';
                                                                    $this->languages_m->translate('purchasing_sms',$default_message);
                                                                ?>               
                                                            </p>
                                                        </div>
                                                        
                                                        <!-- Nested media object -->
                                                        <h4 class="media-heading">
                                                            <?php
                                                                $default_message='How to Top Up SMSes';
                                                                $this->languages_m->translate('how_to_top_up_sms',$default_message);
                                                            ?>
                                                        </h4>

                                                        <a href="#gSubscrModal" style="margin-left:5px;margin-top:20px;margin-bottom:10px;" data-toggle="modal" role="button" class="btn btn-circle btn-lg green" data-backdrop="static" data-keyboard="false">Pay Now <i class="fa fa-chevron-circle-right"></i></a>
                                                        <!--
                                                        <?php if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])||preg_match('/(sandbox)/',$_SERVER['HTTP_HOST'])){?>

                                                        <?php }else{ ?>
                                                        <div class="media">
                                                            <div class="media-body">
                                                                <h5 class="media-heading" style="font-weight:bold;">
                                                                    <?php
                                                                        $default_message='MPesa';
                                                                        $this->languages_m->translate('mpesa',$default_message);
                                                                    ?>
                                                                </h5>
                                                                <a href="#gSubscrModal" style="margin-left:3px;margin-bottom:10px;" data-toggle="modal" role="button" class="btn btn-circle btn-sm green" data-backdrop="static" data-keyboard="false">Pay Now <i class="fa fa-chevron-circle-right"></i></a>
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
                                                                    <li>Enter the Group SMS Number - SMS<?php echo $this->group->account_number;?> and confirm</li>
                                                                    <li>
                                                                        <?php
                                                                            $default_message='Enter the Amount to pay - (Amount Quoted on invoice ) and confirmEnter the Amount to pay - (Bundle Details Below) and confirm';
                                                                            $this->languages_m->translate('enter_amount_to_pay_bundle',$default_message);
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
                                                        <?php } ?>
                                                        <div class="media">
                                                            <div class="media-body">
                                                                <h5 class="media-heading" style="font-weight:bold;">
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
                                                                    <li>Enter Group SMS Number - <strong>SMS<?php echo $this->group->account_number; ?></strong> and confirm</li>
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
                                                        -->
                                                        <p>&nbsp;</p>
                                                        <h4 class="media-heading" style="margin-bottom:-10px;">SMSes Prices</h4>
                                                        <div class="media">
                                                            <div class="media-body">
                                                                <div class="panel panel-info">
                                                                    <!-- Default panel contents -->
                                                                    <div class="" style="margin-top:20px;">
                                                                        <div class="">
                                                                            <!-- Table -->
                                                                            <table class="table table-condensed">
                                                                                <thead>
                                                                                    <th width="8px">#</th>
                                                                                    <th>Price</th>
                                                                                    <th>Number of SMSes</th>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>1</td>
                                                                                        <td>
                                                                                            <?php echo $this->default_country->currency_code.' '.number_to_currency(convert_currency(2,'KES',$this->default_country->currency_code)); ?>  
                                                                                        </td>
                                                                                        <td>1 SMS</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2</td>
                                                                                        <td>
                                                                                            <?php echo $this->default_country->currency_code.' '.number_to_currency(convert_currency(200,'KES',$this->default_country->currency_code)); ?> 
                                                                                        </td>
                                                                                        <td>100 SMSes</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>3</td>
                                                                                        <td>
                                                                                            <?php echo $this->default_country->currency_code.' '.number_to_currency(convert_currency(500,'KES',$this->default_country->currency_code)); ?> 
                                                                                        </td>
                                                                                        <td>300 SMSes</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>4</td>
                                                                                        <td>
                                                                                            <?php echo $this->default_country->currency_code.' '.number_to_currency(convert_currency(1000,'KES',$this->default_country->currency_code)); ?> 
                                                                                        </td>
                                                                                        <td>700 SMSes</td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- END TOP UP SMSES TAB -->
                                <!-- BILLING INVOICES TAB -->
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
                                                    ?>

                                                </th>
                                                <th class="text-right">Subscription(<?php echo $this->default_country->currency_code; ?>)</th>
                                                <th class="text-right">Tax(<?php echo $this->default_country->currency_code; ?>)</th>
                                                <th class="text-right">Total(<?php echo $this->default_country->currency_code; ?>)</th>
                                                <th class="text-right">
                                                    <?php
                                                        $default_message='Balance';
                                                        $this->languages_m->translate('balance',$default_message);
                                                    ?>
                                                </th>
                                                <th>
                                                    <?php
                                                        $default_message='Status';
                                                        $this->languages_m->translate('status',$default_message);
                                                    ?>

                                                </th>
                                                <th>
                                                    <?php
                                                        $default_message='Actions';
                                                        $this->languages_m->translate('actions',$default_message);
                                                    ?>

                                                </th>
                                            </thead>
                                            <tbody>
                                                <?php $i=0; $total_tax=0;$total_subscription=0;$total_amount=0;$total_balance=0;
                                                foreach($invoices as $invoice):?>
                                                    <tr>
                                                        <td><?php echo ++$i;?></td>
                                                        <td><?php echo timestamp_to_date($invoice->due_date);?></td>
                                                        <td class="text-right"><?php
                                                                    echo $billing_cycles[$invoice->billing_cycle].' amount: '.number_to_currency($invoice->amount - $invoice->tax-$invoice->prorated_amount).'<br/>';
                                                                    echo 'Prorated amount: '.number_to_currency($invoice->prorated_amount);
                                                                    $subscription=$invoice->amount-$invoice->tax;
                                                                    if($invoice->prorated_amount){
                                                                        $prorated = TRUE;
                                                                    }else{
                                                                        $prorated = FALSE;
                                                                    }
                                                        ?></td>
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
                                                    <td><a href="#gSubscrModal" data-toggle="modal" role="button" class="btn btn-xs green" data-backdrop="static" data-keyboard="false">Pay Now <i class="fa fa-chevron-circle-right"></i></a></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <?php if($prorated){
                                            echo 'N/B: Prorated amount is the subscriptions charges before the month end.';
                                        }
                                        ?>
                                    </div>
                                    <?php }else{
                                        $total_balance = ($payable_amount->amount+$payable_amount->tax);
                                        ?>
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
                                <!-- END BILLING INVOICES TAB -->
                                <!-- SUBSCRIPTION PAYMENTS TAB -->
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
                                                    ?>
                                                </th>
                                                <th>Payment Method</th>
                                                <th>Tax(<?php echo $this->default_country->currency_code; ?>)</th>
                                                <th class="text-right">Total(<?php echo $this->default_country->currency_code; ?>)</th>
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
                                                        <td><?php echo timestamp_to_message_time($payment->receipt_date);?></td>
                                                        <td>
                                                            <?php 
                                                                echo $this->billing_settings->payment_method[$payment->payment_method];
                                                                echo ' - '.$payment->ipn_transaction_code.' - '.$payment->billing_receipt_number;
                                                            ?>
                                                        </td>
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
                                <!-- END SUBSCRIPTION PAYMENTS TAB -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>



<!--M-Pesa payment and modal-->
<style>
    .mt-radio{
        border:1px solid #eee;
        padding:10px;
        width:260px;
        margin-top:5px;
        margin-left:0px;
        border-radius:2px;
        transition:0.2s;
        cursor: pointer;
    }
    .mt-radio:hover{
        background-color: rgba(0,255,0,0.05);
        border:1px solid rgba(0,255,0,0.1);
        transition:0.2s;
    }
    .rChkd{
        background-color: rgba(0,255,0,0.1);
        border:1px solid rgba(0,255,0,0.2);
    }
    .tab-hdrs{
        border-bottom:3px solid #e6e6e6;
        margin-bottom:20px;
        margin-top:-16px;
    }
    .arrow-up::before {
        border-style: solid;
        border-color: #e6e6e6;
        background-color: #fff;
        border-width: 0.25em 0.25em 0 0;
        content: '';
        display: inline-block;
        height: 1.5em;
        position: relative;
        top: 0.6em;
        transform: rotate(-45deg);
        vertical-align: top;
        width:1.5em;
    }
    .tab-hdr-cont{
        padding:0px;
        padding-top:10px;
        border:0px solid #333;
        margin-bottom:-3px;
        text-align:center;
        cursor:pointer;
    }
    .tab-hdr-cont.selec{
        color:#26A1AB;
    }
    .tab-hdr-cont.selec h4{
        color:#26A1AB;
        font-weight:900;
    }
    .tab-hdr-cont.selec p{
        color:#26A1AB;
        font-weight:600;
    }
    .tab-hdr-cont h4{
        font-weight:400;
        padding:0px;
        margin-bottom:0px;
    }
    .tab-hdr-cont p{
        font-size:9px;
        padding:0px;
        margin-top:-10px;
        margin-bottom:10px;
    }
    
    a.colpse-title{
        text-decoration:none;
    }
    .colpse-title:hover, .colpse-title:active, .colpse-title:visited{
        text-decoration:none;
    }
    .colpse-title .colpse-icon{
        font-size:24px;
        font-weight:400;
        color:#26A1AB;
    }
    .colpse-title h4{
        font-weight:400;
        color:#26A1AB;
    }
    .colpse-body,.paypal-tab-content{
        padding-top:5px;
        padding-left:20px;
    }
    .colpse-hide{
        display:none;
    }
    .c-ul{
        padding:0px!important;
        padding-left:18px!important;
    }

    .paypal-tab-content .selPMode,.p_amnt3{
        display: none;
    }

</style>
<!-- Begin M-Pesa payment button and modal -->
<div id="gSubscrModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <!--
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Pay Now</h4>
            </div>
            -->
            <div class="modal-body">
                <div class="tab-hdrs row">
                    <?php if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])||preg_match('/(sandbox)/',$_SERVER['HTTP_HOST'])){?>
                        <div id="tab-paypal" class="tab-hdr-cont col-sm-4 selec">
                            <h4><img src="<?php echo site_url('templates/admin_themes/groups/img/paypal_small.png');?>" height="30px" alt="PayPal"></h4>
                            <p>Pay using paypal</p>
                            <div id="tab-arrow" class=""></div>
                        </div>
                        <div id="tab-equity" class="tab-hdr-cont col-sm-4 selec">
                            <h4><img src="<?php echo site_url('templates/admin_themes/groups/img/equity_small.png');?>" height="30px" alt="Equity"></h4>
                            <p>Pay using Equity services</p>
                            <div id="tab-arrow" class="arrow-up"></div>
                        </div>
                        <div id="tab-cheque" class="tab-hdr-cont col-sm-4">
                            <h4><img src="<?php echo site_url('templates/admin_themes/groups/img/cheque_small.png');?>" height="30px" alt="Cheque"></h4>
                            <p>Pay using bankers cheque</p>
                            <div id="tab-arrow" class=""></div>
                        </div>
                    <?php }else{  ?>
                        <div id="tab-paypal" class="tab-hdr-cont col-sm-3 selec">
                            <h4><img src="<?php echo site_url('templates/admin_themes/groups/img/paypal_small.png');?>" height="30px" alt="PayPal"></h4>
                            <p>Pay using paypal</p>
                            <div id="tab-arrow" class=""></div>
                        </div>
                        <div id="tab-equity" class="tab-hdr-cont col-sm-3">
                            <h4><img src="<?php echo site_url('templates/admin_themes/groups/img/equity_small.png');?>" height="30px" alt="Equity"></h4>
                            <p>Pay using Equity services</p>
                            <div id="tab-arrow" class=""></div>
                        </div>
                        <div id="tab-cheque" class="tab-hdr-cont col-sm-3">
                            <h4><img src="<?php echo site_url('templates/admin_themes/groups/img/cheque_small.png');?>" height="30px" alt="Cheque"></h4>
                            <p>Pay using bankers cheque</p>
                            <div id="tab-arrow" class=""></div>
                        </div>
                        <div id="tab-mpesa" class="tab-hdr-cont col-sm-3 selec">
                            <h4><img src="<?php echo site_url('templates/admin_themes/groups/img/mpesalogo_small.png');?>" height="30px" alt="M-Pesa"></h4>
                            <p>Pay using M-Pesa</p>
                            <div id="tab-arrow" class="arrow-up"></div>
                        </div>
                    <?php }?>
                </div>
                <?php if(preg_match('/(eazzyclub)/',$_SERVER['HTTP_HOST'])||preg_match('/(sandbox)/',$_SERVER['HTTP_HOST'])){?>
                    <div class="paypal-tab" style="display:none;">
                        <div class="paypal-tab-content">
                            <div class="pAlert"></div>
                            <div class="row">
                                <div class="col-md-12" style="">
                                    <div class="form-group">
                                        <h5><strong>What are you paying for?</strong></h5>
                                        <h6>Choose the service you want to pay for.</h6>
                                        <select name="service_" id="" class="form-control pServ_paypal col-sm-12 append-select2" style="width: 100%;">
                                            <option value = '' selected="selected">--Select Option--</option>
                                            <option value="sub">Chamasoft Group Subscription</option>
                                            <option value="sms">Chamasoft Group SMSes</option>
                                        </select>
                                    </div>
                                    <div class="form-group selPMode">
                                        <h5><strong>Mode of payment</strong></h5>
                                        <h6>Choose to pay the full amount or partial amount.</h6>
                                        <div class="mt-radio-inline">
                                            <label class="mt-radio">
                                                <input type="radio" name="paymentMode" id="paymentMode2" value="partial" checked=""> Partial Payment
                                                <span></span>
                                            </label>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="paymentMode" id="paymentMode1" value="full"> Full Payment (<?php echo $this->default_country->currency_code.' '.number_to_currency($total_balance); ?>)
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12 p_amnt3">
                                    <label class="sr-only" for="exampleInputEmail22">Amount to pay</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <?php echo $this->default_country->currency_code?>
                                        </span>
                                        <input type="text" class="form-control currency" name="amount_pay" id="amount_pay" placeholder="Amount to pay">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button class="btn green paypalpreparepayment" style="float:right;margin-bottom:10px;" type="submit"><i class="fa fa-check"></i> Pay Now</button>
                                </div>
                            </div>
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>
                    <div class="equity-tab">
                        
                        <a class="colpse-title equitel-lnk" href="javascript:;"><h4><i class="colpse-icon fa fa-angle-down"></i> Equitel SIM Card</h4></a>
                        <div class="colpse-body equitel-info">
                            <ul class="c-ul"> 
                                <li>
                                    Go to Equitel Sim toolkit on your phone                                                                                            </li>
                                <li>
                                    Then go to My Money                                                                                            </li>
                                <li>
                                        Then go to Send/Pay                                                                                            </li>
                                <li>
                                    Then go to pay bill and enter Chamasoft Pay bill Number - <strong>967600</strong> and confirm                                                                                            </li>
                                <li>
                                    Enter Chama Account Number - <strong><?php echo $this->group->account_number;?> </strong> and confirm                                                                                            </li>
                                <li>
                                    Enter the Amount to pay - (Amount Quoted on invoice ) and confirm                                                                                            </li>
                                <li>
                                    Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment)                                                                                            </li>
                            </ul>
                        </div>
                        <a class="colpse-title eazzy-lnk" href="javascript:;"><h4><i class="colpse-icon fa fa-angle-right"></i> Eazzy Banking App</h4></a>
                        <div class="colpse-body eazzy-info" style="display:none;">
                            <ul class="c-ul"> 
                                <li>
                                    Go To Paybill Option                                                                                            </li>
                                <li>
                                    Enter <?php echo $this->application_settings->application_name;?> Pay bill number - 967600 and confirm.                                                                                            </li>
                                <li>
                                    Enter your <?php echo $this->application_settings->application_name;?> group number - <strong>259316</strong>                                                                                            </li>
                                <li>
                                    Enter the Amount to pay - (Amount Quoted on invoice) and confirm.                                                                                            </li>
                                <li>
                                    Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment)                                                                                             </li>
                            </ul>
                        </div>

                    </div>
                    <div class="cheque-tab" style="display:none;">
                        <ul class="c-ul"> 
                            <li>
                                Write a cheque to Risk Tick Credit Limited, kindly note the amount is on the proforma invoice sent your email address                                                                                            </li>
                            <li>
                                Deliver the cheque to our offices at Upper Hill, Elgon Court Block D1 along Ralph Bunche Road off Ngong Road                                                                                            </li>
                            <li>
                                Upon receipt our sales team will activate your account for the period subscribed for                                                                                            </li>
                        </ul>
                    </div>
                <?php }else{?>
                    <div class="paypal-tab" style="display:none;">
                        <div class="paypal-tab-content">
                            <div class="pAlert"></div>
                            <div class="row">
                                <div class="col-md-12" style="">
                                    <div class="form-group">
                                        <h5><strong>What are you paying for?</strong></h5>
                                        <h6>Choose the service you want to pay for.</h6>
                                        <select name="service_" id="" class="form-control pServ_paypal col-sm-12 append-select2" style="width: 100%;">
                                            <option value = '' selected="selected">--Select Option--</option>
                                            <option value="sub">Chamasoft Group Subscription</option>
                                            <option value="sms">Chamasoft Group SMSes</option>
                                        </select>
                                    </div>
                                    <div class="form-group selPMode">
                                        <h5><strong>Mode of payment</strong></h5>
                                        <h6>Choose to pay the full amount or partial amount.</h6>
                                        <div class="mt-radio-inline">
                                            <label class="mt-radio">
                                                <input type="radio" name="paymentMode" id="paymentMode2" value="partial" checked=""> Partial Payment
                                                <span></span>
                                            </label>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <label class="mt-radio">
                                                <input type="radio" name="paymentMode" id="paymentMode1" value="full"> Full Payment (<?php echo $this->default_country->currency_code.' '.number_to_currency($total_balance); ?>)
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12 p_amnt3">
                                    <label class="sr-only" for="exampleInputEmail22">Amount to pay</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <?php echo $this->default_country->currency_code;?>
                                        </span>
                                        <input type="text" class="form-control currency" name="amount_pay" id="amount_pay" placeholder="Amount to pay">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button class="btn green paypalpreparepayment" style="float:right;margin-bottom:10px;" type="submit"><i class="fa fa-check"></i> Pay Now</button>
                                </div>
                            </div>
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>
                    <div class="equity-tab" style="display:none;">
                        
                        <a class="colpse-title equitel-lnk" href="javascript:;"><h4><i class="colpse-icon fa fa-angle-down"></i> Equitel SIM Card</h4></a>
                        <div class="colpse-body equitel-info">
                            <ul class="c-ul"> 
                                <li>
                                    Go to Equitel Sim toolkit on your phone                                                                                            </li>
                                <li>
                                    Then go to My Money                                                                                            </li>
                                <li>
                                        Then go to Send/Pay                                                                                            </li>
                                <li>
                                    Then go to pay bill and enter Chamasoft Pay bill Number - <strong>967600</strong> and confirm                                                                                            </li>
                                <li>
                                    Enter Chama Account Number - <strong><?php echo $this->group->account_number;?> </strong> and confirm                                                                                            </li>
                                <li>
                                    Enter the Amount to pay - (Amount Quoted on invoice ) and confirm                                                                                            </li>
                                <li>
                                    Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment)                                                                                            </li>
                            </ul>
                        </div>
                        <a class="colpse-title eazzy-lnk" href="javascript:;"><h4><i class="colpse-icon fa fa-angle-right"></i> Eazzy Banking App</h4></a>
                        <div class="colpse-body eazzy-info" style="display:none;">
                            <ul class="c-ul"> 
                                <li>
                                    Go To Paybill Option                                                                                            </li>
                                <li>
                                    Enter <?php echo $this->application_settings->application_name;?> Pay bill number - 967600 and confirm.                                                                                            </li>
                                <li>
                                    Enter your <?php echo $this->application_settings->application_name;?> group number - <strong>259316</strong>                                                                                            </li>
                                <li>
                                    Enter the Amount to pay - (Amount Quoted on invoice) and confirm.                                                                                            </li>
                                <li>
                                    Make sure the details entered are correct, then proceed to confirm the payment. (You will receive an SMS once the payment is received, confirming the receipt of the payment)                                                                                             </li>
                            </ul>
                        </div>

                    </div>
                    <div class="cheque-tab" style="display:none;">
                        <ul class="c-ul"> 
                            <li>
                                Write a cheque to Risk Tick Credit Limited, kindly note the amount is on the proforma invoice sent your email address                                                                                            </li>
                            <li>
                                Deliver the cheque to our offices at Upper Hill, Elgon Court Block D1 along Ralph Bunche Road off Ngong Road                                                                                            </li>
                            <li>
                                Upon receipt our sales team will activate your account for the period subscribed for                                                                                            </li>
                        </ul>
                    </div>
                    <div class="mpesa-tab">
                        <form class="" id="mpesa_payment" action="POST" method="" role="form">
                            <a class="colpse-title xpress-lnk" href="javascript:;"><h4><i class="colpse-icon fa fa-angle-down"></i> M-Pesa Xpress Billing</h4></a>
                            <div class="colpse-body mpesa-xpress-info">
                                <div class="pAlert"></div>
                                <div class="row">
                                    <div class="col-md-12" style="">
                                        <!--    
                                        <h6 style="padding-bottom:10px;" class="">
                                            A request will be send to the phone number provided.
                                        </h6>
                                        -->
                                        <div class="form-group">
                                            <h5><strong>What are you paying for?</strong></h5>
                                            <h6>Choose the service you want to pay for.</h6>
                                            <select name="service_" id="" class="form-control pServ append-select2">
                                                <option value="sub">Chamasoft Group Subscription</option>
                                                <option value="sms">Chamasoft Group SMSes</option>
                                            </select>
                                        </div>
                                        <div class="form-group selPMode">
                                            <h5><strong>Mode of payment</strong></h5>
                                            <h6>Choose to pay the full amount or partial amount.</h6>
                                            <div class="mt-radio-inline">
                                                <label class="mt-radio">
                                                    <input type="radio" name="paymentMode" id="paymentMode2" value="partial" checked=""> Partial Payment
                                                    <span></span>
                                                </label>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <label class="mt-radio">
                                                    <input type="radio" name="paymentMode" id="paymentMode1" value="full"> Full Payment (<?php echo $this->default_country->currency_code.' '.number_to_currency($total_balance); ?>)
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 p_amnt">
                                        <label class="sr-only" for="exampleInputEmail22">Amount to pay</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <?php echo $this->default_country->currency_code;?>
                                            </span>
                                            <input type="text" class="form-control currency" name="amount_pay" id="amount_pay mpesa_payamount" placeholder="Amount to pay">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 p_phone">
                                        <label class="sr-only" for="phoneNumber">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                +254
                                            </span>
                                            <input type="text" class="form-control" value="<?php echo raw_phone_number(valid_phone($this->user->phone)); ?>" name="phone_pay" id="phone_pay" placeholder="Phone number">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button class="btn green payBtn" style="float:right;margin-bottom:10px;" type="submit"><i class="fa fa-check"></i> Make Payment</button>
                                    </div>
                                </div>
                            </div>

                            <a class="colpse-title paybill-lnk" href="javascript:;"><h4><i class="colpse-icon fa fa-angle-right"></i> M-Pesa Paybill Instructions</h4></a>
                            <div class="colpse-body mpesa-paybill-info" style="display:none;">
                                <ul class="c-ul"> 
                                    <li>
                                        <?php
                                            $default_message='Go To Paybill Option';
                                            $this->languages_m->translate('go_to_pay_bill_option',$default_message);
                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                            $default_message='Enter Chamasoft Pay bill number - 967600 and confirm.';
                                            $this->languages_m->translate('chamasoft_paybill_number',$default_message);
                                        ?>
                                    </li>
                                    <li>
                                        <?php
                                            $default_message='Enter your '.$this->application_settings->application_name.' group number - 
                                            <ul>
                                                <li> <strong>'.$this->group->account_number.'</strong> for subscription and - </li>
                                                <li> <strong>SMS'.$this->group->account_number.'</strong> to buy group SMS </li>
                                            </ul>';
                                            $this->languages_m->translate('enter_chamasoft_group_number',$default_message);
                                        ?>
                                    </strong></li>
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
                        </form>
                    </div>
                <?php }?>
            </div>
            <div class="modal-footer">
                <!--<button class="btn green payBtn" type="submit"><i class="fa fa-check"></i> Make Payment</button>-->
                <button class="btn default closeBtn" data-dismiss="modal" aria-hidden="true"><i class="fa fa-remove"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End M-Pesa payment button and modal -->

<!--<script src="https://www.paypalobjects.com/api/checkout.js"></script>-->

<script>
    $(document).ready(function(){
        function receive_payment(amount,data,payfor){
            $(document).ready(function(){
                if(payfor == 'sms'){
                    var service_ = "sms"
                }else{
                    var service_ = "";
                }
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url(); ?>group/billing/receive_complete_billing',
                    data: {'amount': amount,'data':data,'service_':service_},
                    success: function(response){
                        if(response == 'Successful'){
                            toastr['success']('Your payment has been successfully received, You will receive an email and/or SMS notification for the same bill payment.','Bill payment successfully received');
                            location.reload();
                        }else{
                            alert('Could not complete payment '+response);
                        }
                    },
                    error: function(response){
                        alert('error');
                    }
                });
            });
        }

        function convert_currency(amount,content,payfor){
            var dolar_amount = 0;
            App.blockUI({
                target: content,
                overlayColor: 'grey',
                animate: true
            });

            if(payfor = 'sms'){
                var description = 'SMS purchase payment for <?php echo $this->group->name;?>';
            }else{
                var description = '<?php echo $this->group->name;?> subscription payment';
            }
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>group/billing/calculate_conversion",
                data: {'amount': amount},
                success: function(response){
                    App.unblockUI(content); 
                    pay_amount = response;
                    $('.paypal-tab-content input[name="amount_pay"]').attr('disabled','disabled');
                    $('.paypal-tab-content select[name="service_"]').attr('disabled','disabled');
                    $('.paypal-tab-content input[name="paymentMode"]').attr('disabled','disabled');                    
                    paypalpaymentAlerts('info','<strong>Pay USD '+pay_amount+'.</strong> To complete your transaction, select option below')
                    paypal.Button.render({
                        // Set your environment
                        env: 'production', // sandbox | production
                        // Specify the style of the button
                        style: {
                            layout: 'vertical',  // horizontal | vertical
                            size:   'medium',    // medium | large | responsive
                            shape:  'rect',      // pill | rect
                            color:  'gold'       // gold | blue | silver | white | black
                        },

                        // Specify allowed and disallowed funding sources
                        //
                        // Options:
                        // - paypal.FUNDING.CARD
                        // - paypal.FUNDING.CREDIT
                        // - paypal.FUNDING.ELV
                        funding: {
                            allowed: [
                                paypal.FUNDING.CARD,
                                paypal.FUNDING.CREDIT
                            ],
                            disallowed: []
                        },

                        // PayPal Client IDs - replace with your own
                        // Create a PayPal app: https://developer.paypal.com/developer/applications/create
                        client: {
                            sandbox: 'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R',
                            production: 'AVFVaViFoKQOjzyqmOmRr1_4SYsmxjICzpLhewcLRBJNU03UhGWQf41GgIL2BjMwVyD4HHlHDzV0rImD'
                        },

                        payment: function (data, actions) {
                            return actions.payment.create({
                                payment: {
                                    transactions: [{
                                        amount: {
                                            total: pay_amount,
                                            currency: 'USD'
                                        },
                                        description: description,
                                    }]
                                }
                            });
                        },

                        onAuthorize: function (data, actions) {
                            return actions.payment.execute().then(function () {
                                    App.blockUI({
                                        target: content,
                                        overlayColor: 'grey',
                                        animate: true
                                    });
                                    receive_payment(amount,data,payfor);
                            });
                        }
                    }, '#paypal-button-container');
                },
                error: function(response){
                    alert('error');
                }
            });
        }

        $(document).on('shown.bs.modal', "#gSubscrModal",function(e){
            $('.append-select2').select2();
            FormInputMask.init();
            //alert('Hello!');
        });
        $(document).on('hidden.bs.modal', "#gSubscrModal",function(e){
            $('.paypal-tab-content input[name="amount_pay"]').removeAttr('disabled','disabled').val('');
            $('.paypal-tab-content select[name="service_"]')
                    .removeAttr('disabled','disabled')
                    .val('')
                    .trigger('change');
            $('.paypal-tab-content input[name="paymentMode"]').removeAttr('disabled','disabled'); 
            $('.paypal-tab-content .pAlert').addClass('hidden');
            $('.paypalpreparepayment').show();
            $('#paypal-button-container').html(''); 
            $('#tab-mpesa').click();
            $('#uniform-paymentMode2').click();
            $('#uniform-paymentMode2 input[type="radio"]').click();
        });

        $('#tab-paypal').click(function(){
            $('#tab-mpesa #tab-arrow').removeClass('arrow-up');$('#tab-mpesa').removeClass('selec');
            $('#tab-equity #tab-arrow').removeClass('arrow-up');$('#tab-equity').removeClass('selec');
            $('#tab-cheque #tab-arrow').removeClass('arrow-up');$('#tab-cheque').removeClass('selec');
            $('#tab-paypal #tab-arrow').addClass('arrow-up');$('#tab-paypal').addClass('selec');
            $('.mpesa-tab').slideUp();$('.equity-tab').slideUp();$('.cheque-tab').slideUp();
            $('.paypal-tab').slideDown();
            $('#gSubscrModal .modal-footer .payBtn').slideUp();
        });
        $('#tab-mpesa').click(function(){
            $('#tab-paypal #tab-arrow').removeClass('arrow-up');$('#tab-paypal').removeClass('selec');
            $('#tab-equity #tab-arrow').removeClass('arrow-up');$('#tab-equity').removeClass('selec');
            $('#tab-cheque #tab-arrow').removeClass('arrow-up');$('#tab-cheque').removeClass('selec');
            $('#tab-mpesa #tab-arrow').addClass('arrow-up');$('#tab-mpesa').addClass('selec');
            $('.paypal-tab').slideUp();$('.equity-tab').slideUp();$('.cheque-tab').slideUp();
            $('.mpesa-tab').slideDown();
            $('#gSubscrModal .modal-footer .payBtn').slideDown();
        });
        $('#tab-equity').click(function(){
            $('#tab-paypal #tab-arrow').removeClass('arrow-up');$('#tab-paypal').removeClass('selec');
            $('#tab-mpesa #tab-arrow').removeClass('arrow-up');$('#tab-mpesa').removeClass('selec');
            $('#tab-cheque #tab-arrow').removeClass('arrow-up');$('#tab-cheque').removeClass('selec');
            $('#tab-equity #tab-arrow').addClass('arrow-up');$('#tab-equity').addClass('selec');
            $('.paypal-tab').slideUp();$('.mpesa-tab').slideUp();$('.cheque-tab').slideUp();
            $('.equity-tab').slideDown();
            $('#gSubscrModal .modal-footer .payBtn').slideUp();
        });
        $('#tab-cheque').click(function(){
            $('#tab-paypal #tab-arrow').removeClass('arrow-up');$('#tab-paypal').removeClass('selec');
            $('#tab-mpesa #tab-arrow').removeClass('arrow-up');$('#tab-mpesa').removeClass('selec');
            $('#tab-equity #tab-arrow').removeClass('arrow-up');$('#tab-equity').removeClass('selec');
            $('#tab-cheque #tab-arrow').addClass('arrow-up');$('#tab-cheque').addClass('selec');
            $('.paypal-tab').slideUp();$('.mpesa-tab').slideUp();$('.equity-tab').slideUp();
            $('.cheque-tab').slideDown();
            $('#gSubscrModal .modal-footer .payBtn').slideUp();
        });

        $('.xpress-lnk').click(function(){
            if($(this).hasClass('show_')){
                $('.mpesa-xpress-info').slideDown();
                $(this).find('.colpse-icon').addClass('fa-angle-down').removeClass('fa-angle-right');
                $(this).removeClass('show_');
                $('#gSubscrModal .modal-footer .payBtn').slideDown();
            }
            else{
                $('.mpesa-xpress-info').slideUp();
                $(this).find('.colpse-icon').addClass('fa-angle-right').removeClass('fa-angle-down');
                $(this).addClass('show_');
                $('#gSubscrModal .modal-footer .payBtn').slideUp();
            }
        });
        $('.paybill-lnk').click(function(){
            if($(this).hasClass('show_')){
                $('.mpesa-paybill-info').slideUp();
                $(this).find('.colpse-icon').addClass('fa-angle-right').removeClass('fa-angle-down');
                $(this).removeClass('show_');
            }
            else{
                $('.mpesa-paybill-info').slideDown();
                $(this).find('.colpse-icon').addClass('fa-angle-down').removeClass('fa-angle-right');
                $(this).addClass('show_');
                //$('.xpress-lnk').click();
            }
        });
        $('.equitel-lnk').click(function(){
            if($(this).hasClass('show_')){
                $('.equitel-info').slideDown();
                $(this).find('.colpse-icon').addClass('fa-angle-down').removeClass('fa-angle-right');
                $(this).removeClass('show_');
            }
            else{
                $('.equitel-info').slideUp();
                $(this).find('.colpse-icon').addClass('fa-angle-right').removeClass('fa-angle-down');
                $(this).addClass('show_');
            }
        });
        $('.eazzy-lnk').click(function(){
            if($(this).hasClass('show_')){
                $('.eazzy-info').slideUp();
                $(this).find('.colpse-icon').addClass('fa-angle-right').removeClass('fa-angle-down');
                $(this).removeClass('show_');
            }
            else{
                $('.eazzy-info').slideDown();
                $(this).find('.colpse-icon').addClass('fa-angle-down').removeClass('fa-angle-right');
                $(this).addClass('show_');
            }
        });
        
        var pmde = 'sub';
        var pmde_type = 'partial';
        $(document).on('change','select.pServ',function() {
            $('.pAlert').addClass('hidden');
            if(this.value=='sms'){
                pmde = 'sms';
                $('.selPMode').slideUp();
                //$('.selPMode').fadeOut('fast');
            }
            else if(this.value=='sub'){
                pmde = 'sub';
                $('.selPMode').slideDown();
                //$('.selPMode').fadeIn('fast');
            }
        });

        $(document).on('change','select.pServ_paypal',function() {
            $('.pAlert').addClass('hidden');
            if(this.value=='sms'){
                paypalpmde = 'sms';
                $('.paypal-tab-content .selPMode').slideUp();
                $('.p_amnt3').slideDown();
            }else if(this.value=='sub'){
                paypalpmde = 'sub';
                $('.paypal-tab-content .selPMode').slideDown();
                var paymentMode =  $('.paypal-tab-content input[name="paymentMode"]:checked').val();
                if(paymentMode == 'partial'){
                    $('.p_amnt3').slideDown();
                }else{
                    $('.p_amnt3').slideUp();
                }
            }else{
                paypalpmde = '';
                $('.paypal-tab-content .selPMode').slideUp();
                $('.p_amnt3').slideUp();
            }
        });

        $(document).on('change','.paypal-tab-content input[name="paymentMode"]',function(e){
            var paymentMode = $('.paypal-tab-content input[name="paymentMode"]:checked').val();
            if(paymentMode == 'partial'){
                $('.p_amnt3').slideDown();
            }else{
                $('.p_amnt3').slideUp();
            }
        });

        $(document).on('click','.paypalpreparepayment',function(e){
            var payfor = $('select.pServ_paypal').val();
            var paymentMode =  $('.paypal-tab-content input[name="paymentMode"]:checked').val();
            if(payfor == ''){
                paypalpaymentAlerts('danger','<strong>Error!</strong> Select what you are paying for');           
            }else{
                if(paymentMode == 'partial'){
                    var amount_pay = $('.paypal-tab-content input[name="amount_pay"]').val();
                }else{
                    var amount_pay = <?php echo $total_balance; ?>;
                }
                var content = $('.paypal-tab-content');
                $(this).hide();
                if(amount_pay){
                    convert_currency(amount_pay,content,payfor);
                }else{
                    paypalpaymentAlerts('danger', '<strong>Error!</strong> Amount being paid for is required.');
                }
                
            }        
            e.preventDefault();
        });

        $(document).on('click','a.apply_coupon',function(e){
            var coupon = $('input[name="coupon"]').val();
            if(coupon){
                $.ajax({
                type: "POST",
                    url: "<?php echo base_url(); ?>group/billing/calculate_coupon",
                    data: {'coupon': coupon},
                    success: function(response){
                    }
                });
            }
            e.preventDefault();
        });


        //toggle selected radio
        $("input[name='paymentMode']:checked").parent().addClass("rChkd");
        $("input[name='paymentMode']").change(function() {
            $('.pAlert').addClass('hidden');
            var pMde = $("input[name='paymentMode']:checked").val();
            $("input[name='paymentMode']").closest('label.mt-radio').removeClass("rChkd");
            $("input[name='paymentMode']:checked").closest('label.mt-radio').addClass("rChkd");
            if(pMde=='full'){
                pmde_type = 'full';
                $('#amount_pay').val('<?php echo $total_balance; ?>');
                $('.p_amnt').addClass('hidden');
                //$('.p_amnt3').slideUp();
                $('.p_phone').removeClass('col-md-6');
                $('.p_phone').addClass('col-md-12');
            }
            else if(pMde=='partial'){
                pmde_type = 'partial';
                $('#amount_pay').val('');
                $('.p_phone').removeClass('col-md-12');
                $('.p_phone').addClass('col-md-6');
                $('.p_amnt').removeClass('hidden');
                //$('.p_amnt3').slideDown();
            }
        });

        //remove error on value change
        $('#amount_pay').on('input',function(e){ $('.pAlert').addClass('hidden'); });
        $('#phone_pay').on('input',function(e){ $('.pAlert').addClass('hidden'); });
       /* $('#amount_pay').change(function(){ $('.pAlert').addClass('hidden'); });
        $('#phone_pay').change(function(){ $('.pAlert').addClass('hidden'); });*/

        $("#mpesa_payment").submit(function() {
            $('.payBtn').prop('disabled', true);
            $(".payBtn").html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            
            //if passed validation, send request
            if(valInputs()){
                dataString = $("#mpesa_payment").serialize();
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>/group/billing/make_mpesa_payment",
                    data: dataString,
                    success: function(response){
                        //console.log(response);
                        var data = $.parseJSON(response);
                        if(data){
                            if(data.status == 1){
                                paymentAlerts('success', '<strong>Success!</strong> A request was initiated successfully. Check your phone to continue.');
                                //reset submit button
                                $(".payBtn").html('<i class="fa fa-check"></i> Make Payment');
                                $('.payBtn').prop('disabled', false);
                                //close modal after 5 seconds
                                setTimeout(function() {
                                    $('#gSubscrModal').modal('hide');
                                    $('.pAlert').addClass('hidden');
                                    //$("#mpesa_payment").trigger("reset");//reset form
                                }, 5000);
                            }else{
                                paymentAlerts('danger', '<strong>Error!</strong> '+data.message);
                                $(".payBtn").html('<i class="fa fa-check"></i> Make Payment');
                                $('.payBtn').prop('disabled', false);
                            }
                        }
                    },
                    error: function(response){
                        //console.log(response);
                        paymentAlerts('danger', '<strong>Error!</strong> A request could not be initiated at this time, please try again later.');
                        //reset submit button
                        $(".payBtn").html('<i class="fa fa-check"></i> Make Payment');
                        $('.payBtn').prop('disabled', false);
                    }
                });
            }else{
                //reset submit button
                $(".payBtn").html('<i class="fa fa-check"></i> Make Payment');
                $('.payBtn').prop('disabled', false);
            };

            //disble submitting form
            return false;
        });

        function paypalpaymentAlerts(mde, msg){
            $('.paypal-tab-content .pAlert').addClass('hidden');
            $('.paypal-tab-content  .pAlert').html('<div class="alert alert-'+mde+' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>'+msg+'</div>');
            $('.paypal-tab-content  .pAlert').removeClass('hidden');
        }

        function paymentAlerts(mde, msg){
            $('.pAlert').addClass('hidden');
            $('.pAlert').html('<div class="alert alert-'+mde+' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>'+msg+'</div>');
            $('.pAlert').removeClass('hidden');
        }

        function valInputs(){
            //get input
            var pamnt = $('#mpesa_payamount').val();
            var pphne =$('#phone_pay').val();
            var isnum_p = /^\d+$/.test(pphne);

            if(pamnt==''){
                paymentAlerts('danger', '<strong>Error!</strong> Amount being paid for is required.');
                $('#amount_pay').focus();
                return false;
            }
            else if(pphne==''){
                paymentAlerts('danger', '<strong>Error!</strong> Phone number to use for transaction is required.');
                $('#phone_pay').focus();
                return false;
            }
            else if(pphne.length < 8){
                paymentAlerts('danger', '<strong>Error!</strong> Phone number used is too short. Kindly check and try again.');
                $('#phone_pay').focus();
                return false;
            }
            else if(pphne.length > 14){
                paymentAlerts('danger', '<strong>Error!</strong> Phone number used is too long. Kindly check and try again.');
                $('#phone_pay').focus();
                return false;
            }
            else if(!isnum_p){
                paymentAlerts('danger', '<strong>Error!</strong> Phone number contains invalid characters. Please check and try again.');
                $('#phone_pay').focus();
                return false;
            }
            else{
                return true;
            }
        }
    });
</script>