<?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
    <div class="form-body">
        <div class="loan_application_form_holder" id="loan_application_form_holder">
            
        </div>

        <div class="existing_loans_from_payroll" id="existing_loans_from_payroll" style="display: none;">
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
                                $net_pay = 0;
                                $percentage_net_pay = 0;
                                foreach($existing_loans as $member_loan):
                                    if($member_loan->is_loan_exisiting == 1){
                                        //print_r($existing_loans);
                                        $net_pay += currency($member_loan->net_pay);
                                        $percentage_net_pay += currency($member_loan->percentage_net_pay);
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
                                     </tfoot><?php
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

        <div class="existing_loan_break_down" id="existing_loan_break_down" style="display: none;">
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
                                    
                                        if(!empty($member_loans)){
                                            $total_amount_payable = 0;
                                            $total_amount_paid = 0;
                                            $installment = 0;
                                            foreach($member_loans as $loan):
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
                                        foreach ($existing_loans as $key => $member_loan) {
                                            $new_loan_total_installments += currency($member_loan->loan_amount_installments);
                                        }

                                        foreach ($loan_values as $key => $loan_value) {
                                           $loan_value = (object)$loan_value;
                                           $net_amount_payable += $loan_value->amount_payable;
                                           $monthly_installments = $loan_value->amount_payable;
                                        }

                                        $installment = 0;
                                        $new_bank_loan_installments = 0;
                                        foreach($member_loans as $loan):
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
                                            <?php echo form_input('percentage_net_pay',$this->input->post('percentage_net_pay')?$this->input->post('percentage_net_pay'):$post->percentage_net_pay,'  class="form-control currency" placeholder="Percentage Net Pay" id="percentage_net_pay"'); ?>
                                        </label>                                           

                                       </td>
                                    </tr>
                                 </tfoot>
                        </table>

                    </div>
                </div>
            </fieldset>
        </div>

        <div class="supervisory_form_holder" id="supervisory_form_holder" style="display: none;">
            <fieldset>
                <div class="row">
                    <div class="col-md-4">
                        <label>Deposit Balance (<?php echo $this->group_currency?>)</label>
                        <div class="input-group"> 
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            <?php echo form_input('',"",'  class="form-control currency" id="" autocomplete="off" disabled placeholder="<?php echo  number_to_currency($member_savings);?> " value="<?php echo $member_savings;?>"'); ?>                        
                        </div>                                               
                     </div>
                    <div class="col-md-4">
                        <div class="form-group maximum_loan_amount"> 
                             <label><span class="required"> * </span> 3 Member Deposits</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </span>
                                <?php echo form_input('loan_times_number',$this->input->post('loan_times_number')?:'','  class="form-control currency" id="" autocomplete="off" disabled placeholder="<?php echo  number_to_currency($member_savings) ;?> * <?php echo $loan_type->loan_times_number?> "'); ?>
                            </div>                                    
                        </div>                                
                    </div>
                    <div class="col-md-4">
                       <label>Amount </label>
                        <div class="input-group"> 
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                         <?php echo form_input('',"",'  class="form-control currency" id="" autocomplete="off"  disabled placeholder="<?php echo  number_to_currency($member_savings * $loan_type->loan_times_number ) ;?>"'); ?>
                        </div> 
                    </div>
                </div>
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
                                   <span class="caption-subject bold uppercase ">New Sacco Loan Interest at 14% per Annum</span>
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

        <div class="form-actions" id="form_action_holder" style="display: none;">
            <input type="submit" class="btn blue submit_form_button" name="approve" value=" <?php
                    $default_message='Approve  Request';
                    $this->languages_m->translate('Approve  Request',$default_message);
                ?>">                   
            </input>
            <input type="submit" class="btn red submit_form_button" name="decline" value="<?php
                    $default_message='Decline  Request';
                    $this->languages_m->translate('Decline  Request',$default_message);
                ?>">                 
            </input>
            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                <?php
                    $default_message='Processing';
                    $this->languages_m->translate('processing',$default_message);
                ?>
            </button> 
            <button type="button" class="btn default">
                <?php
                    $default_message='Cancel';
                    $this->languages_m->translate('cancel',$default_message);
                ?>
            </button>
        </div>
    </div>    
<?php echo form_close(); ?>

<script>
    $(window).on('load',function(){
        loan_application_details();
    });

    function loan_application_details(){
        var loan_application_id = '<?php echo $this->uri->segment(4) ?>';
        App.blockUI({
            target: '#loan_application_form_holder',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "POST",
            data:{loan_application_id: loan_application_id},
            url: '<?php echo base_url("ajax/loans/ajax_loan_details"); ?>',
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response);                       
                        if(result.status == '200'){
                            $('#loan_application_form_holder').html(result.html);
                            $('#existing_loans_from_payroll').slideDown();
                            $('#existing_loan_break_down').slideDown();
                            $('#supervisory_form_holder').slideDown();
                            $('#form_action_holder').slideDown();
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
    function view_installments(){
        $('#btnloan_amortization').text('');
        $('#btnloan_amortization').append('<span><i class="fa fa-spinner fa-spin"></i> Processing  </span>');
        $('#btnloan_amortization').attr('disabled',true);
        var loan_application_ids = '<?php echo $loan_applications->id ;?>';
        $('#loan_amortization').html("");
        $('#loan_amortization').css("min-height","70px");
        App.blockUI({
            target: '#loan_break_down',
            overlayColor: 'grey',
            animate: true
        });
        $.ajax({
            type: "POST",
            url: '<?php echo base_url("member/loans/eazzy_club_sacco_loan_calculator"); ?>',
            data:{loan_application_id:loan_application_ids},
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