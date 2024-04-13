<?php echo form_hidden('withdrawal_request_id',$withdrawal_request->id,' id="withdrawal_request_id" '); ?>
<?php echo form_hidden('withdrawal_approval_request_id',$post->id,"id='withdrawal_approval_request_id'"); ?>
<div class="withdrawal_request">
    <span class="error"></span>
    <div class="alert m-alert--default" role="alert">
        <?php echo translate('Hi'); ?> <strong> <?php echo $this->member->first_name.' '.$this->member->last_name; ?></strong>. <?php echo translate('Please respond to this withdrawal request made by'); ?>
        <strong>
                <?php
                    $requested_by = $this->ion_auth->get_user($withdrawal_request->user_id);
                    echo $requested_by->first_name.' '.$requested_by->last_name;
                ?>
        </strong> 

        <!-- <?php echo translate('before it expires on'); ?> <?php echo timestamp_to_date($withdrawal_request->request_expiry_date); ?>. -->
    </div>
    <table class="table table-hover table-borderless">
        <tbody>
            <tr>
                <th nowrap>
                    <?php echo translate('Type'); ?>
                </th>
                <td class="transaction_type"><strong>:</strong> <?php echo $withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]; ?></td>
            </tr>
            <tr>
                <th nowrap>
                    <?php echo translate('Request Date'); ?>
                </th>
                <td class="request_date"><strong>:</strong> <?php echo timestamp_to_date_and_time($withdrawal_request->request_date); ?></td>
            </tr>
            <tr>
                <th nowrap>
                    <?php echo translate('Request By'); ?>
                </th>
                <td class="requested_by"><strong>:</strong> <?php
                    echo $requested_by->first_name.' '.$requested_by->last_name;?>
                </td>
            </tr>
            <tr>
                <th nowrap>
                    <?php echo translate('Recipient'); ?>
                </th>
                <td class="recipient"><strong>:</strong>
                    <?php
                        foreach ($recipient_options as $key => $value) {
                            if(isset($value[$withdrawal_request->recipient_id])){
                                echo $value[$withdrawal_request->recipient_id];
                                if(preg_match('/member/', $withdrawal_request->recipient_id)){
                                    echo ' <span class="m-badge m-badge--success m-badge--wide">Member</span>';
                                }
                            }
                        }
                    ?>
                </span>
            </td>
            </tr>
            <tr>
                <th nowrap="">
                    <?php echo translate('Description'); ?>
                </th>
                <td class="description"><strong>:</strong>
                    <?php
                        if($withdrawal_request->withdrawal_for == 1){ //Loan Disbursement
                            echo $withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]." of ".$this->group_currency." ".number_to_currency($withdrawal_request->amount)." to ".$this->active_group_member_options[$withdrawal_request->member_id].(isset($loan_type_options[$withdrawal_request->loan_type_id])?' for a '.$loan_type_options[$withdrawal_request->loan_type_id].' loan':' for a loan');
                            if($withdrawal_request->description){
                                echo ': '.$withdrawal_request->description;
                            }
                        }else if($withdrawal_request->withdrawal_for == 2){ //Expense Payment
                            echo $withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]." for ".$expense_category_options[$withdrawal_request->expense_category_id]." of <strong> ".$this->group_currency." ".number_to_currency($withdrawal_request->amount)."</strong>";
                            if($withdrawal_request->description){
                                echo ': '.$withdrawal_request->description;
                            }
                        }else if($withdrawal_request->withdrawal_for == 3){ //Dividend Payout
                            echo $withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]."  of <strong> ".$this->group_currency." ".number_to_currency($withdrawal_request->amount)."</strong>";
                            if($withdrawal_request->description){
                                echo ': '.$withdrawal_request->description;
                            }
                        }else if($withdrawal_request->withdrawal_for == 4){ //Welfare
                            echo $withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]." payment of <strong> ".$this->group_currency." ".number_to_currency($withdrawal_request->amount)."</strong>";
                            if($withdrawal_request->description){
                                echo ': '.$withdrawal_request->description;
                            }
                        }else if($withdrawal_request->withdrawal_for == 5){ //Shares Refund
                            $contribution = $this->contributions_m->get($withdrawal_request->contribution_id);
                            echo $withdrawal_request_transaction_names[$withdrawal_request->withdrawal_for]." to ".$this->group_member_options[$withdrawal_request->member_id]." of <strong> ".$this->group_currency." ".number_to_currency($withdrawal_request->amount)."</strong> from ".$contribution_options[$withdrawal_request->$contribution_id];
                            if($withdrawal_request->description){
                                echo ': '.$withdrawal_request->description;
                            }
                        }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="row">
        <div class="col-12 m--align-right">
            <button type="button" class="btn btn-sm m-btn--pil btn-danger m-btn m-btn--custom m-3 decline">
                <?php echo translate('Decline'); ?>
            </button>
            <button type="button" class="btn btn-sm m-btn--pil btn-info m-btn m-btn--custom approve">
                <?php echo translate('Approve'); ?>
            </button>
        </div>
    </div>
    <div class="row pt-2">
        <div class="col-12 m--align-right">
            <a href="javascript:;" class="have_approval_code">
                <?php echo translate('Already have an approval code'); ?>?
            </a>
        </div>
    </div>
</div>

<div class="approve_withdrawal_request" style="display: none;">
    <span class="error"></span>
    <div class="alert m-alert--default" role="alert">
        <strong><?php echo $this->member->first_name; ?></strong>, <?php echo translate('please enter the secure one time approval code sent to your registered phone number to complete the approval'); ?>.
    </div>
    <form class="m-form m-form--dfit m-form--label-align-right m-form--group-seperator m-form--state">
        <div class="form-group m-form__group row">
            <div class="col-lg-6">
                <div class="input-group">
                    <input type="text" name="approval_code" class="form-control m-input m-input-air m-input--solgid text-center" id="approval_code" placeholder="Enter Approval Code">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" id="verify_approval_code">
                            <?php echo translate('Verify Code'); ?>?
                        </button>
                    </div>
                </div>
                <a href="javascript:;" id="resend_approval_request">
                    <?php echo translate('Resend approval code'); ?>?
                </a>
            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-6">
                <button class="btn btn-primary back_to_respond">
                    <i class="la la-hand-o-left"></i>
                    <?php echo translate('Back'); ?>?
                </button>
            </div>
        </div>               
    </form>
</div>

<div class="decline_withdrawal_request" style="display: none;">
    <span class="error"></span>
    <div class="alert m-alert--default mb-0" role="alert">
        <strong><?php echo $this->member->first_name; ?></strong>, 
        <?php echo translate('kindly give a reason why you are declining this withdrawal request'); ?>.
    </div>
    <form class="m-form m-form--dfit m-form--label-align-right m-form--group-seperator m-form--state">
        <div class="form-group m-form__group">
            <label>
                <?php echo translate('Reason'); ?>:
            </label>
            <textarea class="form-control m-input m-input--air m-input--pill" id="decline_reason" rows="3"></textarea>
        </div>
        <div class="form-group m-form__group">
            <button class="btn btn-primary back_to_respond">
                <i class="la la-hand-o-left"></i>
                <?php echo translate('Back'); ?>
            </button>
            <button class="btn btn-success float-right" id="decline_request">
                <?php echo translate('Submit'); ?>
            </button>
        </div>               
    </form>
</div>    

<script>
    $(document).ready(function(){
        $('.approve').on('click',function(){
            mApp.block('.withdrawal_request', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Sending approval code...'
            });
            var withdrawal_approval_request_id = $('input[name="withdrawal_approval_request_id"]').val(); 


            $.ajax({
                type: "POST",
                data: {'id':withdrawal_approval_request_id},
                url: '<?php echo base_url("ajax/withdrawals/send_approval_code"); ?>',
                success: function (data, i, n, r) {
                    
                    if(isJson(data)){
                        var response = $.parseJSON(data);
                        if(response.status == 1){
                            //redirect to withdrawal requests
                            toastr['success']('Approval code sent to your registered phone number.','Success');
                            $(".withdrawal_request").slideUp();
                            $(".approve_withdrawal_request").slideDown();
                            // window.location.href = response.refer;
                        }else{
                            $('.withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                        }
                    }else{
                        $('.withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing your request. Please try again.</div>');
                    }
                    mApp.unblock('.withdrawal_request');
                },
                error: function(){
                    $('.withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing your request. Please try again.</div>');
                    mApp.unblock('.withdrawal_request');
                },
                always: function(){
                    $('.withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing your request. Please try again.</div>');
                    mApp.unblock('.withdrawal_request');
                }
            });
        });
        
        $('#decline_request').on('click',function(e){
            e.preventDefault();
            if($('#decline_reason').val() == ''){
                $('#decline_reason').parent().addClass('has-danger').append('<div class="form-control-feedback">Please give a reason</div>');
            }else{
                bootbox.prompt({
                    title: "Enter your password to decline this request",
                    inputType: 'password',
                    required: true,
                    callback: function(password){
                        if(password){
                            mApp.block('.decline_withdrawal_request', {
                                overlayColor: 'grey',
                                animate: true,
                                type: 'loader',
                                state: 'primary',
                                message: 'Declining...'
                            });
                            var decline_reason = $('#decline_reason').val(); 
                            var withdrawal_approval_request_id = $('input[name="withdrawal_approval_request_id"]').val(); 
                            var withdrawal_request_id = $('input[name="withdrawal_request_id"]').val(); 
                            $.post('<?php echo base_url("ajax/withdrawals/decline_withdrawal_request"); ?>',{'password':password,'withdrawal_approval_request_id':withdrawal_approval_request_id,'withdrawal_request_id':withdrawal_request_id,'decline_reason':decline_reason},function(data){
                                if(isJson(data)){
                                    var response = $.parseJSON(data);
                                    if(response.status == 1){
                                        toastr['success']('You have successfully declined the withdrawal request.','Withdrawal request declined successfully');
                                        window.location.href = response.refer;
                                    }else{
                                        $('.decline_withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                                    }
                                }else{
                                    $('.decline_withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data+'</div>');
                                }
                                mApp.unblock('.decline_withdrawal_request');
                            });
                        }
                    }
                });
            }
        });
        $('.decline').on('click',function(){
            $(".approve_withdrawal_request").slideUp();
            $(".withdrawal_request").slideUp();
            $(".decline_withdrawal_request").slideDown();

            
        });
        

        $('.back_to_respond').on('click',function(e){
            e.preventDefault();
            $(".approve_withdrawal_request").slideUp();
            $(".decline_withdrawal_request").slideUp();
            $(".withdrawal_request").slideDown();
        });

        $('#resend_approval_request').on('click',function(){
            mApp.block('.approve_withdrawal_request', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Resending approval code...'
            });
            //send verification code here
            var withdrawal_approval_request_id = $('input[name="withdrawal_approval_request_id"]').val(); 
            $.ajax({
                type: "POST",
                data: {'id':withdrawal_approval_request_id},
                url: '<?php echo base_url("ajax/withdrawals/send_approval_code"); ?>',
                success: function (data, i, n, r) {
                    
                    if(isJson(data)){
                        var response = $.parseJSON(data);
                        if(response.status == 1){
                            $('#approval_code').val('');
                            //redirect to withdrawal requests
                            toastr['success']('Approval code has been successfully resent to your registered phone number','Success');
                        }else{
                            $('.withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                        }
                    }else{
                        $('.withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing your request. Please try again.</div>');
                    }
                    mApp.unblock('.approve_withdrawal_request');
                },
                error: function(){
                    $('.withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing your request. Please try again.</div>');
                    mApp.unblock('.withdrawal_request');
                },
                always: function(){
                    $('.withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing your request. Please try again.</div>');
                    mApp.unblock('.approve_withdrawal_request');
                }
            });

           
        });
        
        $('#verify_approval_code').on('click',function(){
            if($('#approval_code').val() == ''){
                $('#approval_code').parent().addClass('has-danger');
            }else{
                mApp.block('.approve_withdrawal_request', {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Verifying approval code...'
                });
                var withdrawal_approval_request_id = $('input[name="withdrawal_approval_request_id"]').val(); 
                var approval_code = $('input[name="approval_code"]').val(); 

                $.ajax({
                    type: "POST",
                    data: {'id':withdrawal_approval_request_id,'approval_code':approval_code},
                    url: '<?php echo base_url("ajax/withdrawals/verify_approval_code"); ?>',
                    success: function (data, i, n, r) {
                        
                        if(isJson(data)){
                            var response = $.parseJSON(data);
                            if(response.status == 1){
                                //redirect to withdrawal requests
                                swal({
                                    title:"Approve request?",
                                    text:"The approval code was successfully verified",
                                    type:"info",
                                    showCancelButton:1,
                                    reverseButtons:1,
                                    confirmButtonText:"Yes, approve!"
                                }).then(function(e){
                                    if(e.value){
                                        mApp.block('.approve_withdrawal_request', {
                                            overlayColor: 'grey',
                                            animate: true,
                                            type: 'loader',
                                            state: 'primary',
                                            message: 'Approving request...'
                                        });
                                        //aprrove request here
                                        $.post('<?php echo base_url("ajax/withdrawals/approve_withdrawal_request"); ?>',{'withdrawal_approval_request_id':withdrawal_approval_request_id},function(data){
                                            if(isJson(data)){
                                                var response = $.parseJSON(data);
                                                if(response.status == 1){
                                                    //redirect to withdrawal requests
                                                    toastr['success']('You have successfully approved the withdrawal request.','Success');
                                                    window.location.href = response.refer;
                                                }else{
                                                    $('.approve_withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                                                }
                                            }else{
                                                $('.approve_withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing your request. Please try again.</div>');
                                            }
                                            mApp.unblock('.approve_withdrawal_request');
                                        });
                                    }
                                });
                            }else{
                                swal({
                                    title:"Error",
                                    text:response.message,
                                    type:"error",
                                });
                            }
                            mApp.unblock('.approve_withdrawal_request');
                        }else{
                            $('.approve_withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing your request. Please try again.</div>');
                            mApp.unblock('.approve_withdrawal_request');
                        }
                        
                    },
                    error: function(){
                        $('.approve_withdrawal_request .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing your request. Please try again.</div>');
                        mApp.unblock('.approve_withdrawal_request');
                    },
                    always: function(){
                       
                        setTimeout(function(){mApp.unblock('.approve_withdrawal_request');}, 2000);
                    }
                });
            }
        });

        $('.have_approval_code').on('click',function(){
            $(".withdrawal_request").slideUp();
            $(".approve_withdrawal_request").slideDown();
        });
    });
</script>
