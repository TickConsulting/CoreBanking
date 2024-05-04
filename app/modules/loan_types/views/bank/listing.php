<?php if(empty($posts)){ ?>
    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
        <strong><?php echo translate('Sorry'); ?>! </strong><?php echo  translate('No Loan Types to display') ?>
    </div>
<?php }else{ ?>
    <?php echo form_open('group/loans/action', ' id="form"  class="form-horizontal"'); ?> 
        <?php if ( !empty($pagination['links'])): ?>
        <div class="row col-md-12">
            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> <?php echo translate('Loans'); ?></p>
            <?php 
                echo '<div class ="top-bar-pagination">';
                echo $pagination['links']; 
                echo '</div></div>';
                endif; 
            ?>  

            <div class="m-accordion m-accordion--default" id="m_accordion" role="tablist">
                <?php $i = 0; foreach($posts as $post): ?>
                    <!--begin::Item--> 
                    <div class="m-accordion__item">
                        <div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_item_<?php echo $i; ?>_head" data-toggle="collapse" href="#m_accordion_item_<?php echo $i; ?>_body" aria-expanded="false">
                            <span class="m-accordion__item-icon"><i class="mdi mdi-format-list-bulleted"></i></span>
                            <span class="m-accordion__item-title"><?php echo ($i+1).'. '.$post->name; ?> Loan
                                    <?php if($post->is_hidden){
                                       echo '<span class="float-right m-badge m-badge--danger m-badge--wide">Disabled</span><br/>';
                                    }else{
                                        echo '<span class="float-right m-badge m-badge--success m-badge--wide">'.translate('Active').'</span><br/>';
                                    }?>
                            </span>
                            <span class="m-accordion__item-mode"></span>     
                        </div>

                        <div class="m-accordion__item-body collapse" id="m_accordion_item_<?php echo $i; ?>_body" class=" " role="tabpanel" aria-labelledby="m_accordion_item_<?php echo $i; ?>_head" data-parent="#m_accordion"> 
                            <div class="m-accordion__item-content">
                                <div class="row invoice-body">
                                    <div class="col-xs-12 table-responsive ">
                                        <table class="table table-sm m-table m-table--head-separator-primary table table--hover table-borderless table-condensed loan-types-table">
                                            <thead>
                                                <tr>
                                                    <th width="30%">
                                                        <?php echo $post->name.' '.translate('loan details') ?>
                                                    </th>
                                                    <th class="m--align-right">
                                                        <?php if($this->ion_auth->is_admin()){?>
                                                            <div class="btn-group">
                                                                <a href="<?php echo site_url('bank/loan_types/edit/'.$post->id); ?>" class="btn btn-sm btn-primary m-btn  m-btn m-btn--icon generate_pdf_link">
                                                                    <span>
                                                                        <i class="fa fa-edit"></i>
                                                                        <span>
                                                                            <?php echo translate('Edit');?>
                                                                        </span>
                                                                    </span>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split more_actions_toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                                                    <span class="sr-only">More actions..</span>
                                                                </button>
                                                                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(120px, 31px, 0px);">
                                                                    <a class="dropdown-item view_transaction_alert_link" href="#" style="display: none;">
                                                                        <?php echo translate('View transaction alert');?>
                                                                    </a>
                                                                    <?php if($post->is_hidden){?>
                                                                        <a class="dropdown-item" href="<?php echo site_url('group/loan_types/unhide/'.$post->id); ?>">
                                                                            <?php echo translate('Enable');?>
                                                                        </a>
                                                                    <?php }else{?>
                                                                        <a class="dropdown-item" href="<?php echo site_url('group/loan_types/hide/'.$post->id); ?>">
                                                                            <?php echo translate('Disable');?>
                                                                        </a>
                                                                    <?php }?>
                                                                        <a class="dropdown-item confirmation_link" href="<?php echo site_url('group/loan_types/delete/'.$post->id); ?>">
                                                                            <?php echo translate('Delete');?>
                                                                        </a>
                                                                </div>
                                                            </div>
                                                        <?php }?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="<?php echo $post->id ?>_active_row">
                                                    <td class="m--align-right" nowrap>
                                                        <strong>
                                                            <?php echo translate('Loan Amount');?>
                                                        </strong>
                                                    </td>
                                                    <td>:
                                                        <?php 
                                                            echo $this->loan->loan_amount_type[($post->loan_amount_type?:1)];
                                                        ?>
                                                        <?php  if($post->loan_amount_type == 1  ){
                                                            echo '('.number_to_currency($post->minimum_loan_amount).' - '.number_to_currency($post->maximum_loan_amount).')';
                                                        }else if($post->loan_amount_type == 2){
                                                            echo  '('.$post->loan_times_number.'  times your savings)'; 
                                                        }else if($post->loan_amount_type == ''){
                                                            echo '('.number_to_currency($post->minimum_loan_amount).' - '.number_to_currency($post->maximum_loan_amount).')';
                                                        } ?> 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>
                                                            <?php echo translate('Grace Period');?>
                                                        </strong>
                                                    </td>
                                                    <td>:
                                                        <?php if($post->grace_period == 12){
                                                                echo '1 '.translate('year');
                                                            }else if($post->grace_period == 'date'){
                                                                echo translate('Custom Date');
                                                            }elseif($post->grace_period>=1 || $post->grace_period <=12){
                                                                 echo $post->grace_period.' '.translate('months');
                                                            }
                                                       ;?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong>
                                                            <?php echo translate('Repayment');?>
                                                        </strong>
                                                    </td>
                                                    <td>:
                                                        <?php echo translate($loan_repayment_period_type[$post->loan_repayment_period_type]).' '.translate('of').' '; ?>
                                                        <?php if($post->loan_repayment_period_type == 1){
                                                             echo $post->fixed_repayment_period.' Months';
                                                            }else if ($post->loan_repayment_period_type == 2) {
                                                                 echo $post->minimum_repayment_period.' - '.$post->maximum_repayment_period.' Months';
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong><?php echo translate('Interest');?> </strong>
                                                    </td>
                                                    <td>:
                                                        <?php
                                                            if($post->interest_type ==3){
                                                                echo $interest_types[$post->interest_type]; 
                                                            }else{
                                                                echo $post->interest_rate.'% '.$loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$interest_types[$post->interest_type]; 
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong><?php echo translate('Late Payment Fine');?> </strong>
                                                    </td>
                                                    <td>:
                                                        <?php if($post->enable_loan_fines):?>
                                                            <?php 
                                                                echo $late_loan_payment_fine_types[$post->loan_fine_type].' of ';
                                                                if($post->loan_fine_type==1){
                                                                    echo $this->group_currency.' '.number_to_currency($post->fixed_fine_amount).' fine '.$late_payments_fine_frequency[$post->fixed_amount_fine_frequency].' on ';
                                                                    echo isset($fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on])?$fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on]:'';
                                                                    echo '<br/>';
                                                                }else if($post->loan_fine_type==2){
                                                                    echo $post->percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$post->percentage_fine_frequency].' on '.$percentage_fine_on[$post->percentage_fine_on].'<br/>';
                                                                }else if($post->loan_fine_type==3){
                                                                    if($post->one_off_fine_type==1){
                                                                        echo $this->group_currency.' '.number_to_currency($post->one_off_fixed_amount).' per Installment<br/>';
                                                                    }else if($post->one_off_fine_type==2){
                                                                        echo $post->one_off_percentage_rate.'% on '.$percentage_fine_on[$post->one_off_percentage_rate_on];
                                                                    }
                                                                }
                                                            ?>
                                                        <?php else:?>
                                                            <span class="m-badge m-badge--success m-badge--wide"><?php echo translate('Disabled');?></span><br/>
                                                        <?php endif;?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong><?php echo translate('Outstanding loan balance fine');?> </strong>
                                                    </td>
                                                    <td>:
                                                        <?php if($post->enable_outstanding_loan_balance_fines):
                                                            if($post->outstanding_loan_balance_fine_type==1){
                                                                echo $this->group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_fixed_amount).' '.$late_payments_fine_frequency[$post->outstanding_loan_balance_fixed_fine_frequency].'<br/>';
                                                            }else if($post->outstanding_loan_balance_fine_type==2){
                                                                echo $post->outstanding_loan_balance_percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$post->outstanding_loan_balance_percentage_fine_frequency].' on '.$percentage_fine_on[$post->outstanding_loan_balance_percentage_fine_on].'<br/>';
                                                            }else{
                                                                echo 'One Off Amount '.$this->group_currency.' '.number_to_currency($post->outstanding_loan_balance_fine_one_off_amount).'<br/>';
                                                            }
                                                        ?>
                                                        <?php else:?>
                                                            <span class="m-badge m-badge--success m-badge--wide"><?php echo translate('Disabled');?></span><br/>
                                                        <?php endif;?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong><?php echo translate('Processing Fee Charges');?> </strong>
                                                    </td>
                                                    <td>:
                                                        <?php if($post->enable_loan_processing_fee):?>
                                                            <?php if($post->loan_processing_fee_type==1){
                                                                echo 'Fixed Amount of '.$this->group_currency.' '.number_to_currency($post->loan_processing_fee_fixed_amount).'</br>';
                                                            }else{
                                                                echo $post->loan_processing_fee_percentage_rate.'% of '.$loan_processing_fee_percentage_charged_on[$post->loan_processing_fee_percentage_charged_on].'<br/>';
                                                                }?>
                                                        <?php else:?>
                                                            <span class="m-badge m-badge--success m-badge--wide"><?php echo translate('Disabled');?></span><br/>
                                                        <?php endif;?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong><?php echo translate('Automatic Disbursements');?> </strong>
                                                    </td>
                                                    <td>:
                                                        <?php if($post->enable_automatic_disbursements):?>
                                                            <span class="m-badge m-badge--primary m-badge--wide"><?php echo translate('Enabled');?></span><br/>
                                                        <?php else:?>
                                                            <span class="m-badge m-badge--success m-badge--wide"><?php echo translate('Disabled');?></span><br/>
                                                        <?php endif;?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong><?php echo translate('Allow borrowing  after repaying the existing loan');?> </strong>
                                                    </td>
                                                    <td>:
                                                        <?php if($post->limit_to_one_loan_application):?>
                                                            <span class="m-badge m-badge--success m-badge--wide"><?php echo translate('Enabled');?></span><br/>
                                                        <?php else:?>
                                                            <span class="m-badge m-badge--success m-badge--wide"><?php echo translate('Disabled');?></span><br/>
                                                        <?php endif;?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="m--align-right" nowrap>
                                                        <strong><?php echo translate('Guarantors'); ?> </strong>
                                                    </td>
                                                    <td>:
                                                        <?php if($post->enable_loan_guarantors == 1){
                                                            if($post->loan_guarantors_type == 1){
                                                                echo 'A minimum of '.$post->minimum_guarantors.' guarantors required';
                                                            }else if($post->loan_guarantors_type == 2){
                                                                echo 'A Minimum of '.$post->minimum_guarantors.' guarantors required';
                                                            }
                                                            if($post->loan_guarantors_type == 1){
                                                                echo ' '.translate('every time a member is applying a loan');   
                                                            }else if($post->loan_guarantors_type == 2){
                                                                echo ' '.translate('When an applicant loan request exceeds loan limit ');   
                                                            }
                                                        }else{
                                                            echo '<span class="m-badge m-badge--success m-badge--wide">'.translate('Disabled').'</span>';
                                                        } ?>

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                <?php $i++;
                endforeach; ?>                
            </div>

            <div class="row col-md-12">
                <?php 
                    if( ! empty($pagination['links'])): 
                    echo $pagination['links']; 
                    endif; 
                ?>  
            </div>
        </div>
    <?php echo form_close(); ?> 
<?php } ?>


<script type="text/javascript">

    $(document).ready(function(){
        $(document).on('click','.confirmation_link',function(){
            var element = $(this);
            bootbox.confirm({
                message: "Are you sure you want to void this deposit?",
                // title: "Before you proceed",
                callback: function(result) {
                    if(result==true){
                        if (result === null) {
                            return true;
                        }else{
                            var href = element.attr('href');
                            window.location = href;
                        }
                    }else{
                        return true;
                    }
                }
            });
            return false;
        });

        $(document).on('click','.prompt_confirmation_message_link',function(){
            var id = $(this).attr('id');
            swal({
                title: "Are you sure?", text: "You won't be able to revert this!", type: "warning", showCancelButton: !0, confirmButtonText: "Yes, delete it!", cancelButtonText: "No, cancel!", reverseButtons: !0
            }).then(function(e) {
                if(e.value == true){
                    bootbox.prompt({
                        title: "Input Your password to delete!",
                        inputType: 'password',
                        callback: function (result) {
                            mApp.block('.'+id+'_active_row', {
                                overlayColor: 'grey',
                                animate: true,
                                type: 'loader',
                                state: 'primary',
                            });
                            $.ajax({
                                type:'POST',
                                url:'<?php echo site_url('ajax/loan_types/delete') ?>',
                                data:{'id':id, 'password':result},
                                success: function(response){
                                    if(isJson(response)){
                                        var data = $.parseJSON(response)
                                        if(data.status == '1'){
                                            mApp.unblock('.'+id+'_active_row');
                                            swal("success",data.message, "success")
                                            window.location.href = data.refer;
                                        }else{
                                            mApp.unblock('.'+id+'_active_row');
                                            swal("Cancelled",data.message, "error")
                                        }
                                    }else{
                                        mApp.unblock('.'+id+'_active_row');
                                        swal("Cancelled", "Could not delete your your loan type :)", "error")   
                                    }
                                },
                                error: function(){
                                    mApp.unblock('.'+id+'_active_row');
                                    swal("Cancelled", "Could not delete your loan type :)", "error")
                                },
                            });
                        }
                    });
                }else{
                    swal("Cancelled", "Your loan type is safe :)", "error")
                }
            })
        });

    }); 
</script>