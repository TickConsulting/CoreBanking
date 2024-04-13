<?php if(isset($pdf_true) && $pdf_true){?>
    <link href="<?php echo site_url();?>assets/styling/style.css" rel="stylesheet" type="text/css" /> 
    <link href="<?php echo site_url();?>templates/admin_themes/groups/css/custom.css" rel="stylesheet" type="text/css" /> 
    <style type="text/css">
        .statement_types,.filter_header,.print_layout{
            display: none;
        }
        #statement_paper .table td {
            font-size: 9px;
            padding: .25rem;
        }
        #statement_paper td:nth-child(2) {
            display: none;
        }
        #statement_paper .table td ,.td_data {
            display: none;
        }
        .statement-header-content {
            font-size: 9px;
        }
        #statement_header,.header_paper,.contribution_name,#statement_paper .table th,#statement_footer{
            font-size: 10px;
        }
        #statement_paper {
            padding: none;
            box-shadow: none;
        }
        .pdf_layout{
            display: none;  
        }
    </style>
<?php } ?>
<?php echo form_open('group/loan_invoices/action', ' id="form"  class="form-horizontal"'); ?> 
<div class="col-md-12">
    <div id="statement_paper">
        <div class="row" id="statement_header">
            <div class="col-xs-6 col-sm-6">
                <div class="invoice-logo">
                    <img src="<?php echo is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo); ?>" alt="" class='group-logo image-responsive' /> 
                </div>
            </div> 
            <div class="col-sm-6 text-right" >
                <div class="company-address">
                    <span class="bold uppercase"><?php echo $this->group->name; ?></span>
                    <?php echo nl2br($this->group->address); ?><br/>
                    <span class="bold">Telephone: </span> <?php echo $this->group->phone; ?>
                    <br/>
                    <span class="bold">E-mail Address: </span> <?php echo $this->group->email; ?>
                    <br/>
                </div>
            </div>
        </div>
        <div class="row header_paper">
            <div class="col-xs-7 col-sm-7 statement-header-content">
                <h4>Loan Details:</h4>
                <span class="bold">Loan Type: </span><?php echo isset($loan_type_options[$loan->loan_type_id])?$loan_type_options[$loan->loan_type_id]:translate('Normal Loan');?><br/>
                <span class="bold">Amount Borrowed: </span> <?php echo $this->group_currency.' '.number_to_currency($loan->loan_amount); ?><br/>
                <span class="bold">Loan Date: </span> <?php echo timestamp_to_date($loan->disbursement_date); ?> - <?php echo timestamp_to_date($loan->loan_end_date); ?><br/>
                <span class="bold">Loan Interest Rate: </span> <?php echo $loan->interest_rate;?>% <?php echo $loan->loan_interest_rate_per?$loan_interest_rate_per[$loan->loan_interest_rate_per]:$loan->loan_interest_rate_per[4]?> on <?php echo $interest_types[$loan->interest_type];?><br/>
                <span class="bold">Loan Duration: </span><?php echo $loan->repayment_period;?> Months <br/>
                <span class="bold">Loan Status: </span> 
                    <?php if($loan->is_fully_paid==1):?>
                        <span class="m-badge m-badge--success m-badge--wide"> Fully Paid</span>
                    <?php else:?>
                        <span class="m-badge m-badge--success m-badge--wide">Unpaid</span>
                    <?php endif;?>
                    <br/>
                    <?php if($loan->enable_loan_processing_fee):?>
                        <span class="bold">Loan Processing fee: </span> 
                            <?php if($loan->loan_processing_fee_type==1){
                                echo 'Fixed Amount of '.$this->group_currency.' '.number_to_currency($loan->loan_processing_fee_fixed_amount).'</br>';
                            }else{
                                echo $loan->loan_processing_fee_percentage_rate.'% of '.$loan_processing_fee_percentage_charged_on[$loan->loan_processing_fee_percentage_charged_on].'<br/>';
                                }?>
                    <?php endif;?>
                    <?php if($loan->enable_loan_fines):?>
                        <span class="bold">Late Payment Fine: </span>
                            <?php if($loan->loan_fine_type==1){
                                echo $this->group_currency.' '.number_to_currency($loan->fixed_fine_amount).' fine '.$late_payments_fine_frequency[$loan->fixed_amount_fine_frequency].' on ';
                                echo isset($fixed_amount_fine_frequency_on[$loan->fixed_amount_fine_frequency_on])?$fixed_amount_fine_frequency_on[$loan->fixed_amount_fine_frequency_on]:'';
                                echo '<br/>';
                            }else if($loan->loan_fine_type==2){
                                echo $loan->percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan->percentage_fine_frequency].' on '.$percentage_fine_on[$loan->percentage_fine_on].'<br/>';
                            }else if($loan->loan_fine_type==3){
                                if($loan->one_off_fine_type==1){
                                    echo 'One Off Amount of '. $this->group_currency.' '.number_to_currency($loan->one_off_fixed_amount).' per Installment<br/>';
                                }else if($loan->one_off_fine_type==2){
                                    echo $loan->one_off_percentage_rate.'% One of Fine on '.$percentage_fine_on[$loan->one_off_percentage_rate_on].' per Installment<br/>';
                                }
                            }
                            ?>
                    <?php endif;?>
                    <?php if($loan->enable_outstanding_loan_balance_fines):?>
                        <span class="bold">Outstanding Balance Fine Date: </span><?php echo timestamp_to_date($loan->outstanding_loan_balance_fine_date);?><br/>
                        <span class="bold">Outstanding Loan Balance Fines: </span>
                        <?php if($loan->outstanding_loan_balance_fine_type==1){
                            echo $this->group_currency.' '.number_to_currency($loan->outstanding_loan_balance_fine_fixed_amount).' '.$late_payments_fine_frequency[$loan->outstanding_loan_balance_fixed_fine_frequency].'<br/>';
                        }else if($loan->outstanding_loan_balance_fine_type==2){
                            echo $loan->outstanding_loan_balance_percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan->outstanding_loan_balance_percentage_fine_frequency].' on '.$percentage_fine_on[$loan->outstanding_loan_balance_percentage_fine_on].'<br/>';
                        }else{
                            echo 'One Off Amount '.$this->group_currency.' '.number_to_currency($loan->outstanding_loan_balance_fine_one_off_amount).'<br/>';
                        }?>
                    <?php endif;?>
                    <span class="bold">Loan Fine Deferment: </span><?php if($loan->enable_loan_fine_deferment){
                            echo '<span class="m-badge m-badge--info m-badge--wide">Active</span>'.'<br/>';
                        }else{
                            echo '<span class="m-badge m-badge--success m-badge--wide">Inactive</span>'.'<br/>';
                        } ?>


                        <?php
                        if($loan->is_a_joint_loan == 1){
                            echo'<span class="bold">Is a joint loan with: </span>';
                            foreach ($loan->joint_loan_members as $joint_loan_member) {
                                echo '<br/>'.$joint_loan_member->first_name.' '.$joint_loan_member->last_name;
                            }
                        }
                      
                        ?>
            </div>
            <div class="col-md-5 text-right">
                <div>
                    <h5>Member Details:</h5>
                    <strong><?php echo translate('Name');?>: </strong> <?php echo $loan->first_name.' '.$loan->last_name?>
                    <br>
                    <strong><?php echo translate('Phone');?>: </strong> <?php echo $loan->phone;?>
                    <br>
                    <strong><?php echo translate('Email Address');?>: </strong> <?php echo $loan->email;?>
                    <br>
                    <strong><?php echo translate('Member Number');?>: </strong> <?php echo $loan->membership_number;?>
                    <br>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <hr/>
        <div class="row">
            <div class="col-md-12">
                <?php if(!empty($posts)){ ?>
                    <span class="bold contribution_name">Loan Installment Breakdown as at <?php echo timestamp_to_date(time());?></span>
                    <div class="table-responsive">
                        <table class="table table-hover table-condensed table-statement">
                            <thead>
                                <tr>
                                    <?php if($this->group->enable_absolute_loan_recalculation){?>
                                        <th width='2%' nowrap class="td_data">
                                            <label class="m-checkbox">
                                                <input type="checkbox" name="check" value="all" class="check_all">
                                                <span></span>
                                            </label>
                                        </th>
                                    <?php } ?>
                                    <th nowrap class="text-right" >#</th>
                                    <th class="invoice-title" >
                                        <?php
                                            $default_message='Description';
                                            $this->languages_m->translate('description',$default_message);
                                        ?>

                                    </th>
                                    <th nowrap class="invoice-title ">
                                        <?php
                                            $default_message='Invoice Date';
                                            $this->languages_m->translate('invoice_date',$default_message);
                                        ?>

                                    </th>
                                    <th nowrap class="invoice-title ">
                                        <?php
                                            $default_message='Due Date';
                                            $this->languages_m->translate('due_date',$default_message);
                                        ?>

                                    </th>
                                    <th class="invoice-title  text-right">Payable(<?php echo $this->group_currency; ?>)</th>
                                    <th class="invoice-title  text-right">Interest(<?php echo $this->group_currency; ?>)</th>
                                    <th class="invoice-title  text-right">Principal(<?php echo $this->group_currency; ?>)</th>
                                    <th class="invoice-title  text-right">Processing Fee(<?php echo $this->group_currency; ?>)</th>
                                    <th class="invoice-title  text-right">
                                            <?php
                                                $default_message='Paid';
                                                $this->languages_m->translate('paid',$default_message);
                                            ?>
                                        (<?php echo $this->group_currency; ?>)</th>
                                    <th class="invoice-title  text-right">Balance(<?php echo $this->group_currency; ?>)</th>
                                    <th class="invoice-title text-right">
                                        <?php
                                            $default_message='Status';
                                            $this->languages_m->translate('status',$default_message);
                                        ?>
                                    </th>
                                    <th class="invoice-title text-right">
                                        <?php
                                            $default_message='Actions';
                                            $this->languages_m->translate('actions',$default_message);
                                        ?>

                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=0;$total_balance=0;$total_installment=0;$total_paid=0;
                                    $total_interest = 0;
                                    $total_principle =0;
                                    $total_processing_fee=0;
                                 foreach($posts as $post):$i++;?>
                                <tr>
                                    <?php if($this->group->enable_absolute_loan_recalculation){
                                        if($post->type == 1){
                                    ?>
                                        <td class="td_data">
                                            <label class="m-checkbox">
                                                <input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" />
                                                <span></span>
                                            </label>
                                        </td>
                                    <?php }else{
                                        echo "<td></td>";
                                    }}?>
                                    <td nowrap class="text-right">
                                        <?php echo $i;?>
                                    </td>
                                    <td>
                                        <?php if($post->type==1){
                                            echo 'Installment ';
                                        }else if($post->type==2){
                                            echo 'Late payment fine';
                                        }else if($post->type==3){
                                            echo 'Outstandiing balance';
                                        }else if($post->type==5){
                                            echo 'Share transfer '.$transfer_options[$post->transfer_to];
                                        }?>
                                    </td>
                                    <td>
                                        <?php echo timestamp_to_date($post->invoice_date);?>
                                    </td>
                                    <td>
                                        <?php echo timestamp_to_date($post->due_date);?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo number_to_currency($installment=$post->amount_payable); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo number_to_currency($interest=$post->interest_amount_payable); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo number_to_currency($principle=$post->principle_amount_payable); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo number_to_currency($processing_fee =$post->processing_fee); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php echo number_to_currency($paid =$post->amount_paid); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php 
                                            $balance = $post->amount_payable - $post->amount_paid;
                                            echo number_to_currency($balance); ?>
                                    </td>
                                    <td class="text-right">
                                        <?php if($post->status==1 || $post->status==''){?>
                                            <span class="label label-default label-sm">Unpaid</span>
                                        <?php }else if($post->status==2){?>
                                            <span class="label label-primary label-sm">
                                                <?php
                                                    $default_message='Paid';
                                                    $this->languages_m->translate('paid',$default_message);
                                                ?>

                                            </span>
                                        <?php }?>
                                    </td>
                                    <td>
                                        <?php if($post->type == 1){ ?>
                                            <?php if($post->disable_fines==1){?>
                                                <a href="<?php echo site_url('group/loan_invoices/enable_invoice_penalties/'.$post->id)?>" class="confirmation_link btn btn-sm btn-default">Enable Penalties</a>
                                            <?php }else{?>
                                                <a href="<?php echo site_url('group/loan_invoices/disable_invoice_penalties/'.$post->id)?>" class="confirmation_link btn btn-sm btn-primary">Disable Penalties</a>
                                            <?php }?>

                                            <?php
                                                if($post->book_interest){
                                                    echo '<br/> <i class="fa fa-check"></i>';
                                                }
                                            ?>
                                        <?php }?>
                                    </td>
                                </tr>
                                <?php $total_balance+=$balance; $total_installment+=$installment; $total_paid+=$paid;
                                    $total_interest+=$interest;
                                    $total_principle+=$principle;
                                    $total_processing_fee+=$processing_fee;
                                  endforeach;?>                        
                            </tbody>
                            <tfoot>
                                <tr>
                                    <?php if($this->group->enable_absolute_loan_recalculation){?>
                                        <td>
                                            
                                        </td>
                                    <?php }?>
                                    <td></td>
                                    <td><?php echo translate('Totals'); ?></td>
                                    <td></td>
                                    <td></td>
                                    <td class='text-right'><?php echo number_to_currency($total_installment); ?></td>
                                    <td class='text-right'><?php echo number_to_currency($total_interest); ?></td>
                                    <td class='text-right'><?php echo number_to_currency($total_principle); ?></td>
                                    <td class='text-right'><?php echo number_to_currency ($total_processing_fee); ?></td>
                                    <td class='text-right'><?php echo number_to_currency($total_paid); ?></td>
                                    <td class='text-right'><?php echo number_to_currency($total_balance); ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <?php if ($loan->enable_loan_guarantors):?>
                        <div class="clearfix"></div>
                        <div class="col-sm-12 table-responsive ">
                            <h5>Loan Guarantor Details:</h5>
                            <table class="table table-hover table-striped table-condensed table-statement">
                                <thead>
                                    <tr>
                                        <th class="invoice-title" width="2%">#</th>
                                        <th class="invoice-title ">
                                                <?php
                                                    $default_message='Member Name';
                                                    $this->languages_m->translate('member_name',$default_message);
                                                ?>
                                        </th>
                                        <th class="invoice-title text-right">Guaranteed Amount(<?php echo $this->group_currency;?>)</th>
                                        <th class="invoice-title">Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i=0; foreach($loan_guarantors as $guarantor){$i++?>
                                    <tr>
                                        <td><?php echo $i;?></td>
                                        <td><?php echo $guarantor->guarantor_first_name.' '.$guarantor->guarantor_last_name;?></td>
                                        <td class="text-right"><?php echo number_to_currency($guarantor->guaranteed_amount);?></td>
                                        <td><?php echo $guarantor->guarantor_comment;?></td>
                                    </tr>
                                <?php }?>                                
                                </tbody>
                            </table>
                        </div>
                    <?php endif;?>

                <?php }else{ ?>
                    <div class="col-sm-12 margin-bottom-10 ">
                        <div class="alert alert-info">
                            <h4 class="block">
                                <?php
                                    $default_message='Information! No records to display';
                                    $this->languages_m->translate('no_records_to_display',$default_message);
                                ?>
                                
                            </h4>
                            <p>
                                No loan invoices to display.
                            </p>
                        </div>
                    </div>
                <?php } ?> 
            </div>
        </div>
        <div class="clearfix"></div>
        <hr>
        <div id="statement_footer" >
            <p style="text-align:center;">© <?php echo date('Y')?> . This statement was issued with no alteration </p>
            <p style="text-align:center;">
                <strong>Powered by:</strong>
                <br>
                <img width="150px" src="<?php echo site_url('uploads/logos/'.$this->application_settings->paper_header_logo);?>" alt="<?php echo $this->application_settings->application_name;?> Logo" ?="">
            </p>
        </div>

    </div>
</div>

<div class="print_layout mt-3">
    <div class="row">
        <div class="col-sm-12">
            <a class="btn btn-sm blue hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> Print</a>
            &nbsp;&nbsp;&nbsp;
            <?php $search_string = substr(basename($_SERVER['REQUEST_URI']),strpos(basename($_SERVER['REQUEST_URI']), "?"));?>
            <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().'/'.TRUE.$search_string;?>" target="_blank"><i class='fa fa-file'></i> Generate PDF</a>
            
            <?php if($this->group->enable_absolute_loan_recalculation){?>
                &nbsp;&nbsp;&nbsp;
                <button class="btn btn-sm btn-default confirmation_bulk_action" name='btnAction' value='bulk_book_interest' data-toggle="confirmation" data-placement="top"> <i class='icon-book'></i> Bulk Book Interest</button>
                &nbsp;&nbsp;&nbsp;
                <button class="btn btn-sm btn-success confirmation_bulk_action" name='btnAction' value='disable_bulk_book_interest' data-toggle="confirmation" data-placement="top"> <i class='icon-book'></i> Bulk Disable Book Interest</button>
            <?php }?>
        </div>

    </div>
</div>

</form>
