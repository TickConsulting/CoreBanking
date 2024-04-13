<div class="invoice-content-2 bordered document-border">
    <div class="row invoice-head">
        <div class="col-md-7 col-xs-6">
            <div class="invoice-logo">
                <img src="<?php echo is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo); ?>" alt="" class='group-logo image-responsive' /> 
            </div>
        </div>
        <div class="col-md-5 col-xs-6">
            <div class="company-address">
                <span class="bold uppercase"><?php echo $this->group->name; ?></span><br/>
                <?php echo nl2br($this->group->address); ?><br/>
                <span class="bold">
                        <?php
                            $default_message='Telephone';
                            $this->languages_m->translate('telephone',$default_message);
                        ?>
                    : </span> <?php echo $this->group->phone; ?>
                <br/>
                <span class="bold">
                        <?php
                            $default_message='Email Address';
                            $this->languages_m->translate('email_address',$default_message);
                        ?>
                    : </span> <?php echo $this->group->email; ?>
                <br/>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row invoice-cust-add margin-bottom-20">
        <div class="col-md-7 col-xs-6">
            <span class="bold">
                <?php
                    $default_message='Name';
                    $this->languages_m->translate('name',$default_message);
                ?>
            :</span> <?php 
            echo $group_members_array[$loan_application->member_id]->first_name.' '.$group_members_array[$loan_application->member_id]->last_name; ?><br/>
            <span class="bold">
                <?php
                    $default_message='Phone';
                    $this->languages_m->translate('phone',$default_message);
                ?>
            :</span> <?php echo $group_members_array[$loan_application->member_id]->phone; ?><br/>
            <span class="bold">
                <?php
                    $default_message='Email Address';
                    $this->languages_m->translate('email_address',$default_message);
                ?>
                :</span> <?php echo $group_members_array[$loan_application->member_id]->email; ?>
        </div>
        <div class="col-md-5 col-xs-6">
            
            <span class="bold"> Loan name :</span> <?php echo $loan_type_details->name ?><br/>
            <span class="bold"> Loan Amount: </span><?php echo $this->group_currency.' '. number_to_currency($loan_application->loan_amount); ?> <br>
            <span class="bold"> Loan application date : </span> <?php echo timestamp_to_receipt($loan_application->created_on) ?><br/>
            <span class="bold"> Loan Duration: </span> <?php
                if($loan_type_details->loan_repayment_period_type == 1){
                    echo $loan_type_details->fixed_repayment_period.' Months';
                }else if ($loan_type_details->loan_repayment_period_type == 2) {
                    echo $loan_type_details->minimum_repayment_period.' - '.$loan_type_details->maximum_repayment_period.' Months';
                }?><br> 
            <span class="bold">Guarantor's progress status : </span> <?php
                if(in_array(1, $guarantor_progress)){
                    $guarantor_progress_status = 'Pending';
                }else if(count(array_count_values($guarantor_progress)) == 1){
                    $guarantor_progress_status = ' Approved';
                }
             echo $guarantor_progress_status ?><br/>
            <span class="bold">Supervisor progress status : </span> <?php 
               if(empty($supervisor_recommendations)){
                    $supervisor_progress = '<span class="label label-primary">In Progress</span>';
                }else{
                    $recommendation = $supervisor_recommendations[$loan_application->member_supervisor_id];
                    if($recommendation){
                        if($recommendation->performance_management = 2){
                            if($recommendation->disciplinary_case = 2){
                                $supervisor_progress = '<span class="label label-success">Approved</span>';     
                            }else{
                                $supervisor_progress = '<span class="label label-danger">Declined</span>';
                            }                             
                        }else{
                            $supervisor_progress = '<span class="label label-danger">Declined</span>';
                        }
                    }else{
                        $supervisor_progress = '<span class="label label-warning">No details</span>'; 
                    }
                }
                echo $supervisor_progress ?><br/>
        </div>
    </div>
    <hr/>
    <div class="row invoice-body" id="invoice_body" >
        <?php if(!empty($posts)){ ?>
            <div class="col-xs-12 table-responsive ">
                <div class="guarantor_details" id="guarantor_details"  style="display: none;">
                    <div class="portlet-title">
                        <div class="caption margin-bottom-20 margin-top-10" style="margin-bottom: 10px;">
                            <span class="caption-subject bold uppercase ">Guarantor's</span>
                        </div>
                    </div>
                    <table class="table table-hover table-striped table-condensed table-statement">
                        <thead>
                            <tr>
                                <th class="invoice-title ">
                                    <?php
                                        $default_message='Guarantor Details';
                                        $this->languages_m->translate('guarantor_details',$default_message);
                                    ?>
                                </th>
                                <th class="invoice-title ">
                                    <?php
                                        $default_message='Action Date';
                                        $this->languages_m->translate('action_date',$default_message);
                                    ?>
                                </th>
                                <th  class="invoice-title ">
                                    <?php
                                        $default_message='Amount';
                                        $this->languages_m->translate('amount',$default_message);
                                    ?>
                                </th>
                                <th class="invoice-title ">
                                    <?php
                                        $default_message='Status';
                                        $this->languages_m->translate('status',$default_message);
                                    ?>

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = $this->uri->segment(5, 0); foreach($posts as $post):  ?>
                            <tr>
                                <td>
                                    <?php echo $this->active_group_member_options[$post->guarantor_member_id]?><br>
                                </td>
                                <td>
                                    <?php echo  timestamp_to_receipt($post->created_on) ?>
                                </td>
                                <td>
                                   <?php echo $this->group_currency.' '. number_to_currency($post->amount); ?><br> 
                                </td>
                                <td>
                                    <?php
                                    if($post->loan_request_progress_status == 1){
                                        //loan approval pending
                                        echo '<span class="label label-primary">In Progress</span>';
                                    }else if($post->loan_request_progress_status == 2){
                                        //Guarantor decline loan request
                                        echo '<span class="label label-danger">Declined</span>';
                                    }else if($post->loan_request_progress_status == 3){
                                        //Guarantor approve loan request
                                        echo '<span class="label label-success">Approved</span>';
                                    }
                                    ?>
                                </td>
                               
                            </tr>
                            <?php $i++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="supervisor_recomendations" id="supervisor_recomendations" style="display: none;">
                    <div class="portlet-title">
                        <div class="caption margin-bottom-20 margin-top-20" style="margin-bottom: 10px;">
                            <span class="caption-subject bold uppercase ">Supervisor Recommendation</span>
                        </div>
                    </div>
                    <?php if($supervisor_recommendations){ 
                        ?>
                        <table class="table table-hover table-striped table-condensed table-statement">
                            <thead>
                                <tr>
                                    <th class="invoice-title ">
                                        <?php
                                            $default_message='Supervisor name';
                                            $this->languages_m->translate('supervisor_name',$default_message);
                                        ?>
                                    </th>
                                    <th  class="invoice-title ">
                                        <?php
                                            $default_message='Comment';
                                            $this->languages_m->translate('comment',$default_message);
                                        ?>
                                    </th>
                                    <th class="invoice-title ">
                                        <?php
                                            $default_message='Recommendation';
                                            $this->languages_m->translate('recommendation',$default_message);
                                        ?>
                                    </th>
                                     <th class="invoice-title ">
                                       <?php
                                            $default_message='Status';
                                            $this->languages_m->translate('status',$default_message);
                                        ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <?php 
                                        echo $this->active_group_member_options[$loan_application->member_supervisor_id]?><br>
                                    </td>
                                    <td>
                                        <?php echo $supervisor_recommendations[$loan_application->member_supervisor_id]->comment?>
                                    </td>
                                    <td>
                                        <span >Poor performance management :</span> <?php
                                            if($supervisor_recommendations[$loan_application->member_supervisor_id]->performance_management == 1){
                                                echo 'YES';
                                            }else if($supervisor_recommendations[$loan_application->member_supervisor_id]->performance_management == 2){
                                                echo 'No';
                                            }else if($supervisor_recommendations[$loan_application->member_supervisor_id]->performance_management == 3){
                                                echo 'ANY';
                                            }
                                        ?><br/>
                                        <span > Ongoing/Pending disciplinary cases : </span> <?php
                                            if($supervisor_recommendations[$loan_application->member_supervisor_id]->disciplinary_case == 1){
                                                echo 'YES';
                                            }else if($supervisor_recommendations[$loan_application->member_supervisor_id]->disciplinary_case == 2){
                                                echo 'No';
                                            }
                                        ?><br/>
                                        <span > Date & Stamp : </span> 
                                        <?php echo timestamp_to_receipt($supervisor_recommendations[$loan_application->member_supervisor_id]->recommendation_date) ?>
                                        <br/>

                                    </td>
                                    <td>
                                        <?php
                                            if($supervisor_recommendations[$loan_application->member_supervisor_id]->is_approve){
                                                ?>
                                               <span class="label label-success label-xs"> Approved</span>
                                               <?php
                                            }else if($supervisor_recommendations[$loan_application->member_supervisor_id]->is_decline){ ?>
                                                <span class="label label-danger label-xs"> declined</span>
                                                <?php
                                            }

                                            ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <?php 
                    }else{ ?>
                        <div class="col-xs-12 margin-bottom-10 ">
                            <div class="alert alert-info">
                                <h4 class="block">
                                    <?php
                                        $default_message='Information! No records to display';
                                        $this->languages_m->translate('no_records_to_display',$default_message);
                                    ?>
                                    
                                </h4>
                                <p>
                                    No Supervisor recommendation details.
                                </p>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>            

                <div class="payroll_accountant" id="payroll_accountant" style="display: none;">
                    <div class="portlet-title">
                        <div class="caption margin-bottom-20 margin-top-20" style="margin-bottom: 10px;">
                            <span class="caption-subject bold uppercase ">Human Resource Appraisal : Terms of Employment</span>
                        </div>
                    </div>
                    <?php 
                        if($hr_appraisals){ ?>

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
                                foreach($hr_appraisals as $post):
                                    $loan_application_id = $post->loan_application_id; 

                                 ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td>
                                            <span class="bold"> Loan Applicant :</span> <?php echo $this->active_group_member_options[$post->loan_member_id] ?><br/>
                                            <span class="bold"> Loan name :</span> <?php echo $loan_type[$post->loan_type_id]->name ?><br/>
                                            <span class="bold"> Loan Amount: </span><?php echo $this->group_currency.' '. number_to_currency($post->loan_amount); ?> <br>
                                            <span class="bold"> Loan application date : </span> <?php echo timestamp_to_receipt($loan_application->created_on) ?><br/>
                                            <span class="bold"> Loan Duration: </span> <?php
                                                if($loan_type[$post->loan_type_id]->loan_repayment_period_type == 1){
                                                    echo $loan_type[$post->loan_type_id]->fixed_repayment_period.' Months';
                                                }else if ($loan_type[$post->loan_type_id]->loan_repayment_period_type == 2) {
                                                    echo $loan_type[$post->loan_type_id]->minimum_repayment_period.' - '.$loan_type[$post->loan_type_id]->maximum_repayment_period.' Months';
                                                }?><br>                      

                                        </td>
                                        <td>
                                            <span class="bold">Poor performance management :</span> <?php
                                                if($post->terms_of_employment == 1){
                                                    echo 'Permanent';
                                                }else if($post->terms_of_employment == 2){
                                                    echo 'Contract';
                                                }
                                            ?><br/>
                                            <span class="bold">Contract End Date : </span> <?php                                
                                                    echo timestamp_to_receipt($post->contract_end_date);
                                            ?><br/>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <table class="table table-striped table-bordered table-condensed table-hover table-payments">
                            <tbody>
                                <tr>
                                    <td>Contract Type</td>
                                    <td class="text-right"><?php

                                        if($hr_appraisals[$loan_application->member_id]->terms_of_employment == 1){
                                            echo 'Permanent';
                                        }else if($hr_appraisals[$loan_application->member_id]->terms_of_employment == 2){
                                            echo 'Contract';
                                        }
                                        
                                     ?></td>
                                </tr>
                                <?php 
                                if($hr_appraisals[$loan_application->member_id]->terms_of_employment == 2){ ?>
                                    <tr>
                                        <td>Contract End Date</td>
                                        <td class="text-right">
                                        <?php
                                            if($hr_appraisals[$loan_application->member_id]->terms_of_employment == 2){
                                               ;
                                                echo timestamp_to_receipt($hr_appraisals[$loan_application->member_id]->contract_end_date);
                                            }
                                        ?>                                    
                                        </td>
                                    </tr>
                                    <?php 
                                }else{

                                } ?>
                            </tbody>
                        </table>
                       
                        <div class="existing_loan_break_down" id="existing_loan_break_down" >
                            <fieldset>
                                <div class="">
                                    <div class="portlet-body">
                                        <table class="table table-striped table-bordered table-condensed table-hover table-payments">
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
                                                </tr>
                                            </thead>
                                                <tbody> 
                                                    <?php
                                                        $net_amount_payable = 0;
                                                        $new_loan_total_installments = 0;
                                                        $net_pay = 0;
                                                        $percentage_net_pay = 0;
                                                    foreach($hr_appraisals as $post):
                                                        $member_existing_loans = $existing_loans[$post->loan_application_id];                                                          
                                                        foreach($member_existing_loans as $member_loan): 
                                                            $member_loan = $member_loan;
                                                            //print_r($member_loan); 
                                                            if($member_loan){

                                                                if($member_loan->loan_application_id == $post->loan_application_id){
                                                                
                                                                    $is_loan_exisiting = isset($member_loan->is_loan_exisiting)?$member_loan->is_loan_exisiting:'';
                                                                    if($is_loan_exisiting == 1){
                                                                        $net_pay += currency($member_loan->net_pay);
                                                                        $percentage_net_pay += currency($member_loan->percentage_net_pay);
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

                                                        endforeach;
                                                        ?>
                                                          <tfoot>
                                                                <tr>
                                                                   <td colspan = "3"></td>
                                                                   <td >Net Pay</td>
                                                                   <td><?php echo $this->group_currency .' '. number_to_currency($net_pay)?></td>
                                                                </tr>
                                                                <tr>
                                                                   <td colspan = "3"></td>
                                                                   <td > % age of Net</td>
                                                                   <td> 

                                                                    <label class="form-group">
                                                                        <?php echo $percentage_net_pay ?>
                                                                    </label>                                           

                                                                   </td>
                                                                </tr>
                                                             </tfoot>

                                                </tbody>
                                        </table>

                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        

                        <?php 
                        if(empty($member_loans)):
                        else: ?>
                            <table class="table table-hover table-striped table-condensed table-statement">
                                <thead>
                                    <tr>
                                        <th class="invoice-title ">
                                            <?php
                                                $default_message='Existing Loans Amount ('.$this->group_currency.')';
                                                $this->languages_m->translate('existing_loans',$default_message);
                                            ?>
                                        </th>
                                        <th  class="invoice-title ">
                                            <?php
                                                $default_message='Installments';
                                                $this->languages_m->translate('comment',$default_message);
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
                                                $this->languages_m->translate('%_age',$default_message);
                                            ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = $this->uri->segment(5, 0); foreach($member_loans as $member_loan):
                                      ?>
                                    <tr>
                                        <td>
                                            <?php echo $this->group_currency.' '. number_to_currency($member_loan->loan_amount); ?><br>
                                        </td>
                                        <?php
                                            $balance = array();
                                            $installments = $this->loan_invoices_m->get_loan_installments($member_loan->id);
                                            foreach($installments as $key => $installment) {
                                                $balance[$member_loan->id] = $installment->amount_payable - $installment->amount_paid;
                                            } 
                                        ?> 
                                        <td> 
                                            <?php echo count($installments)?>                                      
                                        </td>
                                        <td>
                                            <?php echo $this->group_currency.' '. number_to_currency($balance[$member_loan->id])?>    
                                        </td>
                                        <td>
                                            
                                        </td>
                                        <td>
                                            
                                        </td>
                                       
                                    </tr>
                                    <?php $i++;
                                    endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif;?>
                    <?php
                    }else{ ?>

                        <div class="col-xs-12 margin-bottom-10 ">
                            <div class="alert alert-info">
                                <h4 class="block">
                                    <?php
                                        $default_message='Information! No records to display';
                                        $this->languages_m->translate('no_records_to_display',$default_message);
                                    ?>
                                    
                                </h4>
                                <p>
                                    Human resource  appraisal terms of employment
                                </p>
                            </div>
                        </div>

                        <?php
                    }  ?>
                    
                </div>

                <div class="sacco_officer" id="sacco_officer" style="display: none;">
                   
                    <div class="portlet-title">
                        <div class="caption margin-bottom-20 margin-top-20" style="margin-bottom: 10px;">
                            <span class="caption-subject bold uppercase ">Sacco Officer</span>
                        </div>
                    </div> 
                    <?php 
                      if($sacco_appraisals){ ?>

                        <div class="existing_loan_break_down" id="existing_loan_break_down" >
                            <fieldset>
                                <div class="">
                                    <div class="portlet-body">
                                        <table class="table table-striped table-bordered table-condensed table-hover table-payments">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        
                                                    </th>
                                                    <th class="invoice-title ">
                                                        <?php
                                                            $default_message='Amount';
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
                                                </tr>
                                            </thead>
                                                <tbody> 
                                                    <?php
                                                        $net_amount_payable = 0;
                                                        $new_loan_total_installments = 0;              

                                                        $loans_available = array(
                                                            '1' => 'Existing Group Loan',
                                                            '2' => 'New Group Loan'
                                                        );
                                                foreach ($sacco_appraisals as $key => $post): 
                                                        $member_existing_loans = $existing_loans[$post->loan_application_id];   
                                                        foreach($member_existing_loans as $member_loan):
                                                            $member_loan = $member_loan;
                                                            //print_r($member_loan); 
                                                            if($member_loan){

                                                                if($member_loan->loan_application_id == $post->loan_application_id){
                                                                
                                                                    $is_loan_exisiting = isset($member_loan->is_loan_exisiting)?$member_loan->is_loan_exisiting:'';
                                                                    if($is_loan_exisiting == 1){
                                                                    ?>
                                                                        <tr>
                                                                            <td rowspan="">
                                                                                <?php echo $loans_available[1]  ?>
                                                                            </td>
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
                                                            $new_loan_total_installments = 0;
                                                            $total_installment = 0;
                                                            $member_existing_loans = $existing_loans[$post->loan_application_id];
                                                            foreach ($member_existing_loans as $key => $member_loan) {
                                                                if($member_loan){
                                                                    $exisiting_loan_application_id = $member_loan->loan_application_id;
                                                                    $new_loan_total_installments += currency($member_loan->loan_amount_installments);
                                                                }
                                                            }
                                                            $loan_value_details = $loan_values[$post->loan_application_id];
                                                            foreach ($loan_value_details as $key => $loan_value) {
                                                               $loan_value = (object)$loan_value;

                                                               $net_amount_payable += $loan_value->amount_payable;
                                                               $monthly_installments = $loan_value->amount_payable;
                                                            }
                                                            
                                                            

                                                            if($group_loan_applications[$post->loan_application_id]->id == $post->loan_application_id){

                                                            ?>
                                                                <tr>
                                                                    <td rowspan="">
                                                                        <?php echo $loans_available[2]  ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $this->group_currency.' '. number_to_currency($loan_application->loan_amount); ?><br>
                                                                    </td>
                                                                    <td> 
                                                                        <?php echo $this->group_currency.' '. number_to_currency($monthly_installments)?>                                     
                                                                    </td>
                                                                    <td>
                                                                       <?php echo $loan_application->repayment_period .' Months'?> 
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $this->group_currency.' '. number_to_currency($loan_application->repayment_period * $monthly_installments); ?><br>    
                                                                    </td> 
                                                                </tr>
                                                                <?php 
                                                            }?> 


                                                    </tbody>
                                                    <tfoot>
                                                    <?php 
                                                    if($group_loan_applications[$post->loan_application_id]->id == $post->loan_application_id){
                                                       $total_installment = $new_loan_total_installments + $monthly_installments;
                                                    }
                                                    if($group_loan_applications[$post->loan_application_id]->id == $post->loan_application_id){ ?>
                                                        <tr>
                                                           <td colspan = "3"></td>
                                                           <td >Total Installnments(All Loans)</td>
                                                           <td><?php echo $this->group_currency .' '. number_to_currency($total_installment)?></td>
                                                        </tr>
                                                        <tr>
                                                           <td colspan = "3"></td>
                                                           <td > % age of Net</td>
                                                           <td>--</td>
                                                        </tr>
                                                    <?php } ?>
                                                 </tfoot>
                                                <?php endforeach; ?>
                                        </table>

                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <?php 

                        if($group_loan_applications[$post->loan_application_id]->id == $post->loan_application_id){
                            ?>
                            <div class="supervisory_form_holder" id="supervisory_form_holder" >
                                <fieldset>
                                    <div class="loan_break_down" id="loan_break_down" style="">
                                        <div class="portlet-title">
                                            <div class="caption margin-bottom-20 margin-top-10" style="margin-bottom: 5px;">
                                                <span class="caption-subject bold uppercase ">New Loan Breakdown</span>
                                            </div>
                                        </div>
                                        <table class="table table-striped table-bordered table-condensed table-hover table-payments">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="100%" colspan="2">
                                                       <span class="caption-subject bold uppercase ">New Sacco Loan Intrest at 14% per Annum</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                                <tr>
                                                    <td>Loan Amount <?php echo $this->group_currency?></td>
                                                    <td class="text-right"><?php echo $this->group_currency .' '. number_to_currency($loan_application->loan_amount) ?></td>
                                                </tr> 
                                                <tr>
                                                    <td>Term</td>
                                                    <td class="text-right"><?php echo $loan_application->repayment_period .' Months'?></td>
                                                </tr> 
                                                <tr>
                                                    <td>Monthly Installments</td>
                                                    <td class="text-right">
                                                        <?php echo $this->group_currency .' '. number_to_currency($monthly_installments) ?>
                                                    </td>
                                                </tr>
                                                <tr><?php

                                                switch ($loan_application->loan_amount){
                                                    case ($loan_application->loan_amount <= 100000 && $loan_application->loan_amount >= 10000): 
                                                        $lace = $loan_application->loan_amount; 
                                                    break;
                                                    default: //default
                                                        $lace = ((0.005) * $loan_application->loan_amount);
                                                    break;
                                                }
                                                ?>
                                                    <td>LACE -0.5% (Min Ugx 10,000 -Max Ugx(10,000))</td>
                                                    <td class="text-right"><?php echo $this->group_currency .' '. number_to_currency($lace)?></td>
                                                </tr> 
                                                 <tr>
                                                    <td>Credit Insurance - 0.7%</td>
                                                    <td class="text-right"><?php echo $this->group_currency .' '. number_to_currency(((0.007) * $loan_application->loan_amount))?></td>
                                                </tr>
                                                 <tr>
                                                    <td>Net Loan Amount</td>
                                                    <td class="text-right"><?php echo $this->group_currency .' '.number_to_currency($net_amount_payable) ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </fieldset>
                            </div>
                        <?php } ?>
                    <?php }else{ ?>

                        <div class="col-xs-12 margin-bottom-10 ">
                            <div class="alert alert-info">
                                <h4 class="block">
                                    <?php
                                        $default_message='Information! No records to display';
                                        $this->languages_m->translate('no_records_to_display',$default_message);
                                    ?>
                                    
                                </h4>
                                <p>
                                    Second Level appraisal and processing by sacco details
                                </p>
                            </div>
                        </div>

                    <?php } ?>                   

                </div>

                <div class="sacco_committe" id="sacco_committe" style="display: none;">
                    <div class="portlet-title">
                        <div class="caption margin-bottom-20 margin-top-20" style="margin-bottom: 10px;">
                            <span class="caption-subject bold uppercase ">Sacco Committee Decision </span>
                        </div>
                    </div>
                    <?php if($committee_decisions){ ?>            
                        <table class="table table-hover table-striped table-condensed table-statement">
                            <thead>
                                <tr>
                                    <th class="invoice-title ">
                                        <?php
                                            $default_message='Commitee Member Details';
                                            $this->languages_m->translate('committee_member_details',$default_message);
                                        ?>
                                    </th>
                                    <th  class="invoice-title ">
                                        <?php
                                            $default_message='Action  Date';
                                            $this->languages_m->translate('action_date',$default_message);
                                        ?>
                                    </th>
                                    <th class="invoice-title ">
                                        <?php
                                            $default_message='Decision';
                                            $this->languages_m->translate('recommendation',$default_message);
                                        ?>
                                    </th>
                                     <th class="invoice-title ">
                                       <?php
                                            $default_message='Status';
                                            $this->languages_m->translate('status',$default_message);
                                        ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0); foreach($committee_decisions as $committee_decision): 
                                //print_r($committee_decision);
                                    if($committee_decision->signatory_member_id){
                                        $committee_member_id = $committee_decision->signatory_member_id;
                                        $progress_status = $committee_decision->loan_signatory_progress_status;
                                    }else if($committee_decision->commitee_member_id){
                                        $committee_member_id = $committee_decision->commitee_member_id;
                                        $progress_status = $committee_decision->committee_progress_status;
                                    }

                                    if($progress_status == 2 && $progress_status == 2 ){
                                        if($committee_decision->approve_comment){
                                            $comment = $committee_decision->approve_comment;  
                                        }else if($committee_decision->is_declined){
                                            $comment = $committee_decision->approve_comment; 
                                        }                                    
                                    }
                                 ?>
                                <tr>
                                    <td>
                                        <?php echo $this->active_group_member_options[$committee_member_id]?><br>
                                    </td>
                                    <td>
                                        <?php if($committee_decision->modified_on){
                                            echo  timestamp_to_receipt($committee_decision->modified_on);  
                                        } 
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if($progress_status == 2 && $progress_status == 4){
                                                echo $comment;
                                            }
                                        ?>
                                        
                                    </td>
                                    <td>
                                        <?php
                                        if($progress_status == 1){
                                            echo '<span class="label label-primary">In Progress</span>';
                                        }else if($progress_status == 2){
                                            echo '<span class="label label-danger">Declined</span>';
                                        }else if($progress_status == 3){
                                            echo '<span class="label label-success">Approved</span>';
                                        }else if($progress_status == 4){
                                            echo '<span class="label label-warning">Deffered</span>';
                                        }
                                        ?>
                                    </td>
                                   
                                </tr>
                                <?php $i++;
                                endforeach; ?>
                            </tbody>
                        </table>
                    <?php }else{ ?>

                        <div class="col-xs-12 margin-bottom-10 ">
                            <div class="alert alert-info">
                                <h4 class="block">
                                    <?php
                                        $default_message='Information! No records to display';
                                        $this->languages_m->translate('no_records_to_display',$default_message);
                                    ?>
                                    
                                </h4>
                                <p>
                                    No credit committe details
                                </p>
                            </div>
                        </div>

                    <?php

                    }?>
                </div>
            </div>

        <?php }else{ ?>
            <div class="col-xs-12 margin-bottom-10 ">
                <div class="alert alert-info">
                    <h4 class="block">
                        <?php
                            $default_message='Information! No records to display';
                            $this->languages_m->translate('no_records_to_display',$default_message);
                        ?>
                        
                    </h4>
                    <p>
                        No Loan requests to display.
                    </p>
                </div>
            </div>
        <?php } ?> 
    </div>
    
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Loan Installments</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div class="loan_amortization" id="loan_amortization"> </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- end modal -->


<script>
    $(window).on('load',function(){
        loan_status();
    });

    function loan_status(){
        var loan_application_id = '<?php echo $this->uri->segment(4) ?>';
        App.blockUI({
            target: '#invoice_body',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "POST",
            data:{loan_application_id: loan_application_id},
            url: '<?php echo base_url("ajax/loans/ajax_loan_information"); ?>',
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response);                       
                        if(result.status == '200'){
                           // if(result.guarantors_available >= 1){
                                $('#guarantor_details').slideDown();
                           // }

                           // if(result.supervisor_recommendations_available >= 1){
                              $('#supervisor_recomendations').slideDown();                                
                          //  }
                           // if(result.payroll_accountant >= 1){
                                $('#payroll_accountant').slideDown();
                           // }
                           // if(result.sacco_appraisal >= 1){
                                $('#sacco_officer').slideDown();
                           // }
                            //if(result.committee_decision >= 1){
                                $('#sacco_committe').slideDown();
                                $('#form_action_holder').slideDown();
                           // }
                        }else if(result.status == '0'){
                           // $('#loan_type_details').html(result.message);  
                        }
                    }else{
                        alert(response);
                    }                          
                    App.unblockUI('#invoice_body');
                }
            }
        );
    }

    function view_installments(loan_id){        
        $('#btnloan_amortization').text('');
        $('#btnloan_amortization').append('<span><i class="fa fa-spinner fa-spin"></i> Processing  </span>');
        $('#btnloan_amortization').attr('disabled',true);

        $('#loan_amortization').html("");
        $('#loan_amortization').css("min-height","70px");
        App.blockUI({
            target: '#loan_break_down',
            overlayColor: 'grey',
            animate: true
        });
        $.ajax({
            type: "POST",
            url: '<?php echo base_url("member/loans/eazzy_club_sacco_existing_loan_calculator"); ?>',
            data:{loan_id:loan_id},
            dataType : "html",
                success: function(response) {
                    $('#exampleModal').modal('show'); // show bootstrap modal when complete loaded
                    $('#loan_amortization').html(response);                    
                    App.unblockUI('#loan_break_down');
                     $('#btnloan_amortization').text('');
                     $('#btnloan_amortization').append('<span> View </span>');
                     $('#btnloan_amortization').attr('disabled',false);
                },
                error:function(response){
                   $('#loan_amortization').html(response);                    
                    App.unblockUI('#loan_break_down'); 
                }
            }
        );
    }

    function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

</script>
