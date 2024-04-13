<?php if(!empty($reconciled_deposits)){ ?>
    <?php echo form_open('admin/transaction_alerts/action', ' id="form"  class="form-horizontal"'); ?> 
        <div class="table-responsive">
            <table class="table table-condensed table-striped table-hover table-header-fixed table-searchable">
                <thead>
                    <tr>
                        <!-- <th width='2%'>
                            <label class="m-checkbox">
                                <input type="checkbox" name="check" value="all" class="check_all">
                                <span></span>
                            </label -->
                        </th>
                        <th width='2%'>
                            #
                        </th>
                        <th width="20%">
                            <?php echo translate('Date');?>
                        </th>
                        <th width="25%">
                            <?php echo translate('Account');?>
                        </th>
                        <th width="25%">
                            <?php echo translate('Details');?>
                        </th>
                        <th class="text-right">
                            <?php echo translate('Amount');?>
                            (<?php echo $this->group_currency; ?>)
                        </th>
                        <th>
                            <?php echo translate('Actions');?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count=1; $total=0; foreach($reconciled_deposits as $reconciled_deposit): ?>
                        <tr  id="reconciled_deposit_row_<?php echo $reconciled_deposit->id; ?>">
                            <!-- <td>
                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                    <input name="action_to[]" type="checkbox" class="checkboxes" value="<?php echo $reconciled_deposit->id; ?>"/>
                                    <span></span>
                                </label>
                            </td> -->
                            <td class='reconciled_deposit_count'><?php echo $count++; ?></td>
                            <td nowrap>
                                <?php echo timestamp_to_date($reconciled_deposit->transaction_date); ?>
                                <br/>
                                <small>
                                    <strong><?php echo translate('Delivered On');?>: </strong><?php echo timestamp_to_date_and_time($reconciled_deposit->created_on); ?>
                                </small>
                                <br/>
                                <small>
                                    <strong><?php echo translate('Reconciled On');?>: </strong><?php echo timestamp_to_date_and_time($reconciled_deposit->modified_on); ?>
                                </small>
                            </td>
                            <td><?php echo $bank_account_options[$reconciled_deposit->account_number]; ?></td>
                            <td>
                                <?php echo $reconciled_deposit->particulars;?>
                                <small>
                                    <a class='toggle_transaction_alert_details' style="cursor: pointer;">
                                        <?php echo translate('More'); ?>..
                                    </a>
                                </small>
                                <div class="transaction_alert_details" style="display: none;">
                                    <?php echo $reconciled_deposit->description; ?>
                                </div>
                            </td>
                            <td class="text-right"><?php $total+=$reconciled_deposit->amount; echo number_to_currency($reconciled_deposit->amount); ?></td>
                            <td class='reconcile_action'>
                                <a href="<?php echo site_url('group/deposits/listing?transaction_alert='.$reconciled_deposit->id); ?>" class="btn btn-primary btn-sm m-btn m-btn m-btn--icon">
                                    <span>
                                        <i class="la la-eye"></i>&nbsp;
                                        <span>
                                            <?php echo translate('View Deposits'); ?>
                                        </span>
                                    </span>
                                </a>
                                <a href="<?php echo site_url('group/transaction_alerts/void_deposit/'.$reconciled_deposit->id); ?>" class="btn btn-danger btn-sm m-btn m-btn m-btn--icon mt-3">
                                    <span>
                                        <i class="la la-trash"></i>&nbsp;
                                        <span>
                                            <?php echo translate('Void Reconciliation'); ?>
                                        </span>
                                    </span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">
                            Totals
                        </td>
                        <td class="text-right"><?php echo number_to_currency($total); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div> 
    <?php echo form_close(); ?>
<?php }else{ ?>
    <div class="alert alert-info">
        <h4 class="block">
            <?php
                $default_message='Information! No records to display';
                $this->languages_m->translate('no_records_to_display',$default_message);
            ?>
        </h4>
        <p>
            <?php echo translate('No reconciled deposits to display') ?>.
        </p>
    </div>
<?php } ?>
<script>
    $(document).ready(function(){
        $('.toggle_transaction_alert_details').click(function(){
            var element = $(this).parent().parent();
            var element2 = $(this);
            element.find('.transaction_alert_details').slideToggle(function(){
                
                 if($(this).is(':visible')){
                    element2.html('Less..');
                }else{
                    element2.html('More..');
                }
            });
        });
    });

  
</script>