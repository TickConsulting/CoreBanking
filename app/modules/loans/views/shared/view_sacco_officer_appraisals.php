<div class="row">
    <div class="col-md-12">
        <?php
        foreach ($posts as $key => $post):
            ?> 
                <div class="panel-group accordion" id="accordion1">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion1" href="#collapse_<?php echo $post->id?>" aria-expanded="false"> Loan Appraisal  & processing by sacco for <?php echo $this->active_group_member_options[$post->loan_member_id]?>  </a>
                            </h4>
                        </div>
                        <div id="collapse_<?php echo $post->id?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                            <div class="panel-body">                               
                                <div class="">
                                    <div class="loan_application_form_holder" id="loan_application_form_holder" style="position: relative;"> 
                                        <div class="well well-lg">
                                            <h5 class="block" style="margin-top: -7px !important; margin-bottom: -3px !important;">
                                                <strong>Loan Type : </strong>  <?php echo $loan_type[$post->loan_type_id]->name ?> 
                                            </h5><strong> Loan Applicant : </strong>  <?php echo $this->active_group_member_options[$post->loan_member_id]?><br>
                                            <strong> Loan Amount : </strong>  <?php echo $this->group_currency.' '. number_to_currency($loan_applications->loan_amount); ?><br>
                                            <strong>  Loan Duration : </strong> <?php echo $loan_applications->repayment_period .' Months'?>
                                        </div>
                                    </div>

                                    <div class="existing_loans_from_payroll" id="existing_loans_from_payroll">
                                        <fieldset>
                                            <div class="">
                                                <div class="caption margin-bottom-20 margin-top-10" style="margin-bottom: 5px;">
                                                    <span class="caption-subject bold uppercase ">Human Resource Appraisal: Terms of Employment</span>
                                                </div> 
                                                <div class="portlet-body">                         
                                                <?php
                                                    $net_amount_payable = 0;
                                                    $new_loan_total_installments = 0;                                     

                                                    $loans_available = array(
                                                        '1' => 'Existing Loan Amount',
                                                        '2' => 'New Group Loan'
                                                    );
                                                    if($existing_loans){ ?>
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
                                                            $member_existing_loans = $existing_loans[$post->loan_application_id];   
                                                            foreach($member_existing_loans as $member_loan):
                                                                if($member_loan->is_loan_exisiting == 1){
                                                              ?>
                                                                <tr>
                                                                    <td rowspan="">
                                                                        <?php echo $loans_available[1]  ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $this->group_currency.' '. number_to_currency($member_loan->existing_loan_amount); ?><br>
                                                                    </td> 
                                                                    <td> 
                                                                        <?php echo $this->group_currency.' '. number_to_currency($member_loan->loan_amount_installments)?>                                      
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
                                                                                <strong>Information!</strong>  Member does not have a loan from payroll Accountant appraisals                                   
                                                                            </div>
                                                                            
                                                                        </td>
                                                                    </tr><?php
                                                                } ?>
                                                            <?php $i=0 ;$i++;                                
                                                            endforeach; 
                                                        }else{ ?>
                                                            <div class="alert alert-info">
                                                                <button class="close" data-dismiss="alert"></button>
                                                                <strong>Information!</strong>  Member does not have a loan from payroll Accountant appraisals                                   
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="existing_loan_break_down" id="existing_loan_break_down">
                                        <fieldset>
                                            <div class="">
                                                <div class="caption margin-bottom-20 margin-top-10" style="margin-bottom: 5px;">
                                                    <span class="caption-subject bold uppercase ">Second Level  Appraisal & processing by sacco</span>
                                                </div>
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
                                                                   // print_r($member_loans);
                                                                
                                                                    if(!empty($member_loans[$post->loan_application_id])){
                                                                        $total_amount_payable = 0;
                                                                        $total_amount_paid = 0;
                                                                        $installment = 0;
                                                                        $member_loans_array = $member_loans[$post->loan_application_id];
                                                                        foreach($member_loans_array as $loan):
                                                                            $calculations = $this->loan_invoices_m->get_loan_installments($loan->id);
                                                                            foreach ($calculations as $key => $callculation):
                                                                                $installment = $callculation->amount_payable;
                                                                                $total_amount_payable += $callculation->amount_payable;
                                                                                $total_amount_paid += $callculation->amount_paid;                                                    
                                                                            endforeach;
                                                                            ?>
                                                                            <tr>
                                                                                <td rowspan="">
                                                                                    <?php echo $loans_available[1]  ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php echo $this->group_currency.' '. number_to_currency($loan->loan_amount); ?><br>
                                                                                </td> 
                                                                                <td> 
                                                                                    <?php echo $this->group_currency.' '. number_to_currency($installment) ?>                                 
                                                                                </td>
                                                                                <td>
                                                                                   <?php echo $loan->repayment_period .' Months'?> 
                                                                                </td>
                                                                                <td>
                                                                                    <?php 

                                                                                    $balance = $total_amount_payable- $total_amount_paid;

                                                                                    echo $this->group_currency.' '. number_to_currency($balance)?>    
                                                                                </td>                                
                                                                            </tr>
                                                                    <?php
                                                                        $i=0 ;$i++;
                                                                
                                                                        endforeach;
                                                                    }else{ ?>
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

                                                                    } ?>

                                                                <?php 
                                                                    $new_loan_total_installments = 0;
                                                                    $total_installment = 0;  
                                                                    $member_existing_loans = $existing_loans[$post->loan_application_id];                                      
                                                                    foreach ($member_existing_loans as $key => $member_loan) {
                                                                        $new_loan_total_installments += currency($member_loan->loan_amount_installments);
                                                                    }

                                                                    $loan_values_array = $loan_values[$post->loan_application_id];
                                                                    foreach ($loan_values_array as $key => $loan_value) {
                                                                       $loan_value = (object)$loan_value;
                                                                       $net_amount_payable += $loan_value->amount_payable;
                                                                       $monthly_installments = $loan_value->amount_payable;
                                                                    }

                                                                    $installment = 0;
                                                                    $new_bank_loan_installments = 0;
                                                                    $member_loans_array = $member_loans[$post->loan_application_id];
                                                                    foreach($member_loans_array as $loan):
                                                                        $calculations = $this->loan_invoices_m->get_loan_installments($loan->id);
                                                                        foreach ($calculations as $key => $callculation):
                                                                            $installment = $callculation->amount_payable;
                                                                            
                                                                        endforeach;
                                                                        $new_bank_loan_installments += $installment;
                                                                    endforeach;

                                                                    //echo number_to_currency($new_loan_total_installments) .'new loan installments from sacco <br>';
                                                                   // echo number_to_currency($monthly_installments) .'new loan sacco loan <br>';

                                                                    //echo number_to_currency($installment) .'from exsiting member loans <br>';
                                                                    //echo number_to_currency($new_bank_loan_installments) .'new bank loans installments <br>';

                                                                    $total_installment = $monthly_installments + $new_loan_total_installments + $new_bank_loan_installments;
                                                                     
                                                                    ?>
                                                                    <tr>
                                                                        <td rowspan="">
                                                                            <?php echo $loans_available[2]  ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $this->group_currency.' '. number_to_currency($loan_applications->loan_amount); ?><br>
                                                                        </td>
                                                                        <td> 
                                                                            <?php echo $this->group_currency.' '. number_to_currency($monthly_installments)?>                                     
                                                                        </td>
                                                                        <td>
                                                                           <?php echo $loan_applications->repayment_period .' Months'?> 
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $this->group_currency.' '. number_to_currency($loan_applications->repayment_period * $monthly_installments); ?><br>    
                                                                        </td>  
                                                                    </tr>

                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                   <td colspan = "3"></td>
                                                                   <td >Total Installments(All Loans)</td>
                                                                   <td><?php echo $this->group_currency .' '. number_to_currency($total_installment)?></td>
                                                                </tr>
                                                                <tr>
                                                                   <td colspan = "3"></td>
                                                                   <td > % age of Net</td>
                                                                   <td> 

                                                                    <label class="form-group">
                                                                        <?php echo $post->percentage_net_pay ?>
                                                                    </label>                                           

                                                                   </td>
                                                                </tr>
                                                             </tfoot>
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
                                                                <td class="text-right"><?php echo $this->group_currency .' '. number_to_currency($loan_applications->loan_amount) ?></td>
                                                            </tr> 
                                                            <tr>
                                                                <td>Term</td>
                                                                <td class="text-right"><?php echo $loan_applications->repayment_period .' Months'?></td>
                                                            </tr> 
                                                            <tr>
                                                                <td>Monthly Installments</td>
                                                                <td class="text-right">
                                                                    <?php echo $this->group_currency .' '. number_to_currency($monthly_installments) ?>
                                                                </td>
                                                            </tr>
                                                            <tr><?php

                                                            switch ($loan_applications->loan_amount){
                                                                case ($loan_applications->loan_amount <= 100000 && $loan_applications->loan_amount >= 10000): 
                                                                    $lace = $loan_applications->loan_amount; 
                                                                break;
                                                                default: //default
                                                                    $lace = ((0.005) * $loan_applications->loan_amount);
                                                                break;
                                                            }
                                                            ?>
                                                                <td>LACE -0.5% (Min Ugx 10,000 -Max Ugx(100,000))</td>
                                                                <td class="text-right"><?php echo $this->group_currency .' '. number_to_currency($lace)?></td>
                                                            </tr> 
                                                             <tr>
                                                                <td>Credit Insurance - 0.7%</td>
                                                                <td class="text-right"><?php echo $this->group_currency .' '. number_to_currency(((0.007) * $loan_applications->loan_amount))?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Net Loan Amount</td>
                                                                <td class="text-right"><?php 
                                                                $net_loan_amount = 0;
                                                                $credit_lace = $lace + (((0.007) * $loan_applications->loan_amount));
                                                                $net_loan_amount = ($loan_applications->loan_amount) - $credit_lace; 
                                                                echo $this->group_currency .' '.number_to_currency($net_loan_amount) ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </fieldset>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        endforeach;

        ?>
    </div>
</div>


<div class="row col-md-12">
            
</div>