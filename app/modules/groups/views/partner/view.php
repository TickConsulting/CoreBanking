<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-green bold uppercase">                   
                        <?php echo $this->partner_menus_m->generate_page_title();?>
                    </span>
                    <div class="caption-desc font-grey-cascade">Group profile details: Group Information,Member Information,Contribution Settings,Bank Accounts and Billing Information. </div>
                </div>
                <?php echo $this->partner_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body">
                <div class="mt-element-list">
                    <div class="mt-list-head list-todo red">
                        <div class="list-head-title-container">
                            <h3 class="list-title">Group Profile Details</h3>
                            <div class="list-head-count">
                                <div class="list-head-count-item">
                                    <i class="fa fa-users"></i> Group Membership Size(Members Registered): <?php $active_size=$post->active_size?:1; echo $post->size.' ('.$active_size.')';?> </div>
                                <div class="list-head-count-item">
                                    <i class="fa fa-hand-paper-o"></i> Contributions: <?php echo count($contributions); ?></div>
                                <div class="list-head-count-item">
                                    <i class="fa fa-institution"></i> Bank Accounts: <?php echo count($bank_accounts); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-list-container list-todo">
                        <div class="list-todo-line"></div>
                        <ul>
                            <li class="mt-list-item">
                                <div class="list-todo-icon bg-white">
                                    <i class="fa fa-info"></i>
                                </div>
                                <div class="list-todo-item dark">
                                    <a class="list-toggle-container" data-toggle="collapse" href="#task-1" aria-expanded="false">
                                        <div class="list-toggle done uppercase">
                                            <div class="list-toggle-title bold">Group Information</div>
                                            <div class="badge badge-default pull-right bold"></div>
                                        </div>
                                    </a>
                                    <div class="task-list panel-collapse collapse in" id="task-1">
                                        <ul>
                                            <li class="task-list-item done">
                                                <div class="task-icon">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-users"></i>
                                                    </a>
                                                </div>
                                                <div class="task-content">
                                                    <h4 class="uppercase bold">
                                                        <a href="javascript:;"><?php echo $post->name; ?></a>
                                                    </h4>
                                                    <ul class="">
                                                        <?php
                                                            $owner = $this->ion_auth->get_user($post->owner);
                                                            $created_by = $this->ion_auth->get_user($post->created_by);
                                                        ?>
                                                        <li><strong>Group Number: </strong><?php echo $post->account_number; ?></li>
                                                        <li><strong>Signed Up On: </strong><?php echo timestamp_to_date($post->created_on); ?></li>
                                                        <li><strong>Group Registered By: </strong><?php echo $owner->first_name.' '.$owner->last_name;?></li>
                                                        <li><strong>E-mail: </strong><?php echo $owner->email?:"N/A"; ?></li>
                                                        <li><strong>Phone: </strong><?php echo $owner->phone?:"N/A"; ?></li>   
                                                        <li><strong>Created By: </strong><?php echo $created_by->first_name.' '.$created_by->last_name;?></li>
                                                        <li><strong>Group E-mail: </strong><?php echo $post->email?:$owner->email?:$created_by->email; ?></li>
                                                        <li><strong>Group Phone: </strong><?php echo $post->phone?:"N/A"; ?></li>        
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="task-list-item">
                                                <div class="task-icon">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-money"></i>
                                                    </a>
                                                </div>
                                                <div class="task-content">
                                                    <h4 class="uppercase bold">
                                                        <a href="javascript:;">Group Billing Information</a>
                                                    </h4>
                                                    Subscription Status: 
                                                    <?php
                                                        $status = $post->status;
                                                        if($status == 1)
                                                        {
                                                            echo '<span class="label label-xs label-success">Subscribed</span>';
                                                            if($arrears>0){
                                                                echo '&nbsp;<span class="label label-xs label-warning">In Arrears</span>';
                                                                echo '&nbsp;'.$this->default_country->currency_code.' '.number_to_currency($arrears).' In Arrears ';
                                                            }else{
                                                                echo '&nbsp;<span class="label label-xs label-info">Subscription Payments Upto Date</span>';
                                                            }
                                                            if(empty($invoices)){
                                                    ?>
                                                        <p>
                                                            <div class="alert alert-info">
                                                                <strong>Info!</strong> No billing invoices to display 
                                                            </div>
                                                        </p>
                                                    <?php
                                                            }else{
                                                    ?>
                                                        <hr/>
                                                        <p>
                                                            <table class="table table-striped table-bordered table-advance table-condensed table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="8px">#</th>
                                                                        <th width="">Billing Date </th>
                                                                        <th class="text-right">Amount Payable </th>
                                                                        <th class="text-right">Amount Paid </th>
                                                                        <th class="text-right">Balance </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $i = 0; foreach($invoices as $invoice): ?>
                                                                    <tr>
                                                                        <td><?php echo ++$i;?></td>
                                                                        <td><?php echo timestamp_to_date($invoice->due_date);?></td>
                                                                        <td class="text-right"><?php echo number_to_currency($subscription=$invoice->amount);?></td>
                                                                        <td class="text-right"><?php echo number_to_currency($amount=$invoice->amount_paid);?></td>
                                                                        <td class="text-right"><?php echo number_to_currency($subscription-$amount);?></td>
                                                                    </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </p>
                                                    <?php
                                                            }
                                                        }
                                                        else if($status == 2)
                                                        {
                                                            echo '<span class="label label-xs label-danger">Suspended</span>';
                                                        }
                                                        else
                                                        {
                                                            if($post->trial_days){
                                                                echo '<span class="label label-sx label-primary">On Trial</span>';
                                                            }else{
                                                                echo '<span class="label label-sx label-default">Expired on '.timestamp_to_date($post->trial_days_end_date).'</span>';
                                                            }
                                                        }
                                                    ?>   
                                                    <p></p>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="task-footer bg-grey">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <a class="task-trash" href="<?php echo site_url("partner/groups/edit/".$post->id); ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </div>                                                
                                                <div class="col-xs-6">
                                                    <a class="task-trash" target="_blank" href="<?php echo $this->application_settings->protocol.$post->slug.'.'.$this->application_settings->url; ?>">
                                                        <i class="fa fa-user-secret"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="mt-list-item">
                                <div class="list-todo-icon bg-white">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="list-todo-item dark">
                                    <a class="list-toggle-container" data-toggle="collapse" href="#task-2" aria-expanded="false">
                                        <div class="list-toggle done uppercase">
                                            <div class="list-toggle-title bold">Group Members</div>
                                            <div class="badge badge-default pull-right bold"><?php echo $post->active_size; ?></div>
                                        </div>
                                    </a>
                                    <div class="task-list panel-collapse collapse" id="task-2">
                                        <ul>
                                            <li class="task-list-item done">
                                                <div class="task-icon">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-list-alt"></i>
                                                    </a>
                                                </div>
                                                <div class="task-content">
                                                    <h4 class="uppercase bold">
                                                        <a href="javascript:;">Members List</a>
                                                    </h4>
                                                    <p>
                                                        <?php if($members):?>
                                                            <table class="table table-striped table-bordered table-advance table-condensed table-hover ">
                                                                <thead>
                                                                    <tr>
                                                                        <th>
                                                                            #
                                                                        </th>
                                                                        <th>
                                                                           Member Name
                                                                        </th>
                                                                        <th>
                                                                            Contact
                                                                        </th>
                                                                        <th>
                                                                            Join Date
                                                                        </th>
                                                                        <th>
                                                                            Last Login
                                                                        </th>
                                                                        <th>
                                                                            Status
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                                                    $i=1;
                                                                    foreach($members as $member):
                                                                    ?>
                                                                        <tr>
                                                                            <td><?php echo $i;?></td>
                                                                            <td><?php echo $member->first_name.' '.$member->last_name;?></td>
                                                                            <td>
                                                                                <?php 
                                                                                   echo $member->email?$member->email.'</br>':'';
                                                                                   echo $member->phone;
                                                                                ?>
                                                                            </td>                                
                                                                            <td ><?php echo str_replace(',','<br/>',timestamp_to_date_and_time($member->created_on));?></td>
                                                                            <td ><?php
                                                                            if($member->last_login){
                                                                                echo str_replace(',','<br/>',timestamp_to_date_and_time($member->last_login));
                                                                            }else{
                                                                                echo 'Never';
                                                                                }?></td>
                                                                            <td class="actions">
                                                                                <?php $status = $member->active;
                                                                                    if($status):
                                                                                        echo '<span class="label label-xs label-primary"> Active </span>';
                                                                                    else:
                                                                                        if($member->is_deleted):
                                                                                            echo '<span class="label label-xs label-danger"> Deleted </span>';
                                                                                        else:
                                                                                            echo '<span class="label label-xs label-warning"> Suspended </span>';
                                                                                        endif;
                                                                                    endif;
                                                                                ?>
                                                                                
                                                                            </td>
                                                                        </tr>
                                                                    <?php $i++; endforeach; 
                                                                    ?>
                                                                </tbody>
                                                            </table>


                                                        <?php else:?>
                                                            <div class="alert alert-info">
                                                                <h4 class="block">Information! No records to display</h4>
                                                                <p>
                                                                    Sorry, there are no members registered within this group.
                                                                </p>
                                                            </div>
                                                        <?php endif;?>

                                                    </p>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li class="mt-list-item">
                                <div class="list-todo-icon bg-white">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="list-todo-item dark">
                                    <a class="list-toggle-container font-white" data-toggle="collapse" href="#task-3" aria-expanded="false">
                                        <div class="list-toggle done uppercase">
                                            <div class="list-toggle-title bold">Contribution Settings</div>
                                            <div class="badge badge-default pull-right bold"><?php echo count($contributions); ?></div>
                                        </div>
                                    </a>
                                    <div class="task-list panel-collapse collapse" id="task-3">
                                        <ul>
                                            <li class="task-list-item done">
                                                <div class="task-icon">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-navicon"></i>
                                                    </a>
                                                </div>
                                                <div class="task-content">
                                                    <h4 class="uppercase bold">
                                                        <a href="javascript:;">Contribution Settings Listing</a>
                                                    </h4>
                                                    <p>
                                                        <?php if(empty($contributions)):?>
                                                            <hr/>
                                                            <div class="alert alert-info">
                                                                <strong>Info!</strong> Group doesn't have any contribution settings created yet.
                                                            </div>
                                                        <?php else:?>
                                                            <table class="table table-striped table-bordered table-advance table-condensed table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th width='2%'>
                                                                            #
                                                                        </th>
                                                                        <th>
                                                                            Name
                                                                        </th>
                                                                        <th>
                                                                            Contribution Particulars
                                                                        </th>
                                                                        <th width="15%" class='text-right'>
                                                                            Amount (<?php echo $group_currency; ?>)
                                                                        </th>
                                                                        <th>
                                                                            Status
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $i = $this->uri->segment(5, 0); foreach($contributions as $contribution): ?>
                                                                        <?php if($contribution->type==1){ ?>
                                                                            <tr>
                                                                                <td><?php echo $i+1;?></td>
                                                                                <td>
                                                                                    <?php echo $contribution->name; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php 
                                                                                        if($contribution->regular_invoicing_active){
                                                                                            $regular_contribution_setting = isset($regular_contribution_settings_array[$contribution->id])?$regular_contribution_settings_array[$contribution->id]:'';
                                                                                            if($regular_contribution_setting){ 
                                                                                                echo '<strong>Contribution Type: </strong>'.$contribution_type_options[$contribution->type];
                                                                                                echo '<br/><strong>Contribution Details: </strong>'; 
                                                                                                if($regular_contribution_setting->contribution_frequency==1){
                                                                                                    //Once a month
                                                                                                    echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$days_of_the_month[$regular_contribution_setting->month_day_monthly].' '.$month_days[$regular_contribution_setting->week_day_monthly?$regular_contribution_setting->week_day_monthly:0];
                                                                                                }else if($regular_contribution_setting->contribution_frequency==6){
                                                                                                    //Weekly
                                                                                                    echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$week_days[$regular_contribution_setting->week_day_weekly];
                                                                                                }else if($regular_contribution_setting->contribution_frequency==7){
                                                                                                    //Fortnight or every two weeks
                                                                                                    echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].', '.$every_two_week_days[$regular_contribution_setting->week_day_fortnight].' '.$week_numbers[$regular_contribution_setting->week_number_fortnight];
                                                                                                }else if($regular_contribution_setting->contribution_frequency==2||$regular_contribution_setting->contribution_frequency==3||$regular_contribution_setting->contribution_frequency==4||$regular_contribution_setting->contribution_frequency==5){
                                                                                                    //Multiple months
                                                                                                    echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].',
                                                                                                    '.$days_of_the_month[$regular_contribution_setting->month_day_multiple].'
                                                                                                    '.$month_days[$regular_contribution_setting->week_day_multiple?$regular_contribution_setting->week_day_multiple:0].', 
                                                                                                    '.$starting_months[$regular_contribution_setting->start_month_multiple];
                                                                                                }
                                                                                                echo '<br/><strong>Invoice Date: </strong>'.timestamp_to_date($regular_contribution_setting->invoice_date);
                                                                                                echo '<br/><strong>Contribution Date: </strong>'.timestamp_to_date($regular_contribution_setting->contribution_date);
                                                                                                echo '<br/><strong>SMS Notifications: </strong>'; echo $regular_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
                                                                                                echo '<br/><strong>Email Notifications: </strong>';echo $regular_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                                                                echo '<br/><strong>Fines: </strong>';echo $regular_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                                                                echo '<br/>';
                                                                                                if($regular_contribution_setting->enable_contribution_member_list){
                                                                                                    echo '<hr/>';
                                                                                                    if(isset($selected_group_members_array[$contribution->id])){
                                                                                                        $group_members = $selected_group_members_array[$contribution->id];
                                                                                                        $count = 1;
                                                                                                        echo '<strong>Members to be invoiced: </strong><br/>';
                                                                                                        foreach($group_members as $member_id){
                                                                                                            if($count==1){
                                                                                                                echo $member_options[$member_id];
                                                                                                            }else{
                                                                                                                echo ','.$member_options[$member_id];
                                                                                                            }
                                                                                                            $count++;
                                                                                                        }
                                                                                                    }
                                                                                                }else{
                                                                                                    echo '<strong>All members to be invoiced </strong><br/>';
                                                                                                }

                                                                                                if($regular_contribution_setting->enable_fines){
                                                                                                    if(isset($contribution_fine_settings_array[$contribution->id])){
                                                                                                        echo '<strong>Contribution fine settings: </strong><br/>';
                                                                                                        $contribution_fine_settings = $contribution_fine_settings_array[$contribution->id];
                                                                                                        $count = 1;
                                                                                                        foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                                                                            if($count>1){
                                                                                                                echo '<br/>';
                                                                                                            }
                                                                                                            echo '<strong>Fine setting #'.$count.'<br/></strong>';
                                                                                                            echo '<strong>Fine Date</strong> '.timestamp_to_date($contribution_fine_setting->fine_date).'<br/>';
                                                                                                            if($contribution_fine_setting->fine_type==1){
                                                                                                                echo $fine_types[$contribution_fine_setting->fine_type];
                                                                                                                echo ' '.$group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
                                                                                                                echo ' '.$fine_mode_options[$contribution_fine_setting->fixed_fine_mode];
                                                                                                                echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->fixed_fine_chargeable_on];
                                                                                                                echo ' '.$fine_frequency_options[$contribution_fine_setting->fixed_fine_frequency];
                                                                                                                echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                                                                            }else if($contribution_fine_setting->fine_type==2){
                                                                                                                echo $fine_types[$contribution_fine_setting->fine_type];
                                                                                                                echo ' '.$contribution_fine_setting->percentage_rate.' % ';
                                                                                                                echo ' '.$percentage_fine_on_options[$contribution_fine_setting->percentage_fine_on];
                                                                                                                echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_chargeable_on];
                                                                                                                echo ' '.$fine_mode_options[$contribution_fine_setting->percentage_fine_mode];
                                                                                                                echo ' '.$fine_frequency_options[$contribution_fine_setting->percentage_fine_frequency];
                                                                                                                echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                                                                            }
                                                                                                            echo '<br/><strong>SMS Notifications: </strong>'; echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
                                                                                                            echo '<br/><strong>Email Notifications: </strong>';echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span><br/>";
                                                                                    
                                                                                                            $count++;
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }else{
                                                                                                echo "<span class='label label-default'>Regular Contribution Setting not Available</span>";
                                                                                            }

                                                                                        }else{
                                                                                            echo "<span class='label label-default'>Invoicing Disabled</span>";
                                                                                        }
                                                                                    ?>
                                                                                </td>
                                                                                <td class='text-right'><?php echo number_to_currency($contribution->amount); ?></td>
                                                                                <td>
                                                                                    <?php 
                                                                                        if($contribution->is_hidden){
                                                                                            echo "<span class='label label-default'>Hidden</span>";
                                                                                        }else{
                                                                                            echo "<span class='label label-success'>Visible</span>";
                                                                                        }
                                                                                    ?>
                                                                                    <?php 
                                                                                        if($contribution->regular_invoicing_active){
                                                                                            echo "<span class='label label-success'>Invoicing Active</span>";
                                                                                        }else{
                                                                                            echo "<span class='label label-default'>Invoicing Disabled</span>";
                                                                                        }
                                                                                    ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php }else if($contribution->type==2){ ?>
                                                                            <tr>
                                                                                <td><?php echo $i+1;?></td>
                                                                                <td>
                                                                                    <?php echo $contribution->name; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php 
                                                                                        if($contribution->one_time_invoicing_active){
                                                                                            $one_time_contribution_setting = isset($one_time_contribution_settings_array[$contribution->id])?$one_time_contribution_settings_array[$contribution->id]:'';
                                                                                            if($one_time_contribution_setting){ 
                                                                                                echo '<strong>Contribution Type: </strong>'.$contribution_type_options[$contribution->type];
                                                                                                echo '<br/><strong>Invoice Date: </strong>'.timestamp_to_date($one_time_contribution_setting->invoice_date);
                                                                                                echo '<br/><strong>Contribution Date: </strong>'.timestamp_to_date($one_time_contribution_setting->contribution_date);
                                                                                                echo '<br/><strong>SMS Notifications: </strong>'; echo $one_time_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
                                                                                                echo '<br/><strong>Email Notifications: </strong>';echo $one_time_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                                                                echo '<br/><strong>Fines: </strong>';echo $one_time_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                                                                echo '<br/>';
                                                                                                if($one_time_contribution_setting->enable_contribution_member_list){
                                                                                                    if(isset($selected_group_members_array[$contribution->id])){
                                                                                                        echo '<hr/>';
                                                                                                        $group_members = $selected_group_members_array[$contribution->id];
                                                                                                        $count = 1;
                                                                                                        echo '<strong>Members to be invoiced: </strong><br/>';
                                                                                                        foreach($group_members as $member_id){
                                                                                                            if($count==1){
                                                                                                                echo $member_options[$member_id];
                                                                                                            }else{
                                                                                                                echo ','.$member_options[$member_id];
                                                                                                            }
                                                                                                            $count++;
                                                                                                        }
                                                                                                    }
                                                                                                }else{
                                                                                                    echo '<strong>All members to be invoiced </strong><br/>';
                                                                                                }

                                                                                                if($one_time_contribution_setting->enable_fines){
                                                                                                    if(isset($contribution_fine_settings_array[$contribution->id])){
                                                                                                        echo '<strong>Contribution fine settings: </strong><br/>';
                                                                                                        $contribution_fine_settings = $contribution_fine_settings_array[$contribution->id];
                                                                                                        $count = 1;
                                                                                                        foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                                                                            if($count>1){
                                                                                                                echo '<br/>';
                                                                                                            }
                                                                                                            echo '<strong>Fine setting #'.$count.'<br/></strong>';
                                                                                                            if($contribution_fine_setting->fine_type==1){
                                                                                                                echo $fine_types[$contribution_fine_setting->fine_type];
                                                                                                                echo ' '.$group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
                                                                                                                echo ' '.$fine_mode_options[$contribution_fine_setting->fixed_fine_mode];
                                                                                                                echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->fixed_fine_chargeable_on];
                                                                                                                echo ' '.$fine_frequency_options[$contribution_fine_setting->fixed_fine_frequency];
                                                                                                                echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                                                                            }else if($contribution_fine_setting->fine_type==2){
                                                                                                                echo $fine_types[$contribution_fine_setting->fine_type];
                                                                                                                echo ' '.$contribution_fine_setting->percentage_rate.' % ';
                                                                                                                echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_on];
                                                                                                                echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_chargeable_on];
                                                                                                                echo ' '.$fine_mode_options[$contribution_fine_setting->percentage_fine_mode];
                                                                                                                echo ' '.$fine_frequency_options[$contribution_fine_setting->percentage_fine_frequency];
                                                                                                                echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                                                                            }
                                                                                                            echo '<br/><strong>SMS Notifications: </strong>'; echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
                                                                                                            echo '<br/><strong>Email Notifications: </strong>';echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span><br/>";
                                                                                    
                                                                                                            $count++;
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }else{
                                                                                                echo "<span class='label label-default'>One Time Contribution Setting not Available</span>";
                                                                                            }

                                                                                        }else{
                                                                                            echo "<span class='label label-default'>Invoicing Disabled</span>";
                                                                                        }
                                                                                    ?>
                                                                                </td>
                                                                                <td class='text-right'><?php echo number_to_currency($contribution->amount); ?></td>
                                                                                <td>
                                                                                    <?php 
                                                                                        if($contribution->is_hidden){
                                                                                            echo "<span class='label label-default'>Hidden</span>";
                                                                                        }else{
                                                                                            echo "<span class='label label-success'>Visible</span>";
                                                                                        }
                                                                                    ?>
                                                                                    <?php 
                                                                                        if($contribution->one_time_invoicing_active){
                                                                                            echo "<span class='label label-success'>Invoicing Active</span>";
                                                                                        }else{
                                                                                            echo "<span class='label label-default'>Invoicing Disabled</span>";
                                                                                        }
                                                                                    ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php }else if($contribution->type==3){ ?>
                                                                            <tr>
                                                                                <td><?php echo $i+1;?></td>
                                                                                <td>
                                                                                    <?php echo $contribution->name; ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php 
                                                                                        echo '<strong>Contribution Type: </strong>'.$contribution_type_options[$contribution->type];
                                                                                    ?>
                                                                                </td>
                                                                                <td class='text-right'><?php echo number_to_currency($contribution->amount); ?></td>
                                                                                <td>
                                                                                    <?php 
                                                                                        if($contribution->is_hidden){
                                                                                            echo "<span class='label label-default'>Hidden</span>";
                                                                                        }else{
                                                                                            echo "<span class='label label-success'>Visible</span>";
                                                                                        }
                                                                                    ?>
                                                                                    <?php 
                                                                                        if($contribution->regular_invoicing_active){
                                                                                            echo "<span class='label label-success'>Invoicing Active</span>";
                                                                                        }else{
                                                                                            echo "<span class='label label-default'>Invoicing Disabled</span>";
                                                                                        }
                                                                                    ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                        <?php $i++;
                                                                        endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        <?php endif;?>
                                                    </p>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li class="mt-list-item">
                                <div class="list-todo-icon bg-white">
                                    <i class="fa fa-institution"></i>
                                </div>
                                <div class="list-todo-item dark">
                                    <a class="list-toggle-container font-white" data-toggle="collapse" href="#task-4" aria-expanded="false">
                                        <div class="list-toggle done uppercase">
                                            <div class="list-toggle-title bold">Group Accounts</div>
                                            <div class="badge badge-default pull-right bold"><?php echo count($bank_accounts); ?></div>
                                        </div>
                                    </a>
                                    <div class="task-list panel-collapse collapse" id="task-4">
                                        <ul>
                                            <li class="task-list-item done">
                                                <div class="task-icon">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-navicon"></i>
                                                    </a>
                                                </div>
                                                <div class="task-content">
                                                    <h4 class="uppercase bold">
                                                        <a href="javascript:;">Group Accounts Listing</a>
                                                    </h4>
                                                    <p>
                                                        <?php if($bank_accounts){?>
                                                            <h5>Bank Accounts</h5>
                                                            <ol class="">
                                                            <?php foreach ($bank_accounts as $bank_account):;?>
                                                                <li><?php echo'<strong>'.$bank_account->bank_name.'</strong> | '.$bank_account->bank_branch.' branch | '.$bank_account->account_name.' | '.$bank_account->account_number;?></li>
                                                            <?php endforeach;?>
                                                            </ol>

                                                        <?php } if($sacco_accounts){?>
                                                            <h5>sacco Accounts</h5>
                                                            <ol>
                                                            <?php foreach ($sacco_accounts as $sacco_account):;?>
                                                                <li><?php echo '<strong>'.$sacco_account->sacco_name.'</strong> | '.$sacco_account->sacco_branch.' | '.$sacco_account->account_name.' | '.$sacco_account->account_number;?></li>
                                                            <?php endforeach;?>
                                                            </ol>

                                                        <?php }if($mobile_money_accounts){?>
                                                            <h5>Mobile Money Accounts</h5>
                                                            <ol>
                                                            <?php foreach ($mobile_money_accounts as $mobile_money_account):;?>
                                                                <li><?php echo '<strong>'.$mobile_money_account->mobile_money_provider_name.'</strong> | '.$mobile_money_account->account_name.' | '.$mobile_money_account->account_number;?></li>
                                                            <?php endforeach;?>
                                                            </ol>

                                                        <?php }if($petty_cash_accounts){?>
                                                            <h5>Petty Cash Accounts</h5>
                                                            <ol>
                                                            <?php foreach ($petty_cash_accounts as $petty_cash_account):;?>
                                                                <li><?php echo '<strong>'.$petty_cash_account->account_name.'</strong>';?></li>
                                                            <?php endforeach;?>
                                                            </ol>
                                                        <?php }?>
                                                        <?php if(empty($bank_accounts) && empty($petty_cash_accounts) && empty($sacco_accounts) && empty($mobile_money_accounts)){?>
                                                            <hr/>
                                                            <div class="alert alert-info">
                                                                <strong>Info!</strong> Group doesn't have any bank accounts created yet.
                                                            </div>
                                                        <?php }?>
                                                    </p>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>      
