<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green">
                    <span class="caption-subject bold uppercase">Transactions Breakdown</span>
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
                                        <th>Month</th>
                                        <th>Deposits</th>
                                        <th>Withdrawals</th>
                                        <th>Total Loans</th>
                                        <th>No of Loans</th>  
                                        <th>Avarage Loans</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($total_deposits as $key => $total_deposit) {
                                            for($month = 1;$month <= 12;$month++){
                                                $deposits = isset($total_deposits[$key][$month])?$total_deposits[$key][$month]:0; 
                                                $withdrawal = isset($total_withdrawals[$key][$month])?$total_withdrawals[$key][$month]:0; 
                                                $number_of_loans = isset($number_of_loans[$key][$month])?$number_of_loans[$key][$month]:0; 
                                                $number_of_active = isset($number_of_active_loans[$key][$month])?$number_of_active_loans[$key][$month]:0;  
                                                $total_loans = isset($total_amount_loans[$key][$month])?$total_amount_loans[$key][$month]:0;
                                                if($total_loans == 0){
                                                    $avarage_loans = 0;
                                                }else{
                                                   $avarage_loans = $total_loans / $number_of_active;
                                                }
                                                echo '<tr>
                                                        <td> ' .$month. '</td>
                                                        <td>' .month_name_to_name($month).", ".$key. ' </td>
                                                        <td><a href="'.site_url("admin/groups/deposit_list/".$month.'-'.$key).'">' .number_to_currency($deposits).'</a></td>
                                                        <td><a href="'.site_url("admin/groups/withdrawal_list/".$month.'-'.$key).'">' .number_to_currency($withdrawal).'</a></td>
                                                        <td><a href="'.site_url("admin/groups/loan_list/".$month.'-'.$key).'">' .number_to_currency($total_loans).'</a></td>
                                                        <td>' .$number_of_active. '</td>
                                                        <td>' .number_to_currency($avarage_loans). '</td>
                                                    </tr>';
                                            }
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
