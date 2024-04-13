<div class="col-md-6">
    <?php echo form_open_multipart($this->uri->uri_string(), ' role="form" class="form_submit" '); ?>
        <div class="form-body ">
            <div class="form-group loan_amount">
                <label>Loan Amount Applying<span class="required">*</span></label>
                <div class="input-group col-xs-12">
                    <span class="input-group-addon">
                        <i class="fa fa-money"></i>
                    </span>
                    <?php echo form_input('loan_amount',$this->input->post('loan_amount')?:$post->loan_amount,'  class="form-control amount_applying currency" placeholder="Loan Amount Applying"'); ?>
                    
                </div>
                <span class="help-block"> Eg.10,000 </span>
            </div>

            <div class="form-group repayment_period">
                <label>Loan Amount Repayment Period<span class="required">*</span></label>
                <div class="input-group col-xs-12">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <?php echo form_input('repayment_period',$this->input->post('repayment_period')?:$post->repayment_period,'  class="form-control months-repayment" placeholder="Repayment Period"'); ?>
                    
                </div>
                <span class="help-block"> Eg.15 </span>
            </div>

            <div class="form-group">
                <label>Agree to the group rules and regulation</label>
                <div class="input-group checkbox-list col-xs-12 ">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('agree_to_rules',1,$this->input->post('agree_to_rules')?:$post->agree_to_rules?:'',' id="agree_to_rules" class="agree_to_rules" '); ?> Agree to group loan rules and regulations indicated to the right
                    </label>
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
            <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">
                    <?php
                        $default_message='Cancel';
                        $this->languages_m->translate('cancel',$default_message);
                    ?>

            </button></a>
        </div>
    <?php echo form_close(); ?>
</div>
<div class="col-md-6">
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
</div>


<?php $min_month = $loan_type->minimum_repayment_period;
      $max_month = $loan_type->maximum_repayment_period;

      $diff = $max_month-$min_month;

      $values='';

      for ($i=1; $i <=$diff ; $i++) { 
        if($i==1){
          $values= '"'.$i.' Month"';
        }else{
            $values = $values.',"'.$i.' Months"';
        }
      }
 ?>


<script type="text/javascript">
    var ComponentsIonSliders = function() {

    var handleBasicDemo = function() {
        $(".amount_applying").ionRangeSlider({
            min: '<?php echo $loan_type->minimum_loan_amount;?>',
            max: '<?php echo $loan_type->maximum_loan_amount;?>',
            from: '<?php echo ($loan_type->maximum_loan_amount+$loan_type->minimum_loan_amount)/2;?>',
            grid: true
        });

        $(".months-repayment").ionRangeSlider({
            from: '<?php echo round($diff/2);?>',
            grid: true,
            values: [<?php echo $values;?>]
        });
    }

    return {
        //main function to initiate the module
        init: function() {
            //handleBasicDemo();
            //handleAdvancedDemo();
        }

    };

}();

jQuery(document).ready(function() {
    ComponentsIonSliders.init();
});
</script>


