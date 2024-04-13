<?php if(!empty($reconciled_withdrawals)){ ?>
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
                    <?php $count=1; $total=0; foreach($reconciled_withdrawals as $reconciled_withdrawal): ?>
                        <tr  id="reconciled_withdrawal_row_<?php echo $reconciled_withdrawal->id; ?>">
                            <!-- <td>
                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                    <input name="action_to[]" type="checkbox" class="checkboxes" value="<?php echo $reconciled_withdrawal->id; ?>"/>
                                    <span></span>
                                </label>
                            </td> -->
                            <td class='reconciled_withdrawal_count'><?php echo $count++; ?></td>
                            <td nowrap>
                                <?php echo timestamp_to_date($reconciled_withdrawal->transaction_date); ?>
                                <br/>
                                <small>
                                    <strong><?php echo translate('Delivered On');?>: </strong><?php echo timestamp_to_date_and_time($reconciled_withdrawal->created_on); ?>
                                </small>
                                <br/>
                                <small>
                                    <strong><?php echo translate('Reconciled On');?>: </strong><?php echo timestamp_to_date_and_time($reconciled_withdrawal->modified_on); ?>
                                </small>
                            </td>
                            <td><?php echo $bank_account_options[$reconciled_withdrawal->account_number]; ?></td>
                            <td>
                                <?php echo $reconciled_withdrawal->particulars;?>
                                <small>
                                    <a class='toggle_transaction_alert_details' style="cursor: pointer;">
                                        <?php echo translate('More'); ?>..
                                    </a>
                                </small>
                                <div class="transaction_alert_details" style="display: none;">
                                    <?php echo $reconciled_withdrawal->description; ?>
                                </div>
                            </td>
                            <td class="text-right"><?php $total+=$reconciled_withdrawal->amount; echo number_to_currency($reconciled_withdrawal->amount); ?></td>
                            <td class='reconcile_action'>
                                <a href="<?php echo site_url('group/withdrawals/listing?transaction_alert='.$reconciled_withdrawal->id); ?>" class="btn btn-primary btn-sm m-btn m-btn m-btn--icon">
                                    <span>
                                        <i class="la la-eye"></i>&nbsp;
                                        <span>
                                            <?php echo translate('View Withdrawals'); ?>
                                        </span>
                                    </span>
                                </a>
                                <a href="<?php echo site_url('group/transaction_alerts/void_withdrawal/'.$reconciled_withdrawal->id); ?>" class="btn btn-danger btn-sm m-btn m-btn m-btn--icon mt-3">
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
    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
        <strong><?php echo translate('Sorry'); ?>!</strong><?php echo translate('There are no reconciled withdrawal records to display');?>
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