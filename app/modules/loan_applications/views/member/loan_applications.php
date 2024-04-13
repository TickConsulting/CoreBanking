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
        <div class="tab-pane " id="my_application" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane active show" id="pending_applications" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane" id="declined_applications" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane" id="approved_applications" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane" id="disbursement_failed" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane" id="disbursed" role="tabpanel" style="min-height: 180px;">
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
                      <!--   <tr>
                            <th nowrap>
                                <?php echo translate('Application By'); ?>
                            </th>
                            <td class="requested_by"></td>
                        </tr> -->
                        <tr>
                            <th nowrap>
                                <?php echo translate('Amount'); ?> (<?php echo $this->group_currency;?>)
                            </th>
                            <td class="amount"></td>
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
                    </tbody>
                </table>
                <div class="row cancel-application" style="display: none;">
                    <div class="col-md-12">
                        <a href="#" class="btn btn-danger btn-sm m-btn  m-btn m-btn--icon mb-4" id="cancel-application" data-id="">
                            <span>
                                <i class="la la-warning"></i>
                                <span>
                                    <?php echo translate('Cancel Application'); ?>
                                </span>
                            </span>
                        </a>
                    </div>
                </div>
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

    $("a[href='#disbursed']").on('shown.bs.tab', function(e) {
       if($.trim($('#disbursed').html()) == ""){
            load_member_disbursed_applications();
        }
    });

    $("a[href='#disbursement_failed']").on('shown.bs.tab', function(e) {
       if($.trim($('#disbursement_failed').html()) == ""){
            load_member_disbursement_failed_applications();
        }
    });

    $("a[href='#pending_applications']").on('shown.bs.tab', function(e) {
       if($.trim($('#pending_applications').html()) == ""){
            load_pending_member_loan_applications();
        }
    });

    $("a[href='#approved_applications']").on('shown.bs.tab', function(e) {
       if($.trim($('#approved_applications').html()) == ""){
            load_member_approved_loan_applications_pending_disbursement();
        }
    });

    $("a[href='#declined_applications']").on('shown.bs.tab', function(e) {
       if($.trim($('#declined_applications').html()) == ""){
            load_declined_member_loan_applications();
        }
    });

    $(document).on('click','.get_loan_application',function(){
        var id = $(this).attr('id');
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/loan_applications/get_loan_application/"); ?>'+id,
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        $('#get_loan_application_modal .loan_approval_requests').html(data.loan_approval_requests);
                        $('#get_loan_application_modal .loan_type').html('<strong>:</strong> '+data.loan_name);
                        $('#get_loan_application_modal .request_date').html('<strong>:</strong> '+data.applied_on);
                        // $('#get_loan_application_modal .requested_by').html('<strong>:</strong> '+data.applied_by);
                        $('#get_loan_application_modal .amount').html('<strong>:</strong> '+data.amount_applied);
                       
                        if(data.is_approved == 1){
                            $('#get_loan_application_modal .badge_holder').html('<span class="m-badge m-badge--success m-badge--wide">Approved</span>');
                            $('.cancel-application').hide();
                            if(data.status == 1){
                                $('#get_loan_application_modal .badge_holder').append('<span class="m-badge m-badge--brand m-badge--wide">Disbursed</span>');
                            }else if(data.status == 2){
                                $('#get_loan_application_modal .badge_holder').append('<span class="m-badge m-badge--danger m-badge--wide">Disbursement Failed</span>');
                            }
                        }else if(data.is_declined == 1){
                            $('#get_loan_application_modal .badge_holder').html('<span class="m-badge m-badge--danger m-badge--wide">Declined</span>');
                            $('#get_loan_application_modal .declined_by').html('<strong>:</strong> '+data.declined_by).parent().slideDown('slow');
                            $('#get_loan_application_modal .decline_reason').html('<strong>:</strong> '+data.decline_reason).parent().slideDown('slow');
                            $('.cancel-application').hide();
                        }else{
                            $('#get_loan_application_modal .badge_holder').html('<span class="m-badge m-badge--warning m-badge--wide">Pending Approval</span>');
                            $('.cancel-application').show();
                            $('#cancel-application').attr('data-id',id);
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

    $(document).on('click','#cancel-application',function(){
        var id = $(this).attr('data-id');
        $('#cancel-application').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
        swal({
            title:"Are you sure?",
            text:"You won't be able to revert this!",
            type:"question",
            showCancelButton:!0,
            confirmButtonText:"Yes, cancel it!"
        }).then(function(e){
            if(e.value){
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url("ajax/loan_applications/cancel_loan_application/"); ?>'+id,
                    success: function(response) {
                        if(isJson(response)){
                            var result = $.parseJSON(response);
                            if(result.status == 1){
                                swal("Success!","The application has been cancelled.","success");
                                $('#get_loan_application_modal .close').trigger('click');
                                load_pending_member_loan_applications();
                            }else{
                                $('#get_loan_application_modal .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+result.message+'</div>');
                            }
                            $('#cancel-application').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                        }else{
                            $('#get_loan_application_modal .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>We encountered an error fetching the data, please refresh and try again</div>');
                            $('#cancel-application').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                        }
                    }
                });
            }else{
                $('#cancel-application').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
            }
        }); 

        
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
        $('#get_loan_application_modal .modal-header, #get_loan_application_modal .loan_approval_requests, #get_loan_application_modal .request_details,#get_loan_application_modal .decline,#get_loan_application_modal .error').slideUp('fast');
        $('#get_loan_application_modal .loan_type, #get_loan_application_modal .request_date, #get_loan_application_modal .requested_by,#get_loan_application_modal .amount,#get_loan_application_modal .badge_holder,#get_loan_application_modal .loan_approval_requests, #get_loan_application_modal .declined_by,#get_loan_application_modal .decline_reason').html('');
    });
});

$(window).on('load',function(){
    load_pending_member_loan_applications();
});

// var controller = "<?php echo $this->uri->segment(1, 0); ?>";
// var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
// var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
// var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

function load_pending_member_loan_applications(){
    mApp.block('#pending_applications', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loan_applications/get_pending_member_loan_applications"); ?>',
        dataType : "html",
            success: function(response) {
                $('#pending_applications').html(response);
                mApp.unblock('#pending_applications');
        }
    });
}

function load_member_approved_loan_applications_pending_disbursement(){
    mApp.block('#approved_applications', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loan_applications/get_member_approved_loan_applications_pending_disbursement"); ?>',
        dataType : "html",
            success: function(response) {
                $('#approved_applications').html(response);
                mApp.unblock('#approved_applications');
        }
    });
}

function load_declined_member_loan_applications(){
    mApp.block('#declined_applications', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loan_applications/get_declined_member_loan_applications"); ?>',
        dataType : "html",
            success: function(response) {
                $('#declined_applications').html(response);
                mApp.unblock('#declined_applications');
        }
    });
}


function load_member_disbursed_applications(){
    mApp.block('#disbursed', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loan_applications/get_disbursed_member_loan_applications"); ?>',
        dataType : "html",
            success: function(response) {
                $('#disbursed').html(response);
                mApp.unblock('#disbursed');
        }
    });
}


function load_member_disbursement_failed_applications(){
    mApp.block('#disbursement_failed', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loan_applications/get_disbursement_failed_member_loan_applications"); ?>',
        dataType : "html",
            success: function(response) {
                $('#disbursement_failed').html(response);
                mApp.unblock('#disbursement_failed');
        }
    });
}

</script>