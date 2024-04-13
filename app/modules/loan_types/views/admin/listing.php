<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                   <?php echo $this->admin_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form" style="min-height: 450px;">
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
                            <table class="table table-bordered table-condensed table-striped table-hover table-searchable">
                                <thead>
                                    <tr>
                                        <th width="8px">
                                            #
                                        </th>
                                       
                                        <th>
                                          Name
                                        </th>
                                       
                                        <th>
                                          Details
                                        </th>
                                        <th>
                                          Active
                                        </th>
                                        <th>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i=$this->uri->segment(5, 0);
                                    foreach($posts as $post):
                                    ?>
                                        <tr>
                                            <td><?php echo $i+1;?></td>
                                            <td><?php echo $post->name;?></td>
                                            <td>
                                                <strong>
                                                    <?php echo translate('Loan Amount');?>
                                                </strong>
                                                :<?php 
                                                    echo $this->loan->loan_amount_type[($post->loan_amount_type?:1)];
                                                ?>
                                                <?php  if($post->loan_amount_type == 1  ){
                                                    echo '('.number_to_currency($post->minimum_loan_amount).' - '.number_to_currency($post->maximum_loan_amount).')';
                                                }else if($post->loan_amount_type == 2){
                                                    echo  '('.$post->loan_times_number.'  times your savings)'; 
                                                }else if($post->loan_amount_type == ''){
                                                    echo '('.number_to_currency($post->minimum_loan_amount).' - '.number_to_currency($post->maximum_loan_amount).')';
                                                } ?> 

                                                <br/>
                                                <strong>
                                                    <?php echo translate('Grace Period');?>
                                                </strong>
                                                <?php if($post->grace_period == 12){
                                                        echo '1 '.translate('year');
                                                    }else if($post->grace_period == 'date'){
                                                        echo translate('Custom Date');
                                                    }elseif($post->grace_period>=1 || $post->grace_period <=12){
                                                         echo $post->grace_period.' '.translate('months');
                                                    }
                                               ;?>
                                               <br/>
                                               <strong>
                                                    <?php echo translate('Repayment');?>
                                                </strong>
                                                <?php echo translate($loan_repayment_period_type[$post->loan_repayment_period_type]).' '.translate('of').' '; ?>
                                                <?php if($post->loan_repayment_period_type == 1){
                                                     echo $post->fixed_repayment_period.' Months';
                                                    }else if ($post->loan_repayment_period_type == 2) {
                                                         echo $post->minimum_repayment_period.' - '.$post->maximum_repayment_period.' Months';
                                                    }
                                                ?>
                                                <br/>
                                                <strong><?php echo translate('Interest');?> </strong>
                                                <?php
                                                    if($post->interest_type ==3){
                                                        echo $interest_types[$post->interest_type]; 
                                                    }else{
                                                        echo $post->interest_rate.'% '.$loan_interest_rate_per[$post->loan_interest_rate_per].' on '.$interest_types[$post->interest_type]; 
                                                    }
                                                ?>
                                                <br/>
                                                <strong><?php echo translate('Late Payment Fine');?> </strong>
                                                <?php if($post->enable_loan_fines):?>
                                                    <?php 
                                                        echo $late_loan_payment_fine_types[$post->loan_fine_type].' of ';
                                                        if($post->loan_fine_type==1){
                                                            echo number_to_currency($post->fixed_fine_amount).' fine '.$late_payments_fine_frequency[$post->fixed_amount_fine_frequency].' on ';
                                                            echo isset($fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on])?$fixed_amount_fine_frequency_on[$post->fixed_amount_fine_frequency_on]:'';
                                                            echo '<br/>';
                                                        }else if($post->loan_fine_type==2){
                                                            echo $post->percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$post->percentage_fine_frequency].' on '.$percentage_fine_on[$post->percentage_fine_on].'<br/>';
                                                        }else if($post->loan_fine_type==3){
                                                            if($post->one_off_fine_type==1){
                                                                echo number_to_currency($post->one_off_fixed_amount).' per Installment<br/>';
                                                            }else if($post->one_off_fine_type==2){
                                                                echo $post->one_off_percentage_rate.'% on '.$percentage_fine_on[$post->one_off_percentage_rate_on];
                                                            }
                                                        }
                                                    ?>
                                                <?php else:?>
                                                    <span class="badge badge-success badge--wide"><?php echo translate('Disabled');?></span><br/>
                                                <?php endif;?>
                                                <br/>
                                                <strong><?php echo translate('Outstanding loan balance fine');?> </strong>
                                                <?php if($post->enable_outstanding_loan_balance_fines):
                                                    if($post->outstanding_loan_balance_fine_type==1){
                                                        echo number_to_currency($post->outstanding_loan_balance_fine_fixed_amount).' '.$late_payments_fine_frequency[$post->outstanding_loan_balance_fixed_fine_frequency].'<br/>';
                                                    }else if($post->outstanding_loan_balance_fine_type==2){
                                                        echo $post->outstanding_loan_balance_percentage_fine_rate.'% fine '.$late_payments_fine_frequency[$post->outstanding_loan_balance_percentage_fine_frequency].' on '.$percentage_fine_on[$post->outstanding_loan_balance_percentage_fine_on].'<br/>';
                                                    }else{
                                                        echo 'One Off Amount '.number_to_currency($post->outstanding_loan_balance_fine_one_off_amount).'<br/>';
                                                    }
                                                ?>
                                                <?php else:?>
                                                    <span class="badge badge-success badge--wide"><?php echo translate('Disabled');?></span><br/>
                                                <?php endif;?>
                                                <br/>
                                                <strong><?php echo translate('Processing Fee Charges');?> </strong>
                                                <?php if($post->enable_loan_processing_fee):?>
                                                    <?php if($post->loan_processing_fee_type==1){
                                                        echo 'Fixed Amount of '.number_to_currency($post->loan_processing_fee_fixed_amount).'</br>';
                                                    }else{
                                                        echo $post->loan_processing_fee_percentage_rate.'% of '.$loan_processing_fee_percentage_charged_on[$post->loan_processing_fee_percentage_charged_on].'<br/>';
                                                        }?>
                                                <?php else:?>
                                                    <span class="badge badge-success badge--wide"><?php echo translate('Disabled');?></span><br/>
                                                <?php endif;?>
                                                <br/>
                                                <strong><?php echo translate('Guarantors'); ?> </strong>
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
                                                    echo '<span class="badge badge-success badge--wide">'.translate('Disabled').'</span>';
                                                } ?>
                                            </td>
                                            <td>
                                                <?php if($post->is_hidden==1){
                                                    echo '<span class="label label-sm label-danger"> Hidden</span>';
                                                }else{
                                                    echo '<span class="label label-sm label-primary"> Active</span>';
                                                }?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?php echo site_url('admin/loan_types/edit/'.$post->id);?>" class="btn btn-xs default">
                                                    <i class="fa fa-edit"></i> Edit &nbsp;&nbsp;
                                                </a>
                                                <a href="<?php echo site_url('admin/loan_types/delete/'.$post->id);?>" class="btn confirmation_link btn-xs btn-danger" data-title="Enter the delete code to delete the language and its data permanently." >
                                                    <i class="fa fa-trash"></i> Delete Loan Type &nbsp;&nbsp;
                                                </a>
                                                <br/><br/>
                                          
                                            </td>
                                        </tr>
                                    <?php $i++; endforeach; 
                                    ?>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                            

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
            </div>
        </div>
    </div>
</div>


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