<div class="withdrawal_requests">
    <span class="error"></span>
    <ul class="nav nav-tabs  m-tabs-line m-tabs-line--success" role="tablist">
        
     
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link approved_requests" data-toggle="tab" href="#approved_requests" role="tab" aria-selected="true"><i class="la la-check-circle"></i>
                <?php echo translate('Approved'); ?>
            </a>
        </li>
         <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link disbursed_requests" data-toggle="tab" href="#disbursed_requests" role="tab" aria-selected="true"><i class="la la-check-circle"></i>
                <?php echo translate('Disbursed'); ?>
            </a>
        </li>
         <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link disbursement_failed_requests" data-toggle="tab" href="#disbursement_failed_requests" role="tab" aria-selected="true"><i class="la la-check-circle"></i>
                <?php echo translate('Disbursement Failed'); ?>
            </a>
        </li>
    </ul>                        
    <div class="tab-content">
        <div class="tab-pane active show" id="pending_requests" role="tabpanel" style="min-height: 180px;"></div>
        <div class="tab-pane" id="approved_requests" role="tabpanel" style="min-height: 180px;"></div>
        <div class="tab-pane" id="declined_requests" role="tabpanel" style="min-height: 180px;"></div>
        <div class="tab-pane" id="disbursed_requests" role="tabpanel" style="min-height: 180px;"></div>
        <div class="tab-pane" id="disbursement_failed_requests" role="tabpanel" style="min-height: 180px;"></div>
    </div> 
</div>



<div class="modal fade" id="get_withdrawal_request_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header" style="display: none;">
            <h5 class="modal-title" id="exampleModalLabel">
                <?php echo translate('Withdrawal Request'); ?>
                <span class="badge_holder">
                </span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body" style="min-height: 150px;">
            <span class="error"></span>
            <div class="alert alert-dismissible fade show m-alert m-alert--outline m-alert--air m-0 mb-4 pb-0 request_details" role="alert" style="display: none;">
                <table class="table table-hover table-sm table-borderless">
                    <tbody>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Type'); ?>
                            </th>
                            <td class="transaction_type"></td>
                        </tr>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Request date'); ?>
                            </th>
                            <td class="request_date"></td>
                        </tr>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Requested By'); ?>
                            </th>
                            <td class="requested_by"></td>
                        </tr>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Recipient'); ?>
                            </th>
                            <td class="recipient"></td>
                        </tr>
                        <tr style="display: none;">
                            <th nowrap>
                                <?php echo translate('Account To'); ?>
                            </th>
                            <td class="account_to"></td>
                        </tr>
                        <tr>
                            <th nowrap>
                                <?php echo translate('Description'); ?>
                            </th>
                            <td class="description"></td>
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
                        <tr class="fail" style="display: none;">
                            <th nowrap>
                                <?php echo translate('Disbursement Fail Reason'); ?>
                            </th>
                            <td class="fail_reason"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="row cancel-request" style="display: none;">
                    <div class="col-md-12">
                        <a href="#" class="btn btn-danger btn-sm m-btn  m-btn m-btn--icon mb-4" id="cancel-request" data-id="">
                            <span>
                                <i class="la la-warning"></i>
                                <span>
                                    <?php echo translate('Cancel Request'); ?>
                                </span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
          
            <div class="table-responsive withdrawal_approval_requests" style="display: none;">

            </div>
        </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
    $("a[href='#approved_requests']").on('shown.bs.tab', function(e) {
       if($.trim($('#approved_requests').html()) == ""){
            load_disbursement_pending_requests();
        }
    });

    $("a[href='#declined_requests']").on('shown.bs.tab', function(e) {
       if($.trim($('#declined_requests').html()) == ""){
            load_declined_requests();
        }
    });

    $("a[href='#disbursed_requests']").on('shown.bs.tab', function(e) {
       if($.trim($('#disbursed_requests').html()) == ""){
            load_disbursed_requests();
        }
    });

    $("a[href='#disbursement_failed_requests']").on('shown.bs.tab', function(e) {
       if($.trim($('#disbursement_failed_requests').html()) == ""){
            load_disbursement_failed_requests();
        }
    });

    

    $(document).on('click','.get_withdrawal_request',function(){
        var id = $(this).attr('id');
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/withdrawals/get_withdrawal_request/"); ?>'+id,
            dataType : "html",
            success: function(response) {
                if(isJson(response)){
                    var data = $.parseJSON(response);
                    $('#get_withdrawal_request_modal .withdrawal_approval_requests').html(data.withdrawal_approval_requests);
                    $('#get_withdrawal_request_modal .transaction_type').html('<strong>:</strong> '+data.withdrawal_request.type);
                    $('#get_withdrawal_request_modal .request_date').html('<strong>:</strong> '+data.withdrawal_request.request_date);
                    $('#get_withdrawal_request_modal .requested_by').html('<strong>:</strong> '+data.withdrawal_request.requested_by);
                    $('#get_withdrawal_request_modal .description').html('<strong>:</strong> '+data.withdrawal_request.description);
                    
                    if(data.withdrawal_request.withdrawal_for == 6){
                        $('#get_withdrawal_request_modal .account_to').html('<strong>:</strong> '+data.withdrawal_request.account_to).parent().show();
                    }
                    
                    if(data.withdrawal_request.status == 'is_approved'){
                        $('#get_withdrawal_request_modal .badge_holder').html('<span class="m-badge m-badge--success m-badge--wide">Approved</span>');
                        if(data.withdrawal_request.is_disbursed == 1){
                            $('#get_withdrawal_request_modal .badge_holder').append('<span class="m-badge m-badge--primary m-badge--wide">Disbursed</span>');
                        }else if(data.withdrawal_request.is_disbursement_declined == 1){
                            $('#get_withdrawal_request_modal .badge_holder').append('<span class="m-badge m-badge--danger m-badge--wide">Disbursement Failed</span>');
                            $('#get_withdrawal_request_modal .fail_reason').html('<strong>:</strong> '+data.withdrawal_request.disbursement_failed_error_message).parent().show();
                        }else{
                            $('#get_withdrawal_request_modal .badge_holder').append('<span class="m-badge m-badge--secondary m-badge--wide">Disbursement Pending</span>');
                        }
                        $('.cancel-request').hide();
                    }else if(data.withdrawal_request.status == 'is_declined'){
                        $('#get_withdrawal_request_modal .badge_holder').html('<span class="m-badge m-badge--danger m-badge--wide">Declined</span>');
                        $('#get_withdrawal_request_modal .declined_by').html('<strong>:</strong> '+data.withdrawal_request.declined_by).parent().show();
                        $('#get_withdrawal_request_modal .decline_reason').html('<strong>:</strong> '+data.withdrawal_request.decline_reason).parent().show();
                        $('.cancel-request').hide();
                    }else{
                        $('#get_withdrawal_request_modal .badge_holder').html('<span class="m-badge m-badge--warning m-badge--wide">Pending Approval</span>');
                        $('.cancel-request').show();
                        $('#cancel-request').attr('data-id',id);
                    }
                    mApp.unblock('#get_withdrawal_request_modal .modal-body');
                    $('#get_withdrawal_request_modal .modal-header, #get_withdrawal_request_modal .withdrawal_approval_requests, #get_withdrawal_request_modal .request_details').slideDown('slow');
                }else{
                    
                    $('#get_withdrawal_request_modal .close').trigger('click');
                    $('.withdrawal_requests .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>We encountered an error fetching the data, please refresh and try again</div>');
                }
            }
        });
    });

    $(document).on('click','#cancel-request',function(){
        var id = $(this).attr('data-id');
        $('#cancel-request').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
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
                    url: '<?php echo base_url("ajax/withdrawals/cancel_withdrawal_request/"); ?>'+id,
                    success: function(response) {
                        if(isJson(response)){
                            var result = $.parseJSON(response);
                            if(result.status == 1){
                                swal("Success!","The request has been cancelled.","success");
                                $('#get_withdrawal_request_modal .close').trigger('click');
                                load_pending_requests();
                            }else{
                                $('#get_withdrawal_request_modal .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+result.message+'</div>');
                            }
                            $('#cancel-request').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                        }else{
                            $('#get_withdrawal_request_modal .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>We encountered an error fetching the data, please refresh and try again</div>');
                            $('#cancel-request').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                        }
                    }
                });

                
            }else{
                $('#cancel-request').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
            }
        }); 
    });

    $('#get_withdrawal_request_modal').on('shown.bs.modal',function(){
        mApp.block('#get_withdrawal_request_modal .modal-body', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Getting withdrawal request data...'
        });
    });

    $('#get_withdrawal_request_modal').on('hidden.bs.modal',function(){
        $('#get_withdrawal_request_modal .modal-header, #get_withdrawal_request_modal .withdrawal_approval_requests, #get_withdrawal_request_modal .request_details, #get_withdrawal_request_modal .decline, #get_withdrawal_request_modal .fail, #get_withdrawal_request_modal cancel-request,#get_withdrawal_request_modal .error').slideUp('fast');
        $('#get_withdrawal_request_modal .request_date, #get_withdrawal_request_modal .requested_by, #get_withdrawal_request_modal .recipient,#get_withdrawal_request_modal .description,#get_withdrawal_request_modal .badge_holder,#get_withdrawal_request_modal .transaction_type,#get_withdrawal_request_modal .withdrawal_approval_requests').html('');
    });

    $(document).on('click','.approve_request',function(e){
        var id = $(this).attr('id');
        swal({
            title:"Are you sure?",
            text:"You won't be able to revert this!",
            type:"question",
            showCancelButton:!0,
            confirmButtonText:"Yes, approve it!"
        }).then(function(e){
            if(e.value){
                e.value&&swal("Approved!","The request has been approved.","success");
                 $('#get_withdrawal_request_modal .close').click();
                load_pending_requests();
            }
        }); 
    });

    $(document).on('click','.decline_request',function(e){
        var id = $(this).attr('id');
        swal({
            title:"Are you sure?",
            text:"You won't be able to revert this!",
            type:"question",
            showCancelButton:!0,
            confirmButtonText:"Yes, decline it!"
        }).then(function(e){
            if(e.value){
                e.value&&swal("Declined!","The request has been declined.","success");
                $('#get_withdrawal_request_modal .close').click();
                load_pending_requests();
            }
        }); 
    });
});

$(window).on('load',function(){
    load_pending_requests();
});

// var controller = "<?php echo $this->uri->segment(1, 0); ?>";
// var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
// var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
// var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

function load_pending_requests(){
    mApp.block('#pending_requests', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/withdrawals/get_pending_withdrawal_requests"); ?>',
        dataType : "html",
            success: function(response) {
                $('#pending_requests').html(response);
                mApp.unblock('#pending_requests');
        }
    });
}

function load_disbursement_pending_requests(){
    mApp.block('#approved_requests', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/withdrawals/get_disbursement_pending_withdrawal_requests"); ?>',
        dataType : "html",
            success: function(response) {
                $('#approved_requests').html(response);
                mApp.unblock('#approved_requests');
        }
    });
}

function load_declined_requests(){
    mApp.block('#declined_requests', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/withdrawals/get_declined_withdrawal_requests"); ?>',
        dataType : "html",
            success: function(response) {
                $('#declined_requests').html(response);
                mApp.unblock('#declined_requests');
        }
    });
}


function load_disbursed_requests(){
    mApp.block('#disbursed_requests', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/withdrawals/get_disbursed_withdrawal_requests"); ?>',
        dataType : "html",
            success: function(response) {
                $('#disbursed_requests').html(response);
                mApp.unblock('#disbursed_requests');
        }
    });
}

function load_disbursement_failed_requests(){
    mApp.block('#disbursement_failed_requests', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/withdrawals/get_disbursement_failed_withdrawal_requests"); ?>',
        dataType : "html",
            success: function(response) {
                $('#disbursement_failed_requests').html(response);
                mApp.unblock('#disbursement_failed_requests');
        }
    });
}
</script>