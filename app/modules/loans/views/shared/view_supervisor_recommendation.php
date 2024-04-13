<?php if(!empty($posts)){ ?>
    <div class="row col-md-12">        
        <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed table-searchable">
            <thead>
                <tr>
                    <th width='2%'>
                        #
                    </th>
                    <th>
                        <?php
                            $default_message='Loan Details';
                            $this->languages_m->translate('fine_date',$default_message);
                        ?>
                    </th>
                    <th>
                        <?php
                            $default_message='Supervisor Recommendation';
                            $this->languages_m->translate('member',$default_message);
                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $i = $this->uri->segment(5, 0); $i++; foreach($posts as $post): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <span class="bold"> Loan Applicant :</span> <?php echo $this->active_group_member_options[$post->loan_request_member_id] ?><br/>
                            <span class="bold"> Loan name :</span> <?php echo $loan_type[$post->loan_type_id]->name ?><br/>
                            <span class="bold"> Loan Amount: </span><?php echo $this->group_currency.' '. number_to_currency($post->loan_amount); ?> <br>
                            <span class="bold"> Loan application date : </span> <?php echo timestamp_to_receipt($loan_application[$post->loan_application_id]->created_on) ?><br/>
                            <span class="bold"> Loan Duration: </span> <?php
                                if($loan_type[$post->loan_type_id]->loan_repayment_period_type == 1){
                                    echo $loan_type[$post->loan_type_id]->fixed_repayment_period.' Months';
                                }else if ($loan_type[$post->loan_type_id]->loan_repayment_period_type == 2) {
                                    echo $loan_type[$post->loan_type_id]->minimum_repayment_period.' - '.$loan_type[$post->loan_type_id]->maximum_repayment_period.' Months';
                                }?><br>                      

                        </td>
                        <td>
                            <span class="bold">Poor performance management :</span> <?php
                                if($post->performance_management == 1){
                                    echo 'YES';
                                }else if($post->performance_management == 2){
                                    echo 'No';
                                }else if($post->performance_management == 3){
                                    echo 'ANY';
                                }
                            ?><br/>
                            <span class="bold">Ongoing/Pending disciplinary : </span> <?php
                                if($post->disciplinary_case == 1){
                                    echo 'YES';
                                }else if($post->disciplinary_case == 2){
                                    echo 'No';
                                }
                            ?><br/>
                            <span class="bold">Date & Stamp : </span> 
                            <?php echo timestamp_to_receipt($post->recommendation_date) ?>
                            <br/>
                            <span class="bold">Comments : </span> 
                            <?php echo $post->comment?>
                            <br/>
                            <?php
                            if($post->is_approve){
                                ?>
                                <span class="bold">Recommendation : </span> <span class="label label-success label-xs"> Approved</span>
                               <?php
                            }else if($post->is_decline){ ?>
                                <span class="bold"> Recommendation : </span><span class="label label-danger label-xs"> declined</span>
                                <?php
                            }

                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
<?php }else{ ?>
    <div class="alert alert-info">
        <h4 class="block">
            <?php
                $default_message='Information! No records to display';
                $this->languages_m->translate('no_records_to_display',$default_message);
            ?>            
        </h4>
        <p>
            No Supervisor recommedations to display.
        </p>
    </div>
<?php } ?>