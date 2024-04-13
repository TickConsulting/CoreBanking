<?php if(!empty($posts)){ ?>
    <div class="row col-md-12">
        <div class="panel-group accordion" id="accordion1">
            <div class="panel panel-default">
                <?php 
                    foreach ($posts as $key => $post): ?>
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion1" href="#collapse_<?php echo $post->id?>" aria-expanded="false"> Human resource appraisal for <?php echo $this->active_group_member_options[$post->loan_member_id]?>  </a>
                            </h4>
                        </div>

                        <div id="collapse_<?php echo $post->id?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">

                            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
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
                                                $default_message='Human Resource Appraisals';
                                                $this->languages_m->translate('human_resource_appraisal',$default_message);
                                            ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = $this->uri->segment(5, 0); $i++; 
                                   // foreach($posts as $post):
                                        $loan_application_id = $post->loan_application_id; 
                                       // / echo $loan_application_id;

                                     ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>
                                                <span class="bold"> Loan Applicant :</span> <?php echo $this->active_group_member_options[$hr_appraisals[$loan_application_id]->loan_member_id] ?><br/>
                                                <span class="bold"> Loan name :</span> <?php echo $loan_type[$post->loan_type_id]->name ?><br/>
                                                <span class="bold"> Loan Amount: </span><?php echo $this->group_currency.' '. number_to_currency($hr_appraisals[$loan_application_id]->loan_amount); ?> <br>
                                                <span class="bold"> Loan application date : </span> <?php echo timestamp_to_receipt($loan_application[$hr_appraisals[$loan_application_id]->loan_application_id]->created_on) ?><br/>
                                                <span class="bold"> Loan Duration: </span> <?php
                                                    if($loan_type[$hr_appraisals[$loan_application_id]->loan_type_id]->loan_repayment_period_type == 1){
                                                        echo $loan_type[$hr_appraisals[$loan_application_id]->loan_type_id]->fixed_repayment_period.' Months';
                                                    }else if ($loan_type[$hr_appraisals[$loan_application_id]->loan_type_id]->loan_repayment_period_type == 2) {
                                                        echo $loan_type[$hr_appraisals[$loan_application_id]->loan_type_id]->minimum_repayment_period.' - '.$loan_type[$hr_appraisals[$loan_application_id]->loan_type_id]->maximum_repayment_period.' Months';
                                                    }?><br>                      

                                            </td>
                                            <td>
                                                <span class="bold">Poor performance management :</span> <?php
                                                    if($hr_appraisals[$loan_application_id]->terms_of_employment == 1){
                                                        echo 'Permanent';
                                                    }else if($hr_appraisals[$loan_application_id]->terms_of_employment == 2){
                                                        echo 'Contract';
                                                    }
                                                ?><br/>
                                                <span class="bold">Contract End Date : </span> <?php                                
                                                        echo timestamp_to_receipt($hr_appraisals[$loan_application_id]->contract_end_date);
                                                ?><br/>
                                            </td>
                                        </tr>
                                    <?php //endforeach; ?>
                                </tbody>
                            </table>
                       
                            <div class="existing_loan_break_down" id="existing_loan_break_down" >
                                <fieldset>
                                    <div class="">
                                        <div class="portlet-body">
                                            <table class="table table-striped table-bordered table-condensed table-hover table-payments">                                                 
                                            <?php
                                                $net_amount_payable = 0;
                                                $new_loan_total_installments = 0;
                                                $member_existing_loans = $existing_loans[$post->loan_application_id];
                                                if(!empty($member_existing_loans)){ ?>
                                                    <thead>
                                                        <tr>
                                                            <th class="invoice-title ">
                                                                <?php
                                                                    $default_message='Exisiting Loan Amount';
                                                                    $this->languages_m->translate('amount',$default_message);
                                                                ?>
                                                            </th>
                                                            <th  class="invoice-title ">
                                                                <?php
                                                                    $default_message='Installments';
                                                                    $this->languages_m->translate('installment',$default_message);
                                                                ?>
                                                            </th>
                                                            <th class="invoice-title ">
                                                                <?php
                                                                    $default_message='Term';
                                                                    $this->languages_m->translate('term',$default_message);
                                                                ?>
                                                            </th>
                                                            <th class="invoice-title ">
                                                               <?php
                                                                    $default_message='Balance';
                                                                    $this->languages_m->translate('balance',$default_message);
                                                                ?>
                                                            </th>
                                                            <th class="invoice-title ">
                                                               <?php
                                                                    $default_message='Net Pay';
                                                                    $this->languages_m->translate('net_pay',$default_message);
                                                                ?>
                                                            </th>
                                                            <th class="invoice-title ">
                                                               <?php
                                                                    $default_message='% age of Net';
                                                                    $this->languages_m->translate('percentage_of_net',$default_message);
                                                                ?>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                  
                                                    foreach($member_existing_loans as $member_loan): 
                                                        $member_loan = $member_loan;
                                                        //print_r($member_loan); 
                                                        if($member_loan){

                                                            if($member_loan->loan_application_id == $post->loan_application_id){
                                                            
                                                                $is_loan_exisiting = isset($member_loan->is_loan_exisiting)?$member_loan->is_loan_exisiting:'';
                                                                if($is_loan_exisiting == 1){
                                                                ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo $this->group_currency.' '. number_to_currency($member_loan->existing_loan_amount); ?><br>
                                                                        </td> 
                                                                        <td> 
                                                                            <?php echo $this->group_currency.' '. $member_loan->loan_amount_installments?>                                      
                                                                        </td>
                                                                        <td>
                                                                           <?php echo $member_loan->repayment_period .' Months'?> 
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $this->group_currency.' '. number_to_currency($member_loan->loan_balance)?>    
                                                                        </td> 
                                                                        <td>
                                                                            <?php echo $this->group_currency.' '. number_to_currency($member_loan->net_pay)?>    
                                                                        </td>                                
                                                                        <td>
                                                                            <?php echo $member_loan->percentage_net_pay?>    
                                                                        </td>
                                                                    </tr>
                                                                <?php }else{ ?>
                                                                    <tr>
                                                                        <td rowspan="">
                                                                            <?php echo $loans_available[1]  ?>
                                                                        </td>
                                                                        <td colspan="4">
                                                                            <div class="alert alert-info">
                                                                                <button class="close" data-dismiss="alert"></button>
                                                                                <strong>Information!</strong>  Member Does not have an exisiting Loan                                   
                                                                            </div>
                                                                            
                                                                        </td>
                                                                    </tr><?php

                                                                    }
                                                                }
                                                            } ?>

                                                    <?php $i=0 ;$i++;
                                                    
                                                    endforeach; 
                                                }else{ ?>
                                                    <div class="alert alert-info">
                                                        <button class="close" data-dismiss="alert"></button>
                                                        <strong>Information!</strong>  Member Does not have an exisiting Loan                                   
                                                    </div><?php
                                                }
                                                ?> 


                                                    </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <br>
                <?php endforeach; ?>


            </div>
        </div>
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