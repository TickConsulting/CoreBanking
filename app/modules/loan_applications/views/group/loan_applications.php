<div class="withdrawal_applications">
    <span class="error"></span>
    <ul class="nav nav-tabs  m-tabs-line m-tabs-line--success" role="tablist">
       <!--  <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link active show" data-toggle="tab" href="#my_application" role="tab" aria-selected="false"><i class="la la-list-o"></i>
                <?php echo translate('My Applications'); ?>
            </a>
        </li> -->
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link  active show" data-toggle="tab" href="#pending_applications" role="tab" aria-selected="false"><i class="la la-hourglass-o"></i>
                <?php echo translate('Pending'); ?>
            </a>
        </li>
        
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link declined_applications" data-toggle="tab" href="#declined_applications" role="tab" aria-selected="true"><i class="la la-ban"></i>
                <?php echo translate('Declined'); ?>
            </a>
        </li>
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link approved_applications" data-toggle="tab" href="#approved_applications" role="tab" aria-selected="true"><i class="la la-check-circle"></i>
                <?php echo translate('Approved'); ?>
            </a>
        </li>

        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link disbursement_failed" data-toggle="tab" href="#disbursement_failed" role="tab" aria-selected="true"><i class="la la-check-circle"></i>
                <?php echo translate('Disbursement Failed'); ?>
            </a>
        </li>

        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link disbursed" data-toggle="tab" href="#disbursed" role="tab" aria-selected="true"><i class="la la-check-circle"></i>
                <?php echo translate('Disbursed'); ?>
            </a>
        </li>
    </ul>                        
    <div class="tab-content">
        <div class="tab-pane " id="my_application" role="tabpanel" style="min-height: 180px;"></div>
        <div class="tab-pane active show" id="pending_applications" role="tabpanel" style="min-height: 180px;"></div>
        <div class="tab-pane" id="approved_applications" role="tabpanel" style="min-height: 180px;"></div>
        <div class="tab-pane" id="declined_applications" role="tabpanel" style="min-height: 180px;"></div>
        <div class="tab-pane" id="disbursement_failed" role="tabpanel" style="min-height: 180px;"></div>
        <div class="tab-pane" id="disbursed" role="tabpanel" style="min-height: 180px;"></div>
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
            <div class="alert alert-dismissible fade show m-alert m-alert--outline m-alert--air m-0 mb-4 pb-0 request_details" role="alert" style="display: none;">
                <span class="error"></span>
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
                            <td class="request_date"></td>
                        </tr>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Application By'); ?>
                            </th>
                            <td class="requested_by"></td>
                        </tr>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Amount'); ?> (<?php echo $this->group_currency;?>)
                            </th>
                            <td class="amount"></td>
                        </tr>
                        <tr style="display: none;">
                            <th nowrap>
                                <?php echo translate('Disbursing Account'); ?>
                            </th>
                            <td class="disbursing_account"></td>
                        </tr>
                        <tr style="display: none;" class="decline">
                            <th nowrap>
                                <?php echo translate('Declined By'); ?>
                            </th>
                            <td class="declined_by"></td>
                        </tr>
                        <tr style="display: none;" class="decline">
                            <th nowrap>
                                <?php echo translate('Decline Reason'); ?>
                            </th>
                            <td class="decline_reason"></td>
                        </tr>
                        <tr style="display: none;">
                            <th nowrap>
                                <?php echo translate('Disbursement Fail Reason'); ?>
                            </th>
                            <td class="disbursement_failed_reason"></td>
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

    $("a[href='#disbursed']").on('shown.bs.tab', function(e) {
       if($.trim($('#disbursed').html()) == ""){
            load_group_disbursed_applications();
        }
    });

    $("a[href='#disbursement_failed']").on('shown.bs.tab', function(e) {
       if($.trim($('#disbursement_failed').html()) == ""){
            load_group_disbursement_failed_applications();
        }
    });

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
            url: '<?php echo base_url("ajax/loan_applications/get_loan_application/"); ?>'+$(this).attr('id'),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        $('#get_loan_application_modal .loan_approval_requests').html(data.loan_approval_requests);
                        $('#get_loan_application_modal .loan_type').html('<strong>:</strong> '+data.loan_name);
                        $('#get_loan_application_modal .request_date').html('<strong>:</strong> '+data.applied_on);
                        $('#get_loan_application_modal .requested_by').html('<strong>:</strong> '+data.applied_by);
                        $('#get_loan_application_modal .amount').html('<strong>:</strong> '+data.amount_applied);
                        if(data.disbursing_account){
                            $('#get_loan_application_modal .disbursing_account').html('<strong>:</strong> '+(data.disbursing_account?data.disbursing_account.account_name:'')+'<small> Selected by '+data.account_set_by+'</small>').parent().slideDown();
                        }
                        if(data.is_approved == 1){
                            $('#get_loan_application_modal .badge_holder').html('<span class="m-badge m-badge--success m-badge--wide">Approved</span>');
                            if(data.status == 1){
                                $('#get_loan_application_modal .badge_holder').append('<span class="m-badge m-badge--brand m-badge--wide">Disbursed</span>');
                            }else if(data.status == 2){
                                $('#get_loan_application_modal .badge_holder').append('<span class="m-badge m-badge--danger m-badge--wide">Disbursement Failed</span>');
                                $('#get_loan_application_modal .disbursement_failed_reason').html('<strong>:</strong> '+(data.disbursement_fail_reason?data.disbursement_fail_reason:'')).parent().slideDown();
                            }
                        }else if(data.is_declined == 1){
                            $('#get_loan_application_modal .badge_holder').html('<span class="m-badge m-badge--danger m-badge--wide">Declined</span>');
                            $('#get_loan_application_modal .declined_by').html('<strong>:</strong> '+data.declined_by).parent().slideDown('slow');
                            $('#get_loan_application_modal .decline_reason').html('<strong>:</strong> '+data.decline_reason).parent().slideDown('slow');
                        }else{
                            $('#get_loan_application_modal .badge_holder').html('<span class="m-badge m-badge--warning m-badge--wide">Pending Approval</span>');
                        }
                        mApp.unblock('#get_loan_application_modal .modal-body');
                        $('#get_loan_application_modal .modal-header, #get_loan_application_modal .loan_approval_requests, #get_loan_application_modal .request_details').slideDown('slow');
                    }else{
                        $('.request_details .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
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
            message: 'Getting loan request data...'
        });
    });

    $('#get_loan_application_modal').on('hidden.bs.modal',function(){
        $('#get_loan_application_modal .modal-header, #get_loan_application_modal .loan_approval_requests, #get_loan_application_modal .request_details,#get_loan_application_modal .decline').slideUp('fast');
        $('#get_loan_application_modal .disbursement_failed_reason').parent().slideUp();

        $('#get_loan_application_modal .loan_type, #get_loan_application_modal .request_date, #get_loan_application_modal .requested_by,#get_loan_application_modal .amount,#get_loan_application_modal .badge_holder,#get_loan_application_modal .loan_approval_requests, #get_loan_application_modal .declined_by,#get_loan_application_modal .decline_reason').html('');
    });

    $('#guarantorModal').on('hidden.bs.modal',function(){
        $('.modal-header, .modal-body .loan_details ,.modal-body #radio_action_holder, .modal-body .guarantor_approval_form , .modal-footer #form_actions_holder').slideUp('fast');
    });

    $('#signatoryModal').on('hidden.bs.modal',function(){
        $('.modal-header, .modal-body #signatory_radio_action_holder ,.modal-body .signatory_approval_form_holder,.modal-body #account_form_holder, .modal-footer #form_actions_signatory_holder ').slideUp('fast');
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
        url: '<?php echo base_url("ajax/loan_applications/get_pending_group_loan_applications"); ?>',
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
        url: '<?php echo base_url("ajax/loan_applications/get_approved_group_loan_applications_pending_disbursement"); ?>',
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
        url: '<?php echo base_url("ajax/loan_applications/get_declined_group_loan_applications"); ?>',
        dataType : "html",
            success: function(response) {
                $('#declined_applications').html(response);
                mApp.unblock('#declined_applications');
        }
    });
}

function load_group_disbursed_applications(){
    mApp.block('#disbursed', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loan_applications/get_disbursed_group_loan_applications"); ?>',
        dataType : "html",
            success: function(response) {
                $('#disbursed').html(response);
                mApp.unblock('#disbursed');
        }
    });
}


function load_group_disbursement_failed_applications(){
    mApp.block('#disbursement_failed', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loan_applications/get_disbursement_failed_group_loan_applications"); ?>',
        dataType : "html",
            success: function(response) {
                $('#disbursement_failed').html(response);
                mApp.unblock('#disbursement_failed');
        }
    });
}

</script>