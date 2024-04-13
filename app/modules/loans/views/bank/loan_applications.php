<div class="withdrawal_applications">
    <span class="error"></span>
    <ul class="nav nav-tabs  m-tabs-line m-tabs-line--success" role="tablist">
        <!-- <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link" data-toggle="tab" href="#my_application" role="tab" aria-selected="false"><i class="la la-list-o"></i>
                <?php echo translate('My Applications'); ?>
            </a>
        </li> -->
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link  active show" data-toggle="tab" href="#pending_applications" role="tab" aria-selected="false"><i class="la la-hourglass-o"></i>
                <?php echo translate('Pending Applications'); ?>
            </a>
        </li>
       
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link declined_applications" data-toggle="tab" href="#declined_applications" role="tab" aria-selected="true"><i class="la la-ban"></i>
                <?php echo translate('Declined Applications'); ?>
            </a>
        </li>
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link approved_applications" data-toggle="tab" href="#approved_applications" role="tab" aria-selected="true"><i class="la la-check-circle"></i>
                <?php echo translate('Approved Applications'); ?>
            </a>
        </li>
    </ul>                        
    <div class="tab-content">
       <!--  <div class="tab-pane" id="my_application" role="tabpanel" style="min-height: 180px;">
        </div> -->
        <div class="tab-pane  active show" id="pending_applications" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane" id="approved_applications" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane" id="declined_applications" role="tabpanel" style="min-height: 180px;">
            
        </div>
    </div> 
</div>



<div class="modal fade" id="get_loan_application_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header" style="display: none;">
            <h5 class="modal-title" id="exampleModalLabel">
                <?php echo translate('Loan Application'); ?>
                <span class="badge_holder">
                </span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body" style="min-height: 150px;">
            <div class="alert alert-dismissible fade show m-alert m-alert--outline m-alert--air m-0 mb-4 pb-0 application_details" role="alert" style="display: none;">
                <table class="table table-hover table-sm table-borderless">
                    <tbody>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Loan Type'); ?>
                            </th>
                            <td class="loan_type"></td>
                        </tr>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Application date'); ?>
                            </th>
                            <td class="application_date"></td>
                        </tr>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Application By'); ?>
                            </th>
                            <td class="applicationed_by"></td>
                        </tr>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Amount'); ?> (<?php echo $this->group_currency;?>)
                            </th>
                            <td class="amount"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
          
            <div class="table-responsive loan_approval_requests">

            </div>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="guarantorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header" style="display: none;">
            <h5 class="modal-title" id="exampleModalLabel">
                <?php echo translate('Guarantor Approvals'); ?>
                <span class="badge_holder">
                </span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
        </div>
        <?php echo form_open_multipart($this->uri->uri_string(), 'class="form_submit m-form m-form--state" role="form" id="guarantor_approval_loan_form"'); ?>
            <div class="modal-body" style="min-height: 150px;">                
                <div id='radio_action_holder' class="form-group m-form__group row pt-0 m--padding-0" style="display: none;">
                    <div class="col-lg-12 m-form__group-sub">
                        <label><?php echo translate('Do you want to Approve or Decline as the guarantor of this loan');?></label>
                        <div class="m-radio-inline">
                            <?php 
                                if($this->input->post('Yes')==1){
                                    $Yes = TRUE;
                                    $No = FALSE;
                                }else if($this->input->post('No')==0){
                                    $Yes = FALSE;
                                    $No = TRUE;
                                }else{
                                    $Yes = TRUE;
                                    $No = FALSE;
                                }
                            ?>
                            <label class="m-radio m-radio--solid m-radio--brand">
                                <?php echo form_radio('guarantor_response',1,$Yes,""); ?>
                                <?php echo translate('Yes');?>
                                <span></span>
                            </label>

                            <label class="m-radio m-radio--solid m-radio--brand">
                                <?php echo form_radio('guarantor_response',0,$No,""); ?>
                                <?php echo translate('No');?>
                                <span></span>
                            </label>
                        </div>                        
                        <span class="m-form__help"><?php echo translate('Please select one option');?></span>
                    </div>
                </div> 
                <?php echo form_hidden('guarantor_id',''); ?>
                <div class="guarantor_approval_form" style="display: none;">
                    <div class="form-group m-form__group row pt-0 m--padding-0">
                        <div class="col-sm-12 m-form__group-sub m-input--air ">
                            <label>
                                <?php echo translate('Approve / Decline Comment');?>
                            </label>
                            <?php
                                $textarea = array(
                                    'name'=>'comment',
                                    'id'=>'',
                                    'class'=>'form-control m-input--air',
                                    'value'=> $this->input->post('comment')?:'',
                                    'cols'=>30,
                                    'rows'=>6,
                                    'maxlength'=>200,
                                    'class'=>'form-control maxlength',
                                    'placeholder'=>'Approve /Decline Comment'
                                ); 
                                echo form_textarea($textarea); 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group m-form__group row pt-0 m--padding-0 " id="form_actions_holder" style="display: none;" >
                    <div class="col-lg-12 col-md-12">
                        <span class="float-lg-left float-md-left float-sm-left float-xl-left">
                            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="approve_member_loan_button" type="button">
                                <?php echo translate('Save Changes');?>
                            </button>
                            &nbsp;&nbsp;
                            <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form " type="button" id="decline_member_loan_button" data-dismiss="modal">
                                <?php echo translate('cancel');?>
                            </button> 
                        </span>
                    </div>
                </div>
            </div>
        <?php echo form_close() ?>
    </div>
  </div>
</div>

<div class="modal fade" id="signatoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header signatory_modal_header" style="display: none;">
            <h5 class="modal-title " id="exampleModalLabel">
                <?php echo translate('Signatory Approvals'); ?>
                <span class="badge_holder">
                </span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
        </div>
        <?php echo form_open_multipart($this->uri->uri_string(), 'class="form_submit m-form m-form--state" role="form" id="signatory_approval_form"'); ?>
            <div class="modal-body" style="min-height: 150px;">                
                <div id='signatory_radio_action_holder' class="form-group m-form__group row pt-0 m--padding-0" style="display: none;">
                    <div class="col-lg-12 m-form__group-sub">
                        <label><?php echo translate('Do you want to Approve or Decline as the signatory of this loan');?></label>
                        <div class="m-radio-inline">
                            <?php 
                                if($this->input->post('Yes')==1){
                                    $Yes = TRUE;
                                    $No = FALSE;
                                }else if($this->input->post('No')==0){
                                    $Yes = FALSE;
                                    $No = TRUE;
                                }else{
                                    $Yes = TRUE;
                                    $No = FALSE;
                                }
                            ?>
                            <label class="m-radio m-radio--solid m-radio--brand">
                                <?php echo form_radio('signatory_response',1,$Yes,""); ?>
                                <?php echo translate('Yes');?>
                                <span></span>
                            </label>

                            <label class="m-radio m-radio--solid m-radio--brand">
                                <?php echo form_radio('signatory_response',0,$No,""); ?>
                                <?php echo translate('No');?>
                                <span></span>
                            </label>
                        </div>                        
                        <span class="m-form__help"><?php echo translate('Please select one option');?></span>
                    </div>
                </div>
                <?php echo form_hidden('signatory_id',''); ?>
                <div class="form-group m-form__group row pt-0 m--padding-0" id="account_form_holder" style="display: none;">
                    <div class="col-sm-12 m-form__group-sub m-input--air " >
                        <label>
                            <?php echo translate('Account');?>
                            <span class="required">*</span>
                        </label>
                        <?php echo form_dropdown('account_id',array(''=>'--Select Account--')+$active_accounts,'','id="account_id" class="form-control m-select2-append"  ') ?>
                    </div>
                </div>
                <div class="signatory_approval_form_holder" style="display: none;">
                    <div class="form-group m-form__group row pt-0 m--padding-0">
                        <div class="col-sm-12 m-form__group-sub m-input--air ">
                            <label>
                                <?php echo translate('Approve / Decline Comment');?>
                            </label>
                            <?php
                                $textarea = array(
                                    'name'=>'comment',
                                    'id'=>'',
                                    'class'=>'form-control m-input--air',
                                    'value'=> $this->input->post('comment')?:'',
                                    'cols'=>30,
                                    'rows'=>6,
                                    'maxlength'=>200,
                                    'class'=>'form-control maxlength',
                                    'placeholder'=>'Approve /Decline Comment'
                                ); 
                                echo form_textarea($textarea); 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group m-form__group row pt-0 m--padding-0 " id="form_actions_signatory_holder" style="display: none;" >
                    <div class="col-lg-12 col-md-12">
                        <span class="float-lg-left float-md-left float-sm-left float-xl-left">
                            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="approve_as_signatory_button" type="button">
                                <?php echo translate('Save Changes');?>
                            </button>
                            &nbsp;&nbsp;
                            <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form " type="button" id="decline_member_button" data-dismiss="modal">
                                <?php echo translate('cancel');?>
                            </button> 
                        </span>
                    </div>
                </div>
            </div>
        <?php echo form_close() ?>
    </div>
  </div>
</div>


<script>
$(document).ready(function(){
    $('.m-select2-append').select2({
        width: "100%",
        placeholder:{
            id: '-1',
            text: "--Select option--",
        }, 
    });
    SnippetGuarantorAction.init();
    SnippetSignatoryAction.init();

    $("a[href='#pending_applications']").on('shown.bs.tab', function(e) {
       if($.trim($('#pending_applications').html()) == ""){
            load_group_pending_applications();
        }
    });

    $("a[href='#approved_applications']").on('shown.bs.tab', function(e) {
       if($.trim($('#approved_applications').html()) == ""){
            load_group_approved_applications();
        }
    });

    $("a[href='#declined_applications']").on('shown.bs.tab', function(e) {
       if($.trim($('#declined_applications').html()) == ""){
            load_group_declined_applications();
        }
    });

    $(document).on('click','.get_loan_application',function(){
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/loans/get_loan_application/"); ?>'+$(this).attr('id'),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        $('#get_loan_application_modal .loan_approval_requests').html(data.loan_approval_requests);
                        $('#get_loan_application_modal .loan_type').html('<strong>:</strong> '+data.loan_name);
                        $('#get_loan_application_modal .application_date').html('<strong>:</strong> '+data.applied_on);
                        $('#get_loan_application_modal .applicationed_by').html('<strong>:</strong> '+data.applied_by);
                        $('#get_loan_application_modal .amount').html('<strong>:</strong> '+data.amount_applied);
                       
                        if(data.is_approved == 1){
                            $('#get_loan_application_modal .badge_holder').html('<span class="m-badge m-badge--success m-badge--wide">Approved</span>');
                        }else if(data.is_declined == '1'){
                            $('#get_loan_application_modal .badge_holder').html('<span class="m-badge m-badge--danger m-badge--wide">Declined</span>');
                        }else{
                            $('#get_loan_application_modal .badge_holder').html('<span class="m-badge m-badge--warning m-badge--wide">Pending Approval</span>');
                        }
                        mApp.unblock('#get_loan_application_modal .modal-body');
                        $('#get_loan_application_modal .modal-header, #get_loan_application_modal .loan_approval_requests, #get_loan_application_modal .application_details').slideDown('slow');
                    }else{
                        $('.application_details .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                    }
                }
            }
        );
    });

    $('#get_loan_application_modal').on('shown.bs.modal',function(){
        mApp.block('#get_loan_application_modal .modal-body', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Getting loan application data...'
        });
    });

    $('#get_loan_application_modal').on('hidden.bs.modal',function(){
        $('#get_loan_application_modal .modal-header, #get_loan_application_modal .loan_approval_requests, #get_loan_application_modal .application_details').slideUp('fast');
        $('#get_loan_application_modal .loan_type, #get_loan_application_modal .application_date, #get_loan_application_modal .applicationed_by,#get_loan_application_modal .amount,#get_loan_application_modal .badge_holder,#get_loan_application_modal .loan_approval_requests').html('');
    });

    $('#guarantorModal').on('hidden.bs.modal',function(){
        $('.modal-header, .modal-body .loan_details ,.modal-body #radio_action_holder, .modal-body .guarantor_approval_form , .modal-footer #form_actions_holder').slideUp('fast');
    });

    $('#signatoryModal').on('hidden.bs.modal',function(){
        $('.modal-header, .modal-body #signatory_radio_action_holder ,.modal-body .signatory_approval_form_holder,.modal-body #account_form_holder, .modal-footer #form_actions_signatory_holder ').slideUp('fast');
    });

    $(document).on('click','.approve_application',function(e){
        var id = $(this).attr('id');
        swal({
            title:"Are you sure?",
            text:"You won't be able to revert this!",
            type:"question",
            showCancelButton:!0,
            confirmButtonText:"Yes, approve it!"
        }).then(function(e){
            if(e.value){
                e.value&&swal("Approved!","The application has been approved.","success");
                $('#get_loan_application_modal .close').click();
                load_group_pending_applications();
            }
        }); 
    });

    $(document).on('click','.decline_application',function(e){
        var id = $(this).attr('id');
        swal({
            title:"Are you sure?",
            text:"You won't be able to revert this!",
            type:"question",
            showCancelButton:!0,
            confirmButtonText:"Yes, decline it!"
        }).then(function(e){
            if(e.value){
                e.value&&swal("Declined!","The application has been declined.","success");
                $('#get_withdrawal_application_modal .close').click();
                load_group_pending_applications();
            }

           
        }); 
    });

    $(document).on('click','#loan_application_guarantor_action',function(){
        var guarantor_id = $(this).attr("data-id");
        if(guarantor_id ==''){
            setTimeout(function(e){
                toastr['warning']('Could not get your loan guarantor details','warning');                
            },3000);
        }else{
            $('#guarantorModal').modal('show');
            mApp.block('#guarantorModal .modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Getting guarantor application data...'
            });
            $.ajax({
                type: "GET",
                url: '<?php echo base_url("ajax/loans/get_guarantor_details/"); ?>'+$(this).attr('data-id'),
                    success: function(response) {
                        if(isJson(response)){
                            var data = $.parseJSON(response);
                            $('#guarantorModal .loan_approval_applications').html(data.loan_approval_applications);
                            $('#guarantorModal .loan_type').html('<strong>:</strong> '+data.loan_name);
                            $('#guarantorModal .application_date').html('<strong>:</strong> '+data.applied_on);
                            $('#guarantorModal .applicationed_by').html('<strong>:</strong> '+data.applied_by);
                            $('#guarantorModal .amount').html('<strong>:</strong> '+data.amount_applied);
                            $('input[name="guarantor_id"]').val(guarantor_id);
                            if(data.is_approved == 1){
                                $('#guarantorModal .badge_holder').html('<span class="m-badge m-badge--success m-badge--wide">Approved</span>');
                            }else if(data.is_declined == '1'){
                                $('#guarantorModal .badge_holder').html('<span class="m-badge m-badge--danger m-badge--wide">Declined</span>');
                            }else{
                                $('#guarantorModal .badge_holder').html('<span class="m-badge m-badge--warning m-badge--wide">Pending Approval</span>');
                            }
                            $('.guarantor_approval_form').slideDown();
                            $('#form_actions_holder').slideDown();
                            $('#radio_action_holder').slideDown();
                            mApp.unblock('#guarantorModal .modal-body');
                            $('#guarantorModal .modal-header, #get_loan_application_modal .loan_approval_applications, #guarantorModal .loan_details').slideDown('slow');
                        }else{
                            $('#error_holder').append('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                        }
                    }
                });
        }
    });

    $(document).on('click','#loan_application_signatory_action',function(){
        var signatory_id = $(this).attr("data-id");
        if(signatory_id ==''){
            setTimeout(function(e){
                toastr['warning']('Could not get your loan signatory details','warning');                
            },3000);
        }else{
            $('#signatoryModal').modal('show');
            mApp.block('#signatoryModal .modal-body',{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Getting signatory application data...'
            });
            $.ajax({
                type: "GET",
                url: '<?php echo base_url("ajax/loans/get_signatory_details/"); ?>'+$(this).attr('data-id'),
                    success: function(response) {
                        if(isJson(response)){
                            var data = $.parseJSON(response);
                            $('input[name="signatory_id"]').val(signatory_id);
                            if(data.is_approved == 1){
                                $('#signatoryModal .badge_holder').html('<span class="m-badge m-badge--success m-badge--wide">Approved</span>');
                            }else if(data.is_declined == '1'){
                                $('#signatoryModal .badge_holder').html('<span class="m-badge m-badge--danger m-badge--wide">Declined</span>');
                            }else{
                                $('#signatoryModal .badge_holder').html('<span class="m-badge m-badge--warning m-badge--wide">Pending Approval</span>');
                            }
                            if(data.account_id){
                                $('#account_id').val(data.account_id).trigger('change')
                            }
                            $('#signatoryModal #signatory_radio_action_holder').slideDown();
                            $('#signatoryModal .signatory_approval_form_holder').slideDown();
                            $('#signatoryModal .modal-body #account_form_holder').slideDown();                            
                            $('#signatoryModal #form_actions_signatory_holder').slideDown();
                            $('.signatory_modal_header').slideDown();
                            mApp.unblock('#signatoryModal .modal-body');
                        }else{
                            $('#error_holder').append('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                        }
                    }
                });
        }
    });   


});

$(window).on('load',function(){
    load_group_pending_applications();
});

// var controller = "<?php echo $this->uri->segment(1, 0); ?>";
// var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
// var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
// var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

function load_group_pending_applications(){
    mApp.block('#pending_applications', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loans/get_pending_group_loan_applications"); ?>',
        dataType : "html",
            success: function(response) {
                $('#pending_applications').html(response);
                mApp.unblock('#pending_applications');
        }
    });
}

function load_group_approved_applications(){
    mApp.block('#approved_applications', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loans/get_approved_group_loan_applications"); ?>',
        dataType : "html",
            success: function(response) {
                $('#approved_applications').html(response);
                mApp.unblock('#approved_applications');
        }
    });
}

function load_group_declined_applications(){
    mApp.block('#declined_applications', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loans/get_declined_group_loan_applications"); ?>',
        dataType : "html",
            success: function(response) {
                $('#declined_applications').html(response);
                mApp.unblock('#declined_applications');
        }
    });
}

</script>