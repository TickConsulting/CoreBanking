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
                                        <th>Deposit Date</th>
                                        <th>Deposit Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $count =0 ;
                                        foreach ($deposits as $key => $deposit) {
                                            $member = isset($member_options[$deposit->member_id])?$member_options[$deposit->member_id]:'';
                                            echo '<tr>
                                            <td> '. ++ $count.'</td>
                                            <td> ' .$group_options[$deposit->group_id]. '</td>
                                             <td>'  .$member. '</td>
                                            <td>' .month_name_to_name($deposit->month).", ".$deposit->year. ' </td>
                                            <td>' .number_to_currency($deposit->amount). '</td>                                                   
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
                                            foreach ($deposits as $key => $deposit) {
                                               $totals += valid_currency($deposit->amount);
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
