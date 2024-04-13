<?php echo form_hidden('loan_application_id',$loan_application->id,' id="loan_application_id" '); ?>
<?php echo form_hidden('loan_type_id',$loan_application->loan_type_id,' id="loan_type_id" '); ?>
<?php echo form_hidden('loan_amount',$loan_application->loan_amount,' id="loan_amount" '); ?>
<?php echo form_hidden('repayment_period',$loan_application->repayment_period,' id="repayment_period" '); ?>
<?php echo form_hidden('signatory_approval_request_id',$signatory_approval_request->id,' id="signatory_approval_request_id" '); ?>

<div class="loan_application">
    <span class="error">
    </span>
    <div class="alert m-alert--default" role="alert">
        <?php echo translate('Hi'); ?> <strong> <?php echo $this->member->first_name.' '.$this->member->last_name; ?></strong>. <?php echo translate('Please respond to this loan application made by'); ?> <?php echo $this->active_group_member_options[$loan_application->member_id];?>
    </div>
    <table class="table table-hover table-borderless">
        <tbody>
            <tr>
                <th nowrap>
                    <?php echo translate('Loan Type'); ?>
                </th>
                <td class="transaction_type"><strong>:</strong> <?php echo isset($loan_type_options[$loan_application->loan_type_id])?$loan_type_options[$loan_application->loan_type_id]:''; ?></td>
            </tr>
            <tr>
                <th nowrap>
                    <?php echo translate('Loan Amount'); ?>
                </th>
                <td class="request_date"><strong>:</strong> <?php echo $this->group_currency.' '.number_to_currency($loan_application->loan_amount); ?></td>
            </tr>
            <tr>
                <th nowrap>
                    <?php echo translate('Requested By'); ?>
                </th>
                <td class="requested_by"><strong>:</strong> <?php echo $this->active_group_member_options[$loan_application->member_id];?>
                </td>
            </tr>
            <tr>
                <th nowrap>
                    <?php echo translate('Requested On'); ?>
                </th>
                <td class="recipient"><strong>:</strong>
                   <?php echo timestamp_to_date_and_time($loan_application->created_on); ?>
                </span>
            </td>
            </tr>
            <tr class="disbursing_account" style="cursor: pointer; display: none;">
                <th nowrap="">
                    <?php echo translate('Disbursing Account'); ?>
                </th>
                <td><strong>:</strong>
                    <span style="cursor: pointer; display: none;" class="change_account no_account_set">
                        No disbursing account selected
                        <br>
                        &nbsp;
                        <a href="javascript:;" class="btn btn-sm btn-primary m-btn m-btn--icon action_button" id="50">
                            <span>
                                <i class="la la-cogs"></i>
                                <span>
                                    <?php echo translate('Select Account Now'); ?>&nbsp;&nbsp; 
                                </span>
                            </span>
                        </a>
                    </span>
                    <span style="cursor: pointer; display: none;" class="change_account account_set">
                        <span class="account_name">
                        </span>
                        <br>
                        
                        <span class="account_set_by" style="display: block;">
                        <!-- <br> -->
                        </span>
                        &nbsp;
                        <a href="javascript:;" class="btn btn-sm btn-primary m-btn m-btn--icon action_button change-account">
                            <span>
                                <i class="la la-cogs"></i>
                                <span>
                                    <?php echo translate('Change'); ?>&nbsp;&nbsp; 
                                </span>
                            </span>
                        </a>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="row">
        <div class="col-12 m--align-left">
            <button type="button" class="btn btn-sm m-btn--pil btn-info m-btn m-btn--custom m-3" id="loan_btn_amortization">
                <?php echo translate('View Armotization'); ?>
            </button>
            <button type="button" class="btn btn-sm m-btn--pil btn-danger m-btn m-btn--custom m-3" data-id="'.$pending_loan_guarantorship_request->id.'"  data-toggle="modal" data-target="#decline_signatory_request_modal">
                <?php echo translate('Decline'); ?>
            </button>
            <button type="button" class="btn btn-sm m-btn--pil btn-primary m-btn m-btn--custom approve">
                <?php echo translate('Approve'); ?>
            </button>
        </div>
    </div>
</div>

<div class="set_account_holder" style="display: none;">
    <div class="alert m-alert--default" role="alert">
        <strong><?php echo translate('Note'); ?>: </strong> <?php echo translate('Selecting your group wallet will automaticaly disburse the loan once approved by all group officials'); ?>.
    </div>
    <form class="m-form m-form--dfit m-form--label-align-right m-form--group-seperator m-form--state">
        <div class="form-group m-form__group row">
            <div class="col-lg-6">
                <div class="input-group">
                    <?php echo form_dropdown('account',array(''=>'Select account')+translate($account_options)+array('0'=>"Add Account"),$loan_application->account_id?:'',' class="form-control m-select2  account" ');?>
                </div>
            </div>
        </div>
        <div class="m-form__actions m-form__actions pl-0 pr-0">
            <div class="row">
                <div class="col-lg-6">
                    <button class="btn btn-secondary back_to_respond float-left">
                        <?php echo translate('Cancel'); ?>
                    </button>
                    <button class="btn btn-primary float-right" type="button" id="change_account">
                        <?php echo translate('Save Account'); ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-group m-form__group row d-none">
            <div class="col-lg-6">
                <button class="btn btn-secondary back_to_respond">
                    <i class="la la-hand-o-left"></i>
                    <?php echo translate('Back'); ?>
                </button>
            </div>
        </div>               
    </form>
</div>

<div class="modal fade" id="create_new_account_pop_up" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Create New Account');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active show" data-toggle="tab" href="#bank_account_tab" onClick="handle_tab_switch('bank_account')">
                            <?php echo translate('Bank');?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#sacco_account_tab" onClick="handle_tab_switch('sacco_account')">
                            <?php echo translate('Group');?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#mobile_money_account_tab" onClick="handle_tab_switch('mobile_money_account')">
                            <?php echo translate('Mobile Money');?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#petty_cash_account_tab" onClick="handle_tab_switch('petty_cash_account')">
                            <?php echo translate('Petty Cash');?>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active show" id="bank_account_tab" role="tabpanel">
                        <?php echo form_open($this->uri->uri_string(),'class=" bank_account_form form_submit m-form m-form--state" id="bank_account_form" role="form"'); ?>
                            <div class="m-form__section m-form__section--first">
                                <span class="error"></span>
                                <div class="form-group m-form__group">
                                    <label for="example_input_full_name">
                                        <?php echo translate('Account Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_name','','id="bank_account_name" class="form-control" placeholder="Account Name"'); ?>
                                  <!--   <span class="m-form__help">
                                        <?php echo translate('Enter your account name as registered');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group">
                                    <label>
                                        <?php echo translate('Bank Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('bank_id',array(''=>'--Select Bank--')+$banks,'','id="bank_id" class="form-control m-select2"  ') ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Select the bank your account is registered to');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group bank_branch_id" style="display: none;">
                                    <label>
                                        <?php echo translate('Bank Branch');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('bank_branch_id',array(''=>'--Select Bank Name First--'),'','class="form-control m-select2" id = "bank_branch_id"  ') ?>
                                    <!-- <span class="m-form__help">
                                        <?php echo translate('Select the bank branch your account is registered to');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group bank_account_number" style="display: none;">
                                    <label>
                                        <?php echo translate('Account Number');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_number','',' id="bank_account_number" class="form-control" placeholder="Account Number"'); ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Enter your account number as registered');?>
                                    </span> -->
                                </div>

                                <div class="row">
                                    <div class="col-md-12 m--align-right">
                                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                            <?php echo translate('Cancel');?>
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="create_bank_account">
                                            <?php echo translate('Submit');?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>

                    <div class="tab-pane" id="sacco_account_tab" role="tabpanel">
                        <?php echo form_open($this->uri->uri_string(),'class=" sacco_account_form form_submit m-form m-form--state" id="sacco_account_form" role="form"'); ?>
                            <div class="m-form__section m-form__section--first">
                                <span class="error"></span>
                                <div class="form-group m-form__group">
                                    <label for="example_input_full_name">
                                        <?php echo translate('Account Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_name','','class="form-control" placeholder="Account Name" id="sacco_account_name" '); ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Enter your account name as registered');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group">
                                    <label>
                                        <?php echo translate('Group Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('sacco_id',array(''=>'--Select Sacco--')+$saccos,'','class="form-control m-select2" id="sacco_id"  ') ?>
                                  <!--   <span class="m-form__help">
                                        <?php echo translate('Select the Group your account is registered to');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group sacco_branch_id" style="display: none;">
                                    <label>
                                        <?php echo translate('Group Branch');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('sacco_branch_id',array(''=>'--No branch records found--'),'','class="form-control m-select2" id = "sacco_branch_id"  ') ?>
                                    <!-- <span class="m-form__help">
                                        <?php echo translate('Select the Group your account is registered to');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group sacco_account_number" style="display: none;">
                                    <label>
                                        <?php echo translate('Account Number');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_number','','class="form-control" placeholder="Account Number" id="sacco_account_number"'); ?>
                                    <!-- <span class="m-form__help">
                                        <?php echo translate('Enter your account number as registered');?>
                                    </span> -->
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 m--align-right">
                                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                            <?php echo translate('Cancel');?>
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="create_sacco_account">
                                            <?php echo translate('Submit');?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>

                    <div class="tab-pane" id="mobile_money_account_tab" role="tabpanel">
                        <?php echo form_open($this->uri->uri_string(),'class=" mobile_money_account_form form_submit m-form m-form--state" id="mobile_money_account_form" role="form"'); ?>
                            <div class="m-form__section m-form__section--first">
                                <span class="error"></span>
                                <div class="form-group m-form__group">
                                    <label for="example_input_full_name">
                                        <?php echo translate('Mobile Money Account Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_name','','class="form-control" placeholder="Mobile Money Account Name" id="mobile_money_account_name" '); ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Enter your account name');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group">
                                    <label>
                                        <?php echo translate('Mobile Money Provider');?>
                                        <span class="required">*</span>
                                    </label>
                                     <?php echo form_dropdown('mobile_money_provider_id',array(''=>'--Select Mobile Money Provider--')+$mobile_money_providers,'','class="form-control  m-select2" id="mobile_money_provider_id"  ') ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Select the mobile money provider your account is registered to');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group mobile_money_account_number" style="display: none;">
                                    <label>
                                        <?php echo translate('Account Number');?>/
                                        <?php echo translate('Till Number');?>/
                                        <?php echo translate('Phone Number');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_number','','class="form-control" placeholder="Account Number / Phone Number / Till Number" id="mobile_money_account_number"'); ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Enter your account number as registered');?>
                                    </span> -->
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 m--align-right">
                                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                            <?php echo translate('Cancel');?>
                                        </button>
                                        <button type="submit" id="create_mobile_money_account" class="btn btn-primary">
                                            <?php echo translate('Submit');?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>

                    <div class="tab-pane" id="petty_cash_account_tab" role="tabpanel">
                        <?php echo form_open($this->uri->uri_string(),'class=" petty_cash_account_form form_submit m-form m-form--state" id="petty_cash_account_form" role="form"'); ?>
                            <div class="m-form__section m-form__section--first">
                                <span class="error"></span>
                                <div class="form-group m-form__group">
                                    <label for="example_input_full_name">
                                        <?php echo translate('Petty Cash Account Name');?>
                                        <span class="required">*</span>                                            
                                    </label>
                                    <?php echo form_input('account_name','','class="form-control slug_parent" placeholder="Petty Cash Account Name " id="petty_cash_account_name"'); ?>
                                    <?php echo form_hidden('slug','','class="form-control slug"'); ?>     
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Enter your account name');?>
                                    </span> -->
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 m--align-right">
                                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                            <?php echo translate('Cancel');?>
                                        </button>
                                        <button type="submit" id="create_petty_cash_account" class="btn btn-primary">
                                            <?php echo translate('Submit');?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
            <?php echo translate('Loan Amortization Schedule'); ?>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <div class="loan_amortization" id="loan_amortization"> </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-primary m-btn  m-btn m-btn--icon" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="decline_signatory_request_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Decline Loan Application'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="decline_signatory_request"'); ?>
                    <div class="form-group">
                        <label for="message-text" class="form-control-label">
                            <?php echo translate('Reason'); ?>:
                        </label>
                        <textarea class="form-control" id="decline_reason"></textarea>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?php echo translate('Cancel'); ?>
                </button>
                <button type="button" class="btn btn-primary decline">
                    <?php echo translate('Decline'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<a class="inline d-none" data-toggle="modal" data-target="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account" data-backdrop="static" data-keyboard="false"><?php echo translate('Add Account');?></a>
<script>
    $(document).ready(function(){
        $('.m-select2').select2({
            width: "100%",
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });

        //add account modal close eventt
        $('#create_new_account_pop_up').on('hidden.bs.modal', function () {
            $(':input','#create_new_account_pop_up')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number,#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number,#mobile_money_account_tab .mobile_money_account_number').slideUp();
        });

        $('#change_account').on('click',function(){
            if($('.account').val() == ''){
                $(this).parent().addClass('has-danger').append('<div class="form-control-feedback">Please select an account</div>');
            }else{
                mApp.block('.set_account_holder', {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Loading...'
                });
                var account_id = $('.account').val();
                var id = $('input[name="loan_application_id"]').val(); 
                $.post('<?php echo site_url('ajax/loan_applications/set_loan_application_disbursing_account');?>',{'id':id,'account_id':account_id},
                function(response){
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            toastr['success']('You have successfully set the disbursing account.','Disbursing account set successfully');
                            $(".set_account_holder").slideUp();
                            $(".decline_loan_application").slideUp();
                            $(".loan_application").slideDown();
                            get_disbursing_account();
                        }else{
                            $('.set_account_holder .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                        }
                    }else{
                        $('.set_account_holder .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                    }
                    mApp.unblock('.set_account_holder');
                });
            }
        });

        $(document).on('click','.approve',function(e){
            e.preventDefault();
            var id = $('input[name="signatory_approval_request_id"]').val(); 
            bootbox.prompt({
                title: "Enter your password to approve this application",
                inputType: 'password',
                required: true,
                callback: function(password){
                    if(password){
                        mApp.block('.loan_application', {
                            overlayColor: 'grey',
                            animate: true,
                            type: 'loader',
                            state: 'primary',
                            message: 'Approving...'
                        });
                        $.post('<?php echo base_url("ajax/loans/group_signatory_approve_loan_application"); ?>',{'password':password,'id':id},function(data){
                            if(isJson(data)){
                                var response = $.parseJSON(data);
                                if(response.status == 1){
                                    toastr['success']('You have successfully approved the loan application.','Loan application approved successfully');
                                    window.location = response.refer;                                 
                                }else{
                                    $('.loan_application .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                                }
                            }else{
                                $('.loan_application .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error occured processing your request, please try again</div>');
                            }
                            mApp.unblock('.loan_application');
                        });
                    }
                }
            });
        });

        $(document).on('click','.decline',function(e){
            if($('#decline_reason').val() == ''){
                console.log('in');
                $('#decline_reason').parent().addClass('has-danger').append('<div class="form-control-feedback">Please give a reason</div>');
            }else{
                var id = $('input[name="signatory_approval_request_id"]').val(); 
                var decline_reason = $('#decline_reason').val();
                bootbox.prompt({
                    title: "Enter your password to decline this application",
                    inputType: 'password',
                    required: true,
                    callback: function(password){
                        if(password){
                            $('#decline_signatory_request_modal .close').trigger('click');
                            mApp.block('.loan_application', {
                                overlayColor: 'grey',
                                animate: true,
                                type: 'loader',
                                state: 'primary',
                                message: 'Declining...'
                            });
                            $.post('<?php echo base_url("ajax/loans/group_signatory_decline_loan_application"); ?>',{'decline_reason':decline_reason,'password':password,'id':id},function(data){
                                if(isJson(data)){
                                    var response = $.parseJSON(data);
                                    if(response.status == 1){
                                        toastr['success']('You have successfully declined the loan application.','Loan application declined successfully');
                                        window.location = response.refer;                                 
                                    }else{
                                        $('.loan_application .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                                    }
                                }else{
                                    $('.loan_application .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error occured processing your request, please try again</div>');
                                }
                                mApp.unblock('.loan_application');
                            });
                        }
                    }
                });
            }
            
        });

        $(document).on('click','#loan_btn_amortization',function(){
            // $(this).addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0)
            mApp.block('.loan_application',{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Generating Amortization Schedule....'
            });

            var loan_type_id = $('input[name="loan_type_id"]').val();
            var loan_amount = $('input[name="loan_amount"]').val();
            var repayment_period  = $('#repayment_period').val();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("ajax/loans/loan_calculator"); ?>',
                data:{loan_type_id:loan_type_id,loan_amount:loan_amount,repayment_period:repayment_period},
                dataType : "html",
                    success: function(response) {
                        // $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                        $('#exampleModal').modal('show'); // show bootstrap modal when complete loaded
                        $('#loan_amortization').html(response);                    
                        mApp.unblock('.loan_application');
                    },
                    error:function(response){
                        // $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1) 
                        $('#loan_amortization').html(response);                    
                        mApp.unblock('.loan_application');                           
                    }
                }
            );  
        });

        $('.back_to_respond').on('click',function(e){
            e.preventDefault();
            $(".set_account_holder").slideUp();
            $(".decline_loan_application").slideUp();
            $(".loan_application").slideDown();
        });

        $(document).on('change','.account',function(){
            if($(this).val() == ''){
                $(this).parent().addClass('has-danger').append('<div class="form-control-feedback">Please select an account</div>');
            }else if($(this).val()=='0'){
                $('#add_new_account').trigger('click');
                $(this).val("").trigger('change');
                $('#create_new_account_pop_up .select2-append').select2({
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });
            }
        });
        
        $(document).on('change','select[name="bank_id"]',function(){
            var empty_branch_list = $('#bank_branch_id').find('select').html();
            var branch_id = '';
            var bank_id = $(this).val();
            $('.bank_branch_id, .bank_account_number').slideUp();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            if(bank_id){
                $.post('<?php echo site_url('group/bank_accounts/ajax_get_bank_branches');?>',{'bank_id':bank_id,'branch_id':branch_id},
                function(data){
                    $('#bank_branch_id').html(data);
                    $('#create_new_account_pop_up .select2-append').select2({
                        width: "100%",
                        placeholder:{
                            id: '-1',
                            text: "--Select option--",
                        }, 
                    });
                    $('.bank_branch_id').slideDown();
                    mApp.unblock('.modal-body');
                });
            }else{
                $('#bank_branch_id').html('<select name="bank_id" class="form-control select2" id="bank_branch_id">'+empty_branch_list+'</select>');
                mApp.unblock('.modal-body');
            }
        });

        $(document).on('change','select[name="bank_branch_id"]',function(){
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            var bank_branch_id = $(this).val();
            if(bank_branch_id){
                $('.bank_account_number').slideDown();
                mApp.unblock('.modal-body');
            }else{
                $('.bank_account_number').slideUp();
                mApp.unblock('.modal-body');
            }
        });

        $(document).on('change','select[name="sacco_id"]',function(){
            var empty_branch_list =$('#sacco_branch_id').find('select').html();
            var branch_id = '';
            var sacco_id = $(this).val();
            $('.sacco_branch_id, .sacco_account_number').slideUp();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            if(sacco_id){
                $.post('<?php echo site_url('group/sacco_accounts/ajax_get_sacco_branches');?>',{'sacco_id':sacco_id,'branch_id':''},
                function(data){
                    $('#sacco_branch_id').html(data);
                    $('#create_new_account_pop_up .select2-append').select2({
                        width: "100%",
                        placeholder:{
                            id: '-1',
                            text: "--Select option--",
                        }, 
                    });
                    $('.sacco_branch_id').slideDown();
                    mApp.unblock('.modal-body');
                });
            }else{
                $('#sacco_branch_id').html('<select name="bank_id" class="form-control select2" id="bank_branch_id">'+empty_branch_list+'</select>');
                mApp.unblock('.modal-body');
            }
        });

        $(document).on('change','select[name="sacco_branch_id"]',function(){
            var element = $(this);
            var sacco_branch_id = $(this).val();
            $('.sacco_account_number').slideUp();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            if(sacco_branch_id){
                $('.sacco_account_number').slideDown();
                mApp.unblock('.modal-body');
            }else{
                mApp.unblock('.modal-body');
            }
        });

        $(document).on('change','select[name="mobile_money_provider_id"]',function(){
            var mobile_money_provider_id = $(this).val();
            $('.mobile_money_account_number').slideUp();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            if(mobile_money_provider_id){
                $('.mobile_money_account_number').slideDown();
                mApp.unblock('.modal-body');
            }else{
                mApp.unblock('.modal-body');
            }
        });

        $(document).on('submit','#bank_account_form',function(e){
            e.preventDefault();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            var form = $(this);
            RemoveDangerClass(form);
            $('#bank_account_tab .error').html('').slideUp();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("ajax/bank_accounts/create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.account').each(function(){
                                $(this).append('<option value="bank-' + data.bank_account.id + '">'+data.bank_account.bank_details+' - ' + data.bank_account.account_name + ' ('+data.bank_account.account_number+')</option>').trigger('change');
                            });
                            $('.set_account_holder select[name="account"]').val("bank-"+data.bank_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new bank account, you can now select it in the accounts dropdown.','Bank account added successfully');
                        }else{
                            $('#bank_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#bank_account_tab input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#bank_account_tab select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                        }
                        
                    }else{
                        $('#bank_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                    }
                    mApp.unblock('.modal-body');
                }
            });
        });

        $(document).on('submit','#sacco_account_form',function(e){
            e.preventDefault();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            var form = $(this);
            RemoveDangerClass(form);
            $('#create_new_account_pop_up .error').html('');
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/sacco_accounts/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.account').each(function(){
                                $(this).append('<option value="sacco-' + data.sacco_account.id + '">'+data.sacco_account.sacco_details+' - ' + data.sacco_account.account_name + ' ('+data.sacco_account.account_number+')</option>').trigger('change');
                            });
                            $('.set_account_holder select[name="account"]').val("sacco-"+data.sacco_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new sacco account, you can now select it in the accounts dropdown.','Sacco account added successfully');
                        }else{
                            $('#sacco_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#sacco_account_tab input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#sacco_account_tab select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                        }
                        
                    }else{
                        $('#sacco_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                    }
                    mApp.unblock('.modal-body');
                }
            });
        });

        $(document).on('submit','#mobile_money_account_form',function(e){
            e.preventDefault();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            var form = $(this);
            RemoveDangerClass(form);
            $('#create_new_account_pop_up .error').html('').slideUp();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/mobile_money_accounts/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.account').each(function(){
                                $(this).append('<option value="mobile-' + data.mobile_money_account.id + '">'+data.mobile_money_account.mobile_money_provider_details+' - ' + data.mobile_money_account.account_name + ' ('+data.mobile_money_account.account_number+')</option>').trigger('change');
                            });
                            $('.set_account_holder select[name="account"]').val("mobile-"+data.mobile_money_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new mobile money account, you can now select it in the accounts dropdown.','Mobile money account added successfully');
                        }else{
                            $('#mobile_money_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#mobile_money_account_tab input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#mobile_money_account_tab select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                        }
                    }else{
                        $('#mobile_money_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                    }
                    mApp.unblock('.modal-body');
                }
            });
        });

        $(document).on('submit','#petty_cash_account_form',function(e){
            e.preventDefault();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            var form = $(this);
            RemoveDangerClass(form);
            $('#create_new_account_pop_up .error').html('').slideUp();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/petty_cash_accounts/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.account').each(function(){
                                $(this).append('<option value="petty-' + data.petty_cash_account.id + '">' + data.petty_cash_account.account_name + '</option>').trigger('change');
                            });
                            $('.set_account_holder select[name="account"]').val("petty-"+data.petty_cash_account.id).trigger('change');

                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new petty cash account, you can now select it in the accounts dropdown.','Petty cash account added successfully');
                        }else{
                            $('#petty_cash_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    if(key == 'account_slug'){
                                        //skip
                                    }else{
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('#petty_cash_account_tab input[name="account_name"]').parent().addClass('has-danger').append(error_message);
                                    }
                                   
                                });
                            }
                        }
                    }else{
                        $('#petty_cash_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                    }
                    mApp.unblock('.modal-body');
                }
            });
        });
    });

    $(window).on('load',function() {
        get_disbursing_account();
    });

    function get_disbursing_account(){
        mApp.block('.loan_application', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Just a moment...'
        });
        var id = $('input[name="loan_application_id"]').val(); 
        $.post('<?php echo site_url('ajax/loan_applications/get_loan_application_disbursing_account');?>',{'id':id},
        function(response){
            if(isJson(response)){
                var data = $.parseJSON(response);
                $('.account_set .account_set_by').slideUp();;
                if(data.status == 1){
                    if(data.account){
                        $('.account_set .account_name').html(data.account.account_name);
                        if(data.account.is_admin){ //completely deny option to change account
                            $('.change-account').remove();
                            $('.set_account_holder').remove();
                        }else{ //even more aggressive
                            
                        }
                        if(data.account.account_set_by){
                            $('.account_set .account_set_by').html('&nbsp;&nbsp;<small>Selected by '+data.account_set_by+'</small>');
                            $('.account_set .account_set_by').slideDown('slow');
                        }

                        if(data.account.is_default == 1){
                            $('.account_set .account_name').append('&nbsp; <span class="m-badge m-badge--primary m-badge--wide">Auto Disburse</span>');
                        }
                        $('.no_account_set').slideUp();
                        $('.account_set').slideDown();
                        $('.loan_application .disbursing_account').slideDown();
                    }else{
                        $('.account_set').slideUp();
                        $('.no_account_set').slideDown();
                        $('.loan_application .disbursing_account').slideDown();
                        $('.change_account').on('click',function(){
                            $(".loan_application").slideUp();
                            $(".set_account_holder").slideDown();
                        });
                    }
                }else{
                    $('.loan_application .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                }
            }else{
                $('.loan_application .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
            }
            mApp.unblock('.loan_application');
        });
    }

    function handle_tab_switch(tab){
        //check tab
        //clear values on other tabs
        //slide up on other tabs
        $('#create_new_account_pop_up .error').html('').slideUp();
        if(tab == 'bank_account'){
            $(':input','#mobile_money_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#mobile_money_account_tab .mobile_money_account_number').slideUp();
            $(':input','#sacco_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }else if(tab == 'sacco_account'){
            $(':input','#mobile_money_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#mobile_money_account_tab .mobile_money_account_number').slideUp();
            $(':input','#bank_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }else if(tab == 'mobile_money_account'){
            $(':input','#sacco_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number').slideUp();
            $(':input','#bank_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }else if(tab == 'petty_cash_account'){
            $(':input','#sacco_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number').slideUp();
            $(':input','#bank_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number').slideUp();
            $(':input','#mobile_money_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#mobile_money_account_tab .mobile_money_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }
    }
</script>
