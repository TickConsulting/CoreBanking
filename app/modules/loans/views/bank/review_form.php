<style type="text/css">
    .account_settings,.decline_settings{
        display: none;
    }
</style>
<div class="row">
    <div class="col-md-6">
        <?php echo form_open_multipart($this->uri->uri_string(), ' role="form" class="form_submit" '); ?>
            <div class="form-body ">
                <div class="form-group review_report">
                    <label>Review Report<span class="required">*</span></label>
                    <div class="input-group col-xs-12">
                        <?php echo form_dropdown('review_report',array(''=>'-Select review report--')+$application_review_reports,$this->input->post('review_report')?:$post->review_report,'  class="form-control select2" placeholder="Select Review"'); ?>
                        
                    </div>
                </div>

                <div class="account_settings">
                    <div class="form-group account_id">
                        <label>Account to Disburse<span class="required">*</span></label>
                        <div class="input-group col-xs-12">
                            <?php echo form_dropdown('account_id',array(''=>'-Select account--')+$accounts,$this->input->post('account_id')?:$post->account_id,'  class="form-control select2"'); ?>
                            
                        </div>
                    </div>
                </div>

                <div class="decline_settings">
                    <div class="form-group decline_message">
                        <label>Reason for declining</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                            </span>
                            <?php
                                $textarea = array(
                                    'name'=>'decline_message',
                                    'id'=>'',
                                    'value'=> $this->input->post('decline_message')?:$post->decline_message,
                                    'cols'=>40,
                                    'rows'=>6,
                                    'maxlength'=>200,
                                    'class'=>'form-control maxlength',
                                    'placeholder'=>'Compose Message here'
                                ); 
                                echo form_textarea($textarea); 
                            ?>
                        </div>
                        <span class="help-block">
                        <?php
                            $default_message='The group name will be automatically attached to your message';
                            $this->languages_m->translate('group_name_message',$default_message);
                        ?>
                        </span>
                    </div>
                </div>

            </div>

            <div class="clearfix"></div>
            <div class="form-actions">
                <button type="submit"  class="btn blue submit_form_button">Submit</button>
                <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                        <?php
                            $default_message='Processing';
                            $this->languages_m->translate('processing',$default_message);
                        ?>
                </button> 
                <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
            </div>
        <?php echo form_close(); ?>
    </div>
    <div class="col-md-6">
       <div class="portlet light bordered" id="blockui_sample_1_portlet_body">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-book font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp sbold">Application Details</span>
                </div>
            </div>
            <div class="portlet-body">
                <p> 
                    <strong>
                        <?php
                            $default_message='Member Name';
                            $this->languages_m->translate('member_name',$default_message);
                        ?>
                     :</strong> <?php echo $this->active_group_member_options[$post->member_id];?>
                </p>

                <p> 
                    <strong>Amount Applied :</strong> <?php echo $this->group_currency.'. '.number_to_currency($post->loan_amount);?>
                </p>
                <p> 
                    <strong>Loan Type :</strong> <?php echo $loan_type->name;?>
                </p>
                <p> 
                    <strong>Repayment Period :</strong> <?php echo $post->repayment_period;?> months
                </p>

            </div>
        </div>
    </div>
</div>

<div class="portlet light bordered" id="blockui_sample_1_portlet_body">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-book font-green-sharp"></i>
            <span class="caption-subject font-green-sharp sbold"><?php echo $loan_type->name;?> Rules</span>
        </div>
    </div>
    <div class="portlet-body">
        <p> 
            <strong>Amount Range :</strong> Between <?php echo $this->group_currency.'. '.number_to_currency($loan_type->minimum_loan_amount).' and '.$this->group_currency.'. '.number_to_currency($loan_type->maximum_loan_amount);?>
        </p>
        <p>
            <strong>Repayment Period Range :</strong> Between <?php echo $loan_type->minimum_repayment_period.' months and '.$loan_type->maximum_repayment_period.' months';?>
        </p>
        <p>
             <strong>Interest Rate :</strong> <?php echo $loan_type->interest_rate.'% per '.$loan_interest_rate_per[$loan_type->loan_interest_rate_per].' on '.$interest_types[$loan_type->interest_type]; ?>
        </p>
        <p>
            <strong>Late Payment Fine: </strong>
            <?php if($loan_type->enable_loan_fines):?>
                <?php 
                    echo $late_loan_payment_fine_types[$loan_type->loan_fine_type].' of ';
                    if($loan_type->loan_fine_type==1){
                        echo $this->group_currency.' '.number_to_currency($loan_type->fixed_fine_amount).' fine '.$late_payments_fine_frequency[$loan_type->fixed_amount_fine_frequency].' on ';
                        echo isset($fixed_amount_fine_frequency_on[$loan_type->fixed_amount_fine_frequency_on])?$fixed_amount_fine_frequency_on[$loan_type->fixed_amount_fine_frequency_on]:'';
                        echo '<br/>';
                    }else if($loan_type->loan_fine_type==2){
                        echo $loan_type->percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan_type->percentage_fine_frequency].' on '.$percentage_fine_on[$loan_type->percentage_fine_on].'<br/>';
                    }else if($loan_type->loan_fine_type==3){
                        if($loan_type->one_off_fine_type==1){
                            echo 'One Off Amount of '. $this->group_currency.' '.number_to_currency($loan_type->one_off_fixed_amount).' per Installment<br/>';
                        }else if($loan_type->one_off_fine_type==2){
                            echo $loan_type->one_off_percentage_rate.'% One of Fine on '.$percentage_fine_on[$loan_type->one_off_percentage_rate_on].' per Installment<br/>';
                        }
                    }
                ?>
            <?php else:?>
                <span class="label label-warning label-xs">Fines Disabled</span><br/>
            <?php endif;?>
        </p>
        <p>
            <strong>Outstanding Loan Payment Fine: </strong>
            <?php if($loan_type->enable_outstanding_loan_balance_fines):
                if($loan_type->outstanding_loan_balance_fine_type==1){
                    echo $this->group_currency.' '.number_to_currency($loan_type->outstanding_loan_balance_fine_fixed_amount).' '.$late_payments_fine_frequency[$loan_type->outstanding_loan_balance_fixed_fine_frequency].'<br/>';
                }else if($loan_type->outstanding_loan_balance_fine_type==2){
                    echo $loan_type->outstanding_loan_balance_percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$loan_type->outstanding_loan_balance_percentage_fine_frequency].' on '.$percentage_fine_on[$loan_type->outstanding_loan_balance_percentage_fine_on].'<br/>';
                }else{
                    echo 'One Off Amount '.$this->group_currency.' '.number_to_currency($loan_type->outstanding_loan_balance_fine_one_off_amount).'<br/>';
                }
            ?>
            <?php else:?>
                <span class="label label-warning label-xs">Outstanding Balance Fines Disabled</span><br/>
            <?php endif;?>
        </p>
        <p>
            <strong>Processing Fee Charges: </strong>
            <?php if($loan_type->enable_loan_processing_fee):?>
                <?php if($loan_type->loan_processing_fee_type==1){
                    echo 'Fixed Amount of '.$this->group_currency.' '.number_to_currency($loan_type->loan_processing_fee_fixed_amount).'</br>';
                }else{
                    echo $loan_type->loan_processing_fee_percentage_rate.'% of '.$loan_processing_fee_percentage_charged_on[$loan_type->loan_processing_fee_percentage_charged_on].'<br/>';
                    }?>
            <?php else:?>
                <span class="label label-warning label-xs">Inactive</span>
                <br/>
            <?php endif;?>
            <br/>
            <strong>Guarantors: </strong>
            <?php if($loan_type->enable_loan_guarantors):?>
                Between <?php echo 'Minimum of '.$loan_type->minimum_guarantors.' and a Maximum of '.$loan_type->maximum_guarantors.' guarantors required'; ?>
            <?php else:?>
                <span class="label label-warning label-xs">Not Required</span>
            <?php endif;?>
        </p>
    </div>
</div> 

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('change','select[name=review_report]',function(){
            var review_report =$(this).val();
            if(review_report==1){
                $('.decline_settings').slideUp();
                $('.account_settings').slideDown();
            }else if(review_report==2){
                $('.account_settings').slideUp();
                $('.decline_settings').slideDown();
            }else{
                $('.decline_settings').slideUp();
                $('.account_settings').slideUp();
            }
        });

        var review_report = $('select[name=review_report]').val();
        if(review_report==1){
            $('.decline_settings').slideUp();
            $('.account_settings').slideDown();
        }else if(review_report==2){
            $('.account_settings').slideUp();
            $('.decline_settings').slideDown();
        }else{
            $('.decline_settings').slideUp();
            $('.account_settings').slideUp();
        }
    });
</script>

