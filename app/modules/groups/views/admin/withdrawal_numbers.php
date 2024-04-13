<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green">
                    <span class="caption-subject bold uppercase">Withdrawal Breakdown</span>
                    <span class="caption-helper">Month by month</span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div id="" class="table-responsive">
                            <table class="table table-bordered table-condensed table-striped table-hover table-header-fixed">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Group Name</th>
                                        <th>Member Name</th>
                                        <th>Withdrawal Date</th>
                                        <th>Withdrawal Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $count =0 ;
                                        foreach ($withdrawals as $key => $withdrawal) {
                                            $member = isset($member_options[$withdrawal->member_id])?$member_options[$withdrawal->member_id]:'';
                                            echo '<tr>
                                            <td> '. ++ $count.'</td>
                                            <td> ' .$group_options[$withdrawal->group_id]. '</td>
                                             <td>'  .$member. '</td>
                                            <td>' .month_name_to_name($withdrawal->month).", ".$withdrawal->year. ' </td>
                                            <td>' .number_to_currency($withdrawal->amount). '</td>                                                   
                                            </tr>';
                                        }
                                    ?>
                                </tbody>
                                <tfooter>
                                    <tr>
                                        <th>Totals</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>
                                            <?php 
                                            $totals = 0;
                                            foreach ($withdrawals as $key => $withdrawal) {
                                               $totals += valid_currency($withdrawal->amount);
                                            }
                                            echo number_to_currency($totals);
                                            ?>
                                        </th>
                                    </tr>
                                </tfooter>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
