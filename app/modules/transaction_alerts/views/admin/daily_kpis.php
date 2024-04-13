<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green">
                    <span class="caption-subject bold uppercase">Daily KPIs</span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <table class="table table-bordered table-condensed table-striped table-hover table-searchable">
                            <thead>
                                <tr>
                                    <th width="8px">#</th>
                                    <th>Daily KPI Name</th>
                                    <th>Daily KPI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1.</td>
                                    <td>Number of Groups Registered</td>
                                    <td><?php echo $groups_signed_up_today_count; ?></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Number of Customers Registered</td>
                                    <td><?php echo $users_signed_up_today_count; ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <h4>By Branches</h4>
                        <table class="table table-bordered table-condensed table-striped table-hover table-searchable">
                            <thead>
                                <tr>
                                    <th width="8px">#</th>
                                    <th>Branch</th>
                                    <th>Number of Groups Registered</th>
                                    <th>Number of Customers Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = 1; foreach($groups_signed_up_today_by_bank_branch as $branch): ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $bank_branch_options[$branch->bank_branch_id]; ?></td>
                                        <td><?php echo $groups_signed_up_today_count_by_bank_branch_array[$branch->bank_branch_id]; ?></td>
                                        <td><?php echo $branch->member_count; ?></td>
                                    </tr>
                                <?php $count++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <h4>Branch Analysis</h4>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div id="" class="table-responsive">
                            <table class="table table-bordered table-condensed table-striped table-hover table-searchable">
                                <thead>
                                    <tr>
                                        <th width="8px">#</th>
                                        <th>Branch</th>
                                        <th class=''>Transactions</th>
                                        <th class='text-right'>Deposits</th>
                                        <th class='text-right'>Withdrawals</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; $group_count = 0; foreach($bank_accounts_by_bank_branch_count as $bank_branch): ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $bank_branch_options[$bank_branch->bank_branch_id]; ?></td>
                                            <td><?php //echo $bank_branch->group_count; ?></td>
                                            <td class="text-right">
                                            <?php 
                                                $total_deposits = isset($total_deposit_transactions_amount_for_today_by_bank_branch_id_array[$bank_branch->bank_branch_id])?$total_deposit_transactions_amount_for_today_by_bank_branch_id_array[$bank_branch->bank_branch_id]:0;
                                                echo number_to_currency($total_deposits); 
                                            ?>
                                            </td>
                                            <td class="text-right">
                                            <?php 
                                                $total_withdrawals = isset($total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array[$bank_branch->bank_branch_id])?$total_withdrawal_transactions_amount_for_today_by_bank_branch_id_array[$bank_branch->bank_branch_id]:0;
                                                echo number_to_currency($total_withdrawals); 
                                            ?>
                                            </td>
                                        </tr>
                                    <?php $group_count += $bank_branch->group_count; endforeach; ?>
                                    <tr>
                                        <th>#</th>
                                        <th>Total</th>
                                        <th><strong><?php //echo $group_count; ?></strong></th>
                                        <th><strong><?php //echo $group_count; ?></strong></th>
                                        <th><strong><?php //echo $group_count; ?></strong></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>