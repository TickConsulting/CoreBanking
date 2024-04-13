<?php 
  if(empty($posts)){ ?>

    <div class="alert alert-info">
        <h4 class="block">
            <?php
                $default_message='Information! No records to display';
                $this->languages_m->translate('no_records_to_display',$default_message);
            ?>            
        </h4>
        <p>
            No pending Supervisor recommedations to display.
        </p>
    </div>

    <?php
  }else{ ?>
    <div id="member_statements_listing" style="position: relative;">  
        <table class="table table-condensed table-striped table-hover table-header-fixed ">
        <thead>
            <tr>
                <th width="2%">
                     <span><div class="checker"><span><input name="check" value="all" class="check_all" type="checkbox"></span></div></span>
                </th>
                <th width="2%">
                    #
                </th>
                <th>
                    Loan Details
                </th>
                <th class="text-right">
                    Status
                </th> 
            </tr>
        </thead>
        <tbody> 
            <?php 
                foreach ($posts as $key => $post):
                ?>
                    <tr> 
                        <td>
                        <span>
                         <div class="checker"><span><input type="checkbox" name="check" value="all" class="check_all"></span></div></span></td>
                        <td>1</td>
                        <td>
                            <strong> Loan Name : </strong> <?php echo $loan_type[$post->loan_type_id]->name?> <br>
                            <strong> Loan Applicant Name : </strong><?php echo $this->active_group_member_options[$post->member_id]?> <br>
                            <strong> Loan Amount : </strong> <?php echo $this->group_currency .' '. number_to_currency($post->loan_amount)?> <br>
                            <strong> Loan Duration : </strong> <span>
                                <?php echo $post->repayment_period?> Months<br>                                                      
                        </span>
                        </td>
                            <td class="text-right">
                                <?php 
                                if($post->member_supervisor_id == $supervisor_recommendations[$post->member_supervisor_id]->supervisor_member_id ){
                                    if($supervisor_recommendations[$post->member_supervisor_id]->is_approve == 1){
                                        echo '<span class="label label-success label-xs"> Approved</span>';
                                    }else if($supervisor_recommendations[$post->member_supervisor_id]->is_approve == 2){
                                        echo '<span class="label label-danger label-xs"> Declined</span>';
                                    }else{
                                        echo '<a href="'.site_url('member/loans/supervisor_recommendation/'.$post->id).'" class="btn btn-sm btn-info"><i class="fa fa-view"></i>View</a>';
                                    }                                    
                                } ?>
                            </td>
                            <td>                     
                        </td>
                    </tr>
            <?php 
            endforeach; ?>
        </tbody>
        </table>
        <div class="clearfix"></div>
    </div>
<?php } ?>