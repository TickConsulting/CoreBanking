<?php if(!empty($posts)){ ?>
<?php echo form_open('group/contributions/action', ' id="form"  class="form-horizontal"'); ?> 

<?php if ( ! empty($pagination['links'])): ?>
    <div class="row col-md-12">
        
        <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span>
            <?php echo translate('Contributions');?>
        </p>

        <?php 
            echo '<div class ="top-bar-pagination">';
            echo $pagination['links']; 
            echo '</div></div>';
            endif; 
        ?> 

        <div class="m-accordion m-accordion--default" id="m_accordion" role="tablist">
            <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                <!--begin::Item--> 
                <div class="m-accordion__item">
                    <div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_item_<?php echo $i; ?>_head" data-toggle="collapse" href="#m_accordion_item_<?php echo $i; ?>_body" aria-expanded="false">
                        <span class="m-accordion__item-icon"><i class="mdi mdi-format-list-bulleted"></i></span>
                        <span class="m-accordion__item-title"><?php echo ($i+1).'. '.$post->name; ?>
                                <?php if($post->is_hidden){
                                   echo '<span class="float-right m-badge m-badge--danger m-badge--wide">Disabled</span><br/>';
                                }else{
                                    echo '<span class="float-right m-badge m-badge--success m-badge--wide">'.translate('Active').'</span><br/>';
                                }?>
                        </span>
                        <span class="m-accordion__item-mode"></span>     
                    </div>

                    <div class="m-accordion__item-body collapse" id="m_accordion_item_<?php echo $i; ?>_body" class=" " role="tabpanel" aria-labelledby="m_accordion_item_<?php echo $i; ?>_head" data-parent="#m_accordion"> 
                        <div class="m-accordion__item-content">
                            <div class="row invoice-body">
                                <div class="col-xs-12 table-responsive ">
                                    <table class="table table-sm m-table m-table--head-separator-primary table table--hover table-borderless table-condensed contributions-listing-table">
                                        <thead>
                                            <tr>
                                                <th width="30%">
                                                    <?php echo translate('Contribution details') ?>
                                                </th>
                                                <th class="m--align-right">
                                                    <div class="btn-group">
                                                        <a href="<?php echo site_url('group/contributions/edit/'.$post->id); ?>" class="btn btn-sm btn-primary m-btn  m-btn m-btn--icon generate_pdf_link">
                                                            <span>
                                                                <i class="fa fa-edit"></i>
                                                                <span>
                                                                    <?php echo translate('Edit');?>
                                                                </span>
                                                            </span>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split more_actions_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                                            <span class="sr-only">More actions..</span>
                                                        </button>
                                                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(120px, 31px, 0px);">
                                                            <a class="dropdown-item view_transaction_alert_link" href="#" style="display: none;">
                                                                <?php echo translate('View transaction alert');?>
                                                            </a>
                                                            <?php if($post->is_hidden){?>
                                                                <a class="dropdown-item" href="<?php echo site_url('group/contributions/unhide/'.$post->id); ?>">
                                                                    <?php echo translate('Enable');?>
                                                                </a>
                                                            <?php }else{?>
                                                                <a class="dropdown-item" href="<?php echo site_url('group/contributions/hide/'.$post->id); ?>">
                                                                    <?php echo translate('Disable');?>
                                                                </a>
                                                            <?php }?>
                                                                <a class="dropdown-item delete_contribution_link <?php echo $post->id; ?>_active_row" data-title="Enter your password to delete the contribution" data-content="Enter your password to delete the contribution"  href="<?php echo site_url('group/contributions/delete/'.$post->id); ?>" id="<?php echo $post->id; ?>">
                                                                    <?php echo translate('Delete');?>
                                                                </a>
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="<?php echo $post->id ?>_active_row">
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Contribution Type');?>
                                                    </strong>
                                                </td>
                                                <td>:
                                                    <?php 
                                                        echo $contribution_type_options[$post->type];
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr class="<?php echo $post->id ?>_active_row">
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Contribution Amount');?>
                                                    </strong>
                                                </td>
                                                <td>:
                                                    <?php 
                                                        echo $this->group_currency.' '.number_to_currency($post->amount);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr class="<?php echo $post->id ?>_active_row">
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Contribution Checkoff');?>
                                                    </strong>
                                                </td>
                                                <td>:
                                                    <?php 
                                                        if($post->enable_checkoff){
                                                            echo '<span class="m-badge m-badge--success m-badge--wide">Enabled</span>';
                                                        }else{
                                                            echo '<span class="m-badge m-badge--danger m-badge--wide">Disabled</span>';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr class="<?php echo $post->id ?>_active_row">
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Contribution Display in member reports');?>
                                                    </strong>
                                                </td>
                                                <td>:
                                                    <?php 
                                                        if($post->enable_deposit_statement_display){
                                                            echo '<span class="m-badge m-badge--success m-badge--wide">Enabled</span>';
                                                        }else{
                                                            echo '<span class="m-badge m-badge--danger m-badge--wide">Disabled</span>';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>

                                            <?php if($post->type==1){ ?>
                                                <?php if($post->regular_invoicing_active){
                                                    $regular_contribution_setting = isset($regular_contribution_settings_array[$post->id])?$regular_contribution_settings_array[$post->id]:''; 
                                                    if($regular_contribution_setting){
                                                        echo '
                                                            <tr>
                                                                <td class="m--align-right" nowrap>
                                                                    <strong>'.
                                                                        translate('Contribution Frequency').
                                                                    '</strong>
                                                                </td>
                                                                <td>: ';
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
                                                                    }else if($regular_contribution_setting->contribution_frequency==8){
                                                                        echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency];
                                                                    }else if($regular_contribution_setting->contribution_frequency == 9){
                                                                        echo $contribution_frequency_options[$regular_contribution_setting->contribution_frequency].''. $contribution_days_option[$regular_contribution_setting->after_first_contribution_day_option].'&nbsp;'. $month_days[$regular_contribution_setting->after_first_day_week_multiple] .'&nbsp;'.$starting_days[$regular_contribution_setting->after_first_starting_day] .'&nbsp; and &nbsp;'. $contribution_days_option[$regular_contribution_setting->after_second_contribution_day_option].'&nbsp;'. $month_days[$regular_contribution_setting->after_second_day_week_multiple] .'&nbsp;'.$starting_days[$regular_contribution_setting->after_second_starting_day]; 
                                                                    }
                                                                    echo '
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="m--align-right" nowrap>
                                                                    <strong>'.
                                                                        translate('Invoice Date').'
                                                                    </strong>
                                                                </td>
                                                                <td>: '.timestamp_to_date($regular_contribution_setting->invoice_date).
                                                                '</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="m--align-right" nowrap>
                                                                    <strong>'.
                                                                        translate('Contribution Date').'
                                                                    </strong>
                                                                </td>
                                                                <td>: '.timestamp_to_date($regular_contribution_setting->contribution_date).
                                                                '</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="m--align-right" nowrap>
                                                                    <strong>'.
                                                                        translate('SMS Notifications').'
                                                                    </strong>
                                                                </td>
                                                                <td>: ';
                                                                    if($regular_contribution_setting->sms_notifications_enabled){
                                                                        echo '<span class="m-badge m-badge--success m-badge--wide">'.translate('Enabled').'</span>';
                                                                    }else{
                                                                        echo '<span class="m-badge m-badge--danger m-badge--wide">'.translate('Disabled').'</span>';
                                                                    }
                                                                echo '</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="m--align-right" nowrap>
                                                                    <strong>'.
                                                                        translate('Email Notifications').'
                                                                    </strong>
                                                                </td>
                                                                <td>: ';
                                                                    if($regular_contribution_setting->email_notifications_enabled){
                                                                        echo '<span class="m-badge m-badge--success m-badge--wide">'.translate('Enabled').'</span>';
                                                                    }else{
                                                                        echo '<span class="m-badge m-badge--danger m-badge--wide">'.translate('Disabled').'</span>';
                                                                    }
                                                                echo '</td>
                                                            </tr>
                                                        ';
                                                    }else{
                                                        echo '
                                                            <tr>
                                                                <td class="text-right">
                                                                    <i class="la la-exclamation-triangle m--font-danger"></i>
                                                                </td>
                                                                <td colspan>
                                                                    : <span class="m--font-danger">
                                                                       Regular Contribution Settings not Available.
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        ';
                                                    }
                                                }else{
                                                    echo '
                                                        <tr>
                                                            <td class="text-right">
                                                                <strong>'.
                                                                    translate('Invoicing').
                                                                '</strong>
                                                            </td>
                                                            <td colspan>
                                                                : <span class="m-badge m-badge--danger m-badge--wide">Disabled</span>
                                                            </td>
                                                        </tr>
                                                    ';
                                                }
                                            }elseif($post->type==2){
                                                if($post->one_time_invoicing_active){
                                                    $one_time_contribution_setting = isset($one_time_contribution_settings_array[$post->id])?$one_time_contribution_settings_array[$post->id]:'';
                                                    if($one_time_contribution_setting){
                                                        // echo '
                                                        //     <tr>
                                                        //         <td class="m--align-right" nowrap>
                                                        //             <strong>'.
                                                        //                 translate('Invoice Date').
                                                        //             '</strong>
                                                        //         </td>
                                                        //         <td>'.timestamp_to_date($one_time_contribution_setting->invoice_date).'</td>
                                                        //     </tr>
                                                        // ';
                                                        //     echo '<br/><strong>Contribution Date: </strong>'.timestamp_to_date($one_time_contribution_setting->contribution_date);
                                                        //     echo '<br/><strong>SMS Notifications: </strong>'; echo $one_time_contribution_setting->sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
                                                        //     echo '<br/><strong>Email Notifications: </strong>';echo $one_time_contribution_setting->email_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                        //     $default_message="Fines";
                                                        //     echo '<br/><strong>'.$this->languages_m->translate('fines',$default_message).': </strong>';echo $one_time_contribution_setting->enable_fines?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>";
                                                        //     echo '<br/>';
                                                        //     if($one_time_contribution_setting->enable_contribution_member_list){
                                                        //         if(isset($selected_group_members_array[$post->id])){
                                                        //             echo '<hr/>';
                                                        //             $group_members = $selected_group_members_array[$post->id];
                                                        //             $count = 1;
                                                        //             echo '<strong>Members to be invoiced: </strong><br/>';
                                                        //             foreach($group_members as $member_id){
                                                        //                 if($count==1){
                                                        //                     echo $group_member_options[$member_id];
                                                        //                 }else{
                                                        //                     echo ','.$group_member_options[$member_id];
                                                        //                 }
                                                        //                 $count++;
                                                        //             }
                                                        //         }
                                                        //     }else{
                                                        //         $default_message="All members to be invoiced";
                                                        //         echo '<strong>'.$this->languages_m->translate('all_members_to_be_invoiced',$default_message).'</strong><br/>';
                                                        //     }

                                                        //     if($one_time_contribution_setting->enable_fines){
                                                        //         if(isset($contribution_fine_settings_array[$post->id])){
                                                        //             echo '<hr/>';
                                                        //             $default_message="Contribution fine settings";
                                                        //             echo '<strong>'.$this->languages_m->translate('contribution_fine_settings',$default_message).': </strong><br/>';
                                                        //             $contribution_fine_settings = $contribution_fine_settings_array[$post->id];
                                                        //             $count = 1;
                                                        //             foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                        //                 if($count>1){
                                                        //                     echo '<br/>';
                                                        //                 }
                                                        //                 $default_message="Fine Setting";
                                                        //                 echo '<strong>'.$this->languages_m->translate('contribution_fine_settings',$default_message).' #'.$count.' - '.$contribution_fine_setting->id.'<br/></strong>';

                                                        //                 if($contribution_fine_setting->fine_type==1){
                                                        //                     echo $fine_types[$contribution_fine_setting->fine_type];
                                                        //                     echo ' '.$this->group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
                                                        //                     echo ' '.$fine_mode_options[$contribution_fine_setting->fixed_fine_mode];
                                                        //                     echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->fixed_fine_chargeable_on];
                                                        //                     echo ' '.$fine_frequency_options[$contribution_fine_setting->fixed_fine_frequency];
                                                        //                     echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                        //                 }else if($contribution_fine_setting->fine_type==2){
                                                        //                     echo $fine_types[$contribution_fine_setting->fine_type];
                                                        //                     echo ' '.$contribution_fine_setting->percentage_rate.' % ';
                                                        //                     echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_on];
                                                        //                     echo ' '.$fine_chargeable_on_options[$contribution_fine_setting->percentage_fine_chargeable_on];
                                                        //                     echo ' '.$fine_mode_options[$contribution_fine_setting->percentage_fine_mode];
                                                        //                     echo ' '.$fine_frequency_options[$contribution_fine_setting->percentage_fine_frequency];
                                                        //                     echo ' '.$fine_limit_options[$contribution_fine_setting->fine_limit];
                                                        //                 }
                                                        //                 echo '<br/><strong>SMS Notifications: </strong>'; echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span>"; 
                                                        //                 echo '<br/><strong>Email Notifications: </strong>';echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='label label-success'>Enabled</span>":"<span class='label label-default'>Disabled</span><br/>";
                                                
                                                        //                 $count++;
                                                        //             }
                                                        //         }
                                                        //     }





















                                                        echo '
                                                            <tr>
                                                                <td class="m--align-right" nowrap>
                                                                    <strong>'.
                                                                        translate('Invoice Date').'
                                                                    </strong>
                                                                </td>
                                                                <td>: '.timestamp_to_date($one_time_contribution_setting->invoice_date).
                                                                '</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="m--align-right" nowrap>
                                                                    <strong>'.
                                                                        translate('Contribution Date').'
                                                                    </strong>
                                                                </td>
                                                                <td>: '.timestamp_to_date($one_time_contribution_setting->contribution_date).
                                                                '</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="m--align-right" nowrap>
                                                                    <strong>'.
                                                                        translate('SMS Notifications').'
                                                                    </strong>
                                                                </td>
                                                                <td>: ';
                                                                    if($one_time_contribution_setting->sms_notifications_enabled){
                                                                        echo '<span class="m-badge m-badge--success m-badge--wide">'.translate('Enabled').'</span>';
                                                                    }else{
                                                                        echo '<span class="m-badge m-badge--danger m-badge--wide">'.translate('Disabled').'</span>';
                                                                    }
                                                                echo '</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="m--align-right" nowrap>
                                                                    <strong>'.
                                                                        translate('Email Notifications').'
                                                                    </strong>
                                                                </td>
                                                                <td>: ';
                                                                    if($one_time_contribution_setting->email_notifications_enabled){
                                                                        echo '<span class="m-badge m-badge--success m-badge--wide">'.translate('Enabled').'</span>';
                                                                    }else{
                                                                        echo '<span class="m-badge m-badge--danger m-badge--wide">'.translate('Disabled').'</span>';
                                                                    }
                                                                echo '</td>
                                                            </tr>
                                                        ';
                                                    }else{
                                                        echo '
                                                            <tr>
                                                                <td class="text-right">
                                                                    <i class="la la-exclamation-triangle m--font-danger"></i>
                                                                </td>
                                                                <td colspan>
                                                                    : <span class="m--font-danger">
                                                                       One Time Contribution Setting not Available.
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        ';
                                                    }
                                                }else{
                                                    echo '
                                                        <tr>
                                                            <td class="text-right">
                                                                <strong>'.
                                                                    translate('Invoicing').
                                                                '</strong>
                                                            </td>
                                                            <td colspan>
                                                                : <span class="m-badge m-badge--danger m-badge--wide">Disabled</span>
                                                            </td>
                                                        </tr>
                                                    ';
                                                }

                                            }elseif($post->type==3){
                                            }?>


                                        </tbody>
                                    </table>

                                    <?php if($post->type==1 || $post->type==2){
                                        if($post->type==1){
                                            if($post->regular_invoicing_active){
                                                if($regular_contribution_setting){
                                                    echo'
                                                        <table class="table m-table table-sm m-table--head-separator-primary contributions-listing-table">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="2">'.translate('Fine Settings').'</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>';
                                                                if($regular_contribution_setting->enable_fines){
                                                                    if(isset($contribution_fine_settings_array[$post->id])){
                                                                        $contribution_fine_settings = $contribution_fine_settings_array[$post->id];
                                                                        $count = 1;
                                                                        foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                                            echo '
                                                                                <tr>
                                                                                    <th scope="row" nowrap>'.
                                                                                        $count.'.
                                                                                    </th>
                                                                                    <td>';
                                                                                    if($contribution_fine_setting->fine_type==1){
                                                                                        echo $fine_types[$contribution_fine_setting->fine_type];
                                                                                        echo ' '.$this->group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
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
                                                                                echo'
                                                                                    <br>'.
                                                                                    translate('Fine date').' :'.timestamp_to_date($contribution_fine_setting->fine_date).'
                                                                                    <br/>
                                                                                    SMS Notifications: '; 

                                                                                    echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='m-badge m-badge--success m-badge--wide'>Enabled</span>":"<span class='m-badge m-badge--info m-badge--wide'>Disabled</span>";
                                                                                    echo '<br/>Email Notifications: ';

                                                                                    echo $contribution_fine_setting->fine_email_notifications_enabled?"<span class='m-badge m-badge--success m-badge--wide'>Enabled</span>":"<span class='m-badge m-badge--info m-badge--wide'>Disabled</span>";
                                                                                echo'
                                                                                    </td>
                                                                                </tr>
                                                                            ';
                                                                            $count++;
                                                                        }
                                                                    }
                                                                }else{
                                                                    echo '
                                                                        <td colspan="2">
                                                                            <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                                                                                <strong>Heads up! </strong>No fine settings configured.
                                                                            </div>
                                                                        </td>
                                                                    ';
                                                                }
                                                                echo'
                                                            </tbody>
                                                        </table>
                                                    ';



                                                    echo '
                                                        <table class="table m-table table-sm m-table--head-separator-primary contributions-listing-table">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="2">'.translate('Members to be invoiced').'</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>';
                                                                if($regular_contribution_setting->enable_contribution_member_list){
                                                                    if(isset($selected_group_members_array[$post->id])){
                                                                        $group_members = $selected_group_members_array[$post->id];
                                                                        $count = 1;
                                                                        foreach($group_members as $member_id){
                                                                    echo '<tr><th scope="row" width="1%">'.$count.'.</th><td>'.$group_member_options[$member_id].'</td></tr>';
                                                                    $count++;
                                                                }
                                                                echo'
                                                            </tbody>
                                                        </table>';
                                                                        
                                                                    }
                                                                }else{
                                                                    echo '
                                                                        <tr>
                                                                            <td colspan="2">
                                                                                <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                                                                                    <strong>Heads up! </strong>All members to be invoiced.
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    ';
                                                                echo'
                                                            </tbody>
                                                        </table>
                                                    ';
                                                                }
                                                }
                                            }
                                        }else{
                                            if($post->one_time_invoicing_active){
                                                if($one_time_contribution_setting){
                                                    echo'
                                                        <table class="table m-table table-sm m-table--head-separator-primary contributions-listing-table">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="2">'.translate('Fine Settings').'</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>';
                                                                if($one_time_contribution_setting->enable_fines){
                                                                    if(isset($contribution_fine_settings_array[$post->id])){
                                                                        $contribution_fine_settings = $contribution_fine_settings_array[$post->id];
                                                                        $count = 1;
                                                                        foreach ($contribution_fine_settings as $contribution_fine_setting) {
                                                                            echo '
                                                                                <tr>
                                                                                    <th scope="row" nowrap>'.
                                                                                        $count.'.
                                                                                    </th>
                                                                                    <td>';
                                                                                    if($contribution_fine_setting->fine_type==1){
                                                                                        echo $fine_types[$contribution_fine_setting->fine_type];
                                                                                        echo ' '.$this->group_currency.' '.number_to_currency($contribution_fine_setting->fixed_amount);
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
                                                                                echo'
                                                                                    <br>'.
                                                                                    translate('Fine date').' :'.timestamp_to_date($contribution_fine_setting->fine_date).'
                                                                                    <br/>
                                                                                    SMS Notifications: '; 

                                                                                    echo $contribution_fine_setting->fine_sms_notifications_enabled?"<span class='m-badge m-badge--success m-badge--wide'>Enabled</span>":"<span class='m-badge m-badge--info m-badge--wide'>Disabled</span>";
                                                                                    echo '<br/>Email Notifications: ';

                                                                                    echo $contribution_fine_setting->fine_email_notifications_enabled?"<span class='m-badge m-badge--success m-badge--wide'>Enabled</span>":"<span class='m-badge m-badge--info m-badge--wide'>Disabled</span>";
                                                                                echo'
                                                                                    </td>
                                                                                </tr>
                                                                            ';
                                                                            $count++;
                                                                        }
                                                                    }
                                                                }else{
                                                                    echo '
                                                                        <td colspan="2">
                                                                            <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                                                                                <strong>Heads up! </strong>No fine settings configured.
                                                                            </div>
                                                                        </td>
                                                                    ';
                                                                }
                                                                echo'
                                                            </tbody>
                                                        </table>
                                                    ';

                                                    echo '
                                                        <table class="table table-sm m-table m-table--head-separator-primary contributions-listing-table">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="2">'.translate('Members to be invoiced').'</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>';
                                                                if($one_time_contribution_setting->enable_contribution_member_list){
                                                                    if(isset($selected_group_members_array[$post->id])){
                                                                        $group_members = $selected_group_members_array[$post->id];
                                                                        $count = 1;
                                                                        foreach($group_members as $member_id){
                                                                    echo '<tr><th scope="row" width="1%">'.$count.'.</th><td>'.$group_member_options[$member_id].'</td></tr>';
                                                                    $count++;
                                                                }
                                                                echo'
                                                            </tbody>
                                                        </table>';
                                                                        
                                                                    }
                                                                }else{
                                                                    echo '
                                                                        <tr>
                                                                            <td colspan="2">
                                                                                <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                                                                                    <strong>Heads up! </strong>All members to be invoiced.
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    ';
                                                                echo'
                                                            </tbody>
                                                        </table>
                                                    ';
                                                                }
                                                }
                                            }
                                        }
                                    }?>

                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            <?php $i++;
            endforeach; ?>                
        </div>
    <div class="row col-md-12">
        <?php 
            if( ! empty($pagination['links'])): 
            echo $pagination['links']; 
            endif; 
        ?>  
    </div>
   <!--  <?php if($posts):?>
        <button class="btn btn-sm btn-success confirmation_bulk_action" name='btnAction' value='bulk_display' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye'></i> Bulk Display</button>

        <button class="btn btn-sm  btn-default confirmation_bulk_action" name='btnAction' value='bulk_hide' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye-slash'></i>
        <?php echo translate('Bulk Hide');?>
        </button>
    <?php endif;?> -->
<?php echo form_close(); ?>
<?php }else{ ?>
    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
        <strong><?php echo translate('Sorry'); ?>! </strong><?php echo  translate('No Contributions to display') ?>
    </div>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click','.confirmation_link',function(){
            var element = $(this);
            bootbox.confirm({
                message: "Are you sure you want to do this ?",
                // title: "Before you proceed",
                callback: function(result) {
                    if(result==true){
                        if (result === null) {
                            return true;
                        }else{
                            var href = element.attr('href');
                            window.location = href;
                        }
                    }else{
                        return true;
                    }
                }
            });
            return false;
        });

        $(document).on('click','.delete_contribution_link',function(e){
            e.preventDefault();
            var id = $(this).attr('id');
            swal({
                title: "Are you sure?", text: "You won't be able to revert this!", type: "warning", showCancelButton: !0, confirmButtonText: "Yes, delete it!", cancelButtonText: "No, cancel!", reverseButtons: !0
            }).then(function(e) {
                if(e.value == true){
                    bootbox.prompt({
                        title: "Input Your password to delete!",
                        inputType: 'password',
                        callback: function (result) {
                            mApp.block('.'+id+'_active_row', {
                                overlayColor: 'grey',
                                animate: true,
                                type: 'loader',
                                state: 'primary',
                                message: 'deleting contribution..'
                            });
                            $.post('<?php echo site_url('ajax/contributions/delete');?>',{'id':id, 'password':result},
                            function(response){
                                if(isJson(response)){
                                    var data = $.parseJSON(response)
                                    if(data.status == '1'){
                                        mApp.unblock('.'+id+'_active_row');
                                        swal("success",data.message, "success")
                                    }else{
                                        mApp.unblock('.'+id+'_active_row');
                                        swal("Error",data.message, "error")
                                    }
                                }else{
                                    mApp.unblock('.'+id+'_active_row');
                                    swal("Error", "Could not delete your contributions :)", "error")   
                                }
                            });
                            // $.ajax({
                            //     type:'POST',
                            //     url:'<?php echo site_url('ajax/contributions/delete') ?>',
                            //     data:{'id':id, 'password':result},
                            //     success: function(response){
                            //         if(isJson(response)){
                            //             var data = $.parseJSON(response)
                            //             if(data.status == '1'){
                            //                 mApp.unblock('.'+id+'_active_row');
                            //                 swal("success",data.message, "success")
                            //             }else{
                            //                 mApp.unblock('.'+id+'_active_row');
                            //                 swal("Error",data.message, "error")
                            //             }
                            //         }else{
                            //             mApp.unblock('.'+id+'_active_row');
                            //             swal("Error", "Could not delete your contributions :)", "error")   
                            //         }
                            //     },
                            //     error: function(){
                            //         mApp.unblock('.'+id+'_active_row');
                            //         swal("Error", "Could not delete your contributions :)", "error")
                            //     },
                            // });
                        }
                    });
                }else{
                    swal("Cancelled", "Your contribution is safe :)", "info")
                }
            })
        });
    });
</script>