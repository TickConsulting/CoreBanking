<?php if(!empty($unreconciled_deposits)){ ?>
    <?php echo form_open('group/transaction_alerts/action', ' id="form"  class="form-horizontal"'); ?> 
        <div class='table-responsive'>
            <table class="table table-bordered table-condensed table-striped table-hover table-header-fixed table-searchable">
                <thead>
                    <tr>
                        <th width='2%'>
                            <input type="checkbox" name="check" value="all" class="check_all">
                        </th>
                        <th width='2%'>
                        	#
                        </th>
                        <th width="20%">
                            <?php
                                $default_message='Date';
                                $this->languages_m->translate('date',$default_message);
                            ?>
                        </th>
                        <th width="25%">
                            <?php
                                $default_message='Account';
                                $this->languages_m->translate('account',$default_message);
                            ?>
                        </th>
                        <th width="25%">
                            <?php
                                $default_message='Details';
                                $this->languages_m->translate('details',$default_message);
                            ?>
                        </th>
                        <th class="text-right">
                            <?php
                                $default_message='Amount';
                                $this->languages_m->translate('amount',$default_message);
                            ?>
                             (<?php echo $this->group_currency; ?>)
                        </th>
                        <!--
                            <th>
                                Actions
                            </th>
                        -->
                    </tr>
                </thead>
                <tbody>
                    <?php $count=1; foreach($unreconciled_deposits as $unreconciled_deposit): ?>
                    	<tr  id="reconciled_deposit_row_<?php echo $unreconciled_deposit->id; ?>">
                    		<td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $unreconciled_deposit->id; ?>" /></td>
                    		<td class='reconciled_deposit_count'><?php echo $count++; ?></td>
                    		<td>
                    			<?php echo timestamp_to_date($unreconciled_deposit->transaction_date); ?><br/>
                                <small><strong>Delivered On:</strong> <?php echo timestamp_to_date_and_time($unreconciled_deposit->created_on); ?></small><br/>
                                <small><strong>Reconciled On:</strong> <?php echo timestamp_to_date_and_time($unreconciled_deposit->modified_on); ?></small>
                    		</td>
                    		<td><?php echo $bank_account_options[$unreconciled_deposit->account_number]; ?></td>
                    		<td>
                                <?php echo $unreconciled_deposit->particulars; ?> <small><a class='toggle_transaction_alert_details'>More..</a></small>
                                <div class="transaction_alert_details">
                                    <?php echo $unreconciled_deposit->description; ?>
                                </div>
                            </td>
                    		<td class="text-right"><?php echo number_to_currency($unreconciled_deposit->amount); ?></td>
                    		<!--
                            <td class='reconcile_action'>
                                <?php if($reconciled_deposit->reconciled){ ?>
                                    <span class="label label-sm label-success"> Reconciled </span>

                                    <a data-toggle="" data-content="#" data-title="" data-id="<?php echo $reconciled_deposit->id; ?>" id="<?php echo $reconciled_deposit->id; ?>"  href="<?php echo site_url('group/deposits/listing?transaction_alert='.$reconciled_deposit->id); ?>" class="btn btn-xs   blue">
                                        <i class="fa fa-eye"></i> View Reconciled Deposits &nbsp;&nbsp; 
                                    </a>
                                    <a data-toggle="" data-content="#" data-title="" data-id="<?php echo $reconciled_deposit->id; ?>" id="<?php echo $reconciled_deposit->id; ?>" href="<?php echo site_url('group/transaction_alerts/void_deposit/'.$reconciled_deposit->id); ?>"  class="btn btn-xs confirmation_link   red">
                                        <i class="fa fa-trash"></i> Void Reconciliation &nbsp;&nbsp; 
                                    </a>
                                <?php }else{ ?>
                                    <a data-toggle="modal" data-content="#reconcile_deposit" data-title="Reconcile Deposit" data-id="<?php echo $reconciled_deposit->id; ?>" id="<?php echo $reconciled_deposit->id; ?>"  href="#" class="btn btn-xs full_width_inline reconcile_deposit  blue">
                                        <i class="fa fa-pencil"></i> Reconcile &nbsp;&nbsp; 
                                    </a>
                                <?php } ?>
                    		</td>
                            -->
                    	</tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
        <p>
            <?php if($unreconciled_deposits):?>
                <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_deactivate' data-toggle="confirmation" data-placement="top"> <i class='icon-trash'></i> Bulk Deactivate</button>
            <?php endif;?>
        </p>
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
            No unreconciled deposits to display.
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